<?php

$locale = get_user_locale();
$map_link = 'https://www.myagileprivacy.com/en/';
$integration_guide_link = 'https://www.myagilepixel.com/en/guide-to-integrations/#api';

if( $locale && $locale == 'it_IT' )
{
    $map_link = 'https://www.myagileprivacy.com/';
    $integration_guide_link = 'https://www.myagilepixel.com/guida-alle-integrazioni/#api';
}

if(! defined( 'MAP_PLUGIN_VERSION' ) ):

?>
<div class="mb-4">
    <?php
        $lang_suffix = 'eng';
        
        if( $locale && $locale == 'it_IT' )
        {
            $lang_suffix = 'ita';
        }

    ?>

    <a href="<?php echo esc_attr( $map_link ); ?>" target="_blank"><img src="<?php echo plugin_dir_url(__DIR__); ?>../img/banner-map-<?php echo esc_attr( $lang_suffix ); ?>.jpg" class="img-fluid"></a>
</div>
<?php endif; ?>

<div class="consistent-box">

    <h5><?php _e( 'Cookie plugin integration guide','myagilepixel' ); ?></h5>
    <div class="mb-2">
            <strong><?php _e( 'No plugin - manually integration via javascript code','myagilepixel' ); ?>:</strong><br>
            <?php _e( 'You have to execute manually the javascript code in order to trigger the data tracking','myagilepixel' ); ?>.

            <?php _e( 'Refer to this guide to complete the integration:','myagilepixel' ); ?>
            <a href="<?php echo esc_attr( $integration_guide_link ); ?>" target="_blank"><?php _e( 'Integration guide','myagilepixel' ); ?></a>.

    </div>

    <div class="mb-2">
            <strong><?php _e( 'None - send data without consent','myagilepixel' ); ?>:</strong><br>
            <?php _e( 'Choose this option if you do not have to request user consent','myagilepixel' ); ?>.
    </div>

    <div class="mb-2">
            <strong><?php _e( 'My Agile Privacy / Cookiebot / Iubenda / GDPR Cookie Consent - CookieYes / Complianz','myagilepixel' ); ?>:</strong><br>
            <?php _e( 'Refer to this guide to complete the integration:','myagilepixel' ); ?>
            <a href="<?php echo esc_attr( $integration_guide_link ); ?>" target="_blank"><?php _e( 'Integration guide','myagilepixel' ); ?></a>.
    </div>

</div> <!-- consistent-box -->

<div class="consistent-box">
    <h5><?php _e( 'Proxification levels guide','myagilepixel' ); ?></h5>
    <div class="mb-2">
            <strong><?php _e( 'High level','myagilepixel' ); ?>:</strong><br>
            <?php _e( 'For those who want to remove user data, their behavior over time and references to marketing campaigns. By choosing this level of proxification, it may be more difficult to track marketing campaign performance','myagilepixel' ); ?>.
    </div>

    <div class="mb-2">
            <strong><?php _e( 'Medium level','myagilepixel' ); ?>:</strong><br>
            <?php _e( 'For those who carry out marketing campaigns and want to track their performance','myagilepixel' ); ?>.
    </div>

    <div class="mb-2">
            <strong><?php _e( 'Low level','myagilepixel' ); ?>:</strong><br>
            <?php _e( 'For those who only want to send data via anonymous IP','myagilepixel' ); ?>.
    </div>
</div> <!-- consistent-box -->

