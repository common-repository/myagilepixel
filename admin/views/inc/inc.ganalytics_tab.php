<div class="consistent-box">
	<h4 class="mb-3">
		<i class="fa-brands fa-google"></i>
		<?php _e( 'Google Analytics 4','myagilepixel' ); ?>
	</h4>

	<div class="row mb-3">
		<label for="ganalytics_enable_field" class="col-sm-5 col-form-label">
			<?php _e( 'Enable Google Analytics tracking proxification','myagilepixel' ); ?>
		</label>

		<div class="col-sm-5">
			<select id="ganalytics_enable_field"
					class="form-select hideShowInput"
					name="ganalytics_enable_field"
					data-hide-show-ref="ganalytics_enable">
				<option value="0" <?php if( $the_options['ganalytics_enable'] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

				<option value="1" <?php if( $the_options['ganalytics_enable'] == '1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
			</select>


			<div class="form-text">
				<?php
					_e( "Enable Google Analytics tracking proxification",'myagilepixel' );
				?>.
			</div>

		</div>
	</div> <!-- row -->

	<div class="ganalytics_enable displayNone" data-value="1">
		<div class="row mb-3" >
			<label for="ganalytics_measurement_id_field" class="col-sm-5 col-form-label">
				<?php _e( 'Measurement ID','myagilepixel' ); ?> (*)
			</label>

			<div class="col-sm-5">
				<input type="text"
						id="ganalytics_measurement_id_field"
						class="form-control"
						name="ganalytics_measurement_id_field"
						value="<?php echo esc_attr( $the_options['ganalytics_measurement_id'] ); ?>">
				<div class="form-text">
					<?php
						_e( "Enter the Analytics property ID here <small>(e.g. G-XX0X00XX00)</small>",'myagilepixel' );
					?>.
				</div>
			</div>
		</div> <!-- row -->


		<div class="row mb-3">
			<label for="ganalytics_debug_mode_field" class="col-sm-5 col-form-label">
				<?php _e( 'Send debug information to Google Analytics','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">

				<select id="ganalytics_debug_mode_field"
							class="form-select"
							name="ganalytics_debug_mode_field">
					<option value="0" <?php if( $the_options['ganalytics_debug_mode'] == '0' ) echo 'selected'; ?>> <?php _e( 'Do not send','myagilepixel' ); ?></option>

					<option value="1" <?php if( $the_options['ganalytics_debug_mode'] == '1' ) echo 'selected'; ?>> <?php _e( 'Send','myagilepixel' ); ?></option>
				</select>


				<div class="form-text">
					<?php
						_e( "Select if you want to activate the debugging mode of the sent data",'myagilepixel' );
					?>.
				</div>

			</div>
		</div> <!-- row -->


		<div class="row mb-3">
			<label for="ganalytics_settings_preset_field" class="col-sm-5 col-form-label">
				<?php _e( 'Data proxification level','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">
				<input type="range" class="preset-input-range" data-set-ref="ganalytics" id="ganalytics_settings_preset_field" name="ganalytics_settings_preset_field" step="1" min="1" max="4" value="<?php echo esc_attr( $the_options['ganalytics_settings_preset'] ); ?>">

				<div class="alert alert-warning d-none" data-preset-value="1">
					<i class="fa-regular fa-pen-ruler"></i> <?php _e( 'Customized proxification level','myagilepixel' ); ?>
				</div>

				<div class="alert alert-light d-none" data-preset-value="2">
					<i class="fa-solid fa-star"></i>
					<?php _e( 'Low proxification level','myagilepixel' ); ?>
				</div>

				<div class="alert alert-info d-none" data-preset-value="3">
					<i class="fa-solid fa-star"></i>
					<i class="fa-solid fa-star"></i>
					<?php _e( 'Medium proxification level','myagilepixel' ); ?>
				</div>

				<div class="alert alert-primary d-none"  data-preset-value="4">
					<i class="fa-solid fa-star"></i>
					<i class="fa-solid fa-star"></i>
					<i class="fa-solid fa-star"></i>
					<?php _e( 'High proxification level','myagilepixel' ); ?>
				</div>

				<div class="form-text pb-2">
					<?php
						_e( "Change the proxification level by choosing from the available presets, or set the bar to zero and customize the parameters",'myagilepixel' );
					?>.
				</div>
			</div>

		</div> <!-- row -->


		<div class="row mb-3">
			<label for="ganalytics_anonymize_ip_field" class="col-sm-5 col-form-label">
				<?php _e( 'Use anonymous IP','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">
				<select id="ganalytics_anonymize_ip_field"
						class="form-select"
						name="ganalytics_anonymize_ip_field"
						data-set="ganalytics"
						data-preset-level="3"
						data-last-preset-customizable-value="2">


					<option value="0" <?php if( $the_options['ganalytics_anonymize_ip'] == '0' ) echo 'selected'; ?>> <?php _e( 'Off','myagilepixel' ); ?></option>
					<option value="1" <?php if( $the_options['ganalytics_anonymize_ip'] == '1' ) echo 'selected'; ?>> <?php _e( 'Always active','myagilepixel' ); ?></option>
				</select>

			</div>
		</div> <!-- row -->

		<div class="row mb-3">
			<label for="ganalytics_remove_user_agent_field" class="col-sm-5 col-form-label">
				<?php _e( 'Remove user agent information','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">

				<select id="ganalytics_remove_user_agent_field"
							class="form-select hideShowInput"
							name="ganalytics_remove_user_agent_field"
							data-set="ganalytics"
							data-preset-level="3"
							data-hide-show-ref="ganalytics_remove_user_agent">
					<option value="0" <?php if( $the_options['ganalytics_remove_user_agent'] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

					<option value="1" <?php if( $the_options['ganalytics_remove_user_agent'] ==' 1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
				</select>


				<div class="form-text">
					<?php
						_e( "Enable to remove the user's User Agent information",'myagilepixel' );
					?>.
				</div>

			</div>
		</div> <!-- row -->

		<div class="row mb-3 ganalytics_remove_user_agent" data-value="1">
			<label for="ganalytics_send_desktop_mobile_user_agent_field" class="col-sm-5 col-form-label">
				<?php _e( 'Track anonymously information about mobile/desktop users','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">

				<select id="ganalytics_send_desktop_mobile_user_agent_field"
							class="form-select"
							name="ganalytics_send_desktop_mobile_user_agent_field">
					<option value="0" <?php if( $the_options['ganalytics_send_desktop_mobile_user_agent'] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

					<option value="1" <?php if( $the_options['ganalytics_send_desktop_mobile_user_agent'] ==' 1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
				</select>


				<div class="form-text">
					<?php
						_e( "In order to distinguish mobile/desktop users, send appropriate and anonymous user agent data",'myagilepixel' );
					?>.
				</div>

			</div>
		</div> <!-- row -->


		<div class="row mb-3">
			<label for="ganalytics_remove_screen_resolution_field" class="col-sm-5 col-form-label">
				<?php _e( 'Remove screen info','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">

				<select id="ganalytics_remove_screen_resolution_field"
							class="form-select"
							name="ganalytics_remove_screen_resolution_field"
							data-set="ganalytics"
							data-preset-level="3">
					<option value="0" <?php if( $the_options['ganalytics_remove_screen_resolution'] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

					<option value="1" <?php if( $the_options['ganalytics_remove_screen_resolution'] == '1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
				</select>


				<div class="form-text">
					<?php
						_e( "Enable to remove user screen information",'myagilepixel' );
					?>.
				</div>

			</div>
		</div> <!-- row -->


		<div class="row mb-3">
			<label for="ganalytics_remove_click_id_field" class="col-sm-5 col-form-label">
				<?php _e( 'Remove "ClickID" Tracking Information','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">

				<select id="ganalytics_remove_click_id_field"
							class="form-select"
							name="ganalytics_remove_click_id_field"
							data-set="ganalytics"
							data-preset-level="3">
					<option value="0" <?php if( $the_options['ganalytics_remove_click_id'] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

					<option value="1" <?php if( $the_options['ganalytics_remove_click_id'] == '1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
				</select>


				<div class="form-text">
					<?php
						_e( "Enable to remove user click tracking information",'myagilepixel' );
					?>.
				</div>

			</div>
		</div> <!-- row -->


		<div class="row mb-3">
			<label for="ganalytics_remove_utm_tag_field" class="col-sm-5 col-form-label">
				<?php _e( 'Remove UTM tag','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">

				<select id="ganalytics_remove_utm_tag_field"
							class="form-select"
							name="ganalytics_remove_utm_tag_field"
							data-set="ganalytics"
							data-preset-level="4">
					<option value="0" <?php if( $the_options['ganalytics_remove_utm_tag'] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

					<option value="1" <?php if( $the_options['ganalytics_remove_utm_tag'] == '1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
				</select>

				<div class="form-text">
					<?php
						_e( "Enable to remove UTM tags from the URL",'myagilepixel' );
					?>.
				</div>


			</div>
		</div> <!-- row -->


		<div class="row mb-3">
			<label for="ganalytics_remove_referrer_field" class="col-sm-5 col-form-label">
				<?php _e( 'Remove external referrals','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">

				<select id="ganalytics_remove_referrer_field"
							class="form-select"
							name="ganalytics_remove_referrer_field"
							data-set="ganalytics"
							data-preset-level="4">
					<option value="0" <?php if( $the_options['ganalytics_remove_referrer'] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

					<option value="1" <?php if( $the_options['ganalytics_remove_referrer'] == '1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
				</select>



				<div class="form-text">
					<?php
						_e( "Enable to remove external referral information",'myagilepixel' );
					?>.
				</div>

			</div>
		</div> <!-- row -->


		<div class="row mb-3">
			<label for="ganalytics_enable_session_life_cookie_duration_field" class="col-sm-5 col-form-label">
				<?php _e( "Use session cookies for user data storage",'myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">

				<select id="ganalytics_enable_session_life_cookie_duration_field"
							class="form-select"
							name="ganalytics_enable_session_life_cookie_duration_field"
							data-set="ganalytics"
							data-preset-level="4">
					<option value="0" <?php if( $the_options['ganalytics_enable_session_life_cookie_duration']=='0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

					<option value="1" <?php if( $the_options['ganalytics_enable_session_life_cookie_duration']=='1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
				</select>

				<div class="form-text">
					<?php
						_e( "Enable to minimize the duration of cookies for user recognition",'myagilepixel' );
					?>.
				</div>


			</div>
		</div> <!-- row -->

	</div>
</div>

<div class="consistent-box ganalytics_enable displayNone" data-value="1">
	<h4 class="mb-2">
		<?php _e( 'Enable Google Analytics Advanced Features', 'myagilepixel' ); ?>
	</h4>

	<div class="row mb-12">

		<div class="col-sm-12">

			<div class="row">

				<div class="mb-2">
				<?php
					_e( "This feature allows you to enable support for Consent Mode v2 and Google Ads Conversion tracking.",'myagilepixel' );
				?>
				</div>
			</div>

			<div class="row">

				<div class="styled_radio d-inline-flex">
					<div class="round d-flex me-4">

						<input type="hidden" name="use_ga_advanced_features_field" value="false" id="use_ga_advanced_features_no">

						<input name="use_ga_advanced_features_field" type="checkbox" value="true" id="use_ga_advanced_features_field" <?php checked( $the_options['use_ga_advanced_features'], true); ?>>

						<label for="use_ga_advanced_features_field" class="me-3 label-checkbox"></label>

						<label for="use_ga_advanced_features_field">
							<?php _e( 'I understand and accept that by enabling this option, this website may not fully comply with GDPR . Enable Google Analytics Advanced Features', 'myagilepixel' ); ?>
						</label>
					</div>
				</div> <!-- ./ styled_radio -->

			</div>

		</div>

	</div> <!-- row -->


</div>

<div class="consistent-box __ganalytics_enable displayNone" data-value="1">
	<h4 class="mb-2">
		<?php _e( 'Enable Consent Mode <small class="mapx_highlight">(Beta)</small>', 'myagilepixel' ); ?>
	</h4>


	<div class="row mb-12">

		<div class="col-sm-12">

			<div class="row">

				<div class="mb-2">
				<?php
					_e( "This feature allows you to enable support for Consent Mode, which helps you track, in a completely anonymous and untraceable manner, the access and events of users who have not provided explicit consent for anonymous use of GA4 .",'myagilepixel' );
				?>
				</div>
			</div>

			<?php

			$is_myagileprivacy_activated = MyAgilePixel::is_myagileprivacy_activated();

			if( $is_myagileprivacy_activated == 1 ):

			?>

				<div class="row">

					<div class="styled_radio d-inline-flex">
						<div class="round d-flex me-4">

							<input type="hidden" name="consent_mode_requested_field" value="false" id="consent_mode_requested_field_no">

							<input name="consent_mode_requested_field" type="checkbox" value="true" id="consent_mode_requested_field" <?php checked( $the_options['consent_mode_requested'], true); ?> class="hideShowInput" data-hide-show-ref="consent_mode_requested_wrapper">

							<label for="consent_mode_requested_field" class="me-3 label-checkbox"></label>

							<label for="consent_mode_requested_field">
								<?php _e( 'I understand and accept that by enabling this option, this website may not fully comply with GDPR and specifically with respect to fully complying with the rules regarding the transfer of data to the United States.', 'myagilepixel' ); ?>
							</label>
						</div>
					</div> <!-- ./ styled_radio -->

				</div>

				<div class="row consent_mode_requested_wrapper displayNone">

					<div class="styled_radio d-inline-flex">
						<div class="round d-flex me-4">

							<input type="hidden" name="consent_mode_enabled_field" value="false" id="consent_mode_enabled_field_no">

							<input name="consent_mode_enabled_field" type="checkbox" value="true" id="consent_mode_enabled_field" <?php checked( $the_options['consent_mode_enabled'], true); ?>>

							<label for="consent_mode_enabled_field" class="me-3 label-checkbox"></label>

							<label for="consent_mode_enabled_field">
								<?php _e( 'I wish to enable Consent Mode and send tracking data in an anonymous form, even in the absence of explicit consent from the user.', 'myagilepixel' ); ?>
							</label>
						</div>
					</div> <!-- ./ styled_radio -->

				</div>

			<?php
			else:
			?>

			   <div class="form-text">
					<?php
						_e( "This function is only available if you have also installed the My Agile Privacy plugin and activated a Premium license key.",'myagilepixel' );
					?>
				</div>

			<?php
			endif;
			?>


		</div>

	</div> <!-- row -->


</div>