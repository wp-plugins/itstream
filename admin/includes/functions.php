<?php
function itstream_admin_tabs( $current = null ) {
    $plugin = ItStream::get_instance();
    $plugin_slug = $plugin->get_plugin_slug();

    $tabs = array(
        'home'      => __( 'Home', $plugin_slug ),
        'account'   => __( 'Account', $plugin_slug )
    );

    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';

    $current = empty( $current ) ? 'home' : $current;
    foreach( $tabs as $tab => $name ) {
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo '<a class="nav-tab' . $class . '" href="?page=' . $plugin_slug . '&tab=' . $tab . '">' . $name . ' </a>';

    }

    echo '</h2>';

    $admin_url = get_admin_url( get_current_blog_id(), 'admin.php?page=' . $plugin_slug . '&tab=' . $current );
    echo '<form method="post" action="' . $admin_url . '">';

    wp_nonce_field( 'its-settings-page', '_its_nonce_settings' );

    $path = realpath( plugin_dir_path( __FILE__ ) . '../views/tabs/' );
    require_once( $path . '/' . $current . '.php' );

    echo '</form>';
}

function admin_notice_message( $type, $message ){
    switch($type) {
        case 'error':
            $class = 'error';
            break;
        case 'success':
        default:
            $class = 'updated';
            break;
    }

    echo '<div class="' . $class . '"><p>' . $message . '</p></div>';
}