<?php
/**
 * Importing product images
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WCIFD_Products_Images
 *
 * @return void
 */
class WCIFD_Products_Images {

	/**
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->handle_image_upload();
	}

	/**
	 * Handle image upload
	 *
	 * @return void
	 */
	public function handle_image_upload() {

		if ( ! function_exists( 'wp_handle_upload' ) ) {

			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {

            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

		$file = isset( $_FILES['file'] ) ? $_FILES['file'] : null;

        if ( ! $file || ! is_array( $file ) || empty( $file['tmp_name'] ) ) {

            error_log( 'WCIFD ERROR | Immagine: nessun file ricevuto.' );

            echo 'OK'; // O un messaggio di errore più specifico per il frontend

            return;
        }

        // Start
        $original_file_name = $file['name']; // Sanitizza il nome originale

        // Ottieni la directory di upload
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['path'];

        // Determina un nome di file univoco, basato sul nome originale
        // Questo è il cuore della soluzione per prevenire il rincorsa di nomi
        $unique_file_name = wp_unique_filename( $upload_path, $original_file_name );

        // Crea un nuovo array $_FILES per wp_handle_upload con il nome univoco desiderato
        // Questo è fondamentale per "forzare" il nome del file se wp_handle_upload non lo fa da solo.
        $uploaded_file_data = array(
            'name'     => $unique_file_name, // Il nome desiderato
            'type'     => $file['type'],
            'tmp_name' => $file['tmp_name'],
            'error'    => $file['error'],
            'size'     => $file['size'],
        );

        /* Delete duplicates - Fai attenzione con questa logica */
        // Se vuoi eliminare le immagini *prima* di caricare la nuova,
        // assicurati che 'name' nel get_posts corrisponda allo SLUG effettivo.
        // Oppure, meglio ancora, usa un meta per la deduplicazione.
        $this->delete_duplicates_by_original_name( $original_file_name ); // Vedi sotto per la modifica

        // End

		/* Delete duplicates */
		/* $this->delete_duplicates( $file ); */

		/* Load image in WP Media */
		$wp_image = wp_handle_upload( $uploaded_file_data, array( 'test_form' => false ) );

		if ( isset( $wp_image['error'] ) ) {

			error_log( 'WCIFD ERROR | Immagine: ' . $file['name'] . ' | ' . print_r( $wp_image['error'], true ) );

			echo 'OK';

			return;

		} elseif ( ! $wp_image ) {

			error_log( 'WCIFD ERROR | Immagine: ' . $file['name'] . ' |  Errore di ricezione' );

			echo 'OK';

			return;
		}

        $filetype = wp_check_filetype( basename( $wp_image['file'] ), null ); // Usa il nome file effettivamente caricato da wp_handle_upload

         $attachment = array(
            'guid'           => $wp_image['url'],
            'post_mime_type' => $filetype['type'],
            'post_title'     => sanitize_title( pathinfo( $unique_file_name, PATHINFO_FILENAME ) ), // Titolo basato sul nome univoco
            'post_content'   => '',
            'post_status'    => 'inherit',
        );

		/* Add attachment */
		$attach_id = wp_insert_attachment( $attachment, $wp_image['file'] );

		/* Generate and update metadata */
		$attach_data = wp_generate_attachment_metadata( $attach_id, $wp_image['file'] );
		wp_update_attachment_metadata( $attach_id, $attach_data );

         // Fondamentale: Memorizza il nome originale del file come meta.
        // Questo sarà il tuo riferimento stabile per abbinare ai prodotti.
        update_post_meta( $attach_id, '_wcifd_original_filename', $original_file_name );

		echo 'OK';
	}

    /**
     * Delete duplicates by original filename stored in post meta.
     *
     * @param string $original_filename The original filename from the Danea system.
     *
     * @return void
     */
    public function delete_duplicates_by_original_name( $original_filename ) {

        $image_ids_to_delete = array();

        // 1. Cerca usando il nuovo post meta (il metodo preferito)
        $args_new_meta = array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'meta_query'     => array(
                array(
                    'key'     => '_wcifd_original_filename',
                    'value'   => $original_filename,
                    'compare' => '=',
                ),
            ),
            'fields'         => 'ids',
            'posts_per_page' => -1,
            'no_found_rows'  => true,
        );
        $image_ids_new_meta = get_posts( $args_new_meta );

        if ( ! empty( $image_ids_new_meta ) ) {
            // Trovati duplicati con il nuovo meta: elimina questi
            $image_ids_to_delete = $image_ids_new_meta;
        } else {
            // 2. Se nessun duplicato trovato con il nuovo meta, prova con il vecchio metodo (post_name)
            // Questo cattura le immagini importate prima dell'introduzione del meta
            $sanitized_slug = sanitize_title( pathinfo( $original_filename, PATHINFO_FILENAME ) );

            $args_old_slug = array(
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'name'           => $sanitized_slug, // Ricerca esatta sullo slug del post
                'fields'         => 'ids',
                'posts_per_page' => -1,
                'no_found_rows'  => true,
            );
            $image_ids_old_slug = get_posts( $args_old_slug );

            if ( ! empty( $image_ids_old_slug ) ) {
                // Trovati duplicati con il vecchio slug: elimina questi
                $image_ids_to_delete = $image_ids_old_slug;
            }
        }

        // Processa l'eliminazione
        if ( ! empty( $image_ids_to_delete ) ) {
            foreach ( $image_ids_to_delete as $id ) {
                wp_delete_post( $id, true ); // true per forzare la cancellazione dal database
                error_log( 'WCIFD INFO | Eliminato duplicato (criterio misto) per ' . $original_filename . ' (ID: ' . $id . ')' );
            }
        }
    }
}

