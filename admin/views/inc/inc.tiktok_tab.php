<div class="consistent-box">
	<h4 class="mb-3">
		<i class="fa-brands fa-tiktok"></i>
		<?php _e( 'TikTok','myagilepixel' ); ?>
	</h4>

	<div class="row mb-3">
		<label for="tiktok_enable_field" class="col-sm-5 col-form-label">
			<?php _e( 'Enable TikTok tracking proxification','myagilepixel' ); ?>
		</label>

		<div class="col-sm-5">
			<select id="tiktok_enable_field"
					class="form-select hideShowInput"
					name="tiktok_enable_field"
					data-hide-show-ref="tiktok_enable">
				<option value="0" <?php if( $the_options['tiktok_enable']=='0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

				<option value="1" <?php if( $the_options['tiktok_enable']=='1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
			</select>


			<div class="form-text">
				<?php
					_e("Enable TikTok tracking proxification",'myagilepixel' );
				?>.
			</div>

		</div>
	</div> <!-- row -->

	<div class="tiktok_enable displayNone" data-value="1">

		<div class="row mb-3">
			<label for="tiktok_pixel_id_field" class="col-sm-5 col-form-label">
				<?php _e( 'Pixel ID','myagilepixel' ); ?> (*)
			</label>

			<div class="col-sm-5">
				<input type="text" id="tiktok_pixel_id_field" class="form-control" name="tiktok_pixel_id_field" value="<?php echo esc_attr( $the_options['tiktok_pixel_id'] ); ?>">
				<div class="form-text">
					<?php
						_e("Enter the TikTok pixel ID here <small>(consisting of 16 digits)</small>",'myagilepixel' );
					?>.
				</div>
			</div>
		</div> <!-- row -->


		<div class="row mb-3">
			<label for="tiktok_access_token_field" class="col-sm-5 col-form-label">
				<?php _e( 'Access Token','myagilepixel' ); ?> (*)
			</label>

			<div class="col-sm-5">
				<input type="text" id="tiktok_access_token_field" class="form-control" name="tiktok_access_token_field" value="<?php echo esc_attr( $the_options['tiktok_access_token'] ); ?>">
				<div class="form-text">
					<?php
						_e("Enter the Access Token here",'myagilepixel' );
					?>.
				</div>
			</div>
		</div> <!-- row -->


		<div class="row mb-3">
			<label for="tiktok_test_event_code_field" class="col-sm-5 col-form-label">
				<?php _e( 'Test event code','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">
				<input type="text" id="tiktok_test_event_code_field" class="form-control" name="tiktok_test_event_code_field" value="<?php echo esc_attr( $the_options['tiktok_test_event_code'] ); ?>">
				<div class="form-text">
					<?php
						_e("Enter here the code to send test events to TikTok",'myagilepixel' );
					?>.
				</div>
			</div>
		</div> <!-- row -->


		<div class="row mb-3">
			<label for="tiktok_settings_preset_field" class="col-sm-5 col-form-label">
				<?php _e( 'Data proxification level','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">
				<input type="range" class="preset-input-range" data-set-ref="tiktok" id="tiktok_settings_preset_field" name="tiktok_settings_preset_field" step="1" min="1" max="4" value="<?php echo esc_attr( $the_options['tiktok_settings_preset'] ); ?>">

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
						_e("Change the proxification level by choosing from the available presets, or set the bar to zero and customize the parameters",'myagilepixel' );
					?>.
				</div>
			</div>

		</div> <!-- row -->


		<div class="row mb-3">
			<label for="tiktok_anonymize_ip_field" class="col-sm-5 col-form-label">
				<?php _e( 'Use anonymous IP','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">
				<select id="tiktok_anonymize_ip_field"
							class="form-select"
							name="tiktok_anonymize_ip_field"
							data-set="tiktok"
							data-preset-level="3"
							data-last-preset-customizable-value="2">

					<option value="0" <?php if( $the_options['ganalytics_anonymize_ip'] == '0' ) echo 'selected'; ?>> <?php _e( 'Off','myagilepixel' ); ?></option>
					<option value="1" <?php if( $the_options['tiktok_anonymize_ip'] == '1' ) echo 'selected'; ?>> <?php _e( 'Always active','myagilepixel' ); ?></option>
				</select>

			</div>
		</div> <!-- row -->


		<div class="row mb-3">
			<label for="tiktok_remove_user_agent_field" class="col-sm-5 col-form-label">
				<?php _e( 'Remove user agent information','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">

				<select id="tiktok_remove_user_agent_field"
							class="form-select hideShowInput"
							name="tiktok_remove_user_agent_field"
							data-set="tiktok"
							data-preset-level="3"
							data-hide-show-ref="tiktok_remove_user_agent">
					<option value="0" <?php if( $the_options['tiktok_remove_user_agent'] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

					<option value="1" <?php if( $the_options['tiktok_remove_user_agent'] == '1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
				</select>


				<div class="form-text">
					<?php
						_e("Enable to remove the user's User Agent information",'myagilepixel' );
					?>.
				</div>

			</div>
		</div> <!-- row -->

		<div class="row mb-3 tiktok_remove_user_agent" data-value="1">
			<label for="tiktok_send_desktop_mobile_user_agent_field" class="col-sm-5 col-form-label">
				<?php _e( 'Track anonymously information about mobile/desktop users','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">

				<select id="tiktok_send_desktop_mobile_user_agent_field"
							class="form-select"
							name="tiktok_send_desktop_mobile_user_agent_field">
					<option value="0" <?php if( $the_options['tiktok_send_desktop_mobile_user_agent'] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

					<option value="1" <?php if( $the_options['tiktok_send_desktop_mobile_user_agent'] ==' 1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
				</select>


				<div class="form-text">
					<?php
						_e( "In order to distinguish mobile/desktop users, send appropriate and anonymous user agent data",'myagilepixel' );
					?>.
				</div>

			</div>
		</div> <!-- row -->


		<div class="row mb-3">
			<label for="tiktok_remove_click_id_field" class="col-sm-5 col-form-label">
				<?php _e( 'Remove "ClickID" Tracking Information','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">

				<select id="tiktok_remove_click_id_field"
							class="form-select"
							name="tiktok_remove_click_id_field"
							data-set="tiktok"
							data-preset-level="3">
					<option value="0" <?php if( $the_options['tiktok_remove_click_id'] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

					<option value="1" <?php if( $the_options['tiktok_remove_click_id'] == '1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
				</select>


				<div class="form-text">
					<?php
						_e("Enable to remove user click tracking information",'myagilepixel' );
					?>.
				</div>

			</div>
		</div> <!-- row -->


		<div class="row mb-3">
			<label for="tiktok_remove_utm_tag_field" class="col-sm-5 col-form-label">
				<?php _e( 'Remove UTM tag','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">

				<select id="tiktok_remove_utm_tag_field"
							class="form-select"
							name="tiktok_remove_utm_tag_field"
							data-set="tiktok"
							data-preset-level="4">
					<option value="0" <?php if( isset( $the_options['tiktok_remove_utm_tag'] ) && $the_options['tiktok_remove_utm_tag'] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

					<option value="1" <?php if( isset( $the_options['tiktok_remove_utm_tag'] ) && $the_options['tiktok_remove_utm_tag'] == '1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
				</select>

				<div class="form-text">
					<?php
						_e("Enable to remove UTM tags from the URL",'myagilepixel' );
					?>.
				</div>

			</div>
		</div> <!-- row -->


		<div class="row mb-3">
			<label for="tiktok_enable_session_life_cookie_duration_field" class="col-sm-5 col-form-label">
				<?php _e("Use session cookies for user data storage",'myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">

				<select id="tiktok_enable_session_life_cookie_duration_field"
							class="form-select"
							name="tiktok_enable_session_life_cookie_duration_field"
							data-set="tiktok"
							data-preset-level="4">
					<option value="0" <?php if( isset( $the_options['tiktok_enable_session_life_cookie_duration'] ) && $the_options['tiktok_enable_session_life_cookie_duration'] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

					<option value="1" <?php if( isset( $the_options['tiktok_enable_session_life_cookie_duration'] ) && $the_options['tiktok_enable_session_life_cookie_duration'] =='1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
				</select>

				<div class="form-text">
					<?php
						_e("Enable to minimize the duration of cookies for user recognition",'myagilepixel' );
					?>.
				</div>


			</div>
		</div> <!-- row -->


	</div>
</div>