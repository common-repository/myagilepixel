<?php

/**
 * The backend-specific functionality of the plugin.
 *
*/
class MyAgilePixelAdmin {
    // Plugin Name
    private $plugin_name;

    // Plugin Version
    private $version;

    // Constructor
    public function __construct( $plugin_name, $version, $plugin_obj )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
		$this->set_locale();
    }

	private function set_locale()
	{
		global $locale;

		$the_settings = MyAgilePixel::get_settings();

		$loaded = $this->my_load_plugin_textdomain(
			MAPX_PLUGIN_SLUG,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/lang/'
		);
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 * Rewritten func to ignore messy mofile
	 * @since    1.3.9
	 * @access   private
	 */
	private function my_load_plugin_textdomain( $domain, $deprecated = false, $plugin_rel_path = false )
	{
		if( isset( $GLOBALS['wp_version'] ) && version_compare( $GLOBALS['wp_version'], '5.0', '<' ) )
		{
			if( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) )
			{
				$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
			}
			else
			{
				$locale = apply_filters( 'plugin_locale', is_admin() ? get_user_locale() : get_locale(), $domain );
			}
		}
		else
		{
			$locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
		}

		$mofile = $domain . '-' . $locale . '.mo';

		/*
		// Try to load from the languages directory first.
		if ( load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $mofile ) ) {
			return true;
		}
		*/

		if ( false !== $plugin_rel_path ) {
			$path = WP_PLUGIN_DIR . '/' . trim( $plugin_rel_path, '/' );
		} elseif ( false !== $deprecated ) {
			_deprecated_argument( __FUNCTION__, '2.7.0' );
			$path = ABSPATH . trim( $deprecated, '/' );
		} else {
			$path = WP_PLUGIN_DIR;
		}


		return load_textdomain( $domain, $path . '/' . $mofile );
	}

	/**
	* Admin Init
	 * @access   public
	*/
	public function admin_init()
	{
	    //db version handling
	    $db_version = MyAgilePixel::nullCoalesce( get_option( MAPX_PLUGIN_DB_VERSION ), null );

	    if( !$db_version )
	    {
	    	update_option( MAPX_PLUGIN_DB_VERSION, MAPX_PLUGIN_DB_VERSION_NUMBER );
	    }
	}

	/**
	* Function for outputting action link ( wp plugins area)
	 * @since    1.0.12
	 * @access   public
	*/
	public function plugin_action_links( $links )
	{
		global $locale;

		$links[] = '<a href="'. get_admin_url(null,'admin.php?page=my-agile-pixel_settings' ).'">'.__( 'Settings','myagilepixel' ).'</a>';
		$links[] = '<a href="https://www.myagilepixel.com/" target="_blank">'.__( 'Support','myagilepixel' ).'</a>';

		return $links;
	}

	//global definitions for inter plugin communication
	public function add_global_defs()
	{
		$the_options = MyAgilePixel::get_settings();

		if( $the_options['general_plugin_active'] &&
			$the_options['general_interface_with'] == 'myagileprivacy' )
		{
			if( $the_options['pa'] == 1 &&
				$the_options['ganalytics_enable'] )
			{
				define ( 'MAPX_my_agile_pixel_ga_on', true );
			}

			if( $the_options['pa'] == 1 &&
				$the_options['facebook_enable'] )
			{
				define ( 'MAPX_my_agile_pixel_fbq_on', true );
			}

			if( $the_options['tiktok_enable'] )
			{
				define ( 'MAPX_my_agile_pixel_tiktok_on', true );
			}
		}
	}

    // Stylesheets for the admin area.
    public function enqueue_styles()
	{
		$do_load = false;

		global $pagenow;
		$current_page_settings = get_current_screen();
		$current_page_post_type = $current_page_settings->post_type;
		$current_page_base = $current_page_settings->base;

		if ( $current_page_base == 'toplevel_page_my-agile-pixel_settings' ||
			 $current_page_base == 'my-agile-pixel_page_my-agile-pixel_user_property_assoc' )
		{
			$do_load = true;
		}

		if( $do_load )
		{
			wp_enqueue_style( $this->plugin_name.'-bootstrap', plugin_dir_url( __FILE__ ) ."css/bootstrap.min.css", array(),$this->version, 'all' );

			wp_enqueue_style( $this->plugin_name.'-fawesome', plugin_dir_url( __FILE__ ) ."css/f-awesome-all.css", array(),$this->version, 'all' );

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) ."css/my-agile-pixel-admin.css", array(),$this->version, 'all' );
		}
	}


    // JS for admin area
    public function enqueue_scripts()
	{
		$do_load = false;

		global $pagenow;
		$current_page_settings = get_current_screen();
		$current_page_post_type = $current_page_settings->post_type;
		$current_page_base = $current_page_settings->base;

		if ( $current_page_base == 'toplevel_page_my-agile-pixel_settings' ||
			 $current_page_base == 'my-agile-pixel_page_my-agile-pixel_user_property_assoc' )
		{
			$do_load = true;
		}

		if( $do_load )
		{
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/my-agile-pixel-admin.js', array( 'jquery' ), $this->version, false );

			wp_enqueue_script( $this->plugin_name.'-bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
		}

	}

	// Add admin page
    public function add_admin_pages()
    {
		global $submenu;

		add_menu_page(
			MAPX_HUMAN_NAME,			 							//page_title
			MAPX_HUMAN_NAME, 										//menu_title
			'manage_options', 										//capability
			MAPX_DOMAIN.'_settings', 								//menu_slug
			array( $this, 'apix_settings_html' ), 					//function
			plugin_dir_url(__DIR__).'/admin/img/menu-icon.png',		//icon_url
			10				 										//position
		);

		add_submenu_page(
			MAPX_DOMAIN.'_settings',
			__( 'User Property Tracking', 'myagilepixel' ),			//page_title
			__( 'User Property Tracking', 'myagilepixel' ), 		//menu_title
			'manage_options', 										//capability
			MAPX_DOMAIN.'_user_property_assoc',
			array( $this, 'apix_user_property_assoc_html' ) 		//function
		);

	}

	/**
	 * get options summary for remote validation
	 *
	 */
	public function get_options_summary()
	{
		$rconfig = MyAgilePixel::get_rconfig();

		$cleaned_options = array();

		$manifest_assoc = null;

		if(	!MAPX_DEV_MODE &&
			$rconfig &&
			isset( $rconfig['allow_manifest'] ) &&
			$rconfig['allow_manifest']
		)
		{
			$manifest_assoc = get_option( MAPX_MANIFEST_ASSOC, null );
		}

		$current_lang = get_locale();

		$other_data = array(
			'mapx_version'				=>	MAPX_PLUGIN_VERSION,
			'locale'					=>	$current_lang,
			'with_my_agile_privacy'		=> 	false,
			'my_agile_pixel_version'	=> 	null,
			'my_agile_pixel_options'	=> 	null,
			'theme_name'				=> 	null,
			'with_multilang'			=> 	false,
			'is_wpml_enabled'			=> 	false,
			'is_polylang_enabled'		=> 	false,
		);

		//bof theme calc
		$my_theme = wp_get_theme();
		if( $my_theme && is_object( $my_theme ) )
		{
			$other_data['theme_name'] = $my_theme->get( 'Name' );
		}
		//eof theme calc

		//bof with_my_agile_privacy
		$with_my_agile_privacy = false;

		if (!function_exists( 'is_plugin_active' ) ) {
			include_once(ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if( is_plugin_active( 'myagileprivacy/my-agile-privacy.php' ) )
		{
			$with_my_agile_privacy = true;

			if( defined( 'MAP_PLUGIN_VERSION' ) )
			{
				$other_data['my_agile_privacy_version'] = MAP_PLUGIN_VERSION;
			}

		}
		$other_data['with_my_agile_privacy'] = $with_my_agile_privacy;

		//eof with_my_agile_privacy

		// Get options
		$the_options = MyAgilePixel::get_settings();
		$do_not_send_in_clear_settings_key = MyAgilePixel::get_do_not_send_in_clear_settings_key();

		//purge do_not_send fields

		foreach( $the_options as $k => $v )
		{
			if( in_array( $k, $do_not_send_in_clear_settings_key ) )
			{
				$cleaned_options[$k] = ( isset( $v ) ) ? '(set)' : '(not set)';
			}
			else
			{
				$cleaned_options[$k] = $v;
			}
		}

		//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $cleaned_options );

		global $locale;
		global $sitepress;
		$is_wpml_enabled = false;
		$is_polylang_enabled = false;
		$with_multilang = false;

		if( function_exists( 'icl_object_id' ) && $sitepress )
		{
			$is_wpml_enabled = true;
			$with_multilang = true;

			$other_data['with_multilang'] = true;
			$other_data['is_wpml_enabled'] = true;

			$multilang_default_lang = $sitepress->get_default_language();
			$wpml_current_lang = ICL_LANGUAGE_CODE;
			$language_list = icl_get_languages();
			$language_list_codes = array();

			foreach( $language_list as $k => $v )
			{
				if( isset( $v['code'] ) )
				{
					$language_list_codes[] = $v['code'];
				}
				elseif( isset( $v['language_code'] ) )
				{
					$language_list_codes[] = $v['language_code'];
				}
			}
		}

		if( defined( 'POLYLANG_FILE' ) )
		{
			$is_polylang_enabled = true;
			$with_multilang = true;

			$other_data['with_multilang'] = true;
			$other_data['is_polylang_enabled'] = true;

			$multilang_default_lang = pll_default_language();
			$language_list_codes = pll_languages_list();

			//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $default );
			//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $language_list_codes );
		}

		$output = array(
			'cleaned_options' 	=>	$cleaned_options,
			'other_data'		=>	$other_data,
			'manifest_assoc'	=>	$manifest_assoc,
		);

		//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $output );

		return $output;
	}



	// Admin page html callback
	public function apix_settings_html()
    {
		// check user capabilities
		if ( !current_user_can( 'manage_options' ) )
		{
			if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( ' apix_settings_html -> missing user permission' );
			return false;
		}

		// Get options
		$the_options = MyAgilePixel::get_settings();
		$rconfig = MyAgilePixel::get_rconfig();

	    if( !empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' )
        {
        	exit();
        }

        $current_client_ip = MyAgilePixel::get_the_user_ip();

		$wasm_environment = false;

		if( isset( $_SERVER ) &&
			isset( $_SERVER['SERVER_SOFTWARE'] ) &&
			$_SERVER['SERVER_SOFTWARE'] == 'PHP.wasm'
		)
		{
			$wasm_environment = true;
		}

		$css_compatibility_fix = false;

		if( isset( $GLOBALS['wp_version'] ) && version_compare( $GLOBALS['wp_version'], '4.2', '<' ) )
		{
			$css_compatibility_fix = true;
		}

		require_once plugin_dir_path( __FILE__ ).'views/settings_page_html.php';
	}


	// Admin page html callback
	public function apix_user_property_assoc_html()
    {
		// check user capabilities
		if ( !current_user_can( 'manage_options' ) )
		{
			if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( ' apix_user_property_assoc_html -> missing user permission' );
			return false;
		}

		// Get options
		$the_options = MyAgilePixel::get_settings();
		$rconfig = MyAgilePixel::get_rconfig();

	    if( !empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' )
        {
        	exit();
        }

		//bof lang check

		global $locale;
		global $sitepress;
		$is_wpml_enabled = false;
		$is_polylang_enabled = false;
		$with_multilang = false;

		if( function_exists( 'icl_object_id' ) && $sitepress )
		{
			$is_wpml_enabled = true;
			$is_polylang_enabled = true;

			$multilang_default_lang = $sitepress->get_default_language();
			$wpml_current_lang = ICL_LANGUAGE_CODE;
			$language_list = icl_get_languages();
			$language_list_codes = array();

			foreach( $language_list as $k => $v )
			{
				if( isset( $v['code'] ) )
				{
					$language_list_codes[] = $v['code'];
				}
				elseif( isset( $v['language_code'] ) )
				{
					$language_list_codes[] = $v['language_code'];
				}
			}
		}

		if( defined( 'POLYLANG_FILE' ) )
		{
			$is_polylang_enabled = true;
			$with_multilang = true;

			$multilang_default_lang = pll_default_language();
			$language_list_codes = pll_languages_list();
		}
		//eof lang check

		$all_posts = array();

		// Get the list of post types
		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		$post_types_selectable = array();

		$unallowed_post_types = array(
			'attachment',
		);

		foreach ( $post_types as $post_type )
		{
			if( !in_array( $post_type->name, $unallowed_post_types ) )
			{
				$post_types_selectable[ $post_type->name ] = $post_type->label;

				$all_posts[ $post_type->name ] = array();
			}
		}

		// Get the list of taxonomies
		/*
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

		$taxonomies_selectable = array();

		$unallowed_taxonomies = array(
			'post_format',
		);

		foreach ( $taxonomies as $taxonomy )
		{
			if( !in_array( $taxonomy->name, $unallowed_taxonomies ) )
			{
				$taxonomies_selectable[ $taxonomy->name ] = $taxonomy->label;
			}
		}
		*/

		if( $is_wpml_enabled )
		{
			$sitepress->switch_lang( $multilang_default_lang );
		}

		$post_status_to_search = array( 'draft', 'publish');

		$cc_args = array(
			'posts_per_page'   	=> 	-1,
			'post_type'        	=>	'any',
			'post_status' 		=> 	$post_status_to_search,
		);

		if( $is_polylang_enabled )
		{
			$cc_args['lang'] = $multilang_default_lang;
		}

		$cc_query = new WP_Query( $cc_args );

		//wpml language reset
		if( $is_wpml_enabled )
		{
			$sitepress->switch_lang( $wpml_current_lang );
		}

		if ( $cc_query->have_posts() )
		{
			foreach ( $cc_query->get_posts() as $p )
			{
				$main_post_id = $p->ID;

				$post_type = get_post_type( $main_post_id );

				$the_title = get_the_title( $main_post_id );

				//data populating
				$all_posts[ $post_type ][ $main_post_id ] = $the_title;
			}

			MyAgilePixel::internal_query_reset();
		}

		$user_property_assoc_saved_settings = MyAgilePixel::nullCoalesceArrayItem( $the_options, 'user_property_assoc', null );

		$css_compatibility_fix = false;

		if( isset( $GLOBALS['wp_version'] ) && version_compare( $GLOBALS['wp_version'], '4.2', '<' ) )
		{
			$css_compatibility_fix = true;
		}

		require_once plugin_dir_path( __FILE__ ).'views/user_property_assoc.php';
	}


	// Update User property Callback
	public function update_user_property_assoc_form_callback()
	{
		// check user capabilities
		if ( !current_user_can( 'manage_options' ) )
		{
			if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( ' update_user_property_assoc_form_callback -> missing user permission' );
			return false;
		}

		// Get global options
		$the_options = MyAgilePixel::get_settings();
		$the_options_save = $the_options;

		$with_missing_fields = false;

		if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $the_options );

	    // Check nonce:
	    check_admin_referer( 'apix-update-' . MAPX_PLUGIN_SETTINGS_FIELD );

		// check form submit
	    if( isset( $_POST['action'] ) && $_POST['action'] == 'apix_update_user_property_assoc_form' )
	    {
			if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $_POST );

	        foreach( $the_options as $key => $value )
	        {
	            if( isset( $_POST[$key . '_field'] ) )
	            {
					if( $key == 'user_property_def' )
	            	{
	            		$list = array();

	            		foreach( $_POST[$key . '_field'] as $item )
	            		{
	            			$trimmed_item = trim( $item );

	            			$list[] = $trimmed_item;
	            		}

	            		$the_options[$key] = json_encode( array_unique( $list ) );

	            	}
	            	elseif( $key == 'user_property_assoc' )
	            	{
		                // Store sanitised values only
		                $the_options[$key] = MyAgilePixel::sanitise_settings( $key, $_POST[$key . '_field'] );
	            	}

	            }
			}

			if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $the_options );

			update_option( MAPX_PLUGIN_SETTINGS_FIELD, $the_options );

			//bof validation part
			if( isset( $the_options['ganalytics_enable'] ) &&
				$the_options['ganalytics_enable'] == 1 &&
				!$the_options['ganalytics_measurement_id'] )
			{
				$with_missing_fields = true;
			}

			if( isset( $the_options['facebook_enable'] ) &&
				$the_options['facebook_enable'] == 1 &&
				( !$the_options['facebook_pixel_id'] || !$the_options['facebook_access_token'] ) )
			{
				$with_missing_fields = true;
			}

			if( isset( $the_options['tiktok_enable'] ) &&
				$the_options['tiktok_enable'] == 1 &&
				( !$the_options['tiktok_pixel_id'] || !$the_options['tiktok_access_token'] ) )
			{
				$with_missing_fields = true;
			}
			//eof validation part

			$lc_hide_local = ( isset( $rconfig ) && $rconfig['lc_hide_local'] && $rconfig['lc_hide_local'] == 1 ) ? 1 : 0;
			$lc_owner_description = ( isset( $rconfig ) && $rconfig['lc_owner_description'] ) ? $rconfig['lc_owner_description'] : null;
			$lc_owner_email = ( isset( $rconfig ) && $rconfig['lc_owner_email'] ) ? $rconfig['lc_owner_email'] : null;
			$lc_owner_website = ( isset( $rconfig ) && $rconfig['lc_owner_website'] ) ? $rconfig['lc_owner_website'] : null;

	        $answer = array(
	        	'success'				=>	true,
	        	'with_missing_fields'	=>	$with_missing_fields,
	        );

	        wp_send_json( $answer );

	        if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $answer );
	    }
	}



	// Update Global Options Callback
	public function update_admin_settings_form_callback()
	{
		// check user capabilities
		if ( !current_user_can( 'manage_options' ) )
		{
			if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( ' update_admin_settings_form_callback -> missing user permission' );
			return false;
		}

		// Get global options
		$the_options = MyAgilePixel::get_settings();
		$the_options_save = $the_options;

		$with_missing_fields = false;

		if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $the_options );

	    // Check nonce:
	    check_admin_referer( 'apix-update-' . MAPX_PLUGIN_SETTINGS_FIELD );

		// check form submit
	    if( isset( $_POST['action'] ) && $_POST['action'] == 'apix_update_admin_settings_form' )
	    {
			$do_clear_file_cache = false;
			$do_revalidation = false;

			$license_user_status = null;
			$license_valid = null;
			$grace_period = false;
			$summary_text = null;

			if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $_POST );

	        foreach( $the_options as $key => $value )
	        {
	            if( isset( $_POST[$key . '_field'] ) )
	            {
	            	if( $key == 'blacklisted_ip' )
	            	{
	            		$list = array();

	            		foreach( $_POST[$key . '_field'] as $item )
	            		{
	            			$trimmed_item = trim( $item );

	            			if( filter_var( $trimmed_item, FILTER_VALIDATE_IP) )
	            			{
	            				$list[] = $trimmed_item;
	            			}
	            		}
	            		$the_options[$key] = json_encode( array_unique( $list ) );
	            	}
					else if( $key == 'blacklisted_events' )
	            	{
	            		$list = array();

	            		foreach( $_POST[$key . '_field'] as $item )
	            		{
	            			$trimmed_item = trim( $item );

	            			$list[] = $trimmed_item;
	            		}

	            		$the_options[$key] = json_encode( array_unique( $list ) );
	            	}
	            	else
	            	{
		                // Store sanitised values only
		                $the_options[$key] = MyAgilePixel::sanitise_settings( $key, $_POST[$key . '_field'] );
	            	}
	            }
			}

			if( $the_options_save['general_plugin_active'] == false &&
				$the_options['general_plugin_active'] == true )
			{
				$do_revalidation = true;
				$do_clear_file_cache = true;
			}

			$rr = false;
			$pa = 0;

			$missing_key = false;

			if( !$the_options['license_valid'] ||
				!$the_options_save['pa'] ||
				$the_options_save['license_code'] != $_POST['license_code_field'] )
			{
				$missing_key = true;
			}

			$now = time();
			$the_timestamp = MyAgilePixel::nullCoalesce( get_option( MAPX_PLUGIN_VALIDATION_TIMESTAMP ), null );

			if( ( $do_revalidation ||
					$the_timestamp == null ||
					$now - $the_timestamp > 86400 ||
					$missing_key ) &&
				isset( $_POST['license_code_field'] )
			)
			{
				$do_clear_file_cache = true;

				//validation part

				$opt_to_send = array();
				foreach( $the_options as $k => $v )
				{
					if( in_array( $k, MAPX_REMOTE_OPT ) )
					{
						$opt_to_send[ $k ] = $v;
					}
				}

				$urlparts = parse_url( home_url() );
				$domain = $urlparts['host'];

				$data_to_send = array(
					'action'			=>	'validation',
					'software_key'		=>	MAPX_SOFTWARE_KEY,
					'hash'				=>	sanitize_text_field( $_POST['license_code_field'] ),
					'domain'			=>	$domain,
					'version'			=>	MAPX_PLUGIN_VERSION,
					'opt_to_send'		=>	json_encode( $opt_to_send ),
					'options_summary'	=>	$this->get_options_summary(),
					'server_data'		=>	MyAgilePixel::getServerFootPrint(),
					'bypass_cache'		=>	( $do_revalidation || $missing_key ) ? 1 : 0,
				);

				$action_result = MyAgilePixel::call_api( $data_to_send );

				if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $action_result );


				if( !$action_result ||
					( $action_result && isset( $action_result['internal_error_message'] ) )
				)
				{
					$rr = false;
				}
				else
				{
					if( $action_result['success'] )
					{
						$rr = true;

						$license_valid = true;
						$grace_period = false;

						if( $action_result['paid_license'] == 0 )
						{
							$license_user_status = 'Demo license';

							if( isset( $action_result['error_msg'] ) )
							{
								$license_valid = false;
								$license_user_status = $action_result['error_msg'];
							}
						}
						else
						{
							if( isset( $action_result['grace_period'] ) && $action_result['grace_period'] == 1 )
							{
								$license_user_status = 'Grace period - expiring soon';
								$grace_period = true;
							}
							elseif( isset( $action_result['error_msg'] ) )
							{
								$license_user_status = $action_result['error_msg'];
							}
							else
							{
								$license_user_status = 'License valid';
							}

							$pa = 1;
						}
					}
					else
					{
						$rr = true;
						$license_valid = false;
						$grace_period = false;
						$license_user_status = $action_result['error_msg'];
					}
				}

				update_option( MAPX_PLUGIN_VALIDATION_TIMESTAMP, $now );
			}

			if( $do_clear_file_cache )
			{
				MyAgilePixel::clear_cache();
				update_option( MAPX_PLUGIN_DO_SYNC_NOW, 1 );
			}

			if( $rr )
			{
				$summary_text = $action_result['summary_text'];

				$the_options['license_user_status'] = $license_user_status;
				$the_options['license_valid'] = $license_valid;
				$the_options['grace_period'] = $grace_period;
				$the_options['summary_text'] = $summary_text;
				$the_options['wl'] = ( isset( $action_result['wl'] ) ) ? $action_result['wl'] : 0;
				$the_options['pa'] = $pa;
				$rconfig = ( isset( $action_result['rconfig'] ) ) ? $action_result['rconfig'] : null;
				update_option( MAPX_PLUGIN_RCONFIG, $rconfig );
			}
			else
			{
				$license_user_status = $the_options['license_user_status'];
				$license_valid = $the_options['license_valid'];
				$grace_period = $the_options['grace_period'];
				$summary_text = $the_options['summary_text'];
				$rconfig = MyAgilePixel::get_rconfig();
			}

			update_option( MAPX_PLUGIN_SETTINGS_FIELD, $the_options );

			//bof validation part
			if( isset( $the_options['ganalytics_enable'] ) &&
				$the_options['ganalytics_enable'] == 1 &&
				!$the_options['ganalytics_measurement_id'] )
			{
				$with_missing_fields = true;
			}

			if( isset( $the_options['facebook_enable'] ) &&
				$the_options['facebook_enable'] == 1 &&
				( !$the_options['facebook_pixel_id'] || !$the_options['facebook_access_token'] ) )
			{
				$with_missing_fields = true;
			}

			if( isset( $the_options['tiktok_enable'] ) &&
				$the_options['tiktok_enable'] == 1 &&
				( !$the_options['tiktok_pixel_id'] || !$the_options['tiktok_access_token'] ) )
			{
				$with_missing_fields = true;
			}
			//eof validation part

			$lc_hide_local = ( isset( $rconfig ) && $rconfig['lc_hide_local'] && $rconfig['lc_hide_local'] == 1 ) ? 1 : 0;
			$lc_owner_description = ( isset( $rconfig ) && $rconfig['lc_owner_description'] ) ? $rconfig['lc_owner_description'] : null;
			$lc_owner_email = ( isset( $rconfig ) && $rconfig['lc_owner_email'] ) ? $rconfig['lc_owner_email'] : null;
			$lc_owner_website = ( isset( $rconfig ) && $rconfig['lc_owner_website'] ) ? $rconfig['lc_owner_website'] : null;

	        $answer = array(
	        	'success'					=>	true,
	        	'license_user_status'		=>	$license_user_status,
	        	'license_valid'				=>	$license_valid,
	        	'grace_period'				=>	$grace_period,
	        	'summary_text'				=>	$summary_text,
	        	'lc_hide_local'				=>	$lc_hide_local,
	        	'lc_owner_description'		=>	$lc_owner_description,
	        	'lc_owner_email'			=>	$lc_owner_email,
	        	'lc_owner_website'			=>	$lc_owner_website,
	        	'with_missing_fields'		=>	$with_missing_fields,
	        	'internal_error_message'	=>	( $action_result && isset( $action_result['internal_error_message'] ) ) ? $action_result['internal_error_message'] : null,
	        );

	        wp_send_json( $answer );

	        if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $answer );
	    }
	}

	/**
	 * f for checking for myagileprivacy
	 */
	public function check_for_map_wp()
	{
		if( !function_exists( 'is_plugin_active' ) )
		{
			include_once(ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if( is_plugin_active( 'myagileprivacy/my-agile-privacy.php' ) )
		{

		}
		else
		{
	    	//display the banner

	    	echo '<script type="text/javascript">'.PHP_EOL;
	    	echo 'jQuery( "#map_banner" ).removeClass( "d-none" );';
	    	echo '</script>'.PHP_EOL;
		}
	}

	/**
	* Function for calling remote sync via cronjob
	 * @since    1.0.12
	 * @access   public
	*/
	public function do_cron_sync()
	{
		if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'do_cron_sync start' );

		// Get options
		$the_options = MyAgilePixel::get_settings();

		update_option( MAPX_PLUGIN_DO_SYNC_NOW, 1 );
		update_option( MAPX_PLUGIN_DO_SYNC_LAST_EXECUTION, 1 );

		$sync_result = $this->triggered_do_cron_sync();

		if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'do_cron_sync end' );

		return true;
	}

	/**
	* Function for executing remote sync
	 * @since    1.0.12
	 * @access   public
	*/
	public function triggered_do_cron_sync()
	{
		if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'start triggered_do_cron_sync' );

		$the_options = MyAgilePixel::get_settings();
		$rconfig = MyAgilePixel::get_rconfig();

		$now = time();

		$sync_last_execution = MyAgilePixel::nullCoalesce( get_option( MAPX_PLUGIN_DO_SYNC_LAST_EXECUTION ), null );

		//bypass blocked cron websites
		if( $sync_last_execution )
		{
			//23 hours
			if( $now - $sync_last_execution > 82800 )
			{
				if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'detected stale sync_last_execution' );

				update_option( MAPX_PLUGIN_DO_SYNC_NOW, 1 );
			}
		}
		else
		{
			update_option( MAPX_PLUGIN_DO_SYNC_NOW, 1 );
		}

		$do_sync_now = MyAgilePixel::nullCoalesce( get_option( MAPX_PLUGIN_DO_SYNC_NOW ), 0 );

		//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $sync_last_execution );
		//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $do_sync_now );

		$rr = false;

		if( $do_sync_now )
		{
			if( isset( $the_options['pa'] ) && $the_options['pa'] == 1 )
			{
				if( $the_options['ganalytics_measurement_id'] )
				{
					MyAgilePixel::download_remote_file( 'https://www.googletagmanager.com/gtag/js?id='.$the_options['ganalytics_measurement_id'], 'ga_offload_script.js' );
				}

				if( !( isset( $rconfig['forbid_local_js_caching'] ) && $rconfig['forbid_local_js_caching'] == 1 ) )
				{
					MyAgilePixel::download_remote_file( 'https://cdn.myagileprivacy.com/cookie-shield.js', 'cookie-shield.js' );

					$cdn_basepath = 'https://cdn.myagilepixel.com/';
					$manifest_file = 'version_manifest.json';

					if( MAPX_DEV_MODE )
					{
						$manifest_filename = plugin_dir_path( MAPX_PLUGIN_FILENAME ) .'dev/'.$manifest_file;
						$manifest_content = file_get_contents( $manifest_filename );
						$manifest = json_decode( $manifest_content, true );
					}
					else
					{
						MyAgilePixel::download_remote_file( $cdn_basepath.$manifest_file, $manifest_file );

						$manifest_filename = MyAgilePixel::get_base_directory_for_cache().$manifest_file;
						$manifest_content = file_get_contents( $manifest_filename );
						$manifest = json_decode( $manifest_content, true );
					}

					//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $manifest );


					if( $manifest && isset( $manifest['manifest_version_file'] ) )
					{
						$manifest_assoc = array();

						$manifest_assoc['manifest_version_file'] = $manifest['manifest_version_file'];
						$manifest_assoc['files'] = array();

						foreach( $manifest['files'] as $remote_file => $remote_details )
						{
							$version = $remote_details['version'];
							$remote_url = $cdn_basepath . $remote_file;
							$path_info = pathinfo( $remote_file );
							$local_file = basename( $remote_file );
							$local_file_with_version = $path_info['filename'] . '-' . $version . '.' . $path_info['extension'];

							$this_item = array(
								'filename'			=>	$local_file_with_version,
								'version'			=> 	$version,
								'remote_details'	=>	$remote_details,
							);

							$manifest_assoc['files'][ $local_file ] = $this_item;

							$do_get_file = true;

							if( $do_get_file )
							{
								MyAgilePixel::download_remote_file( $remote_url, $local_file, $version, $local_file_with_version );
							}
						}

						update_option( MAPX_MANIFEST_ASSOC, $manifest_assoc );
					}
					else
					{
						update_option( MAPX_MANIFEST_ASSOC, null );
					}
				}
			}


			if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'START triggered_do_cron_sync' );

			// Get options
			$the_options = MyAgilePixel::get_settings();

			$urlparts = parse_url( home_url() );
			$domain = $urlparts['host'];

			$opt_to_send = array();
			foreach( $the_options as $k => $v )
			{
				if( in_array( $k, MAPX_REMOTE_OPT ) )
				{
					$opt_to_send[ $k ] = $v;
				}
			}

			$data_to_send = array(
				'action'			=>	'validation',
				'software_key'		=>	MAPX_SOFTWARE_KEY,
				'hash'				=>	$the_options['license_code'],
				'domain'			=>	$domain,
				'version'			=>	MAPX_PLUGIN_VERSION,
				'opt_to_send'		=>	json_encode( $opt_to_send ),
				'options_summary'	=>	$this->get_options_summary(),
				'server_data'		=>	MyAgilePixel::getServerFootPrint(),
			);

			//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $data_to_send );

			$action_result = MyAgilePixel::call_api( $data_to_send );

			//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $action_result );

			$rr = false;
			$pa = 0;

			if( !$action_result ||
				( $action_result && isset( $action_result['internal_error_message'] ) )
			)
			{
				$rr = false;
			}
			else
			{

				if( $action_result['success'] )
				{
					$rr = true;

					$license_valid = true;
					$grace_period = false;

					if( $action_result['paid_license'] == 0 )
					{
						$license_user_status = 'Demo license';

						if( isset( $action_result['error_msg'] ) )
						{
							$license_valid = false;
							$license_user_status = $action_result['error_msg'];
						}
					}
					else
					{
						if( isset( $action_result['grace_period'] ) && $action_result['grace_period'] == 1 )
						{
							$license_user_status = 'Grace period - expiring soon';
							$grace_period = true;
						}
						elseif( isset( $action_result['error_msg'] ) )
						{
							$license_user_status = $action_result['error_msg'];
						}
						else
						{
							$license_user_status = 'License valid';
						}

						$pa = 1;
					}
				}
				else
				{
					$rr = true;
					$license_valid = false;
					$grace_period = false;
					$license_user_status = $action_result['error_msg'];
				}
			}

			if( $rr )
			{
				$summary_text = $action_result['summary_text'];

				$the_options['license_user_status'] = $license_user_status;
				$the_options['license_valid'] = $license_valid;
				$the_options['grace_period'] = $grace_period;
				$the_options['summary_text'] = $summary_text;
				$the_options['wl'] = ( isset( $action_result['wl'] ) ) ? $action_result['wl'] : 0;
				$the_options['pa'] = $pa;
				$rconfig = ( isset( $action_result['rconfig'] ) ) ? $action_result['rconfig'] : null;
				update_option( MAPX_PLUGIN_RCONFIG, $rconfig );
				update_option( MAPX_PLUGIN_SETTINGS_FIELD, $the_options );
			}
			else
			{
				$license_user_status = $the_options['license_user_status'];
				$license_valid = $the_options['license_valid'];
				$grace_period = $the_options['grace_period'];
				$summary_text = $the_options['summary_text'];
				$rconfig = MyAgilePixel::get_rconfig();
			}

			//parsing autoconsume_options

			if( isset( $rconfig ) && isset( $rconfig['autoconsume_options'] ) )
			{
				if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $rconfig['autoconsume_options'] );

				$autoconsume_options = json_decode( $rconfig['autoconsume_options'], true );

				$rconfig['autoconsume_options'] = null;
				update_option( MAPX_PLUGIN_RCONFIG, $rconfig );

				if( isset( $autoconsume_options['forced'] ) )
				{
					if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $the_options );

					if( isset( $autoconsume_options['forced']['forced_auto_update'] ) )
					{
						$the_options['forced_auto_update'] = ( $autoconsume_options['forced']['forced_auto_update'] == 1 ) ? true : false;
						update_option( MAPX_PLUGIN_SETTINGS_FIELD, $the_options );
					}

					if( isset( $autoconsume_options['forced']['internal_debug'] ) )
					{
						$the_options['internal_debug'] = ( $autoconsume_options['forced']['internal_debug'] == 1 ) ? true : false;
						update_option( MAPX_PLUGIN_SETTINGS_FIELD, $the_options );
					}
				}
			}

			update_option( MAPX_PLUGIN_DO_SYNC_NOW, 0 );

			$now = time();
			update_option( MAPX_PLUGIN_DO_SYNC_LAST_EXECUTION, $now );

			if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'END triggered_do_cron_sync' );
		}

		if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'end triggered_do_cron_sync' );
	}

	/*
	 * f. for clearing log file
	*/
	public function admin_clear_logfile()
	{
		//clean logfile if it's stale and debugger is off
		if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER == false )
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

			$expiration_time_in_seconds = 60*60*24;
			$max_age = time() - $expiration_time_in_seconds;

			if( is_file( $filePath ) && filemtime( $filePath ) > $max_age )
			{
				wp_delete_file( $filePath );
			}
		}
	}
}