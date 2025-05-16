<?php
/**
 * User Directory Template
 *
 * Displays a table of users pulled from an external API,
 * with caching to improve performance.
 *
 * @package CustomUserTable
 */

use CUT\Settings;

$cache_key      = 'cut_user_directory_users';
$cache_duration = Settings::get_cache_duration();

// Attempt to retrieve cached users.
$users = get_transient( $cache_key );

if ( false === $users ) {
    $api_url  = Settings::get_api_url();
    $response = wp_remote_get( $api_url );

    if ( is_wp_error( $response ) ) {
        wp_die( esc_html__( 'Failed to fetch users from API: ', 'custom-user-table' ) . esc_html( $response->get_error_message() ) );
    }

    $users = json_decode( wp_remote_retrieve_body( $response ), true );

    // Store the API response in cache.
    set_transient( $cache_key, $users, $cache_duration );
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <title><?php esc_html_e( 'User Directory', 'custom-user-table' ); ?></title>
    <?php wp_head(); ?>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 40px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        #user-detail { border: 1px solid #ccc; padding: 20px; }
    </style>
</head>
<body>
    <h1><?php esc_html_e( 'User Directory', 'custom-user-table' ); ?></h1>

    <table>
        <thead>
            <tr>
                <th><?php echo esc_html__( 'ID', 'custom-user-table' ); ?></th>
                <th><?php echo esc_html__( 'Name', 'custom-user-table' ); ?></th>
                <th><?php echo esc_html__( 'Username', 'custom-user-table' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $users ) && is_array( $users ) ) : ?>
                <?php foreach ( $users as $user ) : ?>
                    <tr>
                        <td>
                            <a href="#" class="cut-user-link" data-id="<?php echo esc_attr( $user['id'] ); ?>">
                                <?php echo esc_html( $user['id'] ); ?>
                            </a>
                        </td>
                        <td>
                            <a href="#" class="cut-user-link" data-id="<?php echo esc_attr( $user['id'] ); ?>">
                                <?php echo esc_html( $user['name'] ); ?>
                            </a>
                        </td>
                        <td>
                            <a href="#" class="cut-user-link" data-id="<?php echo esc_attr( $user['id'] ); ?>">
                                <?php echo esc_html( $user['username'] ); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="3"><em><?php esc_html_e( 'No users found or invalid API response.', 'custom-user-table' ); ?></em></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div id="user-detail">
        <p><em><?php esc_html_e( 'Select a user to see their details.', 'custom-user-table' ); ?></em></p>
    </div>

    <?php wp_footer(); ?>
</body>
</html>