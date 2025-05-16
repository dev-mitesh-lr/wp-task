<?php

namespace CUT;

class Settings {

    const OPTION_NAME = 'cut_user_directory_slug';
    const DEFAULT_SLUG = 'user-directory';
    const CACHE_DURATION_OPTION_NAME = 'cut_user_directory_cache_duration';
    const DEFAULT_CACHE_DURATION = 12 * HOUR_IN_SECONDS; // Default cache duration (12 hours)

    public static function init() {
        add_action('admin_menu', [self::class, 'add_settings_page']);
        add_action('admin_init', [self::class, 'register_settings']);
        add_action('update_option_' . self::OPTION_NAME, [__CLASS__, 'maybe_flush_rewrite_rules'], 10, 2);
    }

      public static function maybe_flush_rewrite_rules($old, $new) {
        if ($old !== $new) {
            flush_rewrite_rules();
        }
    }

    public static function add_settings_page() {
        add_options_page(
            'User Directory Settings',
            'User Directory',
            'manage_options',
            'cut-user-directory-settings',
            [self::class, 'render_settings_page']
        );
    }

    public static function register_settings() {
        // Register options
        register_setting('cut_user_directory_settings', self::OPTION_NAME);
        register_setting('cut_user_directory_settings', 'cut_user_directory_api_url');
        register_setting('cut_user_directory_settings', self::CACHE_DURATION_OPTION_NAME);

        add_settings_section(
            'cut_user_directory_main',
            'Main Settings',
            null,
            'cut-user-directory-settings'
        );

        // Slug field
        add_settings_field(
            'cut_user_directory_slug',
            'Custom Endpoint Slug',
            [self::class, 'render_slug_field'],
            'cut-user-directory-settings',
            'cut_user_directory_main'
        );

        // API URL field
        add_settings_field(
            'cut_user_directory_api_url',
            'API Endpoint URL',
            [self::class, 'render_api_url_field'],
            'cut-user-directory-settings',
            'cut_user_directory_main'
        );

        // Cache Duration field
        add_settings_field(
            'cut_user_directory_cache_duration',
            'Cache Duration (in hours)',
            [self::class, 'render_cache_duration_field'],
            'cut-user-directory-settings',
            'cut_user_directory_main'
        );

        // Clear Cache button
        add_settings_section(
            'cut_user_directory_cache',
            'Cache Settings',
            null,
            'cut-user-directory-settings'
        );

        add_settings_field(
            'clear_cache_button',
            '',
            [self::class, 'render_clear_cache_button'],
            'cut-user-directory-settings',
            'cut_user_directory_cache'
        );
    }


    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>User Directory Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('cut_user_directory_settings');
                do_settings_sections('cut-user-directory-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public static function render_slug_field() {
        $value = esc_attr(get_option(self::OPTION_NAME, self::DEFAULT_SLUG));
        echo '<input type="text" name="' . self::OPTION_NAME . '" value="' . $value . '" class="regular-text" />';
        echo '<p class="description">This slug defines the URL path (e.g., ' . home_url('/') . '<strong>' . $value . '</strong>)</p>';
    }

    public static function render_api_url_field() {
        $value = esc_url(get_option('cut_user_directory_api_url', 'https://jsonplaceholder.typicode.com/users'));
        echo '<input type="url" name="cut_user_directory_api_url" value="' . esc_attr($value) . '" class="regular-text ltr" />';
        echo '<p class="description">Enter the external API URL that returns user data as JSON.</p>';
    }

    public static function render_cache_duration_field() {
        $value = esc_attr(get_option(self::CACHE_DURATION_OPTION_NAME, 12)); // default in hours
        echo '<input type="number" step="1" min="1" name="' . self::CACHE_DURATION_OPTION_NAME . '" value="' . $value . '" class="small-text" /> hours';
        echo '<p class="description">Set the cache duration in hours (default: 12 hours).</p>';
    }

    public static function render_clear_cache_button() {
        // Button to clear cache
        echo '<input type="submit" name="clear_cache" class="button button-secondary" value="Clear Cache" />';
    }

    public static function get_slug() {
        return sanitize_title(get_option(self::OPTION_NAME, self::DEFAULT_SLUG));
    }

    public static function get_api_url() {
        return esc_url_raw(get_option('cut_user_directory_api_url', 'https://jsonplaceholder.typicode.com/users'));
    }

    public static function get_cache_duration() {
        $hours = intval(get_option(self::CACHE_DURATION_OPTION_NAME, 12));
        return $hours * HOUR_IN_SECONDS;
    }

    
    // Clear the cache when the "Clear Cache" button is pressed
    public static function handle_clear_cache_button() {
        if (isset($_POST['clear_cache'])) {
            delete_transient('cut_user_directory_users'); // Clear the cache
            add_settings_error('cut_user_directory_settings', 'cache_cleared', 'Cache has been cleared.', 'updated');
        }
    }
}

// Hook to handle the Clear Cache button
add_action('admin_post_clear_cache', [Settings::class, 'handle_clear_cache_button']);