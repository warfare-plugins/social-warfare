<?php
/**
 * Plugin Name: Social Warfare
 * Plugin URI:  http://warfareplugins.com
 * Description: A plugin to maximize social shares and drive more traffic using the fastest and most intelligent share buttons on the market, calls to action via in-post click-to-tweets, popular posts widgets based on share popularity, link-shortening, Google Analytics and much, much more!
 * Version:     2.3.5
 * Author:      Warfare Plugins
 * Author URI:  http://warfareplugins.com
 * Text Domain: social-warfare
 */

defined( 'WPINC' ) || die;

/**
 * Define plugin constants for use throughout the plugin (Version and Directories)
 *
 */
define( 'SWP_VERSION' , '2.3.5' );
define( 'SWP_PLUGIN_FILE', __FILE__ );
define( 'SWP_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'SWP_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'SWP_STORE_URL', 'https://warfareplugins.com' );

/**
 * Include the plugin's network files.
 *
 */
require_once SWP_PLUGIN_DIR . '/functions/social-networks/googlePlus.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/twitter.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/facebook.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/linkedIn.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/pinterest.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/stumbleupon.php';
require_once SWP_PLUGIN_DIR . '/functions/utilities/update-checker.php';
require_once SWP_PLUGIN_DIR . '/functions/utilities/utility.php';
require_once SWP_PLUGIN_DIR . '/functions/admin/registration.php';

/**
 * Include the plugin's necessary functions files.
 *
 */
add_action( 'plugins_loaded' , 'swp_initiate_plugin' , 20 );
function swp_initiate_plugin() {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	require_once SWP_PLUGIN_DIR . '/functions/utilities/languages.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/url_processing.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/options-fetch.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/options-array.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/curl_functions.php';
	require_once SWP_PLUGIN_DIR . '/functions/widgets/widgets.php';
	require_once SWP_PLUGIN_DIR . '/functions/frontend-output/scripts.php';
	require_once SWP_PLUGIN_DIR . '/functions/click-to-tweet/clickToTweet.php';
	require_once SWP_PLUGIN_DIR . '/functions/frontend-output/sw-shortcode-generator.php';
	require_once SWP_PLUGIN_DIR . '/functions/frontend-output/buttons-standard.php';
	require_once SWP_PLUGIN_DIR . '/functions/frontend-output/buttons-floating.php';
	require_once SWP_PLUGIN_DIR . '/functions/frontend-output/display.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/permalinks.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/share-count-function.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/share-cache.php';
	require_once SWP_PLUGIN_DIR . '/functions/frontend-output/header-meta-tags.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/profile-fields.php';
	require_once SWP_PLUGIN_DIR . '/functions/frontend-output/shortcodes.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/deprecated.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/compatibility.php';
}
/**
 * Include the plugin's admin files.
 */
if ( is_admin() ) {
	require_once SWP_PLUGIN_DIR . '/functions/admin/swp_system_checker.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/columns.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/misc.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/options-page.php';
}
