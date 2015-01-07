<?php
if ( isset($_POST['send']) ) {
    $id = (int)$_POST['send'];
    $player = $_POST['player'][$id];

    switch( $player['size'] ) {
        case 640:
            $w = 640; $h = 360;
            break;
        case 560:
            $w = 560; $h = 315;
            break;
        case 480:
            $w = 480; $h = 270;
            break;
        case 320:
            $w = 320; $h = 180;
            break;
        case 'custom':
        default:
            $w = 640; $h = 360;
            break;
    }

    $iframe = '<iframe width="' . $w . '" height="' . $h . '" src="https://embed.itstream.tv/video.php?code=' . $_POST['send'] . '&amp;width=' . $w . '&amp;height=' . $h;

    if( isset( $player['autoplay'] ) ) {
        $iframe.= '&amp;autostart=true';
    }

    if( isset( $player['mute'] ) ) {
        $iframe.= '&amp;volume=0';
    }

    if( !empty( $player['start'] ) ) {
        $iframe.= '&amp;start=' . $player['start'];
    }

    $iframe.= '" frameborder="0" scrolling="no" allowfullscreen></iframe>';

    return media_send_to_editor($iframe);
}

//if ( false === ( $results = get_transient( 'its_getVideos_results' ) ) ) {
    require_once( ITS_DIR_PATH . 'includes/nusoap.php' );
    require_once( ITS_DIR_PATH . 'admin/includes/class-itstream-ws-params.php' );

    $client = new nusoap_client("http://plugin.itstream.tv/ws?wsdl", 'wsdl');

    $options = get_option('its_options');
    $paramsWs = array(
        'user' => $options['username'],
        'password' => $options['password'],
        'params' => new Itstream_Ws_Params($_POST)
    );

    $results = $client->call('getVideos', $paramsWs);
    //set_transient( 'its_getVideos_results', $results, (30 * MINUTE_IN_SECONDS) );
//}

//if ( false === ( $treeCategory = get_transient( 'its_getVideos_categories' ) ) ) {
    require_once( ITS_DIR_PATH . 'includes/nusoap.php' );
    require_once( ITS_DIR_PATH . 'admin/includes/class-itstream-ws-params.php' );

    $client = new nusoap_client("http://plugin.itstream.tv/ws?wsdl", 'wsdl');

    $options = get_option('its_options');
    $paramsWs = array(
        'user' => $options['username'],
        'password' => $options['password'],
        'category' => empty($_POST['category']) ? 0 : (int)$_POST['category']
    );

    $treeCategory = $client->call('getTreeCategory', $paramsWs);
    //set_transient( 'its_getVideos_categories', $treeCategory, (30 * MINUTE_IN_SECONDS) );
//}
?>

<style>
    body, body > * { background: #FFF; }
    div.media-toolbar {  }
    div.media-toolbar select { height: 28px; line-height: 28px; padding: 2px; vertical-align: middle; background-color: #fff; border: 1px solid #ddd; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.07) inset; color: #333; transition: border-color 0.05s ease-in-out 0s; font-size: 12px; }
    div.media-toolbar-primary { position: absolute; right: 10px; }
    div.media-toolbar-secondary { float: left; }
    table { margin: 0 10px; width:95%; border-collapse: collapse; }
    tr.border { border-bottom: 1px solid #DFDFDF; }
    td.thumb { width: 90px; height:50px; overflow:hidden; }
    td.action { width: 50px; padding: 0 5px; }
    tr.details td.thumb { width: 130px; }
    tr.details td.insert { padding: 10px 0 10px 35px; text-align: right; }
    td.label, td.field { padding: 5px 0; }
    div.media-toolbar { border: 0 solid #dfdfdf; padding: 10px 10px 20px 10px; height: 20px; left: 0; overflow: hidden; position: absolute; right: 0; top: 0; z-index: 100; }
    form#filter { margin-top: 50px; }
    .navigation { padding: 10px 0; text-align: center; }
    .navigation li a, .navigation li a:hover, .navigation li.active a, .navigation li.disabled { color: #000; text-decoration:none; border: 1px solid #c6c6c6; }
    .navigation li { display: inline; }
    .navigation li a, .navigation li a:hover, .navigation li.active a, .navigation li.disabled { border-radius: 3px; cursor: pointer; padding: 12px; padding: 0.75rem; }
    .navigation li a:hover, .navigation li.active a { background-color: gray; border-color: gray; color: #FFF; }
</style>

<div class="media-toolbar">
    <form id="category-filter" action="" method="post">
        <div class="media-toolbar-secondary">
            <select name="category" id="its-categories">
                <option value="0">Tutte le categorie</option>
                <?php print $treeCategory; ?>
            </select>
            <div class="spinner"></div>
        </div>
        <div class="media-toolbar-primary">
            <input type="search" name="search" value="<?php echo $_POST['search']; ?>" placeholder="Cerca" class="search">
        </div>
    </form>
</div>

<div style="clear:both;"></div>

<div style="clear:both;"></div>

<form id="filter" action="" method="post">
    <input type="hidden" name="type" value="<?php echo esc_attr( $GLOBALS['type'] ); ?>" />
    <input type="hidden" name="tab" value="<?php echo esc_attr( $GLOBALS['tab'] ); ?>" />

    <table class="my-video-list">
    <?php if(!empty($results['result'])): ?>
        <?php foreach($results['result'] as $element): ?>
            <tr class="border" id="element-<?php echo $element['id']; ?>">
                <td class="thumb">
                    <img src="<?php echo $element['url_thumb']; ?>" width="100"  />
                </td>
                <td class="title">
                    <?php
                    if( $element['public'] == 'D' ) {
                        echo '<span class="dashicons dashicons-lock" title="' . __( 'This video is private', 'itstream') . '"></span> ';
                    }
                    else {
                        echo '<span class="dashicons"></span> ';
                    }

                    echo $element['name'];
                    ?>
                </td>
                <td class="action">
                    <a href="#" data-details="<?php echo $element['id']; ?>"><?php _e( 'Show', 'itstream' ); ?></a>
                </td>
            </tr>
            <tr class="details border" id="details-<?php echo $element['id']; ?>" style="display:none">
                <td colspan="3">
                    <table>
                        <tr>
                            <td class="thumb">
                                <img src="<?php echo $element['url_thumb']; ?>" width="120" />
                            </td>
                            <td>
                                <strong><?php _e( 'ID', 'itstream'); ?>: </strong><?php echo $element['id']; ?><br />
                                <strong><?php _e( 'Date', 'itstream'); ?>: </strong><?php echo $element['add_date']; ?><br />
                                <strong><?php _e( 'Length', 'itstream'); ?>: </strong><?php echo $element['duration']; ?><br />
                                <strong><?php _e( 'State', 'itstream'); ?>: </strong><?php echo ($element['public']=='D') ? __( 'Private', 'itstream') : __( 'Public', 'itstream'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><?php _e( 'Autoplay', 'itstream'); ?></td>
                            <td class="field">
                                <input type="checkbox" name="player[<?php echo $element['id']; ?>][autoplay]" value="1" />
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><?php _e( 'Mute sound', 'itstream'); ?></td>
                            <td class="field">
                                <input type="checkbox" name="player[<?php echo $element['id']; ?>][mute]" value="1" />
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><?php _e( 'Player size', 'itstream'); ?></td>
                            <td class="field">
                                <select name="player[<?php echo $element['id']; ?>][size]">
                                    <option value="640"><?php _e( 'Standard', 'itstream'); ?> (640x360)</option>
                                    <option value="560"><?php _e( 'Large', 'itstream'); ?> (560x315)</option>
                                    <option value="480"><?php _e( 'Medium', 'itstream'); ?> (480x270)</option>
                                    <option value="320"><?php _e( 'Small', 'itstream'); ?> (320x180)</option>
                                    <!-- option value="custom"><?php _e( 'Custom', 'itstream'); ?> </option -->
                                </select>
                            </td>
                        </tr>
                        <!-- tr>
                            <td class="label"><?php _e( 'Start from', 'itstream'); ?></td>
                            <td class="field">
                                <input type="text" name="player[<?php echo $element['id']; ?>][start]" value="" size="4" />
                            </td>
                        </tr -->
                        <tr>
                            <td colspan="2" class="insert">
                                <button name="send" value="<?php echo $element['id']; ?>" class="button" type="submit"><?php _e( "Add to post", "itstream"); ?></button>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </table>
</form>

<?php
$paged = ( empty( $results['current_page'] ) ) ? 1 : absint( $results['current_page'] );
if( $results['pages'] > 1 ) {
    $max   = intval( $results['pages'] );

	if ( $paged >= 1 ) $links[] = $paged;

	if ( $paged >= 2 ) $links[] = $paged - 1;

	if ( $paged >= 3 ) $links[] = $paged - 2;

	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

    echo '<div class="navigation"><ul>' . "\n";
    echo '<li><a href="#" data-page="' .$results['previous_page'] .'">&laquo; ' . __( 'Previous', 'itstream') . '</a></li>';
    sort( $links );
    foreach ( (array) $links as $link ) {
        $class = $paged == $link ? ' class="active"' : '';
        printf( '<li%s><a href="#" data-page="%s">%s</a></li>' . "\n", $class, $link, $link );
    }

    if ( ! in_array( $max, $links ) ) {
        if ( ! in_array( $max - 1, $links ) )
            echo '<li>…</li>' . "\n";

        $class = $paged == $max ? ' class="active"' : '';
        printf( '<li%s><a href="#" data-page="%s">%s</a></li>' . "\n", $class, $max, $max );
    }

    echo '<li><a href="#" data-page="' .$results['next_page'] .'">' . __( 'Next', 'itstream') . ' &raquo;</a></li>';
}
?>

<script>
    jQuery(document).ready( function( $ ){
        $('.action a').click(function( e ) {
            e.preventDefault();

            elemId = $(this).data('details');
            $( '#details-' + elemId ).toggle();
            label = $(this);
            if(label.html()=='<?php _e( 'Show', 'itstream' ); ?>') {
                label.html('<?php _e( 'Hide', 'itstream' ); ?>');
            }
            else {
                label.html('<?php _e( 'Show', 'itstream' ); ?>');
            }
        });

        $('#its-categories').change(function( e ){
            val = $( this ).val();
            if(val.length) {
                $("div.spinner").show();
                $( 'form#category-filter input').val('');
                $( 'form#category-filter' ).submit();
            }
        });

        $( 'form#category-filter' ).submit(function( e ){
            $( 'div.spinner' ).show();
        });

        $( 'div.navigation a').click(function( e ){
            e.preventDefault();

            page = $( this).data( 'page' );
            var input = $( '<input>' ).attr( 'type', 'hidden' ).attr( 'name', 'page').val( page );
            $( 'form#category-filter' ).append($(input)).submit();
        });
    });
</script>