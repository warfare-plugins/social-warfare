<?php

/**
 * A class of functions used to load the plugin files and functions
 *
 * @package   SocialWarfare\Utilities
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     2.4.0 | 19 FEB 2018 | Created
 *
 */

add_action( 'plugins_loaded' , 'swp_initiate_plugin' , 20 );


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
function swp_initiate_plugin() {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	require_once SWP_PLUGIN_DIR . '/functions/utilities/languages.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/url_processing.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/options-fetch.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/options-array.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/curl_functions.php';
	require_once SWP_PLUGIN_DIR . '/functions/widgets/widgets.php';
	require_once SWP_PLUGIN_DIR . '/functions/frontend-output/SWP_Script.php';
	require_once SWP_PLUGIN_DIR . '/functions/click-to-tweet/clickToTweet.php';
    require_once SWP_PLUGIN_DIR . '/functions/frontend-output/SWP_Shortcode.php';
    require_once SWP_PLUGIN_DIR . '/functions/frontend-output/SWP_Shortcode_Generator.php';
	require_once SWP_PLUGIN_DIR . '/functions/frontend-output/buttons-standard.php';
	require_once SWP_PLUGIN_DIR . '/functions/frontend-output/buttons-floating.php';
	require_once SWP_PLUGIN_DIR . '/functions/frontend-output/display.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/permalinks.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/share-count-function.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/share-cache.php';
	require_once SWP_PLUGIN_DIR . '/functions/frontend-output/header-meta-tags.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/profile-fields.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/deprecated.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/compatibility.php';

    new SWP_Script();
    new SWP_Shortcode();
    new SWP_Shortcode_Generator();
}


/**
 * Include the plugin's admin files.
 *
 */
if ( is_admin() ) {
	require_once SWP_PLUGIN_DIR . '/functions/admin/swp_system_checker.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/misc.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/options-page.php';
    require_once SWP_PLUGIN_DIR . '/functions/admin/SWP_Column.php';

    new SWP_Column();
}
