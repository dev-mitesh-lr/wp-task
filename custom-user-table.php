<?php
/**
 * Plugin Name: Custom User Table
 * Description: Displays a user table at a custom endpoint using a Composer-powered plugin structure.
 * Version: 1.0
 * Author: Mitesh P
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define plugin URL constant.
define( 'CUT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Autoload classes using Composer.
require_once __DIR__ . '/vendor/autoload.php';

use CUT\EndpointHandler;
use CUT\AjaxHandler;
use CUT\Settings;

/**
 * Register the custom endpoint on 'init'.
 */
add_action( 'init', [ EndpointHandler::class, 'register_endpoint' ] );

/**
 * Add custom query variables using 'query_vars' filter.
 *
 * @param array $vars List of current query vars.
 * @return array Modified query vars.
 */
add_filter( 'query_vars', [ EndpointHandler::class, 'add_query_vars' ] );

/**
 * Handle output on template redirect for the custom endpoint.
 */
add_action( 'template_redirect', [ EndpointHandler::class, 'handle_output' ] );

/**
 * Enqueue necessary assets for the frontend.
 */
add_action( 'wp_enqueue_scripts', [ EndpointHandler::class, 'enqueue_assets' ] );

/**
 * Handle AJAX request to get user detail (for logged-in users).
 */
add_action( 'wp_ajax_cut_get_user', [ AjaxHandler::class, 'get_user_detail' ] );

/**
 * Handle AJAX request to get user detail (for guests).
 */
add_action( 'wp_ajax_nopriv_cut_get_user', [ AjaxHandler::class, 'get_user_detail' ] );

/**
 * Initialize plugin settings.
 */
Settings::init();