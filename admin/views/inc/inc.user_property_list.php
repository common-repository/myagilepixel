<span class="translate-middle-y forbiddenWarning badge rounded-pill bg-danger  <?php if( $the_options['pa'] == 1 ){echo 'd-none';} ?>">
    <small><?php _e( 'Premium Feature','myagilepixel' ); ?></small>
</span>

<div class="consistent-box <?php if( !$the_options['pa'] ){echo 'forbiddenArea';} ?>">
	<h4 class="mb-3">
		<i class="fa-solid fa-list"></i>
		<?php _e( 'User property definition','myagilepixel' ); ?>

	</h4>
 
	<div class="row">
		<label for="" class="col-sm-5 col-form-label">
			<?php _e( 'Create your own custom user property','myagilepixel' ); ?><br>
			<?php _e( '(e.g. is_blog , is_commercial_post, is_product, etc).','myagilepixel' ); ?>
		</label>

		<div class="col-sm-5">
			<div id="mapx-user-property-definitions-container" class="row dynamic_fields_container">

				<?php
					$the_array = json_decode( $the_options['user_property_def'] , true );

					if( $the_array ):
						foreach( $the_array as $the_item ):

							if( $the_item != '' ):
				?>
							<div class="mapx-user-property-definition-entry map-not-unsafe-string mapx-dynamic-entry mb-2 col-sm-12">
								<div class="input-group">
									<input class="form-control is-valid" name="user_property_def_field[]" value="<?php echo esc_attr( $the_item ); ?>" type="text" autocomplete="off" />
									<button class="btn btn-danger mapx-btn-remove" type="button"><i class="fa-solid fa-minus"></i></button>
								</div>
							</div>
				<?php
							endif;
						endforeach;
					endif;
				?>

				<div class="mapx-user-property-definition map-not-unsafe-string mapx-dynamic-entry mb-2 col-sm-12">
					<div class="input-group">
						<input class="form-control" name="user_property_def_field[]" type="text" autocomplete="off" />
						<button class="btn btn-success mapx-btn-add" type="button"><i class="fa-solid fa-plus"></i></button>
					</div>
				</div>
			</div>
			<div class="form-text">
				<?php

					_e( "Add the user property name you would like track.",'myagilepixel' );

				?>
			</div>


		</div>

	</div>
	<div class="row mt-3">
		<p>
		<?php _e( 'Important: remember to also configure the list of properties you created in the Google Analytics 4 configuration, under the "Custom Definitions" section.','myagilepixel' ); ?>
		<br>
		<a target="blank" href="<?php echo esc_attr( $user_properties_guide_link ); ?>"><?php _e( 'For further information, please refer to the tutorial available here.','myagilepixel' ); ?></a>
		</p>
	</div>
</div>