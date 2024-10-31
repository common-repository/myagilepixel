<script type="text/javascript">
	var apix_settings_success_text='<?php echo esc_html__( 'Settings saved successfully','myagilepixel' );?>';
	var apix_settings_warning_text='<?php echo esc_html__( 'Settings saved successfully, but some mandatory data is missing. Please check the required fields','myagilepixel' );?>';
	var apix_settings_error_message_text='<?php echo esc_html__( 'Error: unable to save','myagilepixel' );?>';
</script>
<?php

	$locale = get_user_locale();

	$user_properties_guide_link = 'https://www.myagilepixel.com/en/how-to-utilize-ga4-user-properties-with-my-agile-pixel-to-enhance-web-analysis-and-marketing-part-1/';
	
	if( $locale && $locale == 'it_IT' )
	{
		$user_properties_guide_link = 'https://www.myagilepixel.com/come-sfruttare-le-proprieta-utente-di-ga4-con-my-agile-pixel-per-potenziare-la-web-analysis-e-il-marketing-parte-1/';
	}
?>

<?php

if( $css_compatibility_fix ):

?>

<style type="text/css">

.tab-content>.active {
    display: block;
    opacity: 1;
}

</style>


<?php

endif;

?>


<div class="wrap apix_user_property" id="apix_general_settings_backend">


	<form action="admin-ajax.php" method="post" id="apix_general_settings_form">
		<input type="hidden" name="action" value="apix_update_user_property_assoc_form" id="action" />

		<?php
			if( function_exists( 'wp_nonce_field' ) )
			{
				wp_nonce_field( 'apix-update-' . MAPX_PLUGIN_SETTINGS_FIELD );
			}
		?>

		<div class="container-fluid mt-5">

			<div class="row">

				<div class="col-sm-2">

				<div style="background: #fff;border-radius:10px;padding:20px 15px; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
					<ul class="nav nav-pills flex-column no-multiple-active" role="tablist">

							<li class="nav-item" role="presentation">
								<button class="nav-link active position-relative" data-bs-toggle="pill" data-bs-target="#instructions" type="button" role="tab" style="width:100%; text-align: left;">
									<i class="fa-regular fa-sheet-plastic"></i>

									<?php _e( 'Usage Instructions', 'myagilepixel' ); ?>

								</button>
							</li>


							<li class="nav-item" role="presentation">
								<button class="nav-link position-relative" data-bs-toggle="pill" data-bs-target="#user_property_list" type="button" role="tab" style="width:100%; text-align: left;">
									<i class="fa-solid fa-list"></i>

									<?php _e( 'User property list', 'myagilepixel' ); ?>

								</button>
							</li>
						</ul>
				</div>

				<div style="background: #fff;margin-top:20px; border-radius:10px;padding:20px 15px; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
					<ul class="nav nav-pills flex-column no-multiple-active" role="tablist">
						<li class="nav-item" role="presentation">
							<a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">
								<h4><?php _e( 'Post type List', 'myagilepixel' ); ?></h4>
							</a>
						</li>
						<?php
							if( isset( $post_types_selectable) && count( $post_types_selectable ) > 0 ):
							foreach( $post_types_selectable as $key => $label ):
						?>
							<li class="nav-item" role="presentation">
								<button class="nav-link position-relative" data-bs-toggle="pill" data-bs-target="#post_type_<?php echo esc_attr( $key );?>" type="button" role="tab" style="width:100%; text-align: left;">
									<small style="display:inline-block;margin-right: 8px;"><i class="fa-solid fa-angle-right"></i></small>
									<?php echo esc_html( $label );?>
								</button>
							</li>
						<?php
							endforeach;
							else:
						?>
						<li class="nav-item" role="presentation">
							<a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">
								<?php _e( 'No post type available', 'myagilepixel' ); ?>
							</a>
						</li>
						<?php
							endif;
						?>
						<div class="d-none">
							<li class="nav-item" role="presentation">
								<a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">
									<?php _e( 'Taxonomy List', 'myagilepixel' ); ?>
								</a>
							</li>
							<?php
								if( isset( $taxonomies_selectable) && count( $taxonomies_selectable ) > 0 ):
								foreach( $taxonomies_selectable as $key => $label ):
							?>
								<li class="nav-item" role="presentation">
									<button class="nav-link position-relative" data-bs-toggle="pill" data-bs-target="#taxonomy_<?php echo esc_attr( $key );?>" type="button" role="tab">
										<i class="fa-solid fa-gears"></i>
										<?php echo esc_html( $label );?>
									</button>
								</li>
							<?php
								endforeach;
								else:
							?>
							<li class="nav-item" role="presentation">
								<a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">
									<?php _e( 'No taxonomy available', 'myagilepixel' ); ?>
								</a>
							</li>
							<?php
								endif;
							?>
						</div>
					</ul>
				</div>
				
				</div>

				<div class="col-sm-10">

					<div class="mb-3 d-none">
						<button class="fake-save-button button-agile btn-md"><?php _e( 'Save settings', 'myagilepixel' ); ?></button>
						<span class="apix_wait text-muted">
							<i class="fas fa-spinner-third fa-fw fa-spin" style="--fa-animation-duration: 1s;"></i> <?php _e( 'Saving in progress', 'myagilepixel' ); ?>...
						</span>
					</div>

					<div class="tab-content">
						<div class="tab-pane fade show active" id="instructions" role="tabpanel">

					        <span class="translate-middle-y forbiddenWarning badge rounded-pill bg-danger  <?php if( $the_options['pa'] == 1){echo 'd-none';} ?>">
					            <small><?php _e( 'Premium Feature','myagilepixel' ); ?></small>
					        </span>

							<div class="consistent-box">
								<div class="row mb-1">
									<h4 class="mb-3">
									<i class="fa-regular fa-sheet-plastic"></i>
										<?php _e( 'Usage Instructions','myagilepixel' ); ?>

									</h4>

									<p>
									<?php _e( 'Advanced tracking? User segments? You are in the right place to enhance web analysis and your marketing performance.','myagilepixel' ); ?>
									</p>
									<p>
									<?php _e( 'Using this panel, you have the ability to track custom properties for the user, segment your visitors, and create more effective marketing campaigns. Currently, this feature is exclusively supported by Google Analytics 4.','myagilepixel' ); ?>
									</p>
									<p>
									<?php _e( 'Enter the user properties you want to track from the "User Properties Tracking" tab, then associate them with the types of posts on your website, selecting a "catch-all" property or choosing the preferred property, page by page, article by article.','myagilepixel' ); ?>
									</p>
									<p>
									<?php _e( 'Important: remember to also configure the list of properties you created in the Google Analytics 4 configuration, under the "Custom Definitions" section.','myagilepixel' ); ?>
									<br>
									<a target="blank" href="<?php echo $user_properties_guide_link; ?>"><?php _e( 'For further information, please refer to the tutorial available here.','myagilepixel' ); ?></a>
									</p>

								</div>
							</div>
						</div>

						<div class="tab-pane fade" id="user_property_list" role="tabpanel">
							<?php include 'inc/inc.user_property_list.php'; ?>
						</div>


						<?php

							$sub_type = 'post_type';

							foreach( $post_types_selectable as $key => $label ):
						?>
								<div class="tab-pane fade" id="post_type_<?php echo esc_attr( $key );?>" role="tabpanel">
									<?php include 'inc/inc.user_property_assoc.edit.php'; ?>
								</div>

						<?php
							endforeach;
						?>


						<?php

							if( 1 == 0 ):
							$sub_type = 'taxonomy';
							foreach( $taxonomies_selectable as $key => $label ):
						?>
								<div class="tab-pane fade" id="taxonomy_<?php echo esc_attr( $key );?>" role="tabpanel">
									<?php include 'inc/inc.user_property_assoc.edit.php'; ?>
								</div>

						<?php
							endforeach;
							endif;
						?>

					</div>

					<div class="row mt-2">
						<div class="col-12  <?php if( !$the_options['pa'] ){echo 'forbiddenArea';} ?>">
							<input type="submit" name="update_user_property_assoc_form" value="<?php _e( 'Save settings', 'myagilepixel' ); ?>" class="button-agile btn-md" id="mapx-save-button" />
							<span class="apix_wait text-muted">
								<i class="fas fa-spinner-third fa-fw fa-spin" style="--fa-animation-duration: 1s;"></i> <?php _e( 'Saving in progress', 'myagilepixel' ); ?>...
							</span>
						</div>
					</div>

				</div>

			</div>

		</div>

	</form>

</div>