<?php
/**
 * Plugin Name: Social Warfare
 * Plugin URI:  http://warfareplugins.com
 * Description: A plugin to maximize social shares and drive more traffic using the fastest and most intelligent share buttons on the market, calls to action via in-post click-to-tweets, popular posts widgets based on share popularity, link-shortening, Google Analytics and much, much more!
 * Version:     2.1.3
 * Author:      Warfare Plugins
 * Author URI:  http://warfareplugins.com
 * Text Domain: social-warfare
 */

/**
 * Define plugin constants for use throughout the plugin (Version and Directories)
 */
define( 'SWP_VERSION' , '2.1.3' );
define( 'SWP_PLUGIN_FILE', __FILE__ );
define( 'SWP_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'SWP_PLUGIN_DIR', dirname( __FILE__ ) );

/**
 * Include the plugin's necessary functions files.
 */
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once SWP_PLUGIN_DIR . '/meta-box/meta-box.php';
require_once SWP_PLUGIN_DIR . '/functions/languages.php';
require_once SWP_PLUGIN_DIR . '/functions/utility.php';
require_once SWP_PLUGIN_DIR . '/functions/registration.php';
require_once SWP_PLUGIN_DIR . '/functions/options-fetch.php';
require_once SWP_PLUGIN_DIR . '/functions/options-array.php';
require_once SWP_PLUGIN_DIR . '/functions/curl_functions.php';
require_once SWP_PLUGIN_DIR . '/functions/widgets.php';
require_once SWP_PLUGIN_DIR . '/functions/scripts.php';
// require_once SWP_PLUGIN_DIR . '/functions/media-options.php';
require_once SWP_PLUGIN_DIR . '/functions/bitly.php';
require_once SWP_PLUGIN_DIR . '/functions/click-to-tweet/clickToTweet.php';
require_once SWP_PLUGIN_DIR . '/functions/sw-shortcode-generator.php';
require_once SWP_PLUGIN_DIR . '/functions/buttons-standard.php';
require_once SWP_PLUGIN_DIR . '/functions/buttons-floating.php';
require_once SWP_PLUGIN_DIR . '/functions/display.php';
require_once SWP_PLUGIN_DIR . '/functions/permalinks.php';
require_once SWP_PLUGIN_DIR . '/functions/post-options.php';
require_once SWP_PLUGIN_DIR . '/functions/share-count-function.php';
require_once SWP_PLUGIN_DIR . '/functions/share-cache.php';
require_once SWP_PLUGIN_DIR . '/functions/header-meta-tags.php';
require_once SWP_PLUGIN_DIR . '/functions/profile-fields.php';
require_once SWP_PLUGIN_DIR . '/functions/shortcodes.php';
require_once SWP_PLUGIN_DIR . '/functions/deprecated.php';

/**
 * Include the plugin's admin files.
 */
if ( is_admin() ) {
	require_once SWP_PLUGIN_DIR . '/functions/admin/columns.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/misc.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/options-page.php';
}

/**
 * Include the plugin's network files.
 */
require_once SWP_PLUGIN_DIR . '/functions/social-networks/googlePlus.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/twitter.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/facebook.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/linkedIn.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/pinterest.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/tumblr.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/reddit.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/stumbleupon.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/yummly.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/email.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/whatsapp.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/pocket.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/buffer.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/hackernews.php';
require_once SWP_PLUGIN_DIR . '/functions/social-networks/flipboard.php';
