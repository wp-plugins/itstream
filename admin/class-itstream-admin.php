<?php
/**
 * Plugin Name.
 *
 * @package   ItStream_Admin
 * @author    it-marketing <info@itmsolution.it>
 * @license   GPL-2.0+
 * @link      http://www.itmarketingsrl.it/
 * @copyright 2014 it-marketing
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-itstream.php`
 *
 * @package ItStream_Admin
 * @author  it-marketing <info@itmsolution.it>
 */
class ItStream_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 */
		$plugin = ItStream::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

        $options = get_option('its_options');
        if( !empty( $options['username'] ) && !empty( $options['password'] ) ) {
            //Add new tab in media upload
            add_filter( 'media_upload_tabs', array( $this, 'video_tab_name' ) );
            add_action( 'media_upload_itstream', array( $this, 'video_tab_content' ) );

            //Add button in editor
            add_action( 'admin_head', array( $this, 'editor_buttons' ) );
        }
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
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		$screen = get_current_screen();
		//if ( $this->plugin_screen_hook_suffix == $screen->id ) {
        if ( 'post' == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), ItStream::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		$screen = get_current_screen();

        if ( in_array( $screen->id, array('post', 'page') ) ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), ItStream::VERSION );
		}

        $options = get_option('its_options');
        $vars = array(
            'customer_id' => $options['customer'],
	        'attach_to_post' => plugins_url('modules/scheduling_attach_to_post.php', __FILE__)
        );

        wp_localize_script( $this->plugin_slug . '-admin-script', 'itstream_ajax', $vars );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Settings', $this->plugin_slug ),
			__( 'ItStream', $this->plugin_slug ),
			//'manage_options',
			'edit_posts', #for editor
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

		$options = get_option('its_options');
		if( empty( $options['username'] ) || empty( $options['password'] ) ) {
			$optionLink = admin_url('/options-general.php?page=itstream&tab=account');
			$msg = sprintf( __( 'Your ItStream plugin is almost ready. %s with your ItStream account to start.', $this->plugin_slug ),  '<a href="' . $optionLink . '"> ' . __( 'Log in', $this->plugin_slug ) . '</a>' );
			admin_notice_message( 'error', $msg );
		}
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
        require_once( ITS_DIR_PATH . 'admin/includes/settings/account.php' );
        require_once( ITS_DIR_PATH . 'admin/views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

    public function video_button( $editor_id = 'content' ) {
        $img = '<span class="wp-media-buttons-icon"></span> ';
        $title = __( 'Add ItStream video', $this->plugin_slug );

        $context = '<a href="#" id="insert-media-button" class="button insert-media add_media" data-editor="' . esc_attr( $editor_id ) . '" title="' . $title . '">' . $img . $title . '</a>';

        echo $context;
    }

    public function video_tab_name( $tabs ) {
        $newtab = array(
            'itstream' => __( 'ItStream', $this->plugin_slug )
        );

        return array_merge( $tabs, $newtab );
    }

    function video_tab_content() {
        return wp_iframe( array( $this, 'video_tab_content_tab' ) );
    }

    public function video_tab_content_tab() {
        require_once( ITS_DIR_PATH . 'admin/views/tabs/mm_video_tab.php' );
    }

    public function editor_buttons() {
        // check user permissions
        if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
            return;
        }

        // check if WYSIWYG is enabled
        if ( 'true' == get_user_option( 'rich_editing' ) ) {
            add_filter( 'mce_external_plugins', array( $this, 'scheduling_button' ) );
            add_filter( 'mce_buttons', array( $this, 'scheduling_register_button' ) );

            /*$options = get_option('its_options');
            $vars = array(
                'customer_id' => $options['customer'],
                'attach_to_post' => plugins_url('modules/scheduling_attach_to_post.php', __FILE__)
            );

            wp_localize_script( $this->plugin_slug . '-admin-script', 'itstream_ajax', $vars );*/
        }
    }

    public function scheduling_button( $plugin_array ) {
        $plugin_array['itstream'] = plugins_url( 'assets/js/editor.js', __FILE__ );

        return $plugin_array;
    }

    public function scheduling_register_button( $buttons ) {
        array_push( $buttons, 'player_scheduling' );

        return $buttons;
    }
}
