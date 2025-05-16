<?php

namespace CUT\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use CUT\AjaxHandler;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

// Custom exception for WP die simulation
class CUT_WPDieException extends \Exception {}

class AjaxHandlerTest extends TestCase {

    use MockeryPHPUnitIntegration;

    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();

        $_POST = [];

        // Catch wp_die via alias
        Functions\when('wp_die')->alias(function() {
            throw new CUT_WPDieException();
        });

        // Mock render function if used by your code
        // Functions\when('CUT\render_user_detail_view')->justReturn('<div>User detail HTML</div>');
    }

    protected function tearDown(): void {
        $_POST = [];
        Monkey\tearDown();
        parent::tearDown();
    }

    public function testGetUserDetailWithInvalidNonce() {
        Functions\expect('check_ajax_referer')
            ->once()
            ->with('cut_user_nonce', 'nonce')
            ->andThrow(new CUT_WPDieException());

        $this->expectException(CUT_WPDieException::class);

        AjaxHandler::get_user_detail();
    }

    public function testGetUserDetailWithInvalidUserId() {
        Functions\expect('check_ajax_referer')
            ->once()
            ->with('cut_user_nonce', 'nonce');

        Functions\expect('wp_send_json_error')
            ->once()
            ->with(['message' => 'Invalid user ID'])
            ->andReturnUsing(function () {
                throw new \Exception('wp_send_json_error called');
            });

        $_POST['user_id'] = 0;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('wp_send_json_error called');

        AjaxHandler::get_user_detail();
    }

    public function testGetUserDetailWithValidData() {
        $settingsMock = Mockery::mock('alias:CUT\Settings');
        $settingsMock->shouldReceive('get_api_url')
            ->once()
            ->andReturn('https://test-api.com/users');
    
        Functions\expect('check_ajax_referer')
            ->once()
            ->with('cut_user_nonce', 'nonce');
    
        Functions\expect('wp_remote_get')
            ->once()
            ->with('https://test-api.com/users/123')
            ->andReturn('success_response');
    
        Functions\expect('is_wp_error')
            ->once()
            ->with('success_response')
            ->andReturn(false);
    
        Functions\expect('wp_remote_retrieve_body')
            ->once()
            ->with('success_response')
            ->andReturn('{"id":123,"name":"Test User"}');
    
        // Don't expect ob_start or ob_get_clean
        // Mock only the render function
        Functions\when('CUT\render_user_detail_view')
            ->justReturn('<div>User detail HTML</div>');
    
        Functions\expect('wp_send_json_success')
            ->once()
            ->with(['html' => '<div>User detail HTML</div>'])
            ->andReturnUsing(function () {
                throw new \Exception('wp_send_json_success called');
            });
    
        $_POST['user_id'] = 123;
    
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('wp_send_json_success called');
    
        AjaxHandler::get_user_detail();
    }
    

    public function testGetUserDetailWithApiError() {
        $settingsMock = Mockery::mock('alias:CUT\Settings');
        $settingsMock->shouldReceive('get_api_url')
            ->once()
            ->andReturn('https://test-api.com/users');

        Functions\expect('check_ajax_referer')
            ->once()
            ->with('cut_user_nonce', 'nonce');

        Functions\expect('wp_remote_get')
            ->once()
            ->with('https://test-api.com/users/123')
            ->andReturn('error_response');

        Functions\expect('is_wp_error')
            ->once()
            ->with('error_response')
            ->andReturn(true);

        Functions\expect('wp_send_json_error')
            ->once()
            ->with(['message' => 'Failed to fetch user details'])
            ->andReturnUsing(function () {
                throw new \Exception('wp_send_json_error called');
            });

        $_POST['user_id'] = 123;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('wp_send_json_error called');

        AjaxHandler::get_user_detail();
    }

    public function testGetUserDetailWithEmptyUserData() {
        $settingsMock = Mockery::mock('alias:CUT\Settings');
        $settingsMock->shouldReceive('get_api_url')
            ->once()
            ->andReturn('https://test-api.com/users');

        Functions\expect('check_ajax_referer')
            ->once()
            ->with('cut_user_nonce', 'nonce');

        Functions\expect('wp_remote_get')
            ->once()
            ->with('https://test-api.com/users/123')
            ->andReturn('success_response');

        Functions\expect('is_wp_error')
            ->once()
            ->with('success_response')
            ->andReturn(false);

        Functions\expect('wp_remote_retrieve_body')
            ->once()
            ->with('success_response')
            ->andReturn('{}');

        Functions\expect('wp_send_json_error')
            ->once()
            ->with(['message' => 'User not found'])
            ->andReturnUsing(function () {
                throw new \Exception('wp_send_json_error called');
            });

        $_POST['user_id'] = 123;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('wp_send_json_error called');

        AjaxHandler::get_user_detail();
    }
}
