<?php
global $its_logged_in;

$its_logged_in = false;
$its_options = get_option( 'its_options' );

if( isset( $_POST['_its_nonce_settings'] ) && wp_verify_nonce( $_POST['_its_nonce_settings'], 'its-settings-page' ) ) {

    require_once( ITS_DIR_PATH . 'includes/nusoap.php' );

    $client = new nusoap_client( 'https://plugin.itstream.tv/ws?wsdl', true );

    if( $error = $client->getError() ) {
        die( __( sprintf( "Client construction error: %s\n", $error ) ) );
    }

    if( $_POST['op'] == 'login' ) {
        $response = $client->call( 'getParams', array(
            'user'      => $_POST['its_username'],
            'password'  => md5($_POST['its_password']),
            'referer'   => get_site_url()
        ));

        if( $error = $client->getError() ) {
            print_r($client->response);
            print_r($client->getDebug());
            die();
        }

        if( $response['esito'] == 'OK' ) {
            $params = $response["result"][0];

            $its_options = array_merge( get_option( 'its_options' ), array(
                'username'          => $_POST['its_username'],
                'password'          => md5( $_POST['its_password'] ),
                'application'       => $params['application'],
                'ip_server'         => $params['ip_server'],
                'remote_port'       => $params['remote_port'],
                'ip_server_live'    => $params['ip_server_live'],
                'customer'          => $params['customer']
            ));

            update_option( 'its_options', $its_options);
            $its_logged_in = true;
        }
        else {
            admin_notice_message( 'error', __( 'Error: account not connected', 'itstream' ) );
        }
    }

    if( $_POST['op'] == 'logout' ) {
        $its_options = array_merge( get_option( 'its_options' ), array(
            'username'          => '',
            'password'          => '',
            'application'       => '',
            'ip_server'         => '',
            'remote_port'       => '',
            'ip_server_live'    => '',
            'customer'          => ''
        ));

        update_option( 'its_options', $its_options);
        $its_logged_in = false;

        admin_notice_message( 'success', __( 'Account disconnected', 'itstream' ) );
    }
}


if( !empty( $its_options['username'] ) && !empty( $its_options['password'] ) ) {
    $its_logged_in = true;
}
