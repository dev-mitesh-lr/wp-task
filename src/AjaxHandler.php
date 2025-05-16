<?php

namespace CUT;

use CUT\Settings;

// Load template rendering function.
require_once dirname(dirname(__FILE__)) . '/includes/template-functions.php';

use function CUT\render_user_detail_view;

class AjaxHandler {

    /**
     * Handles the AJAX request to fetch and return user details.
     *
     * This method verifies the AJAX nonce, retrieves the user ID from the request,
     * makes a remote request to an external API to fetch user data, and returns
     * a rendered HTML view of the user details in the AJAX response.
     *
     * @return void Outputs a JSON response with success or error message.
     */
    public static function get_user_detail() {
        // Verify the nonce for security.
        check_ajax_referer( 'cut_user_nonce', 'nonce' );

        // Get the user ID from the POST request.
        $user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;

        if ( ! $user_id ) {
            wp_send_json_error( [ 'message' => 'Invalid user ID' ] );
        }

        // Get the API URL from settings.
        $api_url = Settings::get_api_url();

        // Make the remote request to fetch user data.
        $response = wp_remote_get( "{$api_url}/{$user_id}" );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( [ 'message' => 'Failed to fetch user details' ] );
        }

        $user = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( empty( $user ) ) {
            wp_send_json_error( [ 'message' => 'User not found' ] );
        }

        // Render the user detail view and return it in the AJAX response.
        $html = render_user_detail_view( $user );

        wp_send_json_success( [ 'html' => $html ] );
    }
}