(function( $ ) {
	'use strict';

	 $(function() {

		var apix_pupup_notify =
		{
			error : function( message )
			{
				var error_element = $( '<div class="apix_notify_popup alert alert-danger"></div>' );
				error_element.html( message );
				this.showNotify( error_element );
			},
			success : function( message )
			{
				var success_element = $( '<div class="apix_notify_popup alert alert-success"></div>' );
				success_element.html( message );
				this.showNotify( success_element );
			},
			warning : function( message )
			{
				var success_element = $( '<div class="apix_notify_popup alert alert-warning"></div>' );
				success_element.html( message );
				this.showNotify( success_element );
			},
			showNotify : function( elm )
			{
				$( 'body' ).append( elm );

				elm.stop( true,true ).animate( {'opacity':1,'right':'40px'}, 1000 );
				setTimeout( function(){
					elm.animate( {'opacity':0,'right':-elm.width()+'px' }, 1000, function(){
					   elm.remove();
					});
				}, 2500);
			}
		};

		var apix_changePresetLevel = function(){
			//console.log( 'apix_changePresetLevel' );

			var all_preset_input_range =  $( '.preset-input-range' );

			all_preset_input_range.each( function(){

				var $this = $( this );
				var context = $this.attr( 'data-set-ref' ); //id of container tab (ganalytics, facebook, ecc)
				var all_preset_input = $( '[data-set="'+context+'"]' );
				var preset_messages = $( '[data-preset-value]', '#'+context );

				apix_setInputAvailability( $this, all_preset_input, preset_messages );

				$this.bind( 'change', function(){
					apix_setInputAvailability( $this, all_preset_input, preset_messages );
				});
			});
		};

		var apix_setInputAvailability = function( $this, all_preset_input, preset_messages ){

			var preset_level = parseInt( $this.val() );

			// for each input, set selected option based on preset level selected by range input
			all_preset_input.each( function(){

				var $this_input = $( this );

				//the preset chosen value
				var this_preset_val = parseInt( $this_input.attr( 'data-preset-level' ) );

				//this is for not setting disabled and preset attribute <= that value
				var this_last_preset_customizable_value = null;

				if( $this_input.attr( 'data-last-preset-customizable-value' ) )
				{
					this_last_preset_customizable_value = parseInt( $this_input.attr( 'data-last-preset-customizable-value' ) );
				}

				if( preset_level == 1 ||
					( !!this_last_preset_customizable_value && preset_level <= this_last_preset_customizable_value )
				)
				{
					$this_input.parent().removeClass( 'disabled' );
				}
				else
				{
					$this_input.parent().addClass( 'disabled' );

					if( preset_level >= this_preset_val  )
					{
						$this_input.val( '1' ).change();
					}
					else
					{
						$this_input.val( '0' ).change();
					}
				}

			});

			/* Hiding all the messages and showing the message that is related to the selected preset level. */
			preset_messages.addClass( 'd-none' ).filter( '[data-preset-value="'+preset_level+'"]' ).removeClass( 'd-none' );
		};

		var apix_createDynamicFields = function( $extrawrapper ){

			//console.log( 'apix_createIpsFields' );

			//add button
			$( document ).on( 'click', '.mapx-btn-add', function( e ){


				//console.log( 'click on mapx-btn-add' );

				e.preventDefault();
				
				var this_button = jQuery( this );
				var box_container = this_button.closest( '.dynamic_fields_container' );

				if( this_button.hasClass( 'mapx-btn-multiple-input' ) )
				{
					var current_dynamic_entry = this_button.parents( '.mapx-dynamic-entry:last' );

					var new_item_html = current_dynamic_entry.prop('outerHTML');

					var data_row_counter = parseInt( box_container.attr( 'data-row-counter' ) );
					var basename = box_container.attr( 'data-basename' );
					var added_data_row_counter = data_row_counter + 1;

					var search = `${basename}[${data_row_counter}]`;
					var replace = `${basename}[${added_data_row_counter}]`;

					new_item_html = new_item_html.replaceAll( search, replace );

					box_container.attr( 'data-row-counter', added_data_row_counter );

					box_container.append( new_item_html );

					var new_dynamic_entry = this_button.parents( '.mapx-dynamic-entry:last' );
				}
				else
				{
					var current_dynamic_entry = this_button.parents( '.mapx-dynamic-entry:first' );
					var cloned_item = current_dynamic_entry.clone();
					var new_dynamic_entry = $( cloned_item ).appendTo( box_container ).find( 'input' ).removeClass( 'is-valid' ).removeClass( 'is-invalid' ).val( '' );

				}

				new_dynamic_entry.find( 'input' ).val( '' );


				box_container.find( '.mapx-dynamic-entry:not(:last) .mapx-btn-add' )
									.removeClass( 'mapx-btn-add' ).addClass( 'mapx-btn-remove' )
									.removeClass( 'btn-success' ).addClass( 'btn-danger' )
									.html( '<i class="fa-solid fa-minus"></i>' );

				if( $extrawrapper.hasClass( 'apix_user_property' ) )
				{
					$extrawrapper.trigger( 'mapx_refreshUserPropertyDef' );
				}
			});

			//remove button
			$( document ).on( 'click', '.mapx-btn-remove', function( e ){
				e.preventDefault();

				$( this ).parents( '.mapx-dynamic-entry:first' ).remove();

				if( $extrawrapper.hasClass( 'apix_user_property' ) )
				{
					$extrawrapper.trigger( 'mapx_refreshUserPropertyDef' );
				}

			});

			//not-unsafe-string validation
			$( document ).on( 'keyup keypress focusout change', '.map-not-unsafe-string input', function( e ){
				var $this_input = $( this );
				var content = $this_input.val();

				//only digit, character allowed, dash and underscore
				var regexp = /^([0-9a-zA-Z_-]+)$/i;

				if( !regexp.test( content ) )
				{
					// Use the replace method with a custom function to filter out non-matching characters
					let sanitizedValue = content.replace(/./g, function( match ) {
						return regexp.test( match ) ? match : '';
					});

					$this_input.val( sanitizedValue );
				}

				if( $extrawrapper.hasClass( 'apix_user_property' ) )
				{
					$extrawrapper.trigger( 'mapx_refreshUserPropertyDef' );
				}
			});

			//ip-entry validation
			$( document ).on( 'keyup keypress focusout change', '.mapx-ip-entry input', function( e ){
				var $this_input = $( this );
				var content = $this_input.val();

				var regexp_ipaddress = /((^\s*((([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))\s*$)|(^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$))/;

				//Removing the validation classes if the input is empty
				if( content == '' )
				{
					$this_input.removeClass( 'is-invalid' ).removeClass( 'is-valid' );
				}
				else
				{
					// Checking if the input is a valid IP address
					if( regexp_ipaddress.test( content ) )
					{
						//right ip address

						$this_input.removeClass( 'is-invalid' ).addClass( 'is-valid' );
					}
					else
					{
						// wrong ip address
						$this_input.removeClass( 'is-valid' ).addClass( 'is-invalid' );
					}
				}
			});
		};

		$(document).ready(function (){

			var $my_agile_pixel_backend = $( '#apix_general_settings_backend' );

			console.log( 'palle1');

			if( $my_agile_pixel_backend.length )
			{

				//multi nav pills active fix
				$('.nav-pills button').click(function() {
					var $selector = $( '.nav-pills.no-multiple-active button', $my_agile_pixel_backend ).not( this );
					$selector.removeClass( 'active') ;
				});


				var user_property_def = [];

				if( $my_agile_pixel_backend.hasClass( 'apix_user_property' ) )
				{
					//mapx_refreshUserPropertyDefInput event
					$( ':input.apix_user_property_select' ).on( 'mapx_refreshUserPropertyDefInput', function( e ){

						let $this = $( this );

						let selected = $this.attr( 'data-selected' );

						let value = $this.val();

						let new_html = '<option value="">...</option>';

						$.each( user_property_def, function( index, this_value ) {

							let selected_attr = '';

							if( this_value == selected ||
								( !!value && value == this_value ) )
							{
								selected_attr = 'selected';
							}

							new_html += `<option value="${this_value}" ${selected_attr}>${this_value}</option>`;
						});

						$this.html( new_html );

					});

					//mapx_refreshUserPropertyDef event
					$my_agile_pixel_backend.bind( 'mapx_refreshUserPropertyDef', function(){

						console.log( 'mapx_refreshUserPropertyDef' );

						user_property_def = [];

						$( ':input[name^="user_property_def_field"]' ).each( function(){

							var value = jQuery(this).val();

							if( value != '' && !user_property_def.includes( value ) )
							{
								user_property_def.push( value );
							}

						});

						$( ':input.apix_user_property_select' ).trigger( 'mapx_refreshUserPropertyDefInput' );


					}).trigger( 'mapx_refreshUserPropertyDef' );
				}

				apix_changePresetLevel();
				apix_createDynamicFields( $my_agile_pixel_backend );

				var $input = $( ':input.hideShowInput', $my_agile_pixel_backend );

				if( $input.length )
				{
					//console.log( 'initInputHideShowWrapper');

					$input.each(function(){

						var $this = $( this );

						$this.bind( 'change', function(){

							var $this = $( this );
							var hide_show_ref = $this.attr( 'data-hide-show-ref' );

							var $ref = $( '.'+hide_show_ref);

							//console.log( $ref );

							if( $this.is( 'input[type="checkbox"]') )
							{
								if( $this.is( '.reverseHideShow') )
								{
									if( $this.is( ':checked') )
									{
										$ref.addClass( 'displayNone' );
									}
									else
									{
										$ref.removeClass( 'displayNone' );
									}
								}
								else
								{
									if( $this.is( ':checked') )
									{
										$ref.removeClass( 'displayNone' );
									}
									else
									{
										$ref.addClass( 'displayNone' );
									}
								}
							}
							else if( $this.is( 'select' ) )
							{
								var value = $this.val();

								$ref.addClass( 'displayNone' );

								var $target = $( '.' + hide_show_ref + '[data-value~="' + value + '"]' );
								$target.removeClass( 'displayNone' );
							}


						}).trigger( 'change' );
					});
				}
			}

			$( '.changeLicenseCode' ).bind( 'click', function( e ){

				e.preventDefault();

				var $license_code_field = $( ':input[name="license_code_field"]');
				var $license_code_wrapper = $( '.license_code_wrapper' );
				var $hide_code_wrapper = $( '.hide_code_wrapper' );

				$license_code_field.val( '' );
				$license_code_wrapper.removeClass( 'd-none' );
				$hide_code_wrapper.addClass( 'd-none' );

			});

			$( '#apix_general_settings_form' ).submit( function( e ){

				e.preventDefault();
				var data = $( this ).serialize();
				var url = $( this ).attr( 'action' );

				var $submit_button = $( this ).find( 'input[type="submit"]' );
				var $fake_submit_buttons = $( this ).find( '.fake-save-button' );

				var $license_status_field = $( '#license_status_field' );

				var $premium_pills = $( '#apix_general_settings_backend .nav-pills .nav-link.premium' );
				var $premium_pills_content = $( 'div', $premium_pills );
				var $premium_badge = $( '.badge', $premium_pills );


				var $license_code_wrapper = $( '.license_code_wrapper' );
				var $hide_code_wrapper = $( '.hide_code_wrapper' );
				var $lc_owner_description = $( '.lc_owner_description' );
				var $lc_owner_email_wrapper = $( '.lc_owner_email_wrapper' );
				var $lc_owner_website_wrapper = $( '.lc_owner_website_wrapper' );
				var $lc_owner_email = $( '.lc_owner_email' );
				var $lc_owner_website = $( '.lc_owner_website' );

				$submit_button.css( {'opacity':'.6','cursor':'default'} ).prop( 'disabled', true );
				$fake_submit_buttons.css( {'opacity':'.6','cursor':'default'} ).prop( 'disabled', true );

				$( '.apix_wait' ).fadeIn();

				$.ajax({
					url:url,
					type:'POST',
					data:data,
					success : function( data )
					{
						$submit_button.css({ 'opacity':'1','cursor':'pointer'} ).prop( 'disabled', false );
						$fake_submit_buttons.css( {'opacity':'1','cursor':'pointer'} ).prop( 'disabled', false );
						$( '.apix_wait' ).fadeOut();

						if( !!data?.with_missing_fields )
						{
							apix_pupup_notify.warning( apix_settings_warning_text );
						}
						else
						{
							apix_pupup_notify.success( apix_settings_success_text );
						}

						//console.log($premium_pills);
						//console.log($premium_badge);

						if( data.license_valid == true &&
							data.grace_period == false)
						{
							$license_status_field.removeClass( 'warning_style' ).addClass( 'success_style' );
							$license_status_field.val( data.license_user_status );

							$premium_pills.removeClass( 'disabled' );
							$premium_pills_content.removeClass( 'opacity-50' );
							$premium_badge.addClass( 'd-none' );

							if( data.lc_hide_local == 1 )
							{
								$license_code_wrapper.addClass( 'd-none' );
								$hide_code_wrapper.removeClass( 'd-none' );
								$lc_owner_description.html( data.lc_owner_description );

								if( data.lc_owner_email )
								{
									$lc_owner_email.html( '<a href="mailto:'+ data.lc_owner_email + '" target="blank">' + data.lc_owner_email +'</a>' );
									$lc_owner_email_wrapper.removeClass( 'd-none' );
								}
								else
								{
									$lc_owner_email_wrapper.addClass( 'd-none' );
								}

								if( data.lc_owner_email )
								{
									$lc_owner_website.html( '<a href="'+ data.lc_owner_email + '" target="blank">' + data.lc_owner_website +'</a>' );

									$lc_owner_website_wrapper.removeClass( 'd-none' );
								}
								else
								{
									$lc_owner_website_wrapper.addClass( 'd-none' );
								}
							}
							else
							{
								$license_code_wrapper.removeClass( 'd-none' );
								$hide_code_wrapper.addClass( 'd-none' );

								$lc_owner_email_wrapper.addClass( 'd-none' );
								$lc_owner_website_wrapper.addClass( 'd-none' );

								$lc_owner_description.html( '' );
								$lc_owner_email.html( '' );
								$lc_owner_website.html( '' );
							}
						}
						else
						{
							$license_status_field.removeClass( 'success_style' ).addClass( 'warning_style' );
							$license_status_field.val( data.license_user_status );

							$premium_pills.addClass( 'disabled' );
							$premium_pills_content.addClass( 'opacity-50' );
							$premium_badge.removeClass( 'd-none' );


							$license_code_wrapper.removeClass( 'd-none' );
							$hide_code_wrapper.addClass( 'd-none' );

							$lc_owner_email_wrapper.addClass( 'd-none' );
							$lc_owner_website_wrapper.addClass( 'd-none' );

							$lc_owner_description.html( '' );
							$lc_owner_email.html( '' );
							$lc_owner_website.html( '' );
						}
					},
					error:function( err )
					{
						$submit_button.css( {'opacity':'1','cursor':'pointer'} ).prop( 'disabled', false );
						$( '.apix_wait' ).fadeOut();

						apix_pupup_notify.error( apix_settings_error_message_text );

						console.error( err );
					}
				});
			});

			var $save_trigger_buttons = $( '.fake-save-button' );
			if( $save_trigger_buttons.length )
			{
				$save_trigger_buttons.on( 'click', function( e ){
					e.preventDefault();
					$( '#mapx-save-button' ).trigger( 'click' );
				});
			}
		});

	 });
})( jQuery );


jQuery(document).ready(function ()
{

});