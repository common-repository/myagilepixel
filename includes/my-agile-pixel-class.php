<?php

/**
 * Core definitions
 */

define ( 'MAPX_SOFTWARE_KEY', 'mpx_wp' );
define ( 'MAPX_HUMAN_NAME', 'My Agile Pixel' );
define ( 'MAPX_PLUGIN_DB_KEY_PREFIX', 'AgilePixel' );
define ( 'MAPX_PLUGIN_SETTINGS_FIELD', MAPX_PLUGIN_DB_KEY_PREFIX );
define ( 'MAPX_PLUGIN_RCONFIG', MAPX_PLUGIN_DB_KEY_PREFIX . '_rconfig' );
define ( 'MAPX_PLUGIN_DO_SYNC_NOW', MAPX_PLUGIN_DB_KEY_PREFIX . 'do_sync_now' );
define ( 'MAPX_PLUGIN_DO_SYNC_LAST_EXECUTION', MAPX_PLUGIN_DB_KEY_PREFIX . 'do_sync_last_execution' );
define ( 'MAPX_PLUGIN_VALIDATION_TIMESTAMP', MAPX_PLUGIN_DB_KEY_PREFIX . 'validation_timestamp' );
define ( 'MAPX_PLUGIN_DB_VERSION', MAPX_PLUGIN_DB_KEY_PREFIX . 'db_version_number' );
define ( 'MAPX_PLUGIN_DB_VERSION_NUMBER', 1 );
define ( 'MAPX_DOMAIN', MAPX_PLUGIN_NAME );
define ( 'MAPX_MANIFEST_ASSOC', 'my-agile-pixel-manifest' );
define ( 'MAPX_API_ENDPOINT', 'https://auth.myagileprivacy.com/wp_api' );
define ( 'MAPX_GA_ENDPOINT', 'https://region1.google-analytics.com/g/collect?' );
define ( 'MAPX_FBCAPI_ENDPOINT', 'https://graph.facebook.com/' );
define ( 'MAPX_FBCAPI_VERSION_API', 'v19.0' );
define ( 'MAPX_TIKTOKAPI_ENDPOINT', 'https://business-api.tiktok.com/open_api/' );
define ( 'MAPX_TIKTOKAPI_VERSION_API', 'v1.2' );
define ( 'MAPX_DESKTOP_UA', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36' );
define ( 'MAPX_MOBILE_UA', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/113.0.5672.121 Mobile/15E148 Safari/604.1' );
define ( 'MAPX_REMOTE_OPT', array( 'ganalytics_measurement_id' ) );


/**
 * Core plugin class.
 *
 */

class MyAgilePixel {

    // Unique identifier of this plugin.
    protected $plugin_name;

	// Current version of the plugin.
	protected $version;

    // stored user options
	private static $stored_options = array();

    /**
	 * Core functionality of the plugin.
	 *
	 * It sets plugin name, plugin version.
	 * It loads dependencies, set the locale, and hoocks for admin and frontend area
    */
    public function __construct()
	{
		$this->version = MAPX_PLUGIN_VERSION;

		$this->plugin_name = MAPX_PLUGIN_NAME;

		$this->load_classes_and_dependencies();
		$this->admin_hooks();
		$this->frontend_hooks();
	}

	/**
	 * Load the required dependencies.
	 *
	 * @access   private
	 */
    private function load_classes_and_dependencies() {

        //The class for defining all actions that occur in the backend area.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/my-agile-pixel-admin.php';


		// The class for defining all the functionalities for the frontend part
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'frontend/my-agile-pixel-frontend.php';
	}

	/**
	 * Register all of the hooks related to the backend area
	 *
	 * @access   private
	 */
    private function admin_hooks()
    {
		$rconfig = self::get_rconfig();

        $plugin_admin = new MyAgilePixelAdmin( $this->plugin_name, $this->version, $this );

		//add cron scheduled functions
		if( !( isset( $rconfig ) &&
				isset( $rconfig['disable_cronjob'] ) &&
				$rconfig['disable_cronjob'] == 1 ) )
		{
			add_action( 'my_agile_pixel_do_cron_sync_daily_hook', array( $plugin_admin, 'do_cron_sync' ) );
		}

		//wp_footer hook
		add_action( 'wp_footer', array( $plugin_admin, 'triggered_do_cron_sync' ) );

		if( !is_admin() )
		{
			return;
		}

		//repeated on admin_footer
		add_action( 'admin_footer', array( $plugin_admin, 'triggered_do_cron_sync' ) );

		add_action( 'admin_footer', array( $plugin_admin, 'check_for_map_wp' ) );

		//admin init
		add_action( 'admin_init', array( $plugin_admin, 'admin_init' ) );

        /* Admin menu */
		add_action( 'admin_menu', array( $plugin_admin, 'add_admin_pages' ), 11 );

		//Admin head hook
		add_action( 'admin_head', array( $plugin_admin, 'add_global_defs' ) );

		/* Admin callback actions */
		add_action( 'wp_ajax_nopriv_apix_update_admin_settings_form', array( $plugin_admin, 'update_admin_settings_form_callback' ) );
		add_action( 'wp_ajax_apix_update_admin_settings_form', array( $plugin_admin, 'update_admin_settings_form_callback' ) );


		add_action( 'wp_ajax_nopriv_apix_update_user_property_assoc_form', array( $plugin_admin, 'update_user_property_assoc_form_callback' ) );
		add_action( 'wp_ajax_apix_update_user_property_assoc_form', array( $plugin_admin, 'update_user_property_assoc_form_callback' ) );


        /* Generic Admin styles*/
		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles' ) );

		/* Generic Admin scripts*/
		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_scripts' ) );

		//add settings links for the menu
		add_filter( 'plugin_action_links_'.plugin_basename( MAPX_PLUGIN_FILENAME ), array( $plugin_admin, 'plugin_action_links' ) );

		//add cron scheduled functions
		if( !( isset( $rconfig ) &&
				isset( $rconfig['disable_cronjob'] ) &&
				$rconfig['disable_cronjob'] == 1 ) )
		{
			//schedule an action if it's not already scheduled
			if ( ! wp_next_scheduled( 'my_agile_pixel_do_cron_sync_daily_hook' ) )
			{
			    wp_schedule_event( time(), 'daily', 'my_agile_pixel_do_cron_sync_daily_hook' );
			}
		}
		else
		{
			//clean daily event schedule if exists
			if ( wp_next_scheduled( 'my_agile_pixel_do_cron_sync_daily_hook' ) )
			{
				wp_clear_scheduled_hook( 'my_agile_pixel_do_cron_sync_daily_hook' );
			}
		}

		add_action( 'admin_footer', array( $plugin_admin, 'admin_clear_logfile' ) );
    }


	/**
	 * Register all of the hooks related to the frontend part
	 *
	 * @access   private
	 */
    private function frontend_hooks()
	{
		$plugin_frontend = new MyAgilePixelFrontend( $this->plugin_name, $this->version, $this );

		$the_settings = self::get_settings();
		$rconfig = self::get_rconfig();

		$compatibility_mode = false;

		if( isset( $the_settings['compatibility_mode'] ) &&
			$the_settings['compatibility_mode']
		)
		{
			$compatibility_mode = $the_settings['compatibility_mode'];
		}

		/* Frontend scripts*/

		$skip = $this::check_buffer_skip_conditions();

		//blocked on page preview
		if(
			//elementor
			!isset( $_GET['elementor-preview'] ) &&
			//divi
			( !isset( $_GET['et_fb'] ) || $_GET['et_fb'] != 1 ) )
		{
			//old inject mode
			if( $skip == 'false' &&
				!(
					isset( $rconfig ) &&
					isset( $rconfig['old_inject_mode'] ) &&
					$rconfig['old_inject_mode'] == 1
				)
			)
			{
				//new inject mode
				add_action( 'wp_head', array( $plugin_frontend, 'enqueue_scripts' ), PHP_INT_MIN + 1 );

				add_action( 'wp_head', array( $plugin_frontend, 'enqueue_styles' ), PHP_INT_MIN + 1 );

				if( $the_settings['pa'] == 1 )
				{
					$is_myagileprivacy_activated = MyAgilePixel::is_myagileprivacy_activated();

					if( !$is_myagileprivacy_activated )
					{
						add_action( 'wp_head', array( $plugin_frontend, 'wp_head_inject' ), PHP_INT_MIN );
					}
				}

				add_action( 'wp_head', array( $plugin_frontend, 'add_inline_script' ), PHP_INT_MIN + 2 );
				add_action( 'wp_footer', array( $plugin_frontend, 'finalize_inline_script' ), PHP_INT_MIN + 1 );
			}
			else
			{
				//old inject mode
				add_action( 'wp_enqueue_scripts', array( $plugin_frontend, 'enqueue_scripts' ), PHP_INT_MIN );

				add_action( 'wp_enqueue_scripts', array( $plugin_frontend, 'enqueue_styles' ), PHP_INT_MIN );

				if( $the_settings['pa'] == 1 )
				{
					$is_myagileprivacy_activated = MyAgilePixel::is_myagileprivacy_activated();

					if( !$is_myagileprivacy_activated )
					{
						add_action( 'wp_head', array( $plugin_frontend, 'wp_head_inject' ), PHP_INT_MIN );
					}
				}

				add_action( 'wp_footer', array( $plugin_frontend, 'add_inline_script' ), PHP_INT_MIN );
				add_action( 'wp_footer', array( $plugin_frontend, 'finalize_inline_script' ) , PHP_INT_MIN + 1 );
			}

			if( $this->is_woocommerce_activated() && $the_settings['woocommerce_enable'] )
			{
				add_action( 'woocommerce_after_shop_loop_item', array( $plugin_frontend, 'woo_listing_impression' ) );
				add_action( 'woocommerce_after_shop_loop_item', array( $plugin_frontend, 'woo_listing_click' ) );
				add_action( 'woocommerce_after_single_product', array( $plugin_frontend, 'woo_product_detail' ) );
				add_action( 'woocommerce_after_checkout_form', array( $plugin_frontend, 'woo_checkout_process' ) );
			}
		}

		add_action( 'wp_ajax_nopriv_mpx_send_data', array( $plugin_frontend, 'mpx_send_data_callback' ) );
		add_action( 'wp_ajax_mpx_send_data', array( $plugin_frontend, 'mpx_send_data_callback' ) );

		/*auto update*/
		add_filter( 'auto_update_plugin', array( $plugin_frontend, 'auto_update_plugins' ), 10, 2 );

		if( $compatibility_mode &&
			isset( $GLOBALS['wp_version'] ) &&
			version_compare( $GLOBALS['wp_version'], '5.7', '>=' )
		)
		{
			add_filter( 'wp_inline_script_attributes', array( $plugin_frontend, 'add_inline_script_attributes' ), 10, 2 );
		}
    }

	/**
	 * Function for doing better query reset
	 */
	public static function internal_query_reset()
	{
		$rconfig = self::get_rconfig();

		if( $rconfig && isset( $rconfig['use_alt_query_reset'] ) && $rconfig['use_alt_query_reset'] )
		{
			wp_reset_postdata();
		}
		else
		{
			wp_reset_query();
		}

		return true;
	}


	/**
 	* Returns sanitised content
	 * @access   public
	*/
	public static function sanitise_settings( $key, $value )
	{
		$ret = null;
		switch( $key ){
			//as is
			case 'user_property_assoc':
				$ret = $value;
				break;
			// text to bool conversion
			case 'is_on':
			case 'consent_mode_requested':
			case 'consent_mode_enabled':
			case 'forced_auto_update':
			case 'use_ga_advanced_features':
			case 'compatibility_mode':
				if( $value === 'true' || $value === true )
				{
					$ret = true;
				}
				elseif( $value === 'false' || $value === false )
				{
					$ret = false;
				}
				else
				{
					$ret = false;
				}
				break;
			//integer
			case '__integer':
				$ret = intval( $value );
				break;
			// hex colors
			case '__hex_color':
				if ( preg_match( '/^#[a-f0-9]{6}|#[a-f0-9]{3}$/i', $value ) )
				{
					$ret =  $value;
				}
				else {
					// Failover = assign '#000' (black)
					$ret =  '#000';
				}
				break;
			// html (no js code )
			case '__html_no_js_code':
				$ret = wp_kses( $value, self::allowed_html_tags(), self::allowed_protocols() );
				break;
			//url
			case '__url':
				$ret = wp_kses( trim( $value ), self::allowed_html_tags(), self::allowed_protocols() );
				break;
			//css
			case '__custom_css':
				$ret = esc_html( $value );
				break;
			// Basic sanitisation for other fields
			default:
				$ret = sanitize_text_field( $value );
				break;
		}
		return $ret;
	}


	/**
	 * Returns list of HTML tags allowed in HTML fields for use in declaration of wp_kset field validation.
	 * @access   public
	 */
	public static function allowed_html_tags()
	{
		$allowed_html = array(
			'a' => array(
				'href' => array(),
				'id' => array(),
				'class' => array(),
				'title' => array(),
				'target' => array(),
				'rel' => array(),
				'style' => array(),
				'role' => array(),
				'data-map_action' => array(),
			),
			'input' => array(
				'id' => array(),
				'name'=> array(),
				'type'=> array(),
				'value'=> array(),
				'class'=> array(),
				'data-cookie-baseindex'=>array(),
				'data-default-color'=>array(),
			),
			'b' => array(),
			'br' => array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'div' => array(
				'id' => array(),
				'class' => array(),
				'style' => array(),
				'data-nosnippet' => array(),
				'data-map_action'=> array(),
				'data-cookie-baseindex'=>array(),
				'data-cookie-name'=>array(),
			),
			'em' => array (
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'i' => array(),
			'img' => array(
				'src' => array(),
				'id' => array(),
				'class' => array(),
				'alt' => array(),
				'style' => array()
			),
			'p' => array (
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'span' => array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'strong' => array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'h1' => array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'h2' => array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'h3' => array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'h4' => array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'h5' => array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'h6' => array(
				'id' => array(),
				'class' => array(),
				'style' => array()
			),
			'label' => array(
				'id' => array(),
				'class' => array(),
				'style' => array(),
				'for' => array(),
				'data-map-enable' => array(),
				'data-map-disable' => array(),
			),
			'option' => array(
				'name' => array(),
				'value' => array(),
				'selected' => array(),
			),
		);
		$html5_tags = array( 'article','section','aside','details','figcaption','figure','footer','header','main','mark','nav','summary','time' );

		foreach( $html5_tags as $html5_tag )
		{
			$allowed_html[$html5_tag] = array(
				'id' 	=> array(),
				'class' => array(),
				'style' => array()
			);
		}
		return $allowed_html;
	}


	/**
	 * Returns list of allowed protocols, used in wp_kset field validation.
	 * @access   public
	 */
	public static function allowed_protocols()
	{
		return array ( 'http', 'https' );
	}


	/**
	 * Get rconfig settings.
	 */
	public static function get_rconfig()
	{
		return self::nullCoalesce( get_option( MAPX_PLUGIN_RCONFIG ), array() );
	}


	/**
	 * Get current settings.
	 * @access   public
	 */
    public static function get_settings()
	{
		$settings = self::get_default_settings();
		self::$stored_options = get_option( MAPX_PLUGIN_SETTINGS_FIELD, array() );

        if( !empty( self::$stored_options ) )
		{
			foreach( self::$stored_options as $key => $option )
			{
				$settings[$key] = self::sanitise_settings( $key, $option );
			}
		}

		return $settings;
	}

	/**
	 * Returns default settings
	 * @access   public
	 */
    public static function get_default_settings( $key='' )
	{
		$settings = array(
			'general_plugin_active'									=> 	1,
			'general_interface_with'								=> 'none',

			'wl'													=>	0,
			'pa'													=>	0,
			'license_code'											=>	'',
			'license_user_status'									=>	'Demo License',
			'license_valid'											=>	true,
			'grace_period'											=>	false,


			'ganalytics_enable'										=>	0,
			'ganalytics_debug_mode'									=>	0,
			'ganalytics_settings_preset'							=> 	4,
			'ganalytics_measurement_id'								=> 	null,
			'ganalytics_anonymize_ip'								=> 	1,
			'ganalytics_remove_click_id'							=>	1,
			'ganalytics_remove_utm_tag'								=> 	1,
			'ganalytics_remove_user_agent'							=> 	1,
			'ganalytics_send_desktop_mobile_user_agent'				=> 	0,
			'ganalytics_remove_screen_resolution'					=> 	1,
			'ganalytics_remove_referrer'							=> 	1,
			'ganalytics_enable_session_life_cookie_duration' 		=> 	1,

			'facebook_enable'										=>	0,
			'facebook_settings_preset'								=> 	4,
			'facebook_pixel_id'										=>	null,
			'facebook_access_token'									=>	null,
			'facebook_test_event_code'								=>	null,
			'facebook_anonymize_ip'									=>	1,
			'facebook_remove_click_id'								=>	1,
			'facebook_remove_utm_tag'								=> 	1,
			'facebook_remove_user_agent'							=> 	1,
			'facebook_send_desktop_mobile_user_agent'				=> 	0,
			'facebook_enable_session_life_cookie_duration'			=> 	1,

			'tiktok_enable'											=>	0,
			'tiktok_settings_preset'								=> 	4,
			'tiktok_pixel_id'										=>	null,
			'tiktok_access_token'									=>	null,
			'tiktok_test_event_code'								=>	null,
			'tiktok_anonymize_ip'									=>	1,
			'tiktok_remove_click_id'								=>	1,
			'tiktok_remove_utm_tag'									=> 	1,
			'tiktok_remove_user_agent'								=> 	1,
			'tiktok_send_desktop_mobile_user_agent'					=> 	0,
			'tiktok_enable_session_life_cookie_duration'			=> 	1,
			'woocommerce_enable'									=>	0,
			'blacklisted_ip'										=> 	null,
			'blacklisted_events'									=> 	null,
			'internal_debug'										=>	1,
			'block_script_using_cookieshield'						=>	1,

			'consent_mode_requested'								=>	0,
			'consent_mode_enabled'									=>	0,

			'forced_auto_update'									=>	true,

			'user_property_assoc'									=>	null,
			'user_property_def'										=> 	null,

			'use_ga_advanced_features'								=> 	0,
			'compatibility_mode'									=>	0,
		);

		$settings = apply_filters( 'apix_plugin_settings', $settings );

		return $key != "" ? $settings[ $key ] : $settings;
	}

	/**
	 * Returns JSON object containing user settings (ga)
	 * @since    1.0.12
	 * @access   public
	 */
	public static function get_json_settings_analytics()
	{
		$settings = self::get_settings();
		$rconfig = self::get_rconfig();

		$send_fixed_measurement_id = false;

		if( $rconfig &&
			isset( $rconfig['send_fixed_measurement_id'] ) &&
			$rconfig['send_fixed_measurement_id'] )
		{
			$send_fixed_measurement_id = true;
		}

		$logged_in_and_admin = false;
		$internal_debug = false;

		if( current_user_can( 'manage_options' ) )
		{
		    $logged_in_and_admin = true;

		    $internal_debug = filter_var( self::nullCoalesce( $settings['internal_debug'], false ),
																		FILTER_VALIDATE_BOOLEAN );
		}

		if( $rconfig &&
			isset( $rconfig['verbose_remote_log'] ) &&
			$rconfig['verbose_remote_log'] )
		{
			$internal_debug = true;
		}

		$legit_ga4_inject = false;

		if( $rconfig &&
			isset( $rconfig['legit_ga4_inject'] ) &&
			$rconfig['legit_ga4_inject'] )
		{
			$legit_ga4_inject = true;
		}

		$ga4_loaded_check = false;

		if( $rconfig &&
			isset( $rconfig['ga4_loaded_check'] ) &&
			$rconfig['ga4_loaded_check'] )
		{
			$ga4_loaded_check = true;
		}

		if( $settings &&
			isset( $settings['compatibility_mode'] ) &&
			$settings['compatibility_mode']
		)
		{
			$legit_ga4_inject = true;
			$ga4_loaded_check = true;
		}

		if( $settings['pa'] == 1 )
		{
			$consent_mode_requested_and_enabled = false;

			if( filter_var( self::nullCoalesce( $settings['consent_mode_requested'], false ), FILTER_VALIDATE_BOOLEAN ) &&
				filter_var( self::nullCoalesce( $settings['consent_mode_enabled'], false ), FILTER_VALIDATE_BOOLEAN )
			)
			{
				$consent_mode_requested_and_enabled = true;
			}

			$ga_offload_script_url = null;

			$local_file_exists = self::cached_file_exists( 'ga_offload_script.js' );

			if( !$local_file_exists )
			{
				self::download_remote_file( 'https://www.googletagmanager.com/gtag/js?id='.$settings['ganalytics_measurement_id'], 'ga_offload_script.js' );
				$local_file_exists = self::cached_file_exists( 'ga_offload_script.js' );
			}

			if( $local_file_exists )
			{
				$base_ref = self::get_base_url_for_cache();
				$ga_offload_script_url = $base_ref.'ga_offload_script.js';
			}

			$return_settings = array(
				'logged_in_and_admin'					=> 	$logged_in_and_admin,
				'internal_debug'						=>	$internal_debug,


				'ganalytics_enable'						=> 	filter_var(
																		self::nullCoalesce( $settings['ganalytics_enable'], false ),
																		FILTER_VALIDATE_BOOLEAN ),

				'debug_mode'							=> 	filter_var(
																		self::nullCoalesce( $settings['ganalytics_debug_mode'], false ),
																		FILTER_VALIDATE_BOOLEAN ),

				'anonymize_ip'							=>	filter_var(
																		self::nullCoalesce( $settings['ganalytics_anonymize_ip'], false ),
																		FILTER_VALIDATE_BOOLEAN ),

				'remove_click_id'						=>	filter_var(
																		self::nullCoalesce( $settings['ganalytics_remove_click_id'],  false ), FILTER_VALIDATE_BOOLEAN ),

				'remove_utm_tag'						=>	filter_var(
																		self::nullCoalesce( $settings['ganalytics_remove_utm_tag'], false ),
																		FILTER_VALIDATE_BOOLEAN ),

				'remove_user_agent'						=>	filter_var(
																		self::nullCoalesce( $settings['ganalytics_remove_user_agent'], false ),
																		FILTER_VALIDATE_BOOLEAN ),


				'send_desktop_mobile_user_agent'		=>	filter_var(
																		self::nullCoalesce( $settings['ganalytics_send_desktop_mobile_user_agent'], false ),
																		FILTER_VALIDATE_BOOLEAN ),


				'remove_screen_resolution'				=> 	filter_var(
																		self::nullCoalesce( $settings['ganalytics_remove_screen_resolution'], false ),
																		FILTER_VALIDATE_BOOLEAN ),

				'remove_referrer'						=> 	filter_var(
																		self::nullCoalesce( $settings['ganalytics_remove_referrer'], false ),
																		FILTER_VALIDATE_BOOLEAN ),

				'enable_session_life_cookie_duration' 	=> 	filter_var(
																		self::nullCoalesce( $settings['ganalytics_enable_session_life_cookie_duration'], false ),
																		FILTER_VALIDATE_BOOLEAN ),


				'consent_mode_requested_and_enabled'	=>	$consent_mode_requested_and_enabled,
				'use_ga_advanced_features'				=>	false,

				'ganalytics_measurement_id'				=>	$settings['ganalytics_measurement_id'],

				'ga_offload_script_url'					=>	$ga_offload_script_url,
				'send_fixed_measurement_id'				=>	$send_fixed_measurement_id,


				'legit_ga4_inject'						=>	$legit_ga4_inject,
				'ga4_loaded_check'						=>	$ga4_loaded_check,
				'compatibility_mode'					=>	filter_var(
																		self::nullCoalesce( $settings['compatibility_mode'], false ),
																		FILTER_VALIDATE_BOOLEAN ),

			);
		}
		else
		{
			$return_settings = array();
		}

		return $return_settings;
	}

	/**
	 * Returns JSON object containing user settings (fbqapi)
	 * @since    1.0.12
	 * @access   public
	 */
	public static function get_json_settings_fbqapi()
	{
		$settings = self::get_settings();
		$rconfig = self::get_rconfig();

		$logged_in_and_admin = false;
		$internal_debug = false;

		if( current_user_can( 'manage_options' ) )
		{
		    $logged_in_and_admin = true;

		    $internal_debug = filter_var( self::nullCoalesce( $settings['internal_debug'], false ),
																		FILTER_VALIDATE_BOOLEAN );
		}

		if( $rconfig &&
			isset( $rconfig['verbose_remote_log'] ) &&
			$rconfig['verbose_remote_log'] )
		{
			$internal_debug = true;
		}

		if( $settings['pa'] == 1 )
		{
			$return_settings = array(

				'logged_in_and_admin'					=> 	$logged_in_and_admin,
				'internal_debug'						=>	$internal_debug,

				'facebook_enable'						=> 	filter_var(
																		self::nullCoalesce( $settings['facebook_enable'], false ),
																		FILTER_VALIDATE_BOOLEAN ),

				'anonymize_ip'							=>	filter_var(
																		self::nullCoalesce( $settings['facebook_anonymize_ip'], false ),
																		FILTER_VALIDATE_BOOLEAN ),


				'remove_click_id'						=>	filter_var(
																		self::nullCoalesce( $settings['facebook_remove_click_id'], false ),
																		FILTER_VALIDATE_BOOLEAN ),

				'remove_utm_tag'						=>	filter_var(
																		self::nullCoalesce( $settings['facebook_remove_utm_tag'], false ),
																		FILTER_VALIDATE_BOOLEAN ),

				'remove_user_agent'						=>	filter_var(
																		self::nullCoalesce( $settings['facebook_remove_user_agent'], false ),
																		FILTER_VALIDATE_BOOLEAN ),

				'send_desktop_mobile_user_agent'		=>	filter_var(
																		self::nullCoalesce( $settings['facebook_send_desktop_mobile_user_agent'], false ),
																		FILTER_VALIDATE_BOOLEAN ),

				'enable_session_life_cookie_duration' 	=> 	filter_var(
																		self::nullCoalesce( $settings['facebook_enable_session_life_cookie_duration'], false ),
																		FILTER_VALIDATE_BOOLEAN ),

			);
		}
		else
		{
			$return_settings = array();
		}

		return $return_settings;
	}


	/**
	 * Returns JSON object containing user settings (tiktokapi)
	 * @since    1.0.12
	 * @access   public
	 */
	public static function get_json_settings_tiktokapi()
	{
		$settings = self::get_settings();
		$rconfig = self::get_rconfig();

		$logged_in_and_admin = false;
		$internal_debug = false;

		if( current_user_can( 'manage_options' ) )
		{
		    $logged_in_and_admin = true;

		    $internal_debug = filter_var( self::nullCoalesce( $settings['internal_debug'], false ),
																		FILTER_VALIDATE_BOOLEAN );
		}

		if( $rconfig &&
			isset( $rconfig['verbose_remote_log'] ) &&
			$rconfig['verbose_remote_log'] )
		{
			$internal_debug = true;
		}

		$return_settings = array(

			'logged_in_and_admin'					=> 	$logged_in_and_admin,
			'internal_debug'						=>	$internal_debug,

			'tiktok_enable'							=> 	filter_var(
																	self::nullCoalesce( $settings['tiktok_enable'], false ),
																	FILTER_VALIDATE_BOOLEAN ),

			'anonymize_ip'							=>	filter_var( self::nullCoalesce( $settings['tiktok_anonymize_ip'], false ),
																	FILTER_VALIDATE_BOOLEAN ),


			'remove_click_id'						=>	filter_var(
																	self::nullCoalesce( $settings['tiktok_remove_click_id'], false ),
																	FILTER_VALIDATE_BOOLEAN ),

			'remove_utm_tag'						=>	filter_var(
																	self::nullCoalesce( $settings['tiktok_remove_utm_tag'], false ),
																	FILTER_VALIDATE_BOOLEAN ),

			'remove_user_agent'						=>	filter_var(
																	self::nullCoalesce( $settings['tiktok_remove_user_agent'], false ),
																	FILTER_VALIDATE_BOOLEAN ),

			'send_desktop_mobile_user_agent'		=>	filter_var(
																	self::nullCoalesce( $settings['tiktok_send_desktop_mobile_user_agent'], false ),
																	FILTER_VALIDATE_BOOLEAN ),

			'enable_session_life_cookie_duration' 	=> 	filter_var(
																	self::nullCoalesce( $settings['tiktok_enable_session_life_cookie_duration'], false ),
																	FILTER_VALIDATE_BOOLEAN ),
		);

		return $return_settings;
	}


	/**
	 * Makes a call to the WP License Manager API.
	 *
	 * @access   public
	 */
	public static function call_api( $params )
	{
	    $url = MAPX_API_ENDPOINT;

		$site_url = null;

		if( function_exists( 'get_site_url' ) )
		{
			$site_url = get_site_url();
		}

		// Set up arguments for POST request
		$args = array(
			'sslverify' =>	false,
			'headers' 	=>	array(
				'Referer' 	=> $site_url,
			),
			'body' 		=>	$params
		);

	    // Send the request
	    $response = wp_remote_post( $url, $args );

	    if ( is_wp_error( $response ) )
	    {
			//let's try http

			$http_response = wp_remote_post( str_replace( 'https://','http://', $url ), $args );

			if( !is_wp_error( $http_response ) )
			{
				$response_body = wp_remote_retrieve_body( $http_response );
				$result = json_decode( $response_body, true );

				return $result;
			}
			else
			{
				if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $http_response );

				$error_code = array_key_first( $response->errors );
				$error_message = $response->errors[ $error_code ][0];

				$error_code_http = array_key_first( $http_response->errors );
				$error_message_http = $http_response->errors[ $error_code ][0];

				$result = array(
					'internal_error_message'	=>	"$error_code -> $error_message , $error_code_http -> $error_message_http",
				);

				return $result;
			}

	        return false;
	    }

	    $response_body = wp_remote_retrieve_body( $response );
	    $result = json_decode( $response_body, true );

	    return $result;
	}


	/**
	 * Check if WooCommerce is activated
	 */
	public static function is_woocommerce_activated() {
		if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
	}


	/**
	 * Check if MyAgilePrivacy is activated
	 */
	public static function is_myagileprivacy_activated() {

		if( !function_exists( 'is_plugin_active' ) )
		{
			include_once(ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$active = is_plugin_active( 'myagileprivacy/my-agile-privacy.php' );

		return $active;
	}


	/**
	 * get the user ip address
	 */
	public static function get_the_user_ip() {

		if( !empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) )
		{
			//check ip from cloudflare
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}
		elseif( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ){

			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )
		{
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif( !empty( $_SERVER['REMOTE_ADDR'] ) )
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		else
		{
			$ip = null;
		}

		return apply_filters( 'wpb_get_ip', $ip );
	}


	/**
	 * check for buffer / script inclusion skip
	 * @access   public
	*/
	public static function check_buffer_skip_conditions()
	{
		$skip = 'false';

		global $wp;
		global $pagenow;
		global $wp_query;
		global $wp_rewrite;
		$feeds = null;

		if( is_object( $wp_rewrite ) )
		{
			$feeds = $wp_rewrite->feeds;
		}

		//url check
		$current_href = null;

		if( is_object( $wp ) )
		{
			if( isset( $_SERVER['QUERY_STRING'] ) )
			{
				$current_href = add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
			}
			else
			{
				$current_href = home_url( $wp->request );
			}
		}

		$alt_current_href = null;

		if( isset( $_SERVER['SCRIPT_URI'] ) )
		{
			$alt_current_href = $_SERVER['SCRIPT_URI'];
		}
		elseif( isset( $_SERVER['REQUEST_URI'] ) )
		{
			$alt_current_href = $_SERVER['REQUEST_URI'];
		}

		$rconfig = self::get_rconfig();

		//regexp check
		if( isset( $rconfig['url_skip_regexp'] ) )
		{
			$url_skip_regexp = $rconfig['url_skip_regexp'];

			if( is_object( $wp ) )
			{
				$found = false;

				foreach( $url_skip_regexp as $regexp )
				{
					if( ( $current_href && preg_match( $regexp, $current_href ) ) ||
						( $alt_current_href && preg_match( $regexp, $alt_current_href ) )
					)
					{
						$found = true;
					}
				}

				if( $found ) $skip = 'true';
			}
		}

		//feed check
		$feed_url_list = array();

		if( $feeds )
		{
			$found = false;

			foreach ( $feeds as $feed )
			{
				$feed_url_list[] = get_feed_link( $feed );
			}

			foreach( $feed_url_list as $feed_url )
			{
				if( ( $current_href && $current_href == $feed_url ) ||
					( $alt_current_href && $alt_current_href == $feed_url )
				)
				{
					$found = true;
				}
			}

			if( $found ) $skip = 'true';
		}


		//widgets
		if( $pagenow && $pagenow === 'widgets.php' ) $skip = 'true';

		//amp
		if( ( function_exists( 'amp_is_request' ) && amp_is_request() ) ||
			isset( $_GET['amp'] ) ||
			strpos( $_SERVER['REQUEST_URI'], '/amp/' ) !== false ) $skip = 'true';

		//elementor
		if( isset( $_GET['elementor-preview'] ) ) $skip = 'true';

		//divi
		if ( isset( $_GET['et_fb'] ) && $_GET['et_fb'] == 1 ) $skip = 'true';

		//no admin
		if( is_admin() ) $skip = 'true';

		//no rss
		if( isset( $wp_query ) && is_feed() ) $skip = 'true';

		//divi
		if( function_exists( 'et_fb_is_enabled' ) && et_fb_is_enabled() ) $skip = 'true';

		// page builder
		if( is_customize_preview() ) $skip = 'true';

		//xml rpc, ajax, admin
		if( ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) || isset($_POST['_wpnonce']) || (function_exists( "wp_doing_ajax" ) && wp_doing_ajax()) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_SERVER["HTTP_X_REQUESTED_WITH"] ) )  $skip = 'true';

		//matomo
		if( strpos( $_SERVER['REQUEST_URI'], 'plugins/matomo/app' ) !== false ) $skip = 'true';

		//is_json
		if( ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) || strpos( $_SERVER['REQUEST_URI'], '/wp-json/' ) !== false ) $skip = 'true';

		if( defined( 'REST_REQUEST' ) ) $skip = 'true';

		//wp-login and similar pages
		if( MyAgilePixel::is_wplogin() ) $skip = 'true';

		//rest request
        if (defined( 'REST_REQUEST' ) && REST_REQUEST // (#1)
                || isset($_GET['rest_route']) // (#2)
                        && strpos( $_GET['rest_route'], '/', 0 ) === 0)
                 $skip = 'true';

        //post
        if( !empty( $_POST ) ) $skip = 'true_due_to_post';

        return $skip;
	}


	/**
	 * check for wp login page
	 * @access   public
	*/
	public static function is_wplogin()
	{
		if( function_exists( 'login_header' ) )
	    {
	    	return true;
	    }

		if( isset( $_GET['page'] ) && $_GET['page'] == 'sign-in' )
		{
		   return true;
		}

	    $ABSPATH_MY = str_replace(array( '\\','/' ), DIRECTORY_SEPARATOR, ABSPATH);
	    return ((in_array($ABSPATH_MY.'wp-login.php', get_included_files()) || in_array($ABSPATH_MY.'wp-register.php', get_included_files()) ) || (isset($_GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php' ) || $_SERVER['PHP_SELF']== '/wp-login.php' );
	}


	/**
	 * get cache base directory url
	 */
	public static function get_base_url_for_cache()
	{
		if( !defined( 'MAPX_PLUGIN_NAME' ) ) return null;

		$current_plugin_url = plugin_dir_url( MAPX_PLUGIN_FILENAME );

		$final_url = $current_plugin_url. '/local-cache/'.MAPX_PLUGIN_NAME.'/';

		//remove unnecessary slashes
		$final_url = preg_replace( '/([^:])(\/{2,})/', '$1/', $final_url );

		return  $final_url;
	}

	/**
	 * get cache base directory url
	 */
	public static function get_base_directory_for_cache()
	{
		if( !defined( 'MAPX_PLUGIN_NAME' ) ) return null;

		$current_plugin_dir = plugin_dir_path( MAPX_PLUGIN_FILENAME );

		return $current_plugin_dir . '/local-cache/'.MAPX_PLUGIN_NAME.'/';
	}

	/**
	 * check for file exists
	 */
	public static function cached_file_exists( $local_filename )
	{
		$directory = MyAgilePixel::get_base_directory_for_cache();

		if( $directory )
		{
			$local_filename_fullpath = $directory.$local_filename;

			if ( is_file( $local_filename_fullpath ) )
			{
				return true;
			}
		}


		return false;
	}

	/**
	 * download remote file
	 */
	public static function download_remote_file( $remote_filename, $local_filename, $version_number=null, $alt_local_filename=null )
	{
		if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( "download_remote_file call with param remote_filename=$remote_filename, local_filename=$local_filename, version_number=$version_number, alt_local_filename=$alt_local_filename" );

		$directory = MyAgilePixel::get_base_directory_for_cache();

		if( !$directory )
		{
			return false;
		}

		$local_filename_fullpath = $directory.$local_filename;

		$local_alt_filename_fullpath = null;

		if( $alt_local_filename )
		{
			$local_alt_filename_fullpath = $directory.$alt_local_filename;
		}

		$expiration_time_in_seconds = 60*60*24;
		$max_age = time() - $expiration_time_in_seconds;

		$manifest_assoc = get_option( MAPX_MANIFEST_ASSOC, null );

		if( $manifest_assoc &&
			isset( $manifest_assoc['files'][ $local_filename ] ) &&
			$manifest_assoc['files'][ $local_filename ] &&
			$version_number &&
			$alt_local_filename )
		{
			if( version_compare( $manifest_assoc['files'][ $local_filename ]['version'], $version_number , '>=' ) &&
				is_file( $local_alt_filename_fullpath ) )
			{
				//no download needed
				if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'check A : no download needed' );

				return true;
			}
			else
			{
				if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER )
				{
					$debug_info = array(
						'remote_version_number'			=>	$manifest_assoc['files'][ $local_filename ]['version'],
						'this_version_number'			=>	$version_number,
						'version_check'					=>	version_compare( $manifest_assoc['files'][ $local_filename ]['version'], $version_number , '>=' ),
						'local_alt_filename_fullpath'	=> 	$local_alt_filename_fullpath,
						'local_alt_filename_check'		=>	is_file( $local_alt_filename_fullpath ),

					);

					MyAgilePixel::write_log( $debug_info );
				}
			}
		}
		else
		{
			if( $alt_local_filename )
			{
				if ( is_file( $local_filename_fullpath ) && filemtime( $local_filename_fullpath ) > $max_age &&
					is_file( $local_alt_filename_fullpath ) && filemtime( $local_alt_filename_fullpath ) > $max_age
				)
				{
					if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'check B : no download needed' );
					return true;
				}
			}
			else
			{
				if ( is_file( $local_filename_fullpath ) && filemtime( $local_filename_fullpath ) > $max_age )
				{
					if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'check C : no download needed' );
					return true;
				}
			}
		}

		if( file_exists( $local_filename_fullpath ) )
		{
			wp_delete_file( $local_filename_fullpath );
		}

		if( $alt_local_filename )
		{
			if( file_exists( $local_alt_filename_fullpath ) )
			{
				wp_delete_file( $local_alt_filename_fullpath );
			}
		}

		if( ! wp_mkdir_p( $directory ) )
		{
			if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'Error creating needed directory: ' . $directory );
			return false;
		}

		if ( ! function_exists( 'download_url' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$tmp_file = download_url( $remote_filename );

		if( !$tmp_file || !is_string( $tmp_file ) )
		{
			if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'Error downloading remote_filename: ' . $remote_filename );
			return false;
		}

		copy( $tmp_file, $local_filename_fullpath );

		if( $alt_local_filename )
		{
			copy( $tmp_file, $local_alt_filename_fullpath );
		}

		if( file_exists( $tmp_file ) ) @unlink( $tmp_file );

		if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'download_remote_file -> remote file downloaded to '.$local_filename_fullpath . ' from '.$remote_filename );

		//old folder cleanup
		$old_cache_dir = WP_CONTENT_DIR . '/local-cache/'.MAPX_PLUGIN_NAME.'/';
		MyAgilePixel::clear_cache( $old_cache_dir, true ) ;

		return true;
	}

	/**
	 * clear file cache
	 */
	public static function clear_cache( $directory = null, $remove_dir = false )
	{
		if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( "clear_cache with params directory=$directory, remove_dir=$remove_dir" );

		if( !$directory )
		{
			$directory = MyAgilePixel::get_base_directory_for_cache();
		}

		if( !$directory )
		{
			return false;
		}

		if( !is_dir( $directory ) )
		{
			return false;
		}

		$objects = scandir( $directory );
		foreach ( $objects as $object ) {
			if ( $object != "." && $object != ".." ) {
				if ( is_dir( $directory . DIRECTORY_SEPARATOR . $object ) && ! is_link( $directory . "/" . $object ) ) {
					MyAgilePixel::clear_cache( $directory . DIRECTORY_SEPARATOR . $object );
				} else {

					$this_filepath = $directory . DIRECTORY_SEPARATOR . $object;

					if( file_exists( $this_filepath ) ) @unlink( $this_filepath );
				}
			}
		}

		if( $remove_dir )
		{
			rmdir( $directory );
		}

		return true;
	}

	/**
		equivalent for php7 null coalesce
	 */
	public static function nullCoalesce( $var, $default = null )
	{
		return isset( $var ) ? $var : $default;
	}

	/**
	 * equivalent for php7 null coalesce (array)
	 */
	public static function nullCoalesceArrayItem( $var, $key, $default = null )
	{
		return isset( $var[ $key ] ) ? $var[ $key ] : $default;
	}


	/**
	 * Returns $do_not_send_in_clear_settings_key
	 */
	public static function get_do_not_send_in_clear_settings_key()
	{
		$do_not_send_in_clear_settings_key = array(
			'license_code',
		);

		return $do_not_send_in_clear_settings_key;
	}


	/**
	 * get server footprint
	 */
	public static function getServerFootPrint()
	{
		$return_data = array();

		$keysToRemove = array(
			'HTTP_COOKIE',
			'HTTP_USER_AGENT',
			'HTTP_X_REAL_IP',
			'HTTP_X_REMOTE_IP',
			'HTTP_CF_CONNECTING_IP',
			'HTTP_CF_IPCOUNTRY',
			'SERVER_ADDR',
			'REMOTE_ADDR',
			'PROXY_REMOTE_ADDR',
			'SSL_CLIENT_CERT',
			'SSL_SERVER_CERT'
		);

		foreach( $_SERVER as $k => $v )
		{
			if( in_array( $k, $keysToRemove ) )
			{
				$v = '(set)';
			}

			$return_data[ $k ] = $v;
		}

		return $return_data;
	}


	/**
	 * write to log file
	 * @access   public
	 */
    public static function write_log($log)
    {
		if( defined( 'MAPX_PLUGIN_NAME' ) )
		{
			$plugin_name = MAPX_PLUGIN_NAME;
		}
		else
		{
			$plugin_name = 'my-agile-pixel';
		}

		$dirPath = WP_CONTENT_DIR . '/debug/';
		$filePath = $dirPath.$plugin_name.'.txt';

    	if( ! wp_mkdir_p( $dirPath ) )
    	{
    		return;
    	}

    	$bt = debug_backtrace();

    	$depth = 0;

        $file = isset($bt[$depth])     ? $bt[$depth]['file'] : null;
        $line = isset($bt[$depth])     ? $bt[$depth]['line'] : 0;
        $func = isset($bt[$depth + 1]) ? $bt[$depth + 1]['function'] : null;

        if (is_array($log) || is_object($log)) {
            $data = print_r($log, true);
        } else {
            $data = $log;
        }

        $string = "file=$file, line=$line, func=$func: ".$data."\n";

    	file_put_contents( $filePath, $string, FILE_APPEND );
    }
}