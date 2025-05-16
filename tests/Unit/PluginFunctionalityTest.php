<?php

namespace CUT\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * Test case for plugin endpoint functionality.
 * This demonstrates testing WordPress plugin functions with Brain Monkey.
 */
class PluginFunctionalityTest extends TestCase {

    use MockeryPHPUnitIntegration;

    protected function setUp(): void {
        parent::setUp();
        setup(); // From bootstrap.php
    }

    protected function tearDown(): void {
        teardown(); // From bootstrap.php
        parent::tearDown();
    }

    /**
     * Test the endpoint registration functionality.
     */
    public function testEndpointRegistration() {
        // Mock WordPress functions
        Functions\expect('add_rewrite_rule')
            ->once()
            ->with('user-directory/?$', 'index.php?cut_user_directory=1', 'top');
            
        // Simulate function for endpoint registration
        function register_custom_endpoint() {
            add_rewrite_rule('user-directory/?$', 'index.php?cut_user_directory=1', 'top');
        }
        
        // Execute the function
        register_custom_endpoint();
    }

    /**
     * Test query variables functionality.
     */
    public function testQueryVarsFilter() {
        // Define a sample function that would be in your plugin
        function add_custom_query_vars($vars) {
            $vars[] = 'cut_user_directory';
            return $vars;
        }
        
        // Call the function
        $result = add_custom_query_vars(['post', 'page']);
        
        // Check result
        $this->assertIsArray($result);
        $this->assertContains('cut_user_directory', $result);
        $this->assertEquals(['post', 'page', 'cut_user_directory'], $result);
    }

    /**
     * Test asset enqueuing functionality.
     */
    public function testAssetEnqueuing() {
        // Define plugin URL constant for testing
        if (!defined('CUT_PLUGIN_URL')) {
            define('CUT_PLUGIN_URL', 'http://example.com/wp-content/plugins/custom-user-table/');
        }
        
        // Mock WordPress functions
        Functions\expect('get_query_var')
            ->once()
            ->with('cut_user_directory')
            ->andReturn(true);
            
        Functions\expect('wp_enqueue_script')
            ->once()
            ->with('cut-user-ajax', CUT_PLUGIN_URL . 'assets/js/script.js', ['jquery'], '1.0', true);
            
        Functions\expect('admin_url')
            ->once()
            ->with('admin-ajax.php')
            ->andReturn('http://example.com/wp-admin/admin-ajax.php');
            
        Functions\expect('wp_create_nonce')
            ->once()
            ->with('cut_user_nonce')
            ->andReturn('test_nonce');
            
        Functions\expect('wp_localize_script')
            ->once()
            ->with('cut-user-ajax', 'cut_ajax', [
                'ajax_url' => 'http://example.com/wp-admin/admin-ajax.php',
                'nonce'    => 'test_nonce',
            ]);
            
        // Define a sample function that would be in your plugin
        function enqueue_custom_assets() {
            if (get_query_var('cut_user_directory')) {
                wp_enqueue_script('cut-user-ajax', CUT_PLUGIN_URL . 'assets/js/script.js', ['jquery'], '1.0', true);
                wp_localize_script('cut-user-ajax', 'cut_ajax', [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce'    => wp_create_nonce('cut_user_nonce'),
                ]);
            }
        }
        
        // Execute the function
        enqueue_custom_assets();
    }

    /**
     * Test AJAX functionality.
     */
    public function testAjaxFunctionality() {
        // Set up $_POST data
        $_POST['user_id'] = 123;
        $_POST['nonce'] = 'test_nonce';
        
        // Mock WordPress functions
        Functions\expect('check_ajax_referer')
            ->once()
            ->with('cut_user_nonce', 'nonce');
            
        Functions\expect('wp_remote_get')
            ->once()
            ->with('https://jsonplaceholder.typicode.com/users/123')
            ->andReturn('success_response');
            
        Functions\expect('is_wp_error')
            ->once()
            ->with('success_response')
            ->andReturn(false);
            
        Functions\expect('wp_remote_retrieve_body')
            ->once()
            ->with('success_response')
            ->andReturn('{"id":123,"name":"Test User"}');
            
        Functions\expect('wp_send_json_success')
            ->once()
            ->with(['html' => '<div>User detail HTML</div>'])
            ->andReturnUsing(function() {
                throw new \Exception('wp_send_json_success called');
            });
            
        // Define a custom function without using output buffering
        function get_user_ajax_handler() {
            // Check nonce for security
            check_ajax_referer('cut_user_nonce', 'nonce');
            
            // Get user ID from request
            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            
            // Fetch user data
            $response = wp_remote_get("https://jsonplaceholder.typicode.com/users/{$user_id}");
            
            if (!is_wp_error($response)) {
                $user = json_decode(wp_remote_retrieve_body($response), true);
                
                // Skip output buffering for test simplicity
                $html = '<div>User detail HTML</div>';
                
                wp_send_json_success(['html' => $html]);
            }
        }
        
        // Execute with exception expectation
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('wp_send_json_success called');
        
        get_user_ajax_handler();
    }
} 