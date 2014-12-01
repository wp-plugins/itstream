<?php
/**
 * ItStream.
 *
 * @package   ItStream
 * @author    it-marketing <info@itmsolution.it>
 * @license   GPL-2.0+
 * @link      http://www.itmarketingsrl.it/
 * @copyright 2014 it-marketing
 */

/**
 * Official ItStream Wordpres plugin
 *
 * @package ItStream
 * @author  itMarketing <info@itmarketingsrl.it>
 */
class ItStream {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'itstream';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_shortcode( 'istream_scheduling', array( $this, 'istream_scheduling_shortcode_parser' ) );
        add_shortcode( 'istream_live', array( $this, 'istream_live_shortcode_parser' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		add_option( 'its_options', array(
            'username'          => '',
            'password'          => '',
            'application'       => '',
            'ip_server'         => '',
            'remote_port'       => '',
            'ip_server_live'    => '',
            'customer'          => ''
        ));

        $role = get_role( 'administrator' );
        $role->add_cap( 'its_manage' );
    }

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
        delete_option( 'its_options' );

        $role = get_role('administrator');
        $role->remove_cap( 'its_manage' );
    }

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, ITS_DIR_PATH . 'languages/' . $domain . '-' . $locale . '.mo' );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * Parser for shortcode [istream_scheduling]
	 *
     * @param    array    $atts    Shortcode's attributes.
	 */
	public function istream_scheduling_shortcode_parser( $atts ) {
        extract( shortcode_atts( array(
            'id' => null,
            'width' => 640,
            'height' => 360,
            'autostart' => true,
            'volume' => null,
            'category' => null
        ), $atts ) );

        if( empty( $id ) ) {
            return null;
        }

        $iframe = '<iframe width="' . $width . '" height="' . $height . '" src="http://embed.itstream.tv/palinsesto.php?code=' . $id;

        if( !empty( $autostart ) ) {
            $iframe.= '&amp;autostart=true';
        }

        if( $volume === "0" ) {
            $iframe.= '&amp;volume=0';
        }

        if( !empty( $category ) ) {
            $iframe.= '&amp;cat=' . $category;
        }

        $iframe.= '" frameborder="0" scrolling="no" id="fame" allowfullscreen></iframe>';

        return $iframe;
	}

    /**
     * Parser for shortcode [istream_scheduling]
     *
     * @param    array    $atts    Shortcode's attributes.
     */
    public function istream_live_shortcode_parser( $atts ) {
        extract( shortcode_atts( array(
            'id' => null,
            'width' => 640,
            'height' => 360,
            'autostart' => true,
            'volume' => null,
            'category' => null
        ), $atts ) );

        if( empty( $id ) ) {
            return null;
        }

        $iframe = '<iframe width="' . $width . '" height="' . $height . '" src="http://embed.itstream.tv/live.php?code=' . $id;

        if( !empty( $autostart ) ) {
            $iframe.= '&amp;autostart=true';
        }

        if( $volume === "0" ) {
            $iframe.= '&amp;volume=0';
        }

        if( !empty( $category ) ) {
            $iframe.= '&amp;cat=' . $category;
        }

        $iframe.= '" frameborder="0" scrolling="no" id="fame" allowfullscreen></iframe>';

        return $iframe;
    }
}
