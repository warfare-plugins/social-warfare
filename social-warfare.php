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
 * Define plugin constants for use throughout the plugin (Version and Directories)
 */
$swp_plugin_url = rtrim( plugin_dir_url( __FILE__ ),'/' );
define( 'SWP_VERSION' , '2.0.7' );
define( 'SWP_PLUGIN_URL' , rtrim( plugin_dir_url( __FILE__ ),'/' ) );
define( 'SWP_PLUGIN_DIR' , dirname( __FILE__ )  );

/**
 * Include the plugin's necessary functions files
 */
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once SWP_PLUGIN_DIR . '/meta-box/meta-box.php';
require_once SWP_PLUGIN_DIR . '/functions/admin-options-page/admin-options-array.php';
require_once SWP_PLUGIN_DIR . '/functions/admin-options-page/admin-options-page.php';
require_once SWP_PLUGIN_DIR . '/functions/utility.php';
require_once SWP_PLUGIN_DIR . '/functions/curl_functions.php';
require_once SWP_PLUGIN_DIR . '/functions/admin-options-page/admin-options-fetch.php';
require_once SWP_PLUGIN_DIR . '/functions/registration.php';
require_once SWP_PLUGIN_DIR . '/functions/widgets.php';
require_once SWP_PLUGIN_DIR . '/functions/scripts.php';
require_once SWP_PLUGIN_DIR . '/functions/bitly.php';
require_once SWP_PLUGIN_DIR . '/functions/click-to-tweet/clickToTweet.php';
require_once SWP_PLUGIN_DIR . '/functions/sw-shortcode-generator.php';
require_once SWP_PLUGIN_DIR . '/functions/buttons-standard.php';
require_once SWP_PLUGIN_DIR . '/functions/buttons-floating.php';
require_once SWP_PLUGIN_DIR . '/functions/permalinks.php';
require_once SWP_PLUGIN_DIR . '/functions/post-options.php';
require_once SWP_PLUGIN_DIR . '/functions/share-count-function.php';
require_once SWP_PLUGIN_DIR . '/functions/cache-rebuild.php';
require_once SWP_PLUGIN_DIR . '/functions/header-meta-tags.php';
require_once SWP_PLUGIN_DIR . '/functions/profile-fields.php';
require_once SWP_PLUGIN_DIR . '/functions/shortcodes.php';
require_once SWP_PLUGIN_DIR . '/functions/deprecated.php';
/**
 * Include the plugin's network files
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

/**
 * Fetch the options that the user set in the admin options page
 */
global $swp_user_options;
$swp_user_options = swp_get_user_options();

/**
 * Add a "Settings" link to the listing on the plugins page
 * @since 	1.0.0
 * @param  	array $links Array of links passed in from WordPress core.
 * @return 	array $links Array of links modified by the function passed back to WordPress
 */
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'swp_settings_link' );
function swp_settings_link( $links ) {
	  $settings_link = '<a href="admin.php?page=social-warfare">Settings</a>';
	  array_unshift( $links, $settings_link );
	  return $links;
}

/**
 * swp_localization_init Load up the text domain for translations
 * @since 	1.0.0
 * @param  	none
 * @return 	none
 */
add_action( 'plugins_loaded', 'swp_localization_init' );
function swp_localization_init() {
	$plugin_dir = basename( dirname( __FILE__ ) );
	load_plugin_textdomain( 'social-warfare', false, $plugin_dir . '/languages' );
}


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

/**
 * A wrapper function for adding the buttons the content or excerpt
 * @since  1.0.0
 * @param  string $content The content
 * @return String $content The modified content
 */
function social_warfare_wrapper( $content ) {
	$array['content'] = $content;
	$content = social_warfare_buttons( $array );
	$content .= '<div class="swp-content-locator"></div>';
	return $content;
}

/**
 * The main social_warfare function used to create the buttons
 * @since 	1.4.0
 * @param  	array  $array An array of options and information to pass into the buttons function
 * @return 	string $content The modified content
 */
function social_warfare( $array = array() ) {
	$array['devs'] = true;
	$content = social_warfare_buttons( $array );
	$content .= '<div class="swp-content-locator"></div>';
	return $content;
}

/**
 * Add the side floating buttons to the footer if they are activated
 * @since 	1.4.0
 */
if ( $swp_user_options['floatOption'] == 'left' || $swp_user_options['floatOption'] == 'right' ) :
	add_action( 'wp_footer', 'socialWarfareSideFloat' );
endif;

/**
 * Add a share counts column to the post listing admin pages; make it Sortable
 * @since	1.4.0
 */
// Registrer the new column
function createSocialSharesColumn( $defaults ) {
	$defaults['swSocialShares'] = 'Social Shares';
	return $defaults;
}
// Populate the new column
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

// Add the actions & filters to get the columns installed
add_action( 'pre_get_posts', 'swp_social_shares_orderby' );
add_filter( 'manage_edit-post_sortable_columns', 'makeSocialSharesSortable' );
add_filter( 'manage_posts_columns', 'createSocialSharesColumn' );
add_action( 'manage_posts_custom_column', 'populateSocialSharesColumn', 10, 2 );
add_filter( 'manage_edit-page_sortable_columns', 'makeSocialSharesSortable' );
add_filter( 'manage_page_posts_columns', 'createSocialSharesColumn' );
add_action( 'manage_page_posts_custom_column', 'populateSocialSharesColumn', 10, 2 );

/**
 * Add the cron job for the registration check functions
 * @since 	2.0.0
 * @todo 	Convert from cron job to use a transit
 * @param 	array 	$schedules 	An array of WordPress's schedules
 * @return 	array 	$schedules 	The modified array of schedules
 */
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

/**
 * A function to reset the cache timestamp on a post after the cache is rebuilt
 * @param integer $post_id The ID of the post to be reset
 * @since 2.0.0
 */
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
