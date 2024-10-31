<div class="consistent-box">
	<h4 class="mb-3">
		<i class="fa-light fa-gears"></i>
		<?php _e( 'General settings','myagilepixel' ); ?>
	</h4>

	<div class="row mb-3">
		<label for="general_plugin_active_field" class="col-sm-5 col-form-label">
			<?php _e( 'Plugin status','myagilepixel' ); ?>:
		</label>

		<div class="col-sm-5">
			<select id="general_plugin_active_field"
					class="form-select"
					name="general_plugin_active_field">
				<option value="0" <?php selected( $the_options['general_plugin_active'], 0 ); ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>
				<option value="1" <?php selected( $the_options['general_plugin_active'], 1 ); ?>> <?php _e( 'Enable','myagilepixel' ); ?></option>
			</select>


			<div class="form-text">
				<?php
					_e( "Set the plugin status to active to enable its features",'myagilepixel' );
				?>.
			</div>


		</div>
	</div> <!-- row -->


	<div class="row mb-3">
		<label for="license_status_field" class="col-sm-5 col-form-label">
			<?php _e( 'License status','myagilepixel' ); ?>:
		</label>

		<div class="col-sm-5">
			<input type="text"
					id="license_status_field"
					class="form-control <?php if( $the_options['license_valid'] && !$the_options['grace_period'] ) echo esc_attr( 'success_style' ); else echo esc_attr( 'warning_style' ); ?>"
					disabled
					value="<?php echo esc_attr( $the_options['license_user_status'] ); ?>">

			<div class="form-text">
				<?php
					_e( "The status of your license",'myagilepixel' );
				?>.
			</div>

		</div>
	</div> <!-- row -->


	<div class="license_code_wrapper <?php if( $rconfig && isset( $rconfig['lc_hide_local'] ) && $rconfig['lc_hide_local'] == 1 ) echo 'd-none'; ?>">

		<div class="row mb-3">
			<label for="license_code_field" class="col-sm-5 col-form-label">
				<?php _e( 'License key','myagilepixel' ); ?>:
			</label>

			<div class="col-sm-5">
				<input type="text"
						id="license_code_field"
						class="form-control"
						name="license_code_field"
						value="<?php echo esc_attr( $the_options['license_code'] ); ?>">

				<div class="form-text">
					<?php
						_e( "Enter your license key here",'myagilepixel' );
					?>.

                            <?php

                                if( isset( $the_options ) && isset( $the_options['license_code'] ) && $the_options['license_code'] ):
                            ?>

                            <br>
                            <?php
                                _e( "Would you like to verify the status of your subscription, download invoices, or carry out other administrative tasks?",'myagilepixel' );
                            ?><br>

                            <a href="https://areaprivata.myagileprivacy.com/" target="blank"><?php _e( "Click here to access your user area.",'myagilepixel' );?></a>

                            <?php
                                endif;
                            ?>


				</div>


			</div>
		</div> <!-- row -->

	</div>

	<div class="hide_code_wrapper <?php if( !( $rconfig && isset( $rconfig['lc_hide_local'] ) && $rconfig['lc_hide_local'] == 1 ) ) echo 'd-none'; ?>">

		<div class="row mb-3">
			<label for="license_code_field" class="col-sm-5 col-form-label">
				<?php _e( 'Reseller info','myagilepixel' ); ?>:
			</label>

			<div class="col-sm-6">

				<h6>
					<?php _e( 'Your license key is provided by','myagilepixel' ); ?> <span class="lc_owner_description"><?php if( isset( $rconfig ) && isset( $rconfig['lc_owner_description'] ) ) echo esc_html( $rconfig['lc_owner_description'] ); ?></span> .
				</h6>
				<div class="my-3">
					<strong>
						<?php _e( 'For further information you can check:','myagilepixel' ); ?>
					</strong>
					<br>

					<span class="lc_owner_website_wrapper <?php if( !( isset( $rconfig ) && isset( $rconfig['lc_owner_website'] ) ) ) echo 'd-none'; ?>">
						<?php _e( 'Reseller Website:','myagilepixel' ); ?> <span class="lc_owner_website"><?php if( isset( $rconfig ) && isset( $rconfig['lc_owner_website'] ) ) echo '<a target="blank" href="'.esc_attr( $rconfig['lc_owner_website'] ).'">'.$rconfig['lc_owner_website'].'</a>'; ?></span>
					</span>
					<br>

					<span class="lc_owner_email_wrapper  <?php if( !( isset( $rconfig ) && isset( $rconfig['lc_owner_email'] ) ) ) echo 'd-none'; ?>">
						<?php _e( 'Reseller Mail:','myagilepixel' ); ?> <span class="lc_owner_email"><?php if( isset( $rconfig ) && isset( $rconfig['lc_owner_email'] ) ) echo '<a href="mailto:'.esc_attr( $rconfig['lc_owner_email'] ).'">'.$rconfig['lc_owner_email'].'</a>'; ?></span>
					</span>
				</div>


				<button class="button-agile btn-md changeLicenseCode"><?php _e( 'Change license code','myagilepixel' ); ?></button>

			</div>
		</div> <!-- row -->


	</div>


	<div class="row mb-3">
		<label for="general_interface_with_field" class="col-sm-5 col-form-label">
			<?php _e( 'Connect with Cookie / GDPR plugin','myagilepixel' ); ?>:
		</label>

		<div class="col-sm-5">
			<select id="general_interface_with_field"
					class="form-select"
					name="general_interface_with_field">
					<option value="none" <?php selected( $the_options['general_interface_with'], 'none' ); ?>> <?php _e( 'No plugin - manually integration via javascript code', 'myagilepixel' ); ?></option>
					<option value="myagileprivacy" <?php selected( $the_options['general_interface_with'], 'myagileprivacy' ); ?>> My Agile Privacy</option>
					<option value="cookiebot" <?php selected( $the_options['general_interface_with'], 'cookiebot' ); ?>> Cookiebot</option>
					<option value="iubenda" <?php selected( $the_options['general_interface_with'], 'iubenda' ); ?>> Iubenda</option>
					<option value="gdpr_cookie_consent" <?php selected( $the_options['general_interface_with'], 'gdpr_cookie_consent' ); ?>> GDPR Cookie Consent / CookieYes</option>
					<option value="complianz" <?php selected( $the_options['general_interface_with'], 'complianz' ); ?>> Complianz</option>
					<option value="none_and_do_no_not_ask_consent" <?php selected( $the_options['general_interface_with'], 'none_and_do_no_not_ask_consent' ); ?>> <?php _e( 'None - send data without consent', 'myagilepixel' ); ?></option>
			</select>


			<div class="form-text">
				<?php
					_e( "If you are using a cookie management plugin, select it from this list to connect it with My Agile Pixel",'myagilepixel' );
				?>.
			</div>


		</div>
	</div> <!-- row -->
</div>