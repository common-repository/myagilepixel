<?php
$this_composed_key = esc_attr( $sub_type ).'_'.esc_attr( $key );
$this_name_field = 'user_property_assoc_field['.$this_composed_key.']';
$this_class_field = 'user_property_assoc_field_enabled_'.esc_attr( $sub_type ).'_'.esc_attr( $key );

$this_catch_all_user_property_composed_key = $this_composed_key.'_catch_all_user_property_def';
$this_catch_all_user_property_def_name = 'user_property_assoc_field['.$this_catch_all_user_property_composed_key.']';


$this_custom_setting_user_property_composed_key = $this_composed_key.'_custom_setting_user_property';
$this_custom_setting_user_property_def_name = 'user_property_assoc_field['.$this_custom_setting_user_property_composed_key.']';

?>


<span class="translate-middle-y forbiddenWarning badge rounded-pill bg-danger  <?php if( $the_options['pa'] == 1){echo 'd-none';} ?>">
    <small><?php _e( 'Premium Feature','myagilepixel' ); ?></small>
</span>

<div class="consistent-box <?php if( !$the_options['pa'] ){echo 'forbiddenArea';} ?>">
	<h4 class="mb-3">
		<i class="fa-solid fa-angle-right" style="display: inline-block; margin-right:12px;"></i>
		<?php echo esc_html( $label );?> - <?php _e( 'Page View Tracking','myagilepixel' ); ?>

	</h4>

	<div class="row mb-3">
		<label for="" class="col-sm-5 col-form-label">
			<?php echo esc_html( $label );?> - <?php _e( 'Enable User Property Page View Tracking','myagilepixel' ); ?>.
		</label>

		<div class="col-sm-5">
			<select id="<?php echo esc_attr( $this_name_field );?>"
					class="form-select hideShowInput"
					name="<?php echo esc_attr( $this_name_field );?>"
					data-hide-show-ref="<?php echo esc_attr( $this_class_field );?>">
				<option value="0" <?php if( isset( $user_property_assoc_saved_settings ) && $user_property_assoc_saved_settings[ $this_composed_key ] == '0' ) echo 'selected'; ?>> <?php _e( 'Disable','myagilepixel' ); ?></option>

				<option value="1" <?php if( isset( $user_property_assoc_saved_settings ) && $user_property_assoc_saved_settings[ $this_composed_key ] == '1' ) echo 'selected'; ?>> <?php _e( 'On - with "catch all" user property ','myagilepixel' ); ?></option>

				<option value="2" <?php if( isset( $user_property_assoc_saved_settings ) && $user_property_assoc_saved_settings[ $this_composed_key ] == '2' ) echo 'selected'; ?>> <?php _e( 'On - custom ','myagilepixel' ); ?></option>
			</select>

		</div>
	</div> <!-- row -->


	<div class="<?php echo esc_attr( $this_class_field );?> displayNone" data-value="1">


		<div class="row mb-3">
			<label for="<?php echo esc_attr( $this_catch_all_user_property_def_name );?>" class="col-sm-5 col-form-label">
				<?php _e( 'Select the user property to send on every page view for this post type.','myagilepixel' ); ?>
			</label>

			<div class="col-sm-5">
				<select id="<?php echo esc_attr( $this_catch_all_user_property_def_name );?>"
						class="form-select apix_user_property_def_catch_all apix_user_property_select"
						name="<?php echo esc_attr( $this_catch_all_user_property_def_name );?>"
						data-selected="<?php if( isset( $user_property_assoc_saved_settings ) && isset ( $user_property_assoc_saved_settings[ $this_catch_all_user_property_composed_key ] ) ) echo( esc_attr( $user_property_assoc_saved_settings[ $this_catch_all_user_property_composed_key ] ) );?>"
						>
				</select>

			</div>
		</div> <!-- row -->

	</div>


	<div class="<?php echo esc_attr( $this_class_field );?> displayNone" data-value="2">

		<?php

			$the_array = ( isset( $user_property_assoc_saved_settings ) && isset( $user_property_assoc_saved_settings[ $this_custom_setting_user_property_composed_key ] ) ) ?  $user_property_assoc_saved_settings[ $this_custom_setting_user_property_composed_key ] : null;

			$this_row_counter = ( $the_array ) ? count( $the_array ) : 0;
		?>

		<div class="row mb-3">
			<label for="" class="col-sm-5 col-form-label">
				<?php _e( 'Customize the user property to send, for the desired post type item.','myagilepixel' ); ?>
			</label>


			<div class="col-sm-5">
				<div
					id="<?php echo esc_attr( $this_composed_key ); ?>-assoc-list-container"
					class="row dynamic_fields_container"
					data-row-counter="<?php echo esc_attr( $this_row_counter );?>"
					data-basename="<?php echo esc_attr( $this_custom_setting_user_property_def_name );?>"
				>

					<?php

						if( $the_array ):

							$i = 0;

							foreach( $the_array as $the_item ):

								if( !( $the_item['elem_id'] == '' && $the_item['user_property'] == '' ) ):

					?>
								<div class="mapx-dynamic-entry mb-2 col-sm-12">
									<div class="input-group">


										<select
											name="<?php echo esc_attr( $this_custom_setting_user_property_def_name );?>[<?php echo esc_attr( $i );?>][elem_id]"
											class="form-select mapx-smaller-select mapx-this-post-list"
										 style="margin-right:10px;">

											<option value="">...</option>

											<?php
												foreach( $all_posts[ $key ] as $kk => $vv ):
													$selected_html = ( $the_item['elem_id'] == $kk ) ? 'selected' : '';

											?>

												<option
													value="<?php echo esc_attr( $kk );?>"
													<?php echo esc_attr( $selected_html );?>
												><?php echo esc_html( $vv );?></option>

											<?php
												endforeach;
											?>

										</select>

										<select
											name="<?php echo esc_attr( $this_custom_setting_user_property_def_name );?>[<?php echo esc_attr( $i );?>][user_property]"
											class="form-select mapx-smaller-select apix_user_property_select mapx-this-post-list"
											data-selected="<?php echo esc_attr( $the_item['user_property'] );?>"
											>
										</select>


										<button class="btn btn-danger mapx-btn-remove" type="button"><i class="fa-solid fa-minus"></i></button>
									</div>
								</div>
					<?php

								endif;
								$i++;
							endforeach;
						endif;
					?>

					<div
						class="mapx-dynamic-entry mb-2 col-sm-12"
					>
						<div class="input-group">

							<select
								name="<?php echo esc_attr( $this_custom_setting_user_property_def_name );?>[<?php echo esc_attr( $this_row_counter );?>][elem_id]"
								class="form-select mapx-smaller-select mapx-this-post-list"
								style="margin-right:10px;">


									<option value="">...</option>
								<?php
									foreach( $all_posts[ $key ] as $kk => $vv ):
								?>

									<option value="<?php echo esc_attr( $kk );?>"><?php echo esc_html( $vv );?></option>

								<?php
									endforeach;
								?>

							</select>

							<select
								name="<?php echo esc_attr( $this_custom_setting_user_property_def_name );?>[<?php echo esc_attr( $this_row_counter );?>][user_property]"
								class="form-select mapx-smaller-select apix_user_property_select mapx-this-post-list"
								>
							</select>

							<button class="btn btn-success mapx-btn-add mapx-btn-multiple-input" type="button"><i class="fa-solid fa-plus"></i></button>
						</div>
					</div>
				</div>

			</div>

		</div> <!-- row -->
	</div>
</div>