<?php
function the_iframe( $params=array() ) {
    extract( shortcode_atts( array(
        'code' => null,
        'width' => 640,
        'height' => 360,
        'autostart' => true,
        'volume' => null,
        'category' => null
    ), $params ) );

    $iframe = '<iframe width="' . $width . '" height="' . $height . '" src="https://embed.itstream.tv/live.php?code=' . $code;

    if( !empty( $autostart ) ) {
        $iframe.= '&amp;autostart=true';
    }

    if( $volume === "0" ) {
        $iframe.= '&amp;volume=0';
    }

    $iframe.= '" frameborder="0" scrolling="no"></iframe>';

    echo $iframe;
}


function call_service( $category ) {
    require_once( ITS_DIR_PATH . '/includes/nusoap.php' );
    require_once( ITS_DIR_PATH . '/admin/includes/class-itstream-ws-params.php' );

    $options = get_option('its_options');
    $client = new nusoap_client("http://plugin.itstream.tv/ws?wsdl", 'wsdl');

    $paramsWs = array(
        'user' => $options['username'],
        'password' => $options['password'],
        'category' => (int)$category
    );

    return $client->call( 'getVideoCategory', $paramsWs );
}