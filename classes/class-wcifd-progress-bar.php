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
        add_action( 'wp_ajax_get-scheduled-actions', array( $this, 'get_scheduled_actions' ) );

    }

    public function get_scheduled_actions() {

        $actions = as_get_scheduled_actions(
            array(
                'hook'  => 'wcifd_import_product_event',
                'group' => 'wcifd-import-product',
                'status' => 'ActionScheduler_Store::STATUS_PENDING',
            ),
            'ids'
        );

        error_log( 'ACTIONS: ' . print_r( $actions, true ) );

        echo intval( count( $actions ) );

        exit;

    }

    public function catalog_update_admin_notice() {

        /* $output      = '<div class="update-nag notice notice-warning ilghera-notice-warning is-dismissible">'; */
        /* $output     .= '<div class="ilghera-notice__content">'; */
        $output      = '<div id="wcifd-progress">';
        $output     .= '<div id="wcifd-bar">10%</div>';
        /* $output     .= '</div>'; */
        $output     .= '<a href="#" class="start-bar">Start</a>';
        $output     .= '</div>';
        /* $output     .= '</div>'; */

        echo wp_kses_post( $output );

    }
}
new WCIFD_Progress_Bar();

