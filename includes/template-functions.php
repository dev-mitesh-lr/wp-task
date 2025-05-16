<?php

namespace CUT;

/**
 * Renders the user detail HTML from a template.
 *
 * @param array $user The user data array.
 * @return string Rendered HTML.
 */

function render_user_detail_view(array $user): string {
    ob_start();
    $user_data = $user;
    
   include plugin_dir_path(__FILE__) . '../src/views/user-detail.php';

    return ob_get_clean();
}
