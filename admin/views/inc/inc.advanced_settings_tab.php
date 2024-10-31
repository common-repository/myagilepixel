<div class="consistent-box">
	<h4 class="mb-3">
		<i class="fa-solid fa-sliders-up"></i>
		<?php _e( 'Advanced Settings','myagilepixel' ); ?>
	</h4>

	<div class="row mb-3">
		<label for="forced_auto_update_field" class="col-sm-5 col-form-label">
			<?php _e( 'Enable plugin auto update','myagilepixel' ); ?>
		</label>

		<div class="col-sm-7">

			<div class="styled_radio d-inline-flex">
				<div class="round d-flex me-4">

					<input type="hidden" name="forced_auto_update_field" value="false" id="forced_auto_update_field_no">

					<input name="forced_auto_update_field" type="checkbox" value="true" id="forced_auto_update_field" <?php checked( $the_options['forced_auto_update'], true); ?>>

					<label for="forced_auto_update_field" class="me-3 label-checkbox"></label>

					<label for="forced_auto_update_field">
						<?php _e( 'Yes, I would like to turn on automatic plugin updates.', 'myagilepixel' ); ?>
					</label>
				</div>
			</div> <!-- ./ styled_radio -->


		</div>
	</div> <!-- row -->


	<div class="row mb-3">
		<label for="internal_debug_field" class="col-sm-5 col-form-label">
			<?php _e( 'Enable debug bar for administrators','myagilepixel' ); ?>
		</label>

		<div class="col-sm-5">
			<select id="internal_debug_field"
					class="form-select"
					name="internal_debug_field">
				<option value="0" <?php selected( $the_options['internal_debug'], 0 ); ?>> <?php _e( 'Disable debug','myagilepixel' ); ?></option>
				<option value="1" <?php selected( $the_options['internal_debug'], 1 ); ?>> <?php _e( 'Enable debug','myagilepixel' ); ?></option>
			</select>


			<div class="form-text">
				<?php
					_e( "Enable this options, then visit your website in order to check the data being passed via proxification",'myagilepixel' );
				?>.
			</div>


		</div>
	</div> <!-- row -->

	<div class="row mb-3">
		<label for="block_script_using_cookieshield_field" class="col-sm-5 col-form-label">
			<?php _e( 'Non-compliant script blocking','myagilepixel' ); ?>
		</label>


		<?php

		$is_myagileprivacy_activated = MyAgilePixel::is_myagileprivacy_activated();

		if( $is_myagileprivacy_activated == 0 ):

		?>

			<div class="col-sm-5">
				<select id="block_script_using_cookieshield_field"
						class="form-select"
						name="block_script_using_cookieshield_field">
					<option value="0" <?php if( $the_options['block_script_using_cookieshield'] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

					<option value="1" <?php if( $the_options['block_script_using_cookieshield'] == '1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
				</select>


				<div class="form-text">
					<?php
						_e( "Keep this option active to block any non-compliant versions of Facebook, Google Analytics or Tik Tok scripts that may be present on the site.",'myagilepixel' );
					?>
				</div>

			</div>

		<?php
		else:
		?>
			<div class="col-sm-5">
			   <div class="form-text">
					<?php
						_e( "You are already protected by My Agile Privacy",'myagilepixel' );
					?>.
				</div>
			</div>
		<?php
		endif;
		?>

	</div> <!-- row -->

	<div class="row mb-3">
		<label for="woocommerce_enable_field" class="col-sm-5 col-form-label">
			<?php _e( 'Enable WooCommerce events','myagilepixel' ); ?>
		</label>


		<?php

		$is_woocommerce_activated = MyAgilePixel::is_woocommerce_activated();

		if( $is_woocommerce_activated ):

		?>

			<div class="col-sm-5">
				<select id="woocommerce_enable_field"
						class="form-select"
						name="woocommerce_enable_field">
					<option value="0" <?php if( $the_options['woocommerce_enable'] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

					<option value="1" <?php if( $the_options['woocommerce_enable'] == '1' ) echo 'selected'; ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
				</select>


				<div class="form-text">
					<?php
						_e( "Enable WooCommerce events integration", 'myagilepixel' );
					?>
				</div>

			</div>

		<?php
		else:
		?>
			<div class="col-sm-5">
			   <div class="form-text">
					<?php
						_e( "WooCommerce is not active on this website.", 'myagilepixel' );
					?>
				</div>
			</div>
		<?php
		endif;
		?>

	</div> <!-- row -->

	<div class="row mb-3">
		<label for="" class="col-sm-5 col-form-label">
			<?php _e( 'Prevent data tracking for those IPs','myagilepixel' ); ?>
		</label>


		<div class="col-sm-5">
			<div id="mapx-ips-list-container" class="row dynamic_fields_container">

				<?php
					$the_array = json_decode( $the_options['blacklisted_ip'] , true );

					if( $the_array ):
						foreach( $the_array as $the_item ):
							if( $the_item != '' ):
				?>
							<div class="mapx-ip-entry mapx-dynamic-entry mb-2 col-sm-12">
								<div class="input-group">
									<input class="form-control is-valid" name="blacklisted_ip_field[]" value="<?php echo esc_attr( $the_item ); ?>" type="text" autocomplete="off" />
									<button class="btn btn-danger mapx-btn-remove" type="button"><i class="fa-solid fa-minus"></i></button>
								</div>
							</div>
				<?php
							endif;
						endforeach;
					endif;
				?>

				<div class="mapx-ip-entry mapx-dynamic-entry mb-2 col-sm-12">
					<div class="input-group">
						<input class="form-control" name="blacklisted_ip_field[]" type="text" autocomplete="off" />
						<button class="btn btn-success mapx-btn-add" type="button"><i class="fa-solid fa-plus"></i></button>
					</div>
				</div>
			</div>
			<div class="form-text">
				<?php

					_e( "Your IP address seems to be:",'myagilepixel' );

					echo " ".esc_html( $current_client_ip );

					?>.
			</div>

		</div>

	</div> <!-- row -->

	<div class="row mb-3">
		<label for="" class="col-sm-5 col-form-label">
			<?php _e( 'Events blacklist','myagilepixel' ); ?>
		</label>


		<div class="col-sm-5">
			<div id="mapx-events-list-container" class="row dynamic_fields_container">

				<?php
					$the_array = json_decode( $the_options['blacklisted_events'] , true );

					if( $the_array ):
						foreach( $the_array as $the_item ):

							if( $the_item != '' ):
				?>
							<div class="mapx-event-entry map-not-unsafe-string mapx-dynamic-entry mb-2 col-sm-12">
								<div class="input-group">
									<input class="form-control is-valid" name="blacklisted_events_field[]" value="<?php echo esc_attr( $the_item ); ?>" type="text" autocomplete="off" />
									<button class="btn btn-danger mapx-btn-remove" type="button"><i class="fa-solid fa-minus"></i></button>
								</div>
							</div>
				<?php
							endif;
						endforeach;
					endif;
				?>

				<div class="mapx-event-entry map-not-unsafe-string mapx-dynamic-entry mb-2 col-sm-12">
					<div class="input-group">
						<input class="form-control" name="blacklisted_events_field[]" type="text" autocomplete="off" />
						<button class="btn btn-success mapx-btn-add" type="button"><i class="fa-solid fa-plus"></i></button>
					</div>
				</div>
			</div>
			<div class="form-text">
				<?php

					_e( "Add the events name you would like not to send.",'myagilepixel' );

				?>
			</div>

		</div>

	</div> <!-- row -->


	<div class="row mb-3">
		<label for="compatibility_mode_field" class="col-sm-5 col-form-label">
			<?php _e( 'Enable compatible mode','myagilepixel' ); ?>
		</label>

		<div class="col-sm-7">

			<div class="styled_radio d-inline-flex">
				<div class="round d-flex me-4">

					<input type="hidden" name="compatibility_mode_field" value="false" id="compatibility_mode_field_no">

					<input name="compatibility_mode_field" type="checkbox" value="true" id="compatibility_mode_field" <?php checked( $the_options['compatibility_mode'], true); ?>>

					<label for="compatibility_mode_field" class="me-3 label-checkbox"></label>

					<label for="compatibility_mode_field">
						<?php _e( 'Enable this option if event tracking is not detected or if our support team has advised you to do so.', 'myagilepixel' ); ?>
					</label>
				</div>
			</div> <!-- ./ styled_radio -->


		</div>
	</div> <!-- row -->

</div>