<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       MyAgilePixel
 * Plugin URI:        https://www.myagilepixel.com/
 * Description:		  Prevent legal compliance issues when using Google Analytics, Facebook Pixel and TikTok Pixel.
 * Version:           3.0.8
 * Requires at least: 4.4.0
 * Requires PHP:      5.6
 * Author:            MyAgilePixel
 * Author URI:        https://www.myagilepixel.com/
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       myagilepixel
 * Domain Path:       /lang
*/

define ( 'MAPX_PLUGIN_VERSION', '3.0.8' );
define ( 'MAPX_PLUGIN_NAME', 'my-agile-pixel' );
define ( 'MAPX_PLUGIN_SLUG', 'myagilepixel' );
define ( 'MAPX_PLUGIN_FILENAME', __FILE__ );
define ( 'MAPX_DEV_MODE', false );

require plugin_dir_path( __FILE__ ) . 'includes/my-agile-pixel-class.php';

//* Starts the plugin execution
function run_my_agile_pixel() {
	ini_set( 'display_errors', 0 );

	$plugin = new MyAgilePixel();

    $rconfig = MyAgilePixel::get_rconfig();

    if( isset( $rconfig ) &&
        isset( $rconfig['verbose_remote_log'] ) &&
        $rconfig['verbose_remote_log'] )
    {
        define ( 'MAPX_DEBUGGER', true );
    }
    else
    {
        define ( 'MAPX_DEBUGGER', false );
    }
}
run_my_agile_pixel();
