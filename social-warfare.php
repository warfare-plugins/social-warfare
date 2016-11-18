<?php
/**
 * Plugin Name: Social Warfare
 * Plugin URI:  http://warfareplugins.com
 * Description: A plugin to maximize social shares and drive more traffic using the fastest and most intelligent share buttons on the market, calls to action via in-post click-to-tweets, popular posts widgets based on share popularity, link-shortening, Google Analytics and much, much more!
 * Version:     2.1.4
 * Author:      Warfare Plugins
 * Author URI:  http://warfareplugins.com
 * Text Domain: social-warfare
 */

defined( 'WPINC' ) || die;

/**
 * Define plugin constants for use throughout the plugin (Version and Directories)
 */
define( 'SWP_VERSION' , '2.1.4' );
define( 'SWP_PLUGIN_FILE', __FILE__ );
define( 'SWP_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'SWP_PLUGIN_DIR', dirname( __FILE__ ) );

/**
 * Include the plugin's network files.
 */
require_once SWP_PLUGIN_DIR . '/functions/social-networks/googlePlus.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/twitter.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/facebook.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/linkedIn.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/pinterest.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/stumbleupon.php';

/**
 * Include the plugin's necessary functions files.
 */
function swp_initiate_plugin() {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	require_once SWP_PLUGIN_DIR . '/functions/languages.php';
	require_once SWP_PLUGIN_DIR . '/functions/url_processing.php';
	require_once SWP_PLUGIN_DIR . '/functions/utility.php';
	require_once SWP_PLUGIN_DIR . '/functions/options-fetch.php';
	require_once SWP_PLUGIN_DIR . '/functions/options-array.php';
	require_once SWP_PLUGIN_DIR . '/functions/curl_functions.php';
	require_once SWP_PLUGIN_DIR . '/functions/widgets.php';
	require_once SWP_PLUGIN_DIR . '/functions/scripts.php';
	require_once SWP_PLUGIN_DIR . '/functions/click-to-tweet/clickToTweet.php';
	require_once SWP_PLUGIN_DIR . '/functions/sw-shortcode-generator.php';
	require_once SWP_PLUGIN_DIR . '/functions/buttons-standard.php';
	require_once SWP_PLUGIN_DIR . '/functions/buttons-floating.php';
	require_once SWP_PLUGIN_DIR . '/functions/display.php';
	require_once SWP_PLUGIN_DIR . '/functions/permalinks.php';
	require_once SWP_PLUGIN_DIR . '/functions/share-count-function.php';
	require_once SWP_PLUGIN_DIR . '/functions/share-cache.php';
	require_once SWP_PLUGIN_DIR . '/functions/header-meta-tags.php';
	require_once SWP_PLUGIN_DIR . '/functions/profile-fields.php';
	require_once SWP_PLUGIN_DIR . '/functions/shortcodes.php';
	require_once SWP_PLUGIN_DIR . '/functions/deprecated.php';
	require_once SWP_PLUGIN_DIR . '/functions/compatibility.php';
}
add_action( 'plugins_loaded' , 'swp_initiate_plugin' , 20 );
/**
 * Include the plugin's admin files.
 */
if ( is_admin() ) {
	require_once SWP_PLUGIN_DIR . '/functions/admin/columns.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/misc.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/options-page.php';
}
