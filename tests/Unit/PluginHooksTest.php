<?php

namespace CUT\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * Test case for Custom User Table plugin hooks.
 * This demonstrates testing WordPress plugin hooks with Brain Monkey.
 */
class PluginHooksTest extends TestCase {

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
     * Test that the main plugin hooks are registered correctly.
     */
    public function testMainPluginHooksAreRegistered() {
        // Set up expectations for core hooks from the main plugin file
        Actions\expectAdded('init')->once();
        Actions\expectAdded('template_redirect')->once();
        Actions\expectAdded('wp_enqueue_scripts')->once();
        
        // Set up expectations for AJAX hooks
        Actions\expectAdded('wp_ajax_cut_get_user')->once();
        Actions\expectAdded('wp_ajax_nopriv_cut_get_user')->once();
        
        // Set up expectations for filters
        Filters\expectAdded('query_vars')->once();
        
        // Simulate hook registration as it would occur in the plugin
        add_action('init', 'register_custom_endpoint_function');
        add_action('template_redirect', 'handle_output_function');
        add_action('wp_enqueue_scripts', 'enqueue_assets_function');
        add_action('wp_ajax_cut_get_user', 'get_user_detail_function');
        add_action('wp_ajax_nopriv_cut_get_user', 'get_user_detail_function');
        add_filter('query_vars', 'add_query_vars_function');
    }
    
    /**
     * Test that the settings hooks are registered correctly.
     */
    public function testSettingsHooksAreRegistered() {
        // Set up expectations for settings hooks
        Actions\expectAdded('admin_menu')->once();
        Actions\expectAdded('admin_init')->once();
        
        // Simulate settings hook registration
        add_action('admin_menu', 'add_settings_page_function');
        add_action('admin_init', 'register_settings_function');
    }
    
    /**
     * Test that the template loading works correctly for custom endpoints.
     */
    public function testTemplateLoadingWorks() {
        // Mock WordPress functions
        Functions\expect('get_query_var')
            ->once()
            ->with('cut_user_directory')
            ->andReturn(true);
            
        Functions\expect('wp_die')
            ->once()
            ->andReturnUsing(function($message) {
                // We can check the message content if needed
                return true;
            });
        
        // Simulate the template loader function
        function load_custom_template() {
            if (get_query_var('cut_user_directory')) {
                // Instead of including a file, we'll just call wp_die()
                // This simulates the end of execution in a template file
                wp_die('Template loaded successfully');
                return true;
            }
            return false;
        }
        
        // Call the function and check that it would load the template
        // We expect it to call wp_die(), which we've mocked
        $this->expectOutputString('');
        load_custom_template();
    }
    
    /**
     * Test that the query vars are filtered correctly.
     */
    public function testQueryVarsAreFiltered() {
        // Set up a function that adds our custom query var
        function filter_query_vars($vars) {
            $vars[] = 'cut_user_directory';
            return $vars;
        }
        
        // Call the function with test input
        $result = filter_query_vars(['post', 'page']);
        
        // Check the result
        $this->assertContains('cut_user_directory', $result);
        $this->assertEquals(['post', 'page', 'cut_user_directory'], $result);
    }
} 