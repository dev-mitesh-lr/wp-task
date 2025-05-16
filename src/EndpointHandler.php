<?php

namespace CUT;

use CUT\Settings;

class EndpointHandler {

    /**
     * Registers a custom rewrite endpoint for the user directory.
     *
     * Uses the slug from plugin settings to add a new rewrite rule.
     *
     * @return void
     */
    public static function register_endpoint() {
        $slug = Settings::get_slug();
        add_rewrite_rule("^{$slug}/?$", 'index.php?cut_user_directory=1', 'top');
    }

    /**
     * Adds the custom query variable used to detect the endpoint.
     *
     * @param array $vars Array of current query variables.
     * @return array Modified array with the custom query variable.
     */
    public static function add_query_vars( $vars ) {
        $vars[] = 'cut_user_directory';
        return $vars;
    }

    /**
     * Handles the output when the custom endpoint is accessed.
     *
     * Includes the user table view template and stops further processing.
     *
     * @return void
     */
    public static function handle_output() {
        if ( get_query_var( 'cut_user_directory' ) ) {
            include dirname( __FILE__ ) . '/views/user-table.php';
            exit;
        }
    }

    /**
     * Enqueues JavaScript assets only when the custom endpoint is active.
     *
     * Also localizes the script with AJAX URL and security nonce.
     *
     * @return void
     */
    public static function enqueue_assets() {
        if ( get_query_var( 'cut_user_directory' ) ) {
            wp_enqueue_script(
                'cut-user-ajax',
                CUT_PLUGIN_URL . 'assets/js/script.js',
                [ 'jquery' ],
                '1.0',
                true
            );

            wp_localize_script( 'cut-user-ajax', 'cut_ajax', [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'cut_user_nonce' ),
            ] );
        }
    }
}