<?php
/*
Plugin Name: Social Warfare
Plugin URI: http://warfareplugins.com
Description: A plugin to maximize social shares and drive more traffic using the fastest and most intelligent share buttons on the market, calls to action via in-post click-to-tweets, popular posts widgets based on share popularity, link-shortening, Google Analytics and much, much more!
Version: 1.4.7
Author: Warfare Plugins
Author URI: http://warfareplugins.com
*/
/*****************************************************************
*                                                                *
*   VERSION AND DIRECTORIES							             *
*                                                                *
******************************************************************/
$pluginVersion = '1.4.7';
define( 'SW_VERSION' , $pluginVersion);
$pluginUrl = rtrim(plugin_dir_url( __FILE__ ),'/');
$pluginDir = dirname(__FILE__);
define( 'SW_META_DIR' , trailingslashit( $pluginDir.'/meta-box' ) );
define( 'SW_PLUGIN_DIR' , $pluginUrl );
/*****************************************************************
*                                                                *
*   INCLUDES: ALL THE FUNCTIONS FILES          					 *
*                                                                *
******************************************************************/
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once SW_META_DIR . 'meta-box.php';
require_once $pluginDir  . '/functions/kilomega.php';
require_once $pluginDir  . '/functions/excerpt.php';
require_once $pluginDir  . '/functions/mobile-detection.php';
require_once $pluginDir  . '/functions/curl_functions.php';
require_once $pluginDir  . '/functions/options-page.php';
require_once $pluginDir  . '/functions/options-fetch.php';
require_once $pluginDir  . '/functions/registration.php';
require_once $pluginDir  . '/functions/widgets.php';
require_once $pluginDir  . '/functions/updates/plugin-update-checker.php';
require_once $pluginDir  . '/functions/bitly.php';
require_once $pluginDir  . '/functions/click-to-tweet/clickToTweet.php';
require_once $pluginDir  . '/functions/sw-shortcode-generator.php';
require_once $pluginDir  . '/functions/buttons-standard.php';
require_once $pluginDir  . '/functions/buttons-floating.php';
require_once $pluginDir  . '/functions/permalinks.php';
require_once $pluginDir  . '/functions/post-options.php';
require_once $pluginDir  . '/functions/share-count-function.php';
require_once $pluginDir  . '/functions/languages.php';
require_once $pluginDir  . '/functions/header-meta-tags.php';
require_once $pluginDir  . '/functions/profile-fields.php';
// Networks
require_once $pluginDir  . '/functions/social-networks/googlePlus.php';
require_once $pluginDir  . '/functions/social-networks/twitter.php';
require_once $pluginDir  . '/functions/social-networks/facebook.php';
require_once $pluginDir  . '/functions/social-networks/linkedIn.php';
require_once $pluginDir  . '/functions/social-networks/pinterest.php';
require_once $pluginDir  . '/functions/social-networks/tumblr.php';
require_once $pluginDir  . '/functions/social-networks/reddit.php';
require_once $pluginDir  . '/functions/social-networks/stumbleupon.php';
require_once $pluginDir  . '/functions/social-networks/yummly.php';
require_once $pluginDir  . '/functions/social-networks/email.php';
require_once $pluginDir  . '/functions/social-networks/whatsapp.php';
require_once $pluginDir  . '/functions/social-networks/pocket.php';
require_once $pluginDir  . '/functions/social-networks/buffer.php';


/*****************************************************************
*                                                                *
*   PLUGINS PAGE: UPDATE CHECKER AND SETTINGS LINK	             *
*                                                                *
******************************************************************/
$sw_update_checker = PucFactory::buildUpdateChecker(
    'https://beta.warfareplugins.com/wp-content/plugins/social-warfare/social-warfare.json',
    __FILE__
);

// Add settings link on plugin page
function sw_settings_link($links) {
	  $settings_link = '<a href="admin.php?page=social-warfare">Settings</a>';
	  array_unshift($links, $settings_link);
	  return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'sw_settings_link' );
/*****************************************************************
*                                                                *
*   ENQUEUE: SCRIPTS AND STYLES									 *
*                                                                *
******************************************************************/
$sw_user_options = sw_get_user_options();

add_action( 'wp_enqueue_scripts', 'enqueueSocialWarfareScripts' );
function enqueueSocialWarfareScripts() {
	global $sw_user_options;
	wp_enqueue_script( 'social_warfare_script', SW_PLUGIN_DIR . '/script.min.js',array( 'jquery' ),SW_VERSION);
	wp_register_style( 'social_warfare', SW_PLUGIN_DIR.'/css/style.css',array(),SW_VERSION );
	wp_enqueue_style( 'social_warfare' );
}

// Enqueue admin and Click to Tweet Styles
add_action( 'admin_enqueue_scripts', 'enqueueSocialWarfareAdminScripts' );
function enqueueSocialWarfareAdminScripts() {
	wp_register_style( 'social_warfare', SW_PLUGIN_DIR.'/css/style.css',array(),SW_VERSION );
	wp_enqueue_style( 'social_warfare' );
	wp_register_style( 'social_warfare_admin', SW_PLUGIN_DIR.'/css/admin.css',array(),SW_VERSION );
	wp_enqueue_style( 'social_warfare_admin' );
	wp_enqueue_script( 'social_warfare_script', SW_PLUGIN_DIR . '/script.min.js',array( 'jquery' ),SW_VERSION);
	wp_enqueue_script( 'social_warfare_admin_script', SW_PLUGIN_DIR . '/admin.js',array( 'jquery' ),SW_VERSION);
}

// Add the Social Warfare Content Filter
add_filter('the_content','social_warfare_wrapper',200);
add_filter('the_excerpt','social_warfare_wrapper');
function social_warfare_wrapper($content) {
	$array['content'] = $content;
	return social_warfare_buttons($array);
}
function social_warfare($array = array()) {
	$array['devs'] = true;
	return social_warfare_buttons($array);
}

/*****************************************************************
*                                                                *
*   LEGACY: STUFF I'M WORKING ON REBUILDING						 *
*                                                                *
******************************************************************/
	// Only modify the content filter when on an actual post or page
/*
	function socialWarfareWrapper( $content ) {
		if( in_the_loop() && is_singular() ) :
			$array['content'] = $content;
			return social_warfare($array);
		else :
			return $content;
		endif;
	}

	if($sw_user_options['rawNumbers'] == 1):
		add_filter('the_content', 'socialWarfareWrapper', 20);
	else:
		if($sw_user_options['visualEditorBug']):
			add_filter('the_content', 'socialWarfare' , 200);
		else:
			add_filter('the_content', 'socialWarfare' , 10);
		endif;
	endif;


	add_filter('the_excerpt', 'socialWarfare', 20);
*/
/*****************************************************************
*                                                                *
*   SHORTCODES: <---- THAT										 *
*                                                                *
******************************************************************/
function socialWarfareShortcode( $atts ){
	return socialWarfare(false,'after',false);
}
function social_warfareShortcode( $array ){
	if(!isset($array['where'])) { $array['where'] = 'after'; }
	if(!isset($array['echo'])) { $array['echo'] = false; }
	if(!isset($array['content'])) { $array['content'] = false; }
	$array['devs'] = true;
	return social_warfare($array);
}

add_shortcode( 'socialWarfare', 'socialWarfareShortcode' );
add_shortcode( 'social_warfare', 'social_warfareShortcode' );
/*****************************************************************
*                                                                *
*   SIDE FLOATER: 												 *
*                                                                *
******************************************************************/
if($sw_user_options['floatOption'] == 'left' || $sw_user_options['floatOption'] == 'right'):
	add_action('wp_footer', 'socialWarfareSideFloat');
endif;
/*****************************************************************
*                                                                *
*   DASHBOARD METRICS											 *
*                                                                *
******************************************************************/
// ADD NEW COLUMN
function createSocialSharesColumn($defaults) {
	$defaults['swSocialShares'] = 'Social Shares';
	return $defaults;
}
// SHOW THE FEATURED IMAGE
function populateSocialSharesColumn($column_name, $post_ID) {
	if ($column_name == 'swSocialShares') {
		$answer = get_post_meta($post_ID,'_totes',true);
		echo intval($answer);
	}
}
// Make the column Sortable
function makeSocialSharesSortable( $columns ) {
	$columns['swSocialShares'] = 'Social Shares';
	return $columns;
}
function sw_social_shares_orderby( $query ) {
    if( ! is_admin() )
        return;

    $orderby = $query->get( 'orderby');

    if( 'Social Shares' == $orderby ) {
        $query->set('meta_key','_totes');
        $query->set('orderby','meta_value_num');
    }
}
add_action( 'pre_get_posts', 'sw_social_shares_orderby' );
add_filter('manage_edit-post_sortable_columns', 'makeSocialSharesSortable');
add_filter('manage_posts_columns', 'createSocialSharesColumn');
add_action('manage_posts_custom_column', 'populateSocialSharesColumn', 10, 2);
/*****************************************************************
*                                                                *
*   REGISTRATION CRON JOBS										 *
*                                                                *
******************************************************************/
// Activate the Cron Job
register_activation_hook(__FILE__, 'sw_activate_registration_cron');
add_action('sw_check_registration_event', 'sw_check_registration_status');
function sw_activate_registration_cron() {
	wp_schedule_event(time(), 'daily', 'sw_check_registration_event');
}

// Deactivate the Cron Job
function socal_warfare_deactivation() {
	wp_clear_scheduled_hook('sw_check_registration_event');
}
register_deactivation_hook(__FILE__, 'social_warfare_deactivation');

// Dump the cache timestamp when the post is saved
function sw_reset_cache_timestamp( $post_id ) {
	delete_post_meta($post_id,'sw_cache_timestamp');

	// Chache the og_image URL
	$imageID = get_post_meta( $post_id , 'nc_ogImage' , true );

	if($imageID):
		$imageURL = wp_get_attachment_url( $imageID );
		delete_post_meta($post_id,'sw_open_graph_image_url');
		update_post_meta($post_id,'sw_open_graph_image_url',$imageURL);
	else:
		$imageURL = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
		delete_post_meta($post_id,'sw_open_thumbnail_url');
		update_post_meta($post_id,'sw_open_thumbnail_url' , $imageURL);
		delete_post_meta($post_id,'sw_open_graph_image_url');
	endif;
}
add_action( 'save_post', 'sw_reset_cache_timestamp' );
