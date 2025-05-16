<?php
/**
 * PHPUnit bootstrap file for Brain Monkey
 */

// First of all, autoload dependencies and define basic constants
require_once __DIR__ . '/../vendor/autoload.php';

// Define WordPress constants that are needed for our tests
if (!defined('ABSPATH')) {
    define('ABSPATH', '/var/www/html/wordpress/');
}

if (!defined('HOUR_IN_SECONDS')) {
    define('HOUR_IN_SECONDS', 60 * 60);
}

// Set up directory constants needed for the tests
define('CUT_PLUGIN_URL', 'http://example.com/wp-content/plugins/custom-user-table/');
define('CUT_PLUGIN_DIR', __DIR__ . '/../');

// Default user directory settings
define('CUT_DEFAULT_SETTINGS', [
    'cut_user_directory_slug' => 'user-directory',
    'cut_user_directory_api_url' => 'https://jsonplaceholder.typicode.com/users',
    'cut_user_directory_cache_duration' => 12 * HOUR_IN_SECONDS,
]);

// Custom exception for WP die simulation - defined here to be available for all tests
if (!class_exists('CUT\\Tests\\Unit\\WPDieException')) {
    class CUT_WPDieException extends \Exception {}
}

/**
 * Setup function that's called before each test
 */
function setup() {
    // Before each test, sets up the WordPress functions mocking and more
    Brain\Monkey\setUp();
    
    // Now we can set up WordPress functions to return specific values
    // This is needed to make our code run in the test environment
    Brain\Monkey\Functions\when('plugin_dir_url')->justReturn(CUT_PLUGIN_URL);
    Brain\Monkey\Functions\when('get_option')->alias(function($option, $default = false) {
        if (isset(CUT_DEFAULT_SETTINGS[$option])) {
            return CUT_DEFAULT_SETTINGS[$option];
        }
        return $default;
    });
}

/**
 * Teardown function that's called after each test
 */
function teardown() {
    // After each test, let's clean up the stuff Brain Monkey created
    Brain\Monkey\tearDown();
} 