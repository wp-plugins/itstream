<?php
/**
 * ItStream Plugin
 *
 * Official wordpress plugin for ItStream
 *
 * @package   ItStream
 * @author    it-marketing <info@itmsolution.it>
 * @license   GPL-2.0+
 * @link      http://www.itmarketingsrl.it/
 * @copyright 2014 it-marketing
 *
 * @wordpress-plugin
 * Plugin Name:       ItStream
 * Plugin URI:        https://www.itstream.tv/plugins/wordpress
 * Description:       Official plugin. Embed videos hosted on ItStream. Requires a itstream.tv account.
 * Version:           1.0.0
 * Author:            it-marketing
 * Author URI:        http://www.itmarketingsrl.it/
 * Text Domain:       itstream
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin root path
define( 'ITS_DIR_PATH', plugin_dir_path( __FILE__ ) );

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-itstream.php' );

register_activation_hook( __FILE__, array( 'ItStream', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'ItStream', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'ItStream', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

    require_once( plugin_dir_path( __FILE__ ) . 'admin/includes/functions.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'admin/class-itstream-admin.php' );
	add_action( 'plugins_loaded', array( 'ItStream_Admin', 'get_instance' ) );

}
