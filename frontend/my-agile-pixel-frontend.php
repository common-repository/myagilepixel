<?php

/**
 * The frontend-specific functionality of the plugin.
 *
*/
class MyAgilePixelFrontend {
	// Plugin Name
	private $plugin_name;

	// Plugin Version
	private $version;
	public $plugin_obj;

	private $to_append_html;

	// Constructor
	public function __construct( $plugin_name, $version, $plugin_obj )
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_obj = $plugin_obj;
	}

	//global definitions for inter plugin communication
	public function add_global_defs()
	{
		$the_options = MyAgilePixel::get_settings();

		if( isset( $the_options['general_plugin_active'] ) &&
			$the_options['general_plugin_active'] )
		{
			if( $the_options['pa'] == 1 &&
				$the_options['ganalytics_enable'] )
			{
				define( 'MAPX_my_agile_pixel_ga_on', true );
			}

			if( $the_options['pa'] == 1 &&
				$the_options['facebook_enable'] )
			{
				define( 'MAPX_my_agile_pixel_fbq_on', true );
			}

			if( $the_options['tiktok_enable'] )
			{
				define( 'MAPX_my_agile_pixel_tiktok_on', true );
			}
		}
	}


	/**
	 * Register the js for the frontend area.
	 * @access   public
	 */
	public function enqueue_scripts()
	{
		$this->add_global_defs();

		$the_options = MyAgilePixel::get_settings();
		$rconfig = MyAgilePixel::get_rconfig();
	}

	/**
	 * adds metadata to inline script tags
	 * @access   public
	 */
	public function add_inline_script_attributes( $attr, $javascript )
	{
	    if( $attr &&
	    	isset( $attr['id'] ) &&
	    	strpos( $attr['id'], 'mpx_inline_' ) === 0 )
	    {
	    	$new_attributes = array(
	    		'data-cfasync' 			=> 	'false',
	    		'class' 				=> 	'my_agile_privacy_do_not_touch',
	    		'data-no-optimize' 		=> 	'1',
	    		'data-no-defer' 		=> 	'1',
	    		'consent-skip-blocker'	=>	'1',
	    	);
	    	$attr = array_merge( $new_attributes, $attr );
	    }

	    return $attr;
	}

	/**
	 * Register the css for the frontend area.
	 * @access   public
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) ."css/mapx.css", array(), $this->version, 'all' );
	}

	/**
	 * Prints inline execution script
	 * @access   public
	 */
	public function add_inline_script()
	{
		$the_options = MyAgilePixel::get_settings();
		$rconfig = MyAgilePixel::get_rconfig();

		$manifest_assoc = null;

		if(	!MAPX_DEV_MODE &&
			$rconfig &&
			isset( $rconfig['allow_manifest'] ) &&
			$rconfig['allow_manifest']
		)
		{
			$manifest_assoc = get_option( MAPX_MANIFEST_ASSOC, null );
		}

		$compatibility_mode = false;

		if( isset( $the_options['compatibility_mode'] ) &&
			$the_options['compatibility_mode']
		)
		{
			$compatibility_mode = $the_options['compatibility_mode'];
		}

		$final_output_script = "";
		$consent_mode_requested_and_enabled = false;
		$using_myagileprivacy = false;
		$using_iubenda_or_cookiebot_or_other = false;
		$using_no_consent_manager_and_just_start = false;
		$use_ga_advanced_features = $the_options['use_ga_advanced_features'];

		if( $the_options['general_plugin_active'] )
		{
			echo '<script data-cfasync="false" class="" type="text/javascript" src="'.plugin_dir_url(__FILE__) . 'js/myagilepixel.js'.'" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1"></script>' . PHP_EOL;

			if ( $the_options['general_interface_with'] == 'myagileprivacy' )
			{
				$using_myagileprivacy = true;
				$use_ga_advanced_features = true;
			}

			if ( $the_options['general_interface_with'] == 'cookiebot' ||
				$the_options['general_interface_with'] == 'iubenda' ||
				$the_options['general_interface_with'] == 'gdpr_cookie_consent' ||
				$the_options['general_interface_with'] == 'complianz' ||
				$the_options['general_interface_with'] == 'none_and_do_no_not_ask_consent' )
			{
				$using_iubenda_or_cookiebot_or_other = true;
				$use_ga_advanced_features = true;

				$final_output_script = '<script data-cfasync="false" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">' . PHP_EOL;

				$final_output_script .= 'function startMyAgilePixel(){' . PHP_EOL;
			}

			if ( $the_options['general_interface_with'] == 'none_and_do_no_not_ask_consent' )
			{
				$using_no_consent_manager_and_just_start = true;
			}

			if( $the_options['pa'] == 1 )
			{
				if( MAPX_DEV_MODE )
				{
					echo '<script data-cfasync="false" class="" type="text/javascript" src="'.plugin_dir_url(__FILE__) . '../dev/cdn.myagilepixel.js'.'" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1"></script>' . PHP_EOL;
				}
				else
				{
					$local_file_exists = false;

					$base_ref = "";

					if( !( isset( $rconfig['forbid_local_js_caching'] ) && $rconfig['forbid_local_js_caching'] == 1 ) )
					{
						$local_file_exists = MyAgilePixel::cached_file_exists( 'myagilepixel.js' );

						if( $local_file_exists )
						{
							$base_ref = MyAgilePixel::get_base_url_for_cache();
						}
					}

					if( !$local_file_exists )
					{
						$base_ref = "https://cdn.myagilepixel.com/";

						update_option( MAPX_PLUGIN_DO_SYNC_NOW, 1 );
					}

					$script_filename = 'myagilepixel.js';

					if( $manifest_assoc &&
						isset( $manifest_assoc['files'][ $script_filename ] ) &&
						$manifest_assoc['files'][ $script_filename ]
					)
					{
						$script_filename = $manifest_assoc['files'][ $script_filename ]['filename'];
					}

					$the_script = $base_ref.$script_filename;

					if( !$local_file_exists && !(
						isset( $rconfig ) &&
						isset( $rconfig['prevent_preconnect_prefetch'] ) &&
						$rconfig['prevent_preconnect_prefetch'] == 1 ) )
					{
						echo '<link rel="preconnect" href="'.$base_ref.'" crossorigin />'.PHP_EOL.'<link rel="dns-prefetch" href="'.$base_ref.'" />'.PHP_EOL;
					}

					echo '<script data-cfasync="false" class="" type="text/javascript" src="'.$the_script.'" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1"></script>'.PHP_EOL;
				}

				$analytics_options = ( $the_options['ganalytics_enable'] ) ? MyAgilePixel::get_json_settings_analytics() : array();
				$fbcapi_options = ( $the_options['facebook_enable'] ) ? MyAgilePixel::get_json_settings_fbqapi() : array();
				$tiktokapi_options = ( $the_options['tiktok_enable'] ) ? MyAgilePixel::get_json_settings_tiktokapi() : array();

				if( isset( $analytics_options['consent_mode_requested_and_enabled'] ) )
				{
					$analytics_options['consent_mode_requested_and_enabled'] = $consent_mode_requested_and_enabled;
				}

				if( $use_ga_advanced_features )
				{
					$analytics_options['use_ga_advanced_features'] = $use_ga_advanced_features;
				}

				$other_options = null;

				if( isset( $rconfig['output_options'] ) )
				{
					$other_options = json_decode( $rconfig['output_options'] );
				}

				echo '<script data-cfasync="false" class="" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;

				echo 'var mpx_settings='.json_encode( array(
					'compatibility_mode'		=>	( $the_options['compatibility_mode'] ) ? true : false,
					'analytics' 				=> 	$analytics_options,
					'fbcapi'					=>	$fbcapi_options,
					'tiktokapi'   				=> 	$tiktokapi_options,
					'ajax_url'					=>	admin_url( 'admin-ajax.php' ),
					'sec_token'					=>	wp_create_nonce( 'mpx_callback' ),
					'analytics_enabled'			=>	( $the_options['ganalytics_enable'] ) ? true : false,
					'fbcapi_enabled'			=>	( $the_options['facebook_enable'] ) ? true : false,
					'tiktokapi_enabled'			=>	( $the_options['tiktok_enable'] ) ? true : false,
					'other_options'				=>	$other_options,
					'general_interface_with'	=>	$the_options['general_interface_with'],
				)).';'.PHP_EOL;

				echo '</script>' . PHP_EOL;

				if( $the_options['ganalytics_enable'] && ( $using_myagileprivacy || $use_ga_advanced_features ) )
				{
					echo '<script data-cfasync="false" class="" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;
					echo 'window.MyAgilePixelProxyBeacon( mpx_settings );'.PHP_EOL;;
					echo '</script>' . PHP_EOL;
				}

				if( $the_options['ganalytics_enable'] )
				{
					if( $using_myagileprivacy )
					{
						if( $consent_mode_requested_and_enabled )
						{
							$final_output_script .= '<script data-cfasync="false" class="my_agile_privacy_activate autoscan_mode map_inline_script_blocked map_blocked_content" type="text/plain" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1" data-cookie-api-key="my_agile_pixel_ga">'.PHP_EOL;

							$final_output_script .=  'window.MyAgilePixelAnalyticsRecheckConsent( mpx_settings );'.PHP_EOL;

							$final_output_script .=  '</script>'.PHP_EOL;

							$final_output_script .= '<script data-cfasync="false"class="my_agile_privacy_do_not_touch" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;
						}
						else
						{
							$final_output_script .= '<script data-cfasync="false" class="my_agile_privacy_activate autoscan_mode map_inline_script_blocked map_blocked_content" type="text/plain" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1" data-cookie-api-key="my_agile_pixel_ga">'.PHP_EOL;
						}
					}

					if( $using_myagileprivacy == false && $using_iubenda_or_cookiebot_or_other == false )
					{
						$final_output_script .= '<script data-cfasync="false" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;

					}

					if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) $final_output_script .=  'console.log( "enabling my_agile_pixel_ga" );'.PHP_EOL;

					$final_output_script .=  'window.MyAgilePixelAnalytics( mpx_settings );'.PHP_EOL;

					if( $using_myagileprivacy || $using_iubenda_or_cookiebot_or_other == false )
					{
						$final_output_script .=  '</script>'.PHP_EOL;
					}
				}

				if( $the_options['facebook_enable'] )
				{
					if( $using_myagileprivacy )
					{
						$final_output_script .=  '<script data-cfasync="false" class="my_agile_privacy_activate autoscan_mode map_inline_script_blocked map_blocked_content" type="text/plain" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1" data-cookie-api-key="my_agile_pixel_fbq">'.PHP_EOL;
					}

					if( $using_myagileprivacy == false && $using_iubenda_or_cookiebot_or_other == false )
					{
						$final_output_script .=  '<script data-cfasync="false" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;
					}

					if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) $final_output_script .=  'console.log( "enabling my_agile_pixel_fbq" );'. PHP_EOL;

					$final_output_script .=  'window.MyAgilePixelFbCAPI( mpx_settings );'.PHP_EOL;

					if( $using_myagileprivacy || $using_iubenda_or_cookiebot_or_other == false )
					{
						$final_output_script .=  '</script>'.PHP_EOL;
					}
				}
			}

			if( $the_options['tiktok_enable'] )
			{
				if( $using_myagileprivacy )
				{
					$final_output_script .=  '<script data-cfasync="false" class="my_agile_privacy_activate autoscan_mode map_inline_script_blocked map_blocked_content" type="text/plain" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1" data-cookie-api-key="my_agile_pixel_tiktok">'.PHP_EOL;
				}

				if( $using_myagileprivacy == false && $using_iubenda_or_cookiebot_or_other == false )
				{
					$final_output_script .=  '<script data-cfasync="false" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;
				}

				if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) $final_output_script .=  'console.log( "enabling my_agile_pixel_tiktoks" );'.PHP_EOL;

				$final_output_script .=  'window.MyAgilePixelTikTokCapi( mpx_settings );'.PHP_EOL;

				if( $using_myagileprivacy || $using_iubenda_or_cookiebot_or_other == false )
				{
					$final_output_script .=  '</script>'.PHP_EOL;
				}
			}

			if( $using_iubenda_or_cookiebot_or_other )
			{
				$final_output_script .= '};'.PHP_EOL;
				$final_output_script .= '</script>'.PHP_EOL;
			}

			echo $final_output_script;

			if( $using_no_consent_manager_and_just_start )
			{
				echo "<script data-cfasync='false' type='text/javascript' data-no-optimize='1' data-no-defer='1' consent-skip-blocker='1'>
						startMyAgilePixel();
					</script>".PHP_EOL;
			}

			if ( $the_options['general_interface_with'] == 'cookiebot' )
			{
				echo "<script data-cfasync='false' type='text/javascript' data-no-optimize='1' data-no-defer='1' consent-skip-blocker='1'>

						window.addEventListener( 'CookiebotOnAccept', function (e) {
							if (Cookiebot.consent.statistics)
							{
								if( mpx_settings.analytics_enabled  )
								{
									window.MyAgilePixelAnalytics( mpx_settings );
								}
							}

							if (Cookiebot.consent.marketing)
							{
								if( mpx_settings.fbcapi_enabled )
								{
									window.MyAgilePixelFbCAPI( mpx_settings );
								}

								if( mpx_settings.tiktokapi_enabled )
								{
									window.MyAgilePixelTikTokCapi( mpx_settings );
								}
							}
						}, false);

					</script>".PHP_EOL;
			}

			$is_woocommerce_activated = MyAgilePixel::is_woocommerce_activated();

			if( $is_woocommerce_activated && $the_options['woocommerce_enable'] )
			{
				global $product;
				$the_product = null;

				if( is_single() && $product )
				{
					if( is_object( $product ) )
					{
						$the_product = $product;
					}
					else
					{
						// Get the product ID
						$product_id = get_the_ID();

						// Get the product object
						$the_product = wc_get_product( $product_id );
					}
				}

				if( $the_product )
				{
					$code = "my_agile_on('click', '.single_add_to_cart_button', function () {
								try{
									if( mpx_settings.analytics_enabled )
									{
										MAPX_Call_TrackFunc.doInvokeTrackingFunction( 'gtag', 'event', 'add_to_cart', {
											'content_type': 'product',
											'items': [ {
												'item_id': '" . esc_js( $the_product->get_sku() ? $the_product->get_sku() : ( '#' . $the_product->get_id() ) ) . "',
												'item_name': '" . esc_js( $the_product->get_title() ) . "',
												'item_category': " . $this::product_get_category_line( $the_product ) . "
											} ],
										});
									}
								}
								catch (error)
								{
									console.error(error);
								}

								try{
									if( mpx_settings.fbcapi_enabled )
									{
										MAPX_Call_TrackFunc.doInvokeTrackingFunction( 'fbq', 'track', 'AddToCart' );
									}
								}
								catch (error)
								{
									console.error(error);
								}

								return;
							});".PHP_EOL;

					echo '<script data-cfasync="false" class="my_agile_privacy_do_not_touch" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;
					echo $code;
					echo '</script>'.PHP_EOL;
				}

				//bof cart code add

				if( function_exists( 'WC' ) &&
					function_exists( 'is_cart' ) &&
					is_cart() )
				{
					// Retrieve the cart data
					$cart_items = array();

					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item )
					{
						$product = $cart_item['data'];
						$product_sku = $product->get_sku();
						$product_id = $cart_item['product_id'];
						$product_name = $product->get_name();
						$quantity = $cart_item['quantity'];
						$currency = get_woocommerce_currency();
						$category = '';
						$price = $product->get_price(); // Retrieve the item price

						// Get product categories
						$terms = get_the_terms( $product_id, 'product_cat');
						if( $terms && !is_wp_error( $terms ) )
						{
							$category = $terms[0]->name; // Assuming only one category per product
						}

						$cart_items[$product_id] = array(
							'product_id'   	=> 	$product_id,
							'product_sku'	=>	( $product_sku ) ? $product_sku : ( '#' .$product_id ),
							'product_name' 	=> 	$product_name,
							'quantity'     	=> 	$quantity,
							'currency'     	=> 	$currency,
							'category'     	=> 	$category,
							'price'     	=> 	$price,
						);
					}


					$cart_code = " var full_cart = ".json_encode( $cart_items ).";".PHP_EOL."
								my_agile_on('click', '.remove', function () {
									try{
										if( mpx_settings.analytics_enabled )
										{
											var product_id = this.dataset.product_id;

											if( !!product_id && !!full_cart && !!full_cart[product_id] )
											{
												MAPX_Call_TrackFunc.doInvokeTrackingFunction( 'gtag', 'event', 'remove_from_cart', {
															currency: full_cart[product_id]?.currency,
															value: full_cart[product_id]?.price,
															items: [
																{
																	item_id: full_cart[product_id]?.product_sku,
																	item_name: full_cart[product_id]?.product_name,
																	index: 0,
																	item_category: full_cart[product_id]?.category,
																	price: full_cart[product_id]?.price,
																	quantity: full_cart[product_id]?.quantity
																}
															]
												});
											}
										}
									}
									catch (error)
									{
										console.error(error);
									}

									return;
								});".PHP_EOL;

					echo '<script data-cfasync="false" class="my_agile_privacy_do_not_touch" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;
					echo $cart_code;
					echo '</script>'.PHP_EOL;
				}

				//eof cart code add

				global $wp;

				$order_id = isset( $wp->query_vars['order-received'] ) ? $wp->query_vars['order-received'] : 0;
				$order    = wc_get_order( $order_id );
				//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $order_id );
				//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $order );

				// Check if is order received page and stop when the products and not tracked
				if ( is_order_received_page() ||
					( $order_id && $order )
				)
				{
					//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'is_order_received_page --> true' );

					if ( $order && ! (bool) $order->get_meta( '_map_tracked' ) )
					{
						//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'doing call to add_transaction' );

						$this->to_append_html .= $this->add_transaction( $order );
					}
				}
			}
		}
	}

	public function finalize_inline_script()
	{
		if( $this->to_append_html )
		{
			echo $this->to_append_html;
		}

		$user_property_code = $this->get_user_property_code();

		if( $user_property_code )
		{
			echo $user_property_code;
		}
	}

	/**
	 * head inject
	 * @access   public
	*/
	public function wp_head_inject()
	{
		$skip = MyAgilePixel::check_buffer_skip_conditions();

		if( $skip == 'false' )
		{
			echo $this->get_head_script_string( false );
		}
	}

	/**
	 * get head script string
	 * @since    1.3.5
	 * @access   public
	*/
	public function get_head_script_string( $block_mode = false )
	{
		$the_options = MyAgilePixel::get_settings();
		$rconfig = MyAgilePixel::get_rconfig();

		$blocks = array(
			'inline' 	=> array(),
			'enqueue'	=> array(),
		);

		$head_script = '';
		$start_config_script = '';

		if( $the_options['block_script_using_cookieshield'] == 1 )
		{
			$cookie_reset_timestamp = 'null';
			$enforce_youtube_privacy = 0;

			$cookie_api_key_remote_id_map_active = array();
			$cookie_api_key_remote_id_map_detectable = array();
			$cookie_api_key_remote_id_map_blocked_without_notification = array();
			$cookie_api_key_friendly_name_map = array();
			$cookie_api_key_not_to_block = array();

			if( $the_options['ganalytics_enable'] )
			{
				$cookie_api_key_remote_id_map_blocked_without_notification['google_analytics'] = 'map_cookie_'.'google_analytics';
			}

			if( $the_options['facebook_enable'] )
			{
				$cookie_api_key_remote_id_map_blocked_without_notification['facebook_remarketing'] = 'map_cookie_'.'facebook_remarketing';
			}

			if( $the_options['tiktok_enable'] )
			{
				$cookie_api_key_remote_id_map_blocked_without_notification['tik_tok'] = 'map_cookie_'.'tik_tok';
			}

			$base_config_script = '';

			$map_full_config = array(
				'config_origin'												=>	'myagilepixel',
				'cookie_reset_timestamp' 									=> 	$cookie_reset_timestamp,
				'cookie_api_key_remote_id_map_active' 						=> 	$cookie_api_key_remote_id_map_active,
				'cookie_api_key_remote_id_map_detectable' 					=> 	$cookie_api_key_remote_id_map_detectable,
				'cookie_api_key_remote_id_map_blocked_without_notification' => 	$cookie_api_key_remote_id_map_blocked_without_notification,
				'cookie_api_key_friendly_name_map' 							=> 	$cookie_api_key_friendly_name_map,
				'cookie_api_key_not_to_block' 								=> 	$cookie_api_key_not_to_block,
				'enforce_youtube_privacy' 									=> 	$enforce_youtube_privacy,
			);

			$base_config_script .= 'var map_full_config='.json_encode( $map_full_config ).';'.PHP_EOL;

			$start_config_script = '<script data-cfasync="false" class="map_advanced_shield" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL.$base_config_script.PHP_EOL.'</script>';

			if( MAPX_DEV_MODE )
			{
				$the_script = plugin_dir_url(__FILE__).'../../myagileprivacy/dev/dev.cookie-shield.js';
			}
			else
			{
				$local_file_exists = false;

				if( !( isset( $rconfig['forbid_local_js_caching'] ) && $rconfig['forbid_local_js_caching'] == 1 ) )
				{
					$local_file_exists = MyAgilePixel::cached_file_exists( 'cookie-shield.js' );

					if( $local_file_exists )
					{
						$map_base_ref = MyAgilePixel::get_base_url_for_cache();
					}
				}

				if( !$local_file_exists )
				{
					$map_base_ref = "https://cdn.myagileprivacy.com/";

					if( isset( $rconfig ) &&
						isset( $rconfig['alt_map_cdn'] ) )
					{
						$map_base_ref = $rconfig['alt_map_cdn'];
					}
				}

				$the_script = $map_base_ref.'cookie-shield.js';
			}

			if( !$local_file_exists && isset( $map_base_ref ) && !(
				isset( $rconfig ) &&
				isset( $rconfig['prevent_preconnect_prefetch'] ) &&
				$rconfig['prevent_preconnect_prefetch'] == 1 ) )
			{
				$head_script .= '<link rel="preconnect" href="'.$map_base_ref.'" crossorigin />'.PHP_EOL.'<link rel="dns-prefetch" href="'.$map_base_ref.'" />'.PHP_EOL;
			}

			$head_script .= '<script data-cfasync="false" class="map_advanced_shield map_lite_shield" type="text/javascript" src="'.$the_script.'" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1"></script>' . PHP_EOL;
			$blocks['enqueue'][] = $the_script;

			if( $the_options['ganalytics_enable'] )
			{
				$head_script .= '<script data-cfasync="false" class="" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;
				$head_script .=  'window.MyAgilePixelProxyBeacon( null );'.PHP_EOL;;
				$head_script .=  '</script>' . PHP_EOL;
			}
		}

		if( $block_mode )
		{
			return $blocks;
		}
		else
		{
			return $start_config_script.$head_script;
		}
	}


	/**
	 * Cross-platform function that gets the IP
	 * address of the server.
	 * @access   public
	 */
	public function getServerIp(){

		$ip = null;

		if( isset( $_SERVER['SERVER_ADDR'] ) ){
			$ip = sanitize_text_field( $_SERVER['SERVER_ADDR'] );
		}
		elseif( isset( $_SERVER['LOCAL_ADDR'] ) ){
			$ip = sanitize_text_field( $_SERVER['LOCAL_ADDR'] );
		}
		else{
			$host = gethostname();
			$ip = gethostbyname( $host );
		}

		return $ip;
	}


	/**
	 * Send data callback
	 *
	 * @access   public
	 */
	public function mpx_send_data_callback()
	{
		if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $_POST );

		//check security param
		//check_ajax_referer( 'mpx_callback', 'sec_token' );

		$referral_url = wp_get_referer();

		$valid_referral_url = wp_validate_redirect( $referral_url );

		if( $valid_referral_url )
		{
			$the_options = MyAgilePixel::get_settings();

			$human_event_name = null;
			$success = false;
			$additional_data = null;
			$output_message = null;
			$detected_error = false;
			$error_description = null;

			// check form submit
			if( isset( $_POST['action'] ) && $_POST['action'] == 'mpx_send_data' )
			{
				$user_property_sent = array();

				$data_decoded = json_decode( stripslashes( $_POST['data'] ), true );
				if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $data_decoded );

				$current_client_ip = MyAgilePixel::get_the_user_ip();
				$blacklisted_ip_list = null;
				$blacklisted_events_list = null;

				if( $the_options['blacklisted_ip'] )
				{
					$blacklisted_ip_list = json_decode( $the_options['blacklisted_ip'], true );
				}

				if( $the_options['blacklisted_events'] )
				{
					$blacklisted_events_list = json_decode( $the_options['blacklisted_events'], true );
				}

				if( !( $_POST['realm'] == 'ga' ||
						$_POST['realm'] == 'fbcapi' ||
						$_POST['realm'] == 'tiktokapi' ||
						$_POST['realm'] == 'beacon_retransmit' )
				)
				{
					$detected_error = true;
					$error_description = 'missing critical data on POST request: invalid or missing realm';
					if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $error_description );
					if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $_POST );

				}

				if( !$detected_error )
				{
					if( $_POST['realm'] == 'beacon_retransmit' )
					{
						$url_to_post_to = $data_decoded['_original_url'];
						$method = $data_decoded['_original_method'];

						$skip_params_to_send = array(
							'_original_url',
							'_original_method',
						);

						$params_to_send = array_diff_key( $data_decoded, array_flip( $skip_params_to_send ) );

						if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $params_to_send );

						$tracking_data_url_params = http_build_query( $params_to_send );

						if( $current_client_ip &&
							$blacklisted_ip_list &&
							in_array( $current_client_ip, $blacklisted_ip_list ) )
						{
							//skip
							$success = true;

							if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'GADS data sending skipped due to ip blacklist' );

							$output_message = 'Google ADS data sending skipped due to ip blacklist.';
						}
						else
						{
							//send
							if( $method == 'get') $result = wp_remote_get( $url_to_post_to, $tracking_data_url_params );

							if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $result );

							$success = true;

							if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'GADS data sent' );

							if( !$output_data_already_sent ) $output_message = 'Google ADS data successfuly sent.';
						}

					}


					if( $_POST['realm'] == 'ga' )
					{
						if( !$the_options['ganalytics_measurement_id'] )
						{
							$detected_error = true;
							$error_description = 'missing ganalytics_measurement_id for realm ga';
							if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $error_description );
							if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $_POST );
						}

						if( !$detected_error )
						{
							$human_event_name = $data_decoded['en'];

							//data building
							$skip_params_to_send = array(
								'__UA',		//user agent
								'__WUCG',	//user consent given
							);

							//User Agent
							$__UA = isset( $data_decoded['__UA'] ) ? $data_decoded['__UA'] : null;

							//Touch device
							$__IS_TOUCH = isset( $data_decoded['__IS_TOUCH'] ) ? $data_decoded['__IS_TOUCH'] : null;

							//with user consent given
							$__WUCG = isset( $data_decoded['__WUCG'] ) ? $data_decoded['__WUCG'] : null;

							//Document Location
							$dl = isset( $data_decoded['dl'] ) ? $data_decoded['dl'] : null;

							//Document Referrer
							$dr = isset( $data_decoded['dr'] ) ? $data_decoded['dr'] : null;

							//User Language
							$ul = isset( $data_decoded['ul'] ) ? $data_decoded['ul'] : null;

							//bof user agent manipulation part

							if( $__UA == null &&
								$the_options['ganalytics_remove_user_agent'] &&
								$the_options['ganalytics_send_desktop_mobile_user_agent'] )
							{
								if( $__IS_TOUCH )
								{
									$__UA = MAPX_MOBILE_UA;
								}
								else
								{
									$__UA = MAPX_DESKTOP_UA;
								}
							}

							//eof user agent manipulation part

							$params_to_send = array_diff_key( $data_decoded, array_flip( $skip_params_to_send ) );

							if( $the_options['ganalytics_anonymize_ip'] == 0 )
							{
								$params_to_send['_uip'] = $current_client_ip;
							}

							//bof calc user property sent description

							$substring = 'up.';
							$alt_substring = 'upn.';

							foreach( $params_to_send as $single_param_key => $single_param_value )
							{
								if( strpos( $single_param_key, $substring ) === 0 )
								{
									$this_string =  substr( $single_param_key, strlen( $substring) ).'='.$single_param_value;

									$user_property_sent[] = $this_string;
								}

								if( strpos( $single_param_key, $alt_substring ) === 0 )
								{
									$this_string =  substr( $single_param_key, strlen( $alt_substring) ).'='.$single_param_value;

									$user_property_sent[] = $this_string;
								}
							}

							//eof calc user property sent description

							$ganalytics_measurement_id_array = explode( ',', $the_options['ganalytics_measurement_id'] );

							$output_data_already_sent = false;

							foreach( $ganalytics_measurement_id_array as $ganalytics_measurement_id )
							{
								$ganalytics_measurement_id_fixed = str_replace('_', '', $ganalytics_measurement_id );

								$params_to_send['tid'] = $ganalytics_measurement_id_fixed;

								if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $params_to_send );

								$tracking_data_url_params = http_build_query( $params_to_send );

								$args = array(
									'body'        => $tracking_data_url_params,
									'timeout'     => '5',
									'redirection' => '5',
									'httpversion' => '1.0',
									'blocking'    => true,
									'headers'     => array(),
									'cookies'     => array(),
								);

								$args['headers']['Authority'] = 'region1.google-analytics.com';
								$args['headers']['Accept'] = '*/*';

								if( $ul )
								{
									$args['headers']['Accept-Language'] = $ul.';q=0.9';
								}

								if( $dl )
								{
									$args['headers']['Origin'] = $dl;
								}

								if( $dr )
								{
									$args['headers']['Referer'] = $dr;
								}

								$args['headers']['Sec-Fetch-Dest'] = 'empty';
								$args['headers']['Sec-Fetch-Mode'] = 'no-cors';
								$args['headers']['Sec-Fetch-Site'] = 'cross-site';

								if( $__UA )
								{
									$args['headers']['User-Agent'] = $__UA;
								}

								if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $args );

								$url_to_post_to = MAPX_GA_ENDPOINT.$tracking_data_url_params;

								if( $current_client_ip &&
									$blacklisted_ip_list &&
									in_array( $current_client_ip, $blacklisted_ip_list ) )
								{
									//skip
									$success = true;

									if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'GA data sending skipped due to ip blacklist' );

									if( !$output_data_already_sent ) $output_message = 'Google Analytics data sending skipped due to ip blacklist (event name: '.$human_event_name.' ).';
								}
								elseif( $human_event_name &&
									$blacklisted_events_list &&
									in_array( $human_event_name, $blacklisted_events_list ) )
								{
									//skip
									$success = true;

									if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'GA data sending skipped due to event blacklist' );

									if( !$output_data_already_sent ) $output_message = 'Google Analytics data sending skipped due to event blacklist (event name: '.$human_event_name.' ).';
								}
								else
								{
									//send
									$result = wp_remote_post( $url_to_post_to, $args );

									//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $result );

									$success = true;

									if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'GA data sent' );

									if( !$output_data_already_sent ) $output_message = 'Google Analytics data successfuly sent (event name: '.$human_event_name.' )';

									if( count( $user_property_sent ) > 0 )
									{
										if( !$output_data_already_sent ) $output_message .= ' with user property '.implode( ' , ', $user_property_sent );
									}

									if( $__WUCG )
									{
										if( $__WUCG == 'true' )
										{
											if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'user consent given' );
											if( !$output_data_already_sent ) $output_message .= ' (user consent given)';
										}
										elseif( $__WUCG == 'false' )
										{
											if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'user consent not given' );

											if( !$output_data_already_sent ) $output_message .= ' (user consent not given)';
										}
									}

									if( !$output_data_already_sent ) $output_message .= '.';
								}

								$output_data_already_sent = true;
							}
						}
					}

					if( $_POST['realm'] == 'fbcapi' )
					{
						if( !( $the_options['facebook_pixel_id'] && $the_options['facebook_access_token'] ) )
						{
							$detected_error = true;
							$error_description = 'missing facebook_pixel_id or facebook_access_token for realm fbcapi';
							if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $error_description );
							if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $_POST );
						}

						if( !$detected_error )
						{
							$human_event_name = $data_decoded['event'];

							//internal_version
							$internal_version = isset( $data_decoded['internal_version'] ) ? intval( $data_decoded['internal_version'] ) : 0;

							//Touch device
							$__IS_TOUCH = isset( $data_decoded['__IS_TOUCH'] ) ? $data_decoded['__IS_TOUCH'] : null;

							$user_data = array();

							$custom_data = $data_decoded['custom_data'];

							if( isset( $custom_data['email'] ) )
							{
								$user_data['em'][] = hash( 'sha256', strtolower( trim( $custom_data['email'] ) ) );
								unset( $custom_data['email'] );
							}

							if( isset( $custom_data['phone'] ) )
							{
								$user_data['ph'][] = hash( 'sha256', preg_replace( '/[^\d+]/', '', trim( $custom_data['phone'] ) ) );
								unset( $custom_data['phone'] );
							}

							if( $data_decoded['user_agent'] )
							{
								$user_data['client_user_agent'] = $data_decoded['user_agent'];
							}
							else
							{
								//bof user agent manipulation part

								if( $the_options['facebook_remove_user_agent'] &&
									$the_options['faacebook_send_desktop_mobile_user_agent'] )
								{
									if( $__IS_TOUCH )
									{
										$user_data['client_user_agent'] = MAPX_MOBILE_UA;
									}
									else
									{
										$user_data['client_user_agent'] = MAPX_DESKTOP_UA;
									}
								}

								//eof user agent manipulation part
							}

							if( $the_options['facebook_anonymize_ip'] == 0 )
							{
								$user_data['client_ip_address'] = $current_client_ip;
							}

							if( $data_decoded['external_id'] )
							{
								$_fbclid = null;
								$user_data['external_id'] = $data_decoded['external_id'];

								//check for fbclid
								if(
									strpos( $data_decoded['external_id'], '_') === 0 &&
									$internal_version >= 2 &&
									isset( $the_options['facebook_remove_click_id'] ) &&
									$the_options['facebook_remove_click_id'] == 0  &&
									$data_decoded['url'] )
								{
									$url_parts = parse_url( $data_decoded['url'] );

									if( isset( $url_parts['query'] ) )
									{
										parse_str( $url_parts['query'], $query_params );

										if( isset( $query_params['fbclid'] ) )
										{
											$_fbclid = $query_params['fbclid'];

										}
									}

									if( !$_fbclid && isset( $data_decoded['clid'] ) )
									{
										$_fbclid = $data_decoded['clid'];
									}

									if( $_fbclid )
									{
										//send fbc
										$user_data['fbc'] = 'fb.1.'.substr( $data_decoded['external_id'], 1 ).'.'.$_fbclid;
									}
								}
							}

							$timestamp = time();


							$event_data = array (
										'action_source'		=>	'website',
										'event_name' 		=> 	$data_decoded['event'],
										'event_time' 		=> 	$timestamp,
										'event_source_url'	=>	$data_decoded['url'],
										'event_id'			=>	$data_decoded['event_id'],
										'user_data'			=>	$user_data,
										'custom_data' 		=>	$custom_data,
							);

							$event_data = array_filter( $event_data );

							$data = array (
									0 => $event_data,
							);

							$params_to_send = array(
								'access_token' 		=> 	$the_options['facebook_access_token'],
								'data'				=>	$data,
							);

							if( $the_options['facebook_test_event_code'] )
							{
								$params_to_send['test_event_code'] = $the_options['facebook_test_event_code'];
							}

							if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $params_to_send );

							$tracking_data_url_params = http_build_query( $params_to_send );

							/**/
							$args = array(
								'body'        => $tracking_data_url_params,
								'timeout'     => '5',
								'redirection' => '5',
								'httpversion' => '1.0',
								'blocking'    => true,
								'headers'     => array(),
								'cookies'     => array(),
							);

							$url_to_post_to = MAPX_FBCAPI_ENDPOINT.MAPX_FBCAPI_VERSION_API.'/'.$the_options['facebook_pixel_id'].'/events';

							if( $current_client_ip &&
								$blacklisted_ip_list &&
								in_array( $current_client_ip, $blacklisted_ip_list ) )
							{
								$success = true;

								if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'FB data sending skipped due to ip blacklist' );

								$output_message = 'Facebook Pixel data sending skipped due to ip blacklist (event name: '.$human_event_name.' ).';
							}
							elseif( $human_event_name &&
								$blacklisted_events_list &&
								in_array( $human_event_name, $blacklisted_events_list ) )
							{
								//skip
								$success = true;

								if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'FB data sending skipped due to event blacklist' );

								$output_message = 'Facebook Pixel data sending skipped due to event blacklist (event name: '.$human_event_name.' ).';
							}
							else
							{
								$result = wp_remote_post( $url_to_post_to, $args );

								//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $result );

								if( !is_wp_error( $result ) )
								{
									$additional_data = $result['body'];

									$decoded_result = json_decode( $additional_data, true );

									if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $decoded_result );

									if( $decoded_result &&
										isset( $decoded_result['error'] ) &&
										isset( $decoded_result['error']['message'] ) )
									{
										$detected_error = true;
										$error_description = $decoded_result['error']['message'];
									}
								}

								$success = true;

								if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'FB data sent' );

								$output_message = 'Facebook Pixel data successfuly sent (event name: '.$human_event_name.' ).';
							}
						}
					}

					if( $_POST['realm'] == 'tiktokapi' )
					{
						if( !( $the_options['tiktok_pixel_id'] && $the_options['tiktok_access_token'] ) )
						{
							$detected_error = true;
							$error_description = 'missing tiktok_pixel_id or tiktok_access_token for realm tiktokapi';
							if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $error_description );
							if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $_POST );
						}

						if( !$detected_error )
						{
							$human_event_name = $data_decoded['event'];

							//Touch device
							$__IS_TOUCH = isset( $data_decoded['__IS_TOUCH'] ) ? $data_decoded['__IS_TOUCH'] : null;

							if( isset( $data_decoded['user_agent'] )
								&& $data_decoded['user_agent']
								&& $data_decoded['user_agent'] != '' )
							{
								$client_user_agent = $data_decoded['user_agent'];
							}
							else
							{
								//bof user agent manipulation part

								if( $the_options['tiktok_remove_user_agent'] &&
									$the_options['tiktok_send_desktop_mobile_user_agent'] )
								{
									if( $__IS_TOUCH )
									{
										$client_user_agent = MAPX_MOBILE_UA;
									}
									else
									{
										$client_user_agent = MAPX_DESKTOP_UA;
									}
								}
								else
								{
									$client_user_agent = MAPX_DESKTOP_UA;
								}

								//eof user agent manipulation part
							}

							$user_data = array();

							if( $data_decoded['external_id'] )
							{
								$user_data['external_id'] = hash( 'sha256', $data_decoded['external_id'] );
							}

							$timestamp_datetime = new DateTime( 'now' );
							$timestamp = $timestamp_datetime->format( DateTime::ATOM ); // Updated ISO8601

							$context = array(
								'page'			=> array(
														'url' => $data_decoded['url'],
													),
							);

							$context = array_filter( $context );

							$custom_data = $data_decoded['custom_data'];

							$properties = array(
								'contents'		=> array(
														'price'			=>	( isset( $custom_data['price'] ) ) ?
																				$custom_data['price'] : null,
														'quantity'		=>	( isset( $custom_data['quantity'] ) ) ?
																				$custom_data['quantity'] : null,
														'content_type'	=>	( isset( $custom_data['content_type'] ) ) ?
																				$custom_data['content_type'] : null,
														'content_id'	=>	( isset( $custom_data['content_id'] ) ) ?
																				$custom_data['content_id'] : null,

													),

								'currency'		=>	( isset( $custom_data['currency'] ) ) ? $custom_data['currency'] : null,
								'value'			=>	( isset( $custom_data['value'] ) ) ? $custom_data['value'] : null,
								'description'	=>	( isset( $custom_data['description'] ) ) ? $custom_data['description'] : null,
								'query'			=>	( isset( $custom_data['query'] ) ) ? $custom_data['query'] : null,
							);


							$properties['contents'] = array_filter( $properties['contents'] );
							$properties = array_filter( $properties );

							if( $the_options['tiktok_anonymize_ip'] == 0 )
							{
								$ip_address_to_send = $current_client_ip;
							}
							else
							{
								$ip_address_to_send = $this->getServerIp();
							}

							$params_to_send = array(
								'pixel_code'	=> 	$the_options['tiktok_pixel_id'],
								'event'			=> 	$data_decoded['event'],
								'event_id'		=> 	$data_decoded['event_id'],
								'timestamp'		=>	$timestamp,
								'user_data'		=> 	$user_data,
								'user_agent'	=>	$client_user_agent,
								'ip'			=>	$ip_address_to_send,
								'context' 		=> 	$context,
								'properties'	=> 	$properties,
							);

							$params_to_send = array_filter( $params_to_send );

							if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $params_to_send );

							$tracking_data_url_params = json_encode( $params_to_send );

							$args = array(
								'body'        => $tracking_data_url_params,
								'timeout'     => '5',
								'redirection' => '5',
								'httpversion' => '1.0',
								'blocking'    => true,
								'headers'     => array(),
								'cookies'     => array(),
							);

							$args['headers']['Access-Token'] = $the_options['tiktok_access_token'];
							$args['headers']['Content-Type'] = 'application/json';


							if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $args );

							$url_to_post_to = MAPX_TIKTOKAPI_ENDPOINT.MAPX_TIKTOKAPI_VERSION_API.'/pixel/track/';

							if( $current_client_ip &&
								$blacklisted_ip_list &&
								in_array( $current_client_ip, $blacklisted_ip_list ) )
							{
								$success = true;

								if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'Tiktok data sending skipped due to ip blacklist' );

								$output_message = 'Tiktok data sending skipped due to ip blacklist (event name: '.$human_event_name.' ).';
							}
							elseif( $human_event_name &&
								$blacklisted_events_list &&
								in_array( $human_event_name, $blacklisted_events_list ) )
							{
								//skip
								$success = true;

								if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'Tiktok data sending skipped due to event blacklist' );

								$output_message = 'Tiktok data sending skipped due to event blacklist (event name: '.$human_event_name.' ).';
							}
							else
							{
								$result = wp_remote_post( $url_to_post_to, $args );

								if( !is_wp_error( $result ) )
								{
									$additional_data = $result['body'];

									$decoded_result = json_decode( $additional_data, true );

									if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $decoded_result );
								}

								$success = true;

								if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'Tiktok data sent' );

								$output_message = 'Tiktok data successfuly sent (event name: '.$human_event_name.' ).';
							}
						}
					}
				}
			}
			else
			{
				$detected_error = true;
				$error_description = 'missing critical data on POST request';
				if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $error_description );
				if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $_POST );
			}

			$answer = array(
				'success'				=>	$success,
				'additional_data'		=>	$additional_data,
				'output_message'		=>	$output_message,
			);

			if( $detected_error )
			{
				$answer['detected_error'] = $detected_error;
			}

			if( $error_description )
			{
				$answer['error_description'] = $error_description;
			}

			wp_send_json( $answer );

			if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $answer );

			die();
		}
		else
		{
			if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'invalid referral '.$referral_url );
		}

		$answer = array(
			'success'				=>	false,
		);

		wp_send_json( $answer );

		if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $answer );

		die();
	}

	/**
	 * Plugin auto update
	 * @access   public
	 */
	public function auto_update_plugins( $update, $item )
	{
		$the_options = MyAgilePixel::get_settings();

		$rconfig = MyAgilePixel::get_rconfig();

		$plugins = array ( MAPX_PLUGIN_SLUG );

		if( is_object( $item ) &&
			property_exists( $item, 'slug' ) &&
			in_array( $item->slug, $plugins ) )
		{
			if( isset( $the_options ) &&
				isset( $the_options['forced_auto_update'] ) )
			{
				return ($the_options['forced_auto_update']) ? true : null;
			}
			elseif( isset( $rconfig ) &&
				isset( $rconfig['disable_plugin_autoupdate'] ) &&
				$rconfig['disable_plugin_autoupdate'] == 1 )
			{
				// use default settings
				return $update;
			}
			else
			{
				// update plugin
				return true;
			}

		} else {
			// use default settings
			return $update;
		}
	}


	/**
	 * Returns a 'category' JSON line based on $product
	 *
	 * @param  WC_Product $_product  Product to pull info for
	 * @return string                Line of JSON
	 */
	private static function product_get_category_line( $_product ) {
		$out            = [];
		$variation_data = $_product->is_type( 'variation' ) ? wc_get_product_variation_attributes( $_product->get_id() ) : false;
		$categories     = get_the_terms( $_product->get_id(), 'product_cat' );

		if ( is_array( $variation_data ) && ! empty( $variation_data ) ) {
			$parent_product = wc_get_product( $_product->get_parent_id() );
			$categories     = get_the_terms( $parent_product->get_id(), 'product_cat' );
		}

		if ( $categories ) {
			foreach ( $categories as $category ) {
				$out[] = $category->name;
			}
		}

		return "'" . esc_js( join( '/', $out ) ) . "',";
	}

	/**
	 * Returns a 'variant' JSON line based on $product
	 */
	private static function product_get_variant_line( $_product ) {
		$out            = '';
		$variation_data = $_product->is_type( 'variation' ) ? wc_get_product_variation_attributes( $_product->get_id() ) : false;

		if ( is_array( $variation_data ) && ! empty( $variation_data ) ) {
			$out = "'" . esc_js( wc_get_formatted_variation( $variation_data, true ) ) . "',";
		}

		return $out;
	}

	/**
	 * Add Item
	 */
	private function add_item( $order, $item ) {

		if( !( $item && is_object( $item ) ) )
		{
			return null;
		}

		$_product = $item->get_product();
		$variant  = $this::product_get_variant_line( $_product );

		$code  = '{';
		$code .= "'id': '" . esc_js( $_product->get_sku() ? $_product->get_sku() : $_product->get_id() ) . "',";
		$code .= "'name': '" . esc_js( $item['name'] ) . "',";
		$code .= "'category': " . $this::product_get_category_line( $_product );

		if ( '' !== $variant ) {
			$code .= "'variant': " . $variant;
		}

		$code .= "'price': '" . esc_js( $order->get_item_total( $item ) ) . "',";
		$code .= "'quantity': '" . esc_js( $item['qty'] ) . "'";
		$code .= '},';

		return $code;
	}


	/**
	 * Measures a listing impression (from search results)
	 */
	public function woo_listing_impression() {

		$is_woocommerce_activated = MyAgilePixel::is_woocommerce_activated();

		if( $is_woocommerce_activated )
		{
			global $product, $woocommerce_loop;
			$this->to_append_html .= $this->listing_impression( $product, $woocommerce_loop['loop'] );
		}
	}

	private function listing_impression( $product, $position ) {

		if( !( $product && is_object( $product ) ) )
		{
			return null;
		}

		$the_options = MyAgilePixel::get_settings();

		$compatibility_mode = false;

		if( isset( $the_options['compatibility_mode'] ) &&
			$the_options['compatibility_mode']
		)
		{
			$compatibility_mode = $the_options['compatibility_mode'];
		}

		if ( isset( $_GET['s'] ) ) {
			$list = "Search Results";
		} else {
			$list = "Product List";
		}

		$code = "setTimeout(function(){
			try{

				if( mpx_settings.analytics_enabled )
				{
					MAPX_Call_TrackFunc.doInvokeTrackingFunction( 'gtag', 'event', 'view_item_list', { 'items': [ {
						'item_id': '" . esc_js( $product->get_sku() ? $product->get_sku() : ( '#' . $product->get_id() ) ) . "',
						'item_name': '" . esc_js( $product->get_title() ) . "',
						'item_category': " . $this::product_get_category_line( $product ) . "
						'list': '" . esc_js( $list ) . "',
						'list_position': '" . esc_js( $position ) . "'
					} ] });
				}

			}
			catch (error)
			{
				console.error(error);
			}

		},200);".PHP_EOL;

		if( $compatibility_mode )
		{
			wp_register_script( 'mpx_inline_listing_impression', false );
			wp_add_inline_script( 'mpx_inline_listing_impression', $code );
			wp_enqueue_script( 'mpx_inline_listing_impression' );
		}
		else
		{
			$this_output = '';

			$this_output .= '<script data-cfasync="false" class="my_agile_privacy_do_not_touch" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;
			$this_output .= $code;
			$this_output .= '</script>'.PHP_EOL;
		}

		if( $compatibility_mode )
		{
			return null;
		}

		return $this_output;
	}


	/**
	 * Measure a product click from a listing page
	 */
	public function woo_listing_click() {

		$is_woocommerce_activated = MyAgilePixel::is_woocommerce_activated();

		if( $is_woocommerce_activated )
		{
			global $product, $woocommerce_loop;
			$this->to_append_html .= $this->listing_click( $product, $woocommerce_loop['loop'] );
		}
	}

	/**
	 * Enqueues JavaScript to build an addProduct and click event
	 *
	 * @param WC_Product $product
	 * @param int $position
	 */
	private function listing_click( $product, $position ) {

		if( !( $product && is_object( $product ) ) )
		{
			return null;
		}

		$the_options = MyAgilePixel::get_settings();

		$compatibility_mode = false;

		if( isset( $the_options['compatibility_mode'] ) &&
			$the_options['compatibility_mode']
		)
		{
			$compatibility_mode = $the_options['compatibility_mode'];
		}

		if ( isset( $_GET['s'] ) ) {
			$list = "Search Results";
		} else {
			$list = "Product List";
		}

		$code = "my_agile_on('click', '.products .post-" . esc_js( $product->get_id() ) . " a', function () {

				var event_type = 'select_content';

				if( this.classList.contains('add_to_cart_button') )
				{
					event_type = 'add_to_cart';
				}

				try{
					if( mpx_settings.analytics_enabled )
					{
						MAPX_Call_TrackFunc.doInvokeTrackingFunction( 'gtag', 'event', event_type, {
							'content_type': 'product',
							'items': [ {
								'item_id': '" . esc_js( $product->get_sku() ? $product->get_sku() : ( '#' . $product->get_id() ) ) . "',
								'item_name': '" . esc_js( $product->get_title() ) . "',
								'item_category': " . $this::product_get_category_line( $product ) . "
								'list_position': '" . esc_js( $position ) . "'
							} ],
						});
					}
				}
				catch (error)
				{
					console.error(error);
				}

				return;
			});
		".PHP_EOL;

		if( $compatibility_mode )
		{
			wp_register_script( 'mpx_inline_listing_click', false );
			wp_add_inline_script( 'mpx_inline_listing_click', $code );
			wp_enqueue_script( 'mpx_inline_listing_click' );
		}
		else
		{
			$this_output = '';

			$this_output .= '<script data-cfasync="false" class="my_agile_privacy_do_not_touch" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;
			$this_output .= $code;
			$this_output .= '</script>'.PHP_EOL;
		}

		if( $compatibility_mode )
		{
			return null;
		}


		return $this_output;
	}

	/**
	 * Measure a product detail view
	 */
	public function woo_product_detail() {

		$is_woocommerce_activated = MyAgilePixel::is_woocommerce_activated();

		if( $is_woocommerce_activated )
		{
			global $product;
			$this->to_append_html .= $this->product_detail( $product );
		}
	}

	/**
	 * Enqueue JavaScript to track a product detail view
	 *
	 * @param WC_Product $product
	 */
	private function product_detail( $product ) {
		if ( empty( $product ) ) {
			return;
		}

		$the_options = MyAgilePixel::get_settings();

		$compatibility_mode = false;

		if( isset( $the_options['compatibility_mode'] ) &&
			$the_options['compatibility_mode']
		)
		{
			$compatibility_mode = $the_options['compatibility_mode'];
		}

		$code = "setTimeout(function(){
			try{
				if( mpx_settings.analytics_enabled )
				{
					MAPX_Call_TrackFunc.doInvokeTrackingFunction( 'gtag', 'event', 'view_item', {
						'items': [ {
							'item_id': '" . esc_js( $product->get_sku() ? $product->get_sku() : ( '#' . $product->get_id() ) ) . "',
							'item_name': '" . esc_js( $product->get_title() ) . "',
							'item_category': " . $this::product_get_category_line( $product ) . "
							'price': '" . esc_js( $product->get_price() ) . "',
						} ]
					});
				}
			}
			catch (error)
			{
				console.error(error);
			}

		},200);".PHP_EOL;

		if( $compatibility_mode )
		{
			wp_register_script( 'mpx_inline_product_detail', false );
			wp_add_inline_script( 'mpx_inline_product_detail', $code );
			wp_enqueue_script( 'mpx_inline_product_detail' );
		}
		else
		{
			$this_output = '';

			$this_output .= '<script data-cfasync="false" class="my_agile_privacy_do_not_touch" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;
			$this_output .= $code;
			$this_output .= '</script>'.PHP_EOL;

			//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $this_output );
		}

		if( $compatibility_mode )
		{
			return null;
		}

		return $this_output;
	}

	/**
	 * Tracks when the checkout form is loaded
	 *
	 */
	public function woo_checkout_process( $checkout ) {

		$is_woocommerce_activated = MyAgilePixel::is_woocommerce_activated();

		if( $is_woocommerce_activated )
		{
			$this->to_append_html .= $this->checkout_process( WC()->cart->get_cart() );
		}
	}

	/**
	 * Enqueue JS to track when the checkout process is started
	 *
	 * @param array $cart items/contents of the cart
	 */
	private function checkout_process( $cart ) {

		if( !( $cart && is_array( $cart ) ) )
		{
			return null;
		}

		$the_options = MyAgilePixel::get_settings();

		$compatibility_mode = false;

		if( isset( $the_options['compatibility_mode'] ) &&
			$the_options['compatibility_mode']
		)
		{
			$compatibility_mode = $the_options['compatibility_mode'];
		}

		$items = "[";

		foreach ( $cart as $cart_item_key => $cart_item ) {
			$product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			$items .= "
				{
					'item_id': '" . esc_js( $product->get_sku() ? $product->get_sku() : ( '#' . $product->get_id() ) ) . "',
					'item_name': '" . esc_js( $product->get_title() ) . "',
					'category': " . $this::product_get_category_line( $product );

			$variant     = $this::product_get_variant_line( $product );
			if ( '' !== $variant ) {
				$items .= "
					'variant': " . $variant;
			}

			$items .= "
					'price': '" . esc_js( $product->get_price() ) . "',
					'quantity': '" . esc_js( $cart_item['quantity'] ) . "'
				},";
		}

		$items .= '
			]';

		$code  = "setTimeout(function(){
			try{

				if( mpx_settings.analytics_enabled )
				{
					MAPX_Call_TrackFunc.doInvokeTrackingFunction( 'gtag', 'event', 'begin_checkout', {
						'items': " . $items . ",
					} );
				}
			}
			catch (error)
			{
				console.error(error);
			}

			try{
				if( mpx_settings.fbcapi_enabled )
				{
					MAPX_Call_TrackFunc.doInvokeTrackingFunction( 'fbq', 'track', 'InitiateCheckout' );
				}
			}
			catch (error)
			{
				console.error(error);
			}

			try{

				if( mpx_settings.tiktokapi_enabled )
				{
					MAPX_Call_TrackFunc.doInvokeTrackingFunction( 'ttq', 'InitiateCheckout' );
				}
			}
			catch (error)
			{
				console.error(error);
			}

		},200);".PHP_EOL;


		if( $compatibility_mode )
		{
			wp_register_script( 'mpx_inline_checkout_process', false );
			wp_add_inline_script( 'mpx_inline_checkout_process', $code );
			wp_enqueue_script( 'mpx_inline_checkout_process' );
		}
		else
		{
			$this_output = '';

			$this_output .= '<script data-cfasync="false" class="my_agile_privacy_do_not_touch" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;
			$this_output .= $code;
			$this_output .= '</script>'.PHP_EOL;

			//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $this_output );
		}

		if( $compatibility_mode )
		{
			return null;
		}

		return $this_output;
	}


	/**
	 * Generate Gtag transaction tracking code
	 *
	 */
	private function add_transaction( $order )
	{
		$the_options = MyAgilePixel::get_settings();

		$compatibility_mode = false;

		if( isset( $the_options['compatibility_mode'] ) &&
			$the_options['compatibility_mode']
		)
		{
			$compatibility_mode = $the_options['compatibility_mode'];
		}

		if( !( $order && is_object( $order ) ) )
		{
			if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( 'exiting add_transaction' );
			return null;
		}

		// Order items
		$items = "[";
		if ( $order->get_items() ) {
			foreach ( $order->get_items() as $item ) {
				$items .= $this::add_item( $order, $item );
			}
		}
		$items .= "]";

		$code = "setTimeout(function(){
			try{
				if( mpx_settings.analytics_enabled )
				{
					MAPX_Call_TrackFunc.doInvokeTrackingFunction( 'gtag', 'event', 'purchase', {
						'transaction_id': '" . esc_js( $order->get_order_number() ) . "',
						'affiliation': '" . esc_js( get_bloginfo( 'name' ) ) . "',
						'value': '" . esc_js( $order->get_total() ) . "',
						'tax': '" . esc_js( $order->get_total_tax() ) . "',
						'shipping': '" . esc_js( $order->get_total_shipping() ) . "',
						'currency': '" . esc_js( $order->get_currency() ) . "',
						'items': {$items},
					});
				}
			}
			catch (error)
			{
				console.error(error);
			}

			try{

				if( mpx_settings.fbcapi_enabled )
				{
					MAPX_Call_TrackFunc.doInvokeTrackingFunction( 'fbq', 'track', 'Purchase', {value: '" . esc_js( $order->get_total() ) . "', currency: '" . esc_js( $order->get_currency() ) . "'});
				}
			}
			catch (error)
			{
				console.error(error);
			}

			try{

				if( mpx_settings.tiktokapi_enabled )
				{
					MAPX_Call_TrackFunc.doInvokeTrackingFunction( 'ttq', 'PlaceAnOrder', {
						content_name: 'order',
						value: " . esc_js( $order->get_total() ) . ",
						currency: '" . esc_js( $order->get_currency() ) . "',
					});
				}
			}
			catch (error)
			{
				console.error(error);
			}

		},300);".PHP_EOL;


		if( $compatibility_mode )
		{
			wp_register_script( 'mpx_inline_add_transaction', false );
			wp_add_inline_script( 'mpx_inline_add_transaction', $code );
			wp_enqueue_script( 'mpx_inline_add_transaction' );
		}
		else
		{
			$this_output = '';

			$this_output .= '<script data-cfasync="false" class="my_agile_privacy_do_not_touch" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;
			$this_output .= $code;
			$this_output .= '</script>'.PHP_EOL;

			//if( defined( 'MAPX_DEBUGGER' ) && MAPX_DEBUGGER ) MyAgilePixel::write_log( $this_output );
		}

		// Mark the order as tracked.
		$order->update_meta_data( '_map_tracked', 1 );
		$order->save();

		if( $compatibility_mode )
		{
			return null;
		}

		return $this_output;
	}


	/**
	 * Generate code for user property tracking
	 *
	 */
	private function get_user_property_code()
	{
		$the_options = MyAgilePixel::get_settings();

		$user_property_assoc_saved_settings = MyAgilePixel::nullCoalesceArrayItem( $the_options, 'user_property_assoc', null );

		if( $user_property_assoc_saved_settings )
		{
			$queried_object = get_queried_object();

			if( $queried_object )
			{
				$queried_object_class = get_class( $queried_object );

				if( $queried_object_class == 'WP_Post' )
				{
					$queried_object_post_id = $queried_object->ID;

					$post_type = $queried_object->post_type;

					$key_to_check = 'post_type_'.$post_type;

					$do_track_this = false;
					$add_user_property = array();

					switch( $user_property_assoc_saved_settings[ $key_to_check ] )
					{
						case '1':

							//catch all
							$do_track_this = true;
							$add_user_property[] = $user_property_assoc_saved_settings[ $key_to_check.'_catch_all_user_property_def' ];
							break;

						case '2':

							//custom

							$post_type_post_custom_setting_user_property = ( isset( $user_property_assoc_saved_settings ) &&  isset( $user_property_assoc_saved_settings[ $key_to_check.'_custom_setting_user_property' ] ) ) ? $user_property_assoc_saved_settings[ $key_to_check.'_custom_setting_user_property' ] : null;

							if( $post_type_post_custom_setting_user_property )
							{
								foreach( $post_type_post_custom_setting_user_property as $item )
								{
									if( $item['elem_id'] == $queried_object_post_id )
									{
										if( !in_array( $item['user_property'], $add_user_property ) )
										{
											$do_track_this = true;
											$add_user_property[] = $item['user_property'];
										}
									}
								}
							}

							break;
					}

					if( $do_track_this && count( $add_user_property ) > 0 )
					{
						$code = "var mapx_add_user_property = []; ".PHP_EOL;

						foreach( $add_user_property as $single_property )
						{
							$code .= "mapx_add_user_property.push( ['".esc_js( $single_property )."' , 1] );".PHP_EOL;
						}

						$this_output = '<script data-cfasync="false" class="my_agile_privacy_do_not_touch" type="text/javascript" data-no-optimize="1" data-no-defer="1" consent-skip-blocker="1">'.PHP_EOL;
						$this_output .= $code;
						$this_output .= '</script>'.PHP_EOL;

						return $this_output;
					}
				}
			}

		}

		return false;

	}
}