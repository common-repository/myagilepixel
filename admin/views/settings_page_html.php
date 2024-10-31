<script type="text/javascript">
	var apix_settings_success_text='<?php echo esc_html__( 'Settings saved successfully','myagilepixel' );?>';
	var apix_settings_warning_text='<?php echo esc_html__( 'Settings saved successfully, but some mandatory data is missing. Please check the required fields','myagilepixel' );?>';
	var apix_settings_error_message_text='<?php echo esc_html__( 'Error: unable to save','myagilepixel' );?>';
</script>

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

<div class="wrap" id="apix_general_settings_backend">

	<div id="map_banner" class="d-none">

		<?php

			$locale = get_user_locale();

			if( $the_options['pa'] == 1 )
			{
				if( $locale && $locale == 'it_IT' )
				{
					echo '<a href="https://www.myagileprivacy.com/?utm_source=referral&utm_medium=plugin-mapx-pro&utm_campaign=backend" target="blank"><img class="img-fluid" src="'.plugin_dir_url( __DIR__  ).'img/banner-privacy-ita.png" ></a>';
				}
				else
				{
					echo '<a href="https://www.myagileprivacy.com/en/?utm_source=referral&utm_medium=plugin-mapx-basic&utm_campaign=backend" target="blank"><img class="img-fluid" src="'.plugin_dir_url( __DIR__  ).'img/banner-privacy-eng.png" ></a>';
				}
			}
			else
			{
				if( $locale && $locale == 'it_IT' )
				{
					echo '<a href="https://www.myagileprivacy.com/?utm_source=referral&utm_medium=plugin-mapx-pro&utm_campaign=backend" target="blank"><img class="img-fluid" src="'.plugin_dir_url( __DIR__ ).'img/banner-privacy-ita.png" ></a>';
				}
				else
				{
					echo '<a href="https://www.myagileprivacy.com/en/?utm_source=referral&utm_mediumplugin-mapx-basic&utm_campaign=backend" target="blank"><img class="img-fluid" src="'.plugin_dir_url( __DIR__  ).'img/banner-privacy-eng.png" ></a>';
				}
			}
		?>


	</div>


    <?php
    if( $wasm_environment ):
    ?>

        <div class="alert alert-danger alert-dismissible fade show mt-5">
            <?php _e( '<b>Warning</b>: You are using a PHP.wasm environment. Due to the limitations of this stack, which emulates a real web server, some functionalities may not work as expected.', 'myagilepixel' ); ?><br>
            <?php _e( 'Specifically, proxying functionalities and data sending to platforms are disabled.', 'myagilepixel' ); ?><br>
        </div>

    <?php
    endif;
    ?>


	<form action="admin-ajax.php" method="post" id="apix_general_settings_form">
		<input type="hidden" name="action" value="apix_update_admin_settings_form" id="action" />

		<?php
			if( function_exists( 'wp_nonce_field' ) )
			{
				wp_nonce_field( 'apix-update-' . MAPX_PLUGIN_SETTINGS_FIELD );
			}
		?>

		<div class="container-fluid mt-5">

			<ul class="nav nav-pills mb-3" role="tablist">

				<li class="nav-item" role="presentation">
					<button class="nav-link active position-relative" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab">
						<i class="fa-solid fa-gears"></i>
						<?php _e( 'General settings','myagilepixel' ); ?>
					</button>
				</li>

				<li class="nav-item" role="presentation">
					<button class="nav-link position-relative premium <?php if( $the_options['pa'] == 0){echo 'disabled';} ?>" data-bs-toggle="pill" data-bs-target="#ganalytics" type="button" role="tab">

						<div class="<?php if( $the_options['pa'] == 0){echo 'opacity-50';} ?>">
							<i class="fa-brands fa-google"></i>
							<?php _e( 'Google Analytics 4','myagilepixel' ); ?>
						</div>

						<span class="position-absolute top-0 end-0 translate-middle-y badge rounded-pill bg-danger <?php if( $the_options['pa'] == 1){echo 'd-none';} ?>">
							<small><?php _e( 'Premium Feature','myagilepixel' ); ?></small>
						</span>
					</button>
				</li>

				<li class="nav-item" role="presentation">
					<button class="nav-link position-relative premium <?php if( $the_options['pa'] == 0){echo 'disabled';} ?>" data-bs-toggle="pill" data-bs-target="#facebook" type="button" role="tab">
						<div class="<?php if( $the_options['pa'] == 0){echo 'opacity-50';} ?>">
							<i class="fa-brands fa-facebook-f"></i>
							<?php _e( 'Facebook','myagilepixel' ); ?>
						</div>

						<span class="position-absolute top-0 end-0 translate-middle-y badge rounded-pill bg-danger <?php if( $the_options['pa'] == 1){echo 'd-none';} ?>">
							<small><?php _e( 'Premium Feature','myagilepixel' ); ?></small>
						</span>
					</button>
				</li>


				<li class="nav-item" role="presentation">
					<button class="nav-link" data-bs-toggle="pill" data-bs-target="#tiktok" type="button" role="tab">
						<i class="fa-brands fa-tiktok"></i>
						<?php _e( 'TikTok','myagilepixel' ); ?>
					</button>
				</li>

				<li class="nav-item" role="presentation">
					<button class="nav-link" data-bs-toggle="pill" data-bs-target="#advanced" type="button" role="tab">
						<i class="fa-solid fa-sliders-up"></i>
						<?php _e( 'Advanced Settings','myagilepixel' ); ?>
					</button>
				</li>


			</ul>

			<div class="row">
				<div class="col-sm-8">

					<div class="mb-3">
						<button class="fake-save-button button-agile btn-md"><?php _e( 'Save settings', 'myagilepixel' ); ?></button>
						<span class="apix_wait text-muted">
							<i class="fas fa-spinner-third fa-fw fa-spin" style="--fa-animation-duration: 1s;"></i> <?php _e( 'Saving in progress', 'myagilepixel' ); ?>...
						</span>
					</div>

					<div class="tab-content">
						<!-- TAB PANEL GENERALE -->
						<div class="tab-pane fade show active" id="general" role="tabpanel">
							<?php include 'inc/inc.general_tab.php'; ?>
						</div> <!-- tabpane general -->
						<!-- TAB PANEL ANALYTICS -->
						<div class="tab-pane fade show" id="ganalytics" role="tabpanel">
							<?php include 'inc/inc.ganalytics_tab.php'; ?>
						</div> <!-- tabpane google analytics -->
						<!-- TAB PANEL FACEBOOK -->
						<div class="tab-pane fade show" id="facebook" role="tabpanel">
							<?php include 'inc/inc.facebook_tab.php'; ?>
						</div> <!-- tabpane facebook -->

						<!-- TAB PANEL TIKTOK -->
						<div class="tab-pane fade show" id="tiktok" role="tabpanel">
							<?php include 'inc/inc.tiktok_tab.php'; ?>
						</div> <!-- tabpane tiktok -->

						<!-- TAB PANEL advanced -->
						<div class="tab-pane fade show" id="advanced" role="tabpanel">
							<?php include 'inc/inc.advanced_settings_tab.php'; ?>
						</div> <!-- tabpane advanced -->

					</div> <!--tab-content-->

					<div class="row mt-2">
						<div class="col-12">
							<input type="submit" name="update_admin_settings_form" value="<?php _e( 'Save settings', 'myagilepixel' ); ?>" class="button-agile btn-md" id="mapx-save-button" />
							<span class="apix_wait text-muted">
								<i class="fas fa-spinner-third fa-fw fa-spin" style="--fa-animation-duration: 1s;"></i> <?php _e( 'Saving in progress', 'myagilepixel' ); ?>...
							</span>
						</div>
					</div>

				</div> <!-- col-sm-8 -->
				<div class="col-sm-4" id="sidebar">
					<?php include 'inc/inc.sidebar.php'; ?>
				</div> <!-- col-sm-4 -->
			</div> <!-- row -->

		</div> <!-- container-fluid -->


	</form>

</div> <!--wrap-->
