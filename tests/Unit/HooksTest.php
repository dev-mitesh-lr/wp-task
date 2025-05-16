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
 * Test case for WordPress hooks.
 * This demonstrates testing with Brain Monkey.
 */
class HooksTest extends TestCase {

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
     * Test that hooks are added at initialization.
     */
    public function testHooksAreAdded() {
        // Set up expectations for add_action
        Actions\expectAdded('init')
            ->once();
            
        Actions\expectAdded('admin_menu')
            ->once();
            
        Actions\expectAdded('admin_init')
            ->once();
            
        Actions\expectAdded('template_redirect')
            ->once();
            
        Actions\expectAdded('wp_enqueue_scripts')
            ->once();
            
        // Set up expectations for ajax actions
        Actions\expectAdded('wp_ajax_cut_get_user')
            ->once();
            
        Actions\expectAdded('wp_ajax_nopriv_cut_get_user')
            ->once();
            
        // Set up expectations for filters
        Filters\expectAdded('query_vars')
            ->once();
        
        // Simulate adding actions
        add_action('init', 'some_function');
        add_action('admin_menu', 'some_function');
        add_action('admin_init', 'some_function');
        add_action('template_redirect', 'some_function');
        add_action('wp_enqueue_scripts', 'some_function');
        add_action('wp_ajax_cut_get_user', 'some_function');
        add_action('wp_ajax_nopriv_cut_get_user', 'some_function');
        
        // Simulate adding filters
        add_filter('query_vars', 'some_filter_function');
    }

    /**
     * Test that we can check if an action is executed.
     */
    public function testActionIsExecuted() {
        // Set up expectations
        Actions\expectDone('init')
            ->once();
        
        // Simulate doing an action
        do_action('init');
    }

    /**
     * Test that a filter is applied correctly.
     */
    public function testFilterIsApplied() {
        // Set up expectations
        Filters\expectApplied('query_vars')
            ->once()
            ->andReturn(['test_var']);
        
        // Simulate applying a filter
        $result = apply_filters('query_vars', []);
        
        // Check the results
        $this->assertEquals(['test_var'], $result);
    }
    
    /**
     * Test that WordPress API functions can be mocked.
     */
    public function testWordPressFunctionMocking() {
        // Mock get_option to return a specific value
        Functions\when('get_option')
            ->justReturn('mocked value');
        
        // Call the function
        $value = get_option('some_option');
        
        // Check the result
        $this->assertEquals('mocked value', $value);
    }
    
    /**
     * Test that we can set expectations on function calls.
     */
    public function testFunctionExpectations() {
        // Set up expectations
        Functions\expect('wp_enqueue_script')
            ->once()
            ->with('script-handle', Mockery::type('string'), [], Mockery::any(), true);
        
        // Call the function
        wp_enqueue_script('script-handle', 'script.js', [], '1.0', true);
    }
} 