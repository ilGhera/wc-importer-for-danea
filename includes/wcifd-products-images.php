<?php

function wcifd_products_images() {
	if ( ! function_exists( 'wp_handle_upload' ) ) {
	    require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	//FILE UPLOAD
	$uploadedfile = $_FILES['file'];
	$upload_overrides = array( 'test_form' => false );
	$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

	if (!$movefile || isset($movefile['error'])) {
	    echo 'Errore durante il caricamento delle immagini.';
	    exit;
	}

	//IMAGE URL
	$image_url = $movefile['url'];
	$image_name = preg_replace( '/\.[^.]+$/', '', basename( $image_url ) );
	$name_parts = explode('-000', $image_name);
	$sku = $name_parts[0];
	//Search the sku in a different way
	$sku2 = (strpos($sku, '-')) ? str_replace('-', '/', $sku) : '';

	// The ID of the post this attachment is for.
	$parent_post_id = wcifd_search_product($sku);
	if(!$parent_post_id && $sku2) {
		$parent_post_id = wcifd_search_product($sku2);
	}

	$filetype = wp_check_filetype( basename( $image_url ), null );

	//UPLOAD DIRECTORY
	$wp_upload_dir = wp_upload_dir();
	$dir = $wp_upload_dir['path'] . '/' . basename( $movefile['url'] );

	$attachment = array(
		'guid'           => $wp_upload_dir['url'] . '/' . basename( $image_url ), 
		'post_mime_type' => $filetype['type'],
		'post_title'     => $image_name,
		'post_content'   => '',
		'post_status'    => 'inherit'
	);

	//INSERT ATTACHMENT
	$attach_id = wp_insert_attachment( $attachment, $image_url, $parent_post_id );

	//REQUIRED BY wp_generate_attachment_metadata()
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	$attach_data = wp_generate_attachment_metadata( $attach_id, $dir );
	wp_update_attachment_metadata( $attach_id, $attach_data );

	set_post_thumbnail( $parent_post_id, $attach_id );

	echo 'OK';	
}
