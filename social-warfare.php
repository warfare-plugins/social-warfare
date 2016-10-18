<?php
/*
Plugin Name: Social Warfare
Plugin URI: http://warfareplugins.com
Description: A plugin to maximize social shares and drive more traffic using the fastest and most intelligent share buttons on the market, calls to action via in-post click-to-tweets, popular posts widgets based on share popularity, link-shortening, Google Analytics and much, much more!
Version: 2.0.7
Author: Warfare Plugins
Author URI: http://warfareplugins.com
Text Domain: social-warfare
*/
/**

 * **************************************************************
 *                                                                *
 *   VERSION AND DIRECTORIES							             *
 *                                                                *
 ******************************************************************/
$pluginVersion = '2.0.7';
define( 'swp_VERSION' , $pluginVersion );
$pluginUrl = rtrim( plugin_dir_url( __FILE__ ),'/' );
$pluginDir = dirname( __FILE__ );
define( 'swp_META_DIR' , trailingslashit( $pluginDir . '/meta-box' ) );
define( 'swp_PLUGIN_DIR' , $pluginUrl );
/**

***************************************************************
*                                                                *
*   INCLUDES: ALL THE FUNCTIONS FILES          					 *
*                                                                *
*/
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once swp_META_DIR . 'meta-box.php';
require_once $pluginDir . '/functions/admin-options-page/admin-options-array.php';
require_once $pluginDir . '/functions/admin-options-page/admin-options-page.php';
require_once $pluginDir . '/functions/utility.php';
require_once $pluginDir . '/functions/curl_functions.php';
require_once $pluginDir . '/functions/admin-options-page/admin-options-fetch.php';
require_once $pluginDir . '/functions/registration.php';
require_once $pluginDir . '/functions/widgets.php';
require_once $pluginDir . '/functions/scripts.php';
// require_once $pluginDir  . '/functions/updates/plugin-update-checker.php';
require_once $pluginDir . '/functions/bitly.php';
require_once $pluginDir . '/functions/click-to-tweet/clickToTweet.php';
require_once $pluginDir . '/functions/sw-shortcode-generator.php';
require_once $pluginDir . '/functions/buttons-standard.php';
require_once $pluginDir . '/functions/buttons-floating.php';
require_once $pluginDir . '/functions/permalinks.php';
require_once $pluginDir . '/functions/post-options.php';
require_once $pluginDir . '/functions/share-count-function.php';
require_once $pluginDir . '/functions/cache-rebuild.php';
require_once $pluginDir . '/functions/header-meta-tags.php';
require_once $pluginDir . '/functions/profile-fields.php';
require_once $pluginDir . '/functions/shortcodes.php';
require_once $pluginDir . '/functions/deprecated.php';
// require_once $pluginDir  . '/functions/media-options.php';
// Networks
require_once $pluginDir . '/functions/social-networks/googlePlus.php';
require_once $pluginDir . '/functions/social-networks/twitter.php';
require_once $pluginDir . '/functions/social-networks/facebook.php';
require_once $pluginDir . '/functions/social-networks/linkedIn.php';
require_once $pluginDir . '/functions/social-networks/pinterest.php';
require_once $pluginDir . '/functions/social-networks/tumblr.php';
require_once $pluginDir . '/functions/social-networks/reddit.php';
require_once $pluginDir . '/functions/social-networks/stumbleupon.php';
require_once $pluginDir . '/functions/social-networks/yummly.php';
require_once $pluginDir . '/functions/social-networks/email.php';
require_once $pluginDir . '/functions/social-networks/whatsapp.php';
require_once $pluginDir . '/functions/social-networks/pocket.php';
require_once $pluginDir . '/functions/social-networks/buffer.php';
require_once $pluginDir . '/functions/social-networks/hackernews.php';
require_once $pluginDir . '/functions/social-networks/flipboard.php';

global $swp_user_options;

$swp_user_options = swp_get_user_options();

/**

 * **************************************************************
 *                                                                *
 *   PLUGINS PAGE: UPDATE CHECKER AND SETTINGS LINK	             *
 *                                                                *
 ******************************************************************/
// $swp_update_checker = PucFactory::buildUpdateChecker(
// 'https://beta.warfareplugins.com/wp-content/plugins/social-warfare/social-warfare.json',
// __FILE__
// );
// Add settings link on plugin page
function swp_settings_link( $links ) {
	  $settings_link = '<a href="admin.php?page=social-warfare">Settings</a>';
	  array_unshift( $links, $settings_link );
	  return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'swp_settings_link' );

/**

 * **************************************************************
 *                                                                *
 *   LANGUAGES & LOCALIZATION						             *
 *                                                                *
 ******************************************************************/
function swp_localization_init() {
	$plugin_dir = basename( dirname( __FILE__ ) );
	load_plugin_textdomain( 'social-warfare', false, $plugin_dir . '/languages' );
}
add_action( 'plugins_loaded', 'swp_localization_init' );

add_filter( 'query_vars', 'swp_add_query_vars' );
/**
 * Register custom query vars.
 *
 * @since  2.1.0
 * @access public
 * @param  array $vars The current query vars.
 * @return array $vars The modified query vars.
 */
function swp_add_query_vars( $vars ) {
	$vars[] = 'swp_cache';

	return $vars;
}

// Add the Social Warfare Content Filter
add_filter( 'the_content','social_warfare_wrapper',200 );
add_filter( 'the_excerpt','social_warfare_wrapper' );

function social_warfare_wrapper( $content ) {
	$array['content'] = $content;
	$content = social_warfare_buttons( $array );
	$content .= '<div class="swp-content-locator"></div>';
	return $content;
}
function social_warfare( $array = array() ) {
	$array['devs'] = true;
	$content = social_warfare_buttons( $array );
	$content .= '<div class="swp-content-locator"></div>';
	return $content;
}

/**

***************************************************************
*                                                                *
*   LEGACY: STUFF I'M WORKING ON REBUILDING						 *
*                                                                *
*/
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

	if($swp_user_options['rawNumbers'] == 1):
		add_filter('the_content', 'socialWarfareWrapper', 20);
	else:
		if($swp_user_options['visualEditorBug']):
			add_filter('the_content', 'socialWarfare' , 200);
		else:
			add_filter('the_content', 'socialWarfare' , 10);
		endif;
	endif;


	add_filter('the_excerpt', 'socialWarfare', 20);
*/

/**

***************************************************************
*                                                                *
*   SIDE FLOATER: 												 *
*                                                                *
*/
if ( $swp_user_options['floatOption'] == 'left' || $swp_user_options['floatOption'] == 'right' ) :
	add_action( 'wp_footer', 'socialWarfareSideFloat' );
endif;
/**

 * **************************************************************
 *                                                                *
 *   DASHBOARD METRICS											 *
 *                                                                *
 ******************************************************************/
// ADD NEW COLUMN
function createSocialSharesColumn( $defaults ) {
	$defaults['swSocialShares'] = 'Social Shares';
	return $defaults;
}
// SHOW THE FEATURED IMAGE
function populateSocialSharesColumn( $column_name, $post_ID ) {
	if ( $column_name == 'swSocialShares' ) {
		$answer = get_post_meta( $post_ID,'_totes',true );
		echo intval( $answer );
	}
}
// Make the column Sortable
function makeSocialSharesSortable( $columns ) {
	$columns['swSocialShares'] = 'Social Shares';
	return $columns;
}
function swp_social_shares_orderby( $query ) {
	if ( ! is_admin() ) {
		return;
	}

	$orderby = $query->get( 'orderby' );

	if ( 'Social Shares' == $orderby ) {
		$query->set( 'meta_key','_totes' );
		$query->set( 'orderby','meta_value_num' );
	}
}
add_action( 'pre_get_posts', 'swp_social_shares_orderby' );
add_filter( 'manage_edit-post_sortable_columns', 'makeSocialSharesSortable' );
add_filter( 'manage_posts_columns', 'createSocialSharesColumn' );
add_action( 'manage_posts_custom_column', 'populateSocialSharesColumn', 10, 2 );
add_filter( 'manage_edit-page_sortable_columns', 'makeSocialSharesSortable' );
add_filter( 'manage_page_posts_columns', 'createSocialSharesColumn' );
add_action( 'manage_page_posts_custom_column', 'populateSocialSharesColumn', 10, 2 );
/**

 * **************************************************************
 *                                                                *
 *   REGISTRATION CRON JOBS										 *
 *                                                                *
 ******************************************************************/
// Ad a custom schedule
function swp_add_monthly_schedule( $schedules ) {
	// add a 'weekly' schedule to the existing set
	$schedules['swp_monthly'] = array(
		'interval' => 2635200,
		'display' => __( 'Once Monthly', 'social-warfare' ),
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'swp_add_monthly_schedule' );
// Activate the Cron Job
register_activation_hook( __FILE__, 'swp_activate_registration_cron' );
add_action( 'swp_check_registration_event', 'swp_check_registration_status' );
function swp_activate_registration_cron() {
	wp_schedule_event( time(), 'swp_monthly', 'swp_check_registration_event' );
}

// Deactivate the Cron Job
// function socal_warfare_deactivation() {
// wp_clear_scheduled_hook('swp_check_registration_event');
// }
// register_deactivation_hook(__FILE__, 'social_warfare_deactivation');
// Dump the cache timestamp when the post is saved
function swp_reset_cache_timestamp( $post_id ) {
	delete_post_meta( $post_id,'swp_cache_timestamp' );

	// Chache the og_image URL
	$imageID = get_post_meta( $post_id , 'nc_ogImage' , true );

	if ( $imageID ) :
		$imageURL = wp_get_attachment_url( $imageID );
		delete_post_meta( $post_id,'swp_open_graph_image_url' );
		update_post_meta( $post_id,'swp_open_graph_image_url',$imageURL );
	else :
		$imageURL = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
		delete_post_meta( $post_id,'swp_open_thumbnail_url' );
		update_post_meta( $post_id,'swp_open_thumbnail_url' , $imageURL );
		delete_post_meta( $post_id,'swp_open_graph_image_url' );
	endif;
}
add_action( 'save_post', 'swp_reset_cache_timestamp' );
