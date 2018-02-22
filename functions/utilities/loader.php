<?php

/**
 * A class of functions used to load the plugin files and functions
 *
 * @package   SocialWarfare\Utilities
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     2.4.0 | 19 FEB 2018 | Created
 *
 * @TODO: Convert this entire file into the Social_Warfare class. This class will load
 * 		 all of the plugins classes and functions and fire the plugin into life. Other
 * 		 Addons will then extend this class to addon and fire up their functionalities.
 *
 * 		 We will change the name of the /functions/ folder to /lib/ and this file and
 * 		 class will reside in the root of that directory.
 *
 * 		 We will create a method for loading each set of classes. One for the frontend
 * 		 output, one for admin classes, one for utility classes, etc.
 *
 *       We're not going to act on this until we have refactored everything in the
 *       swp_initiate_plugin (the stuff being deferred to the plugins_loaded hook)
 *       and moved all of those require_once's outside of that deferment.
 *
 *       We will go aheed and docblock each instantiation of a class in this file as
 *       this file will then essentially serve as a table of contents for the entire
 *       plugin.
 *
 */
class Social_Warfare {

	// Example __construct class that shows the basic logic of what I'm going for.
	public function __construct() {
		$this->load_admin_classes();
		$this->load_frontend_output_classes();
		$this->load_utility_classes();
		$this->load_widget_classes();
		$this->load_social_network_classes();
		$this->instantiate_classes();
	}

}

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

// Classes shouldn't have to be deferred since they won't do anything until instantiated
require_once SWP_PLUGIN_DIR . '/functions/frontend-output/SWP_Script.php';
require_once SWP_PLUGIN_DIR . '/functions/frontend-output/SWP_Shortcode.php';
require_once SWP_PLUGIN_DIR . '/functions/frontend-output/SWP_Shortcode_Generator.php';
require_once SWP_PLUGIN_DIR . '/functions/admin/SWP_User_Profile.php';
require_once SWP_PLUGIN_DIR . '/functions/frontend-output/SWP_Header_Output.php';
require_once SWP_PLUGIN_DIR . '/functions/frontend-output/SWP_Display.php';



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
	require_once SWP_PLUGIN_DIR . '/functions/click-to-tweet/clickToTweet.php';
	require_once SWP_PLUGIN_DIR . '/functions/frontend-output/buttons-standard.php';
	require_once SWP_PLUGIN_DIR . '/functions/frontend-output/buttons-floating.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/permalinks.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/share-count-function.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/share-cache.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/deprecated.php';
	require_once SWP_PLUGIN_DIR . '/functions/utilities/compatibility.php';

    new SWP_Script();
    new SWP_Shortcode();
    new SWP_Shortcode_Generator();
	new SWP_User_Profile();
	new SWP_Header_Output();
    new SWP_Display();
}


/**
 * Include the plugin's admin files.
 *
 */
if ( is_admin() ) {
	require_once SWP_PLUGIN_DIR . '/functions/admin/swp_system_checker.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/SWP_Settings_Link.php';
	require_once SWP_PLUGIN_DIR . '/functions/admin/options-page.php';
    require_once SWP_PLUGIN_DIR . '/functions/admin/SWP_Column.php';

    new SWP_Column();
	new SWP_Settings_Link();
}
