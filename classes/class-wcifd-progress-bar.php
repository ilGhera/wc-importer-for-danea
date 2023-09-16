<?php
/**
 * Mostra una progress bar al ricevimento dei prodotti 
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/classes
 *
 * @since 1.6.1
 */

/**
 * Class WCIFD_Progress_Bar
 */
class WCIFD_Progress_Bar {

    public function __construct() {

        add_action( 'admin_notices', array( $this, 'catalog_update_admin_notice' ) );
        add_action( 'wp_ajax_get-total-actions', array( $this, 'get_total_actions' ) );
        add_action( 'wp_ajax_get-scheduled-actions', array( $this, 'get_scheduled_actions' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'data_to_script' ) );

    }

    public function data_to_script() {

        $options = array(
            'completedMessage' => __( 'Products import was completed!', 'wc-importer-for-danea' ),
        );

        wp_localize_script( 'wcifd-admin-nav', 'options', $options );

    }


    public function get_total_actions() {

        $transient = get_transient( 'wcifd-total-actions' );

        echo intval( $transient );

        exit;

    }


    public function get_scheduled_actions() {

        $actions = as_get_scheduled_actions(
            array(
                'hook'     => 'wcifd_import_product_event',
                'group'    => 'wcifd-import-product',
                'status'   => ActionScheduler_Store::STATUS_PENDING,
                'per_page' => -1,
            ),
            'ids'
        );

        if ( 0 === count( $actions ) ) {

            error_log( 'DELETE TRANSIENT' );
            delete_transient( 'wcifd-total-actions' );

        }

        /* error_log( 'ACTIONS: ' . print_r( $actions, true ) ); */

        echo intval( count( $actions ) );

        exit;

    }


    public function catalog_update_admin_notice() {

        $output      = '<div class="update-nag notice notice-warning ilghera-notice-warning catalog-update is-dismissible">';
            $output     .= '<div class="ilghera-notice__content">';
                $output      .= '<div class="ilghera-notice__message">';
                $output      .= '<b>' . esc_html__( 'WC Importer for Danea', 'wc-importer-for-danea' ) . '</b> - '; 
                $output      .= '<span class="wcifd-progress-bar-text">' . esc_html( 'Products import is running.', 'wc-importer-for-danea' ) . '</span>'; 
                $output      .= '</div>';
                $output      .= '<div id="wcifd-progress-bar">';
                    $output     .= '<div id="wcifd-progress"></div>';
                    $output     .= '<span class="precentage">0%</span>';
                $output     .= '</div>';
            $output     .= '</div>';
        $output     .= '</div>';

        echo wp_kses_post( $output );

    }
}
new WCIFD_Progress_Bar();

