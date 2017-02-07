<?php
/**
 * Functions to enhance compatibility with other plugins
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2016, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

defined( 'WPINC' ) || die;

/**
 * Disabe Open Graph tags on Simple Podcast Press Pages
 *
 * @since 1.4.0
 * @access public
 */
if ( is_plugin_active( 'simple-podcast-press/simple-podcast-press.php' ) ) {
	global $ob_wp_simplepodcastpress;
	remove_action( 'wp_head' , array( $ob_wp_simplepodcastpress, 'spp_open_graph' ) , 1 );
}

/**
 * A function to remove all Social Warfare fields when a post
 * is duplicated using the duplicate post plugin.
 *
 * @since  2.1.4
 * @access public
 * @param  integer $id The post ID
 * @return none
 */
function swp_remove_fields($id) {

    // Delete any share count fields
    delete_post_meta( $id , '_buffer_shares' );
    delete_post_meta( $id , '_facebook_shares' );
    delete_post_meta( $id , '_flipboard_shares' );
    delete_post_meta( $id , '_googlePlus_shares' );
    delete_post_meta( $id , '_hacker_news_shares' );
    delete_post_meta( $id , '_linkedIn_shares' );
    delete_post_meta( $id , '_pinterest_shares' );
    delete_post_meta( $id , '_pocket_shares' );
    delete_post_meta( $id , '_reddit_shares' );
    delete_post_meta( $id , '_stumbleupon_shares' );
    delete_post_meta( $id , '_tumblr_shares' );
    delete_post_meta( $id , '_twitter_shares' );
    delete_post_meta( $id , '_whatsapp_shares' );
    delete_post_meta( $id , '_yummly_shares' );
    delete_post_meta( $id , '_totes' );

    // Delete any bitly links
    delete_post_meta( $id , 'bitly_link_buffer' );
    delete_post_meta( $id , 'bitly_link_facebook' );
    delete_post_meta( $id , 'bitly_link_flipboard' );
    delete_post_meta( $id , 'bitly_link_googlePlus' );
    delete_post_meta( $id , 'bitly_link_hacker_news' );
    delete_post_meta( $id , 'bitly_link_linkedIn' );
    delete_post_meta( $id , 'bitly_link_pinterest' );
    delete_post_meta( $id , 'bitly_link_pocket' );
    delete_post_meta( $id , 'bitly_link_reddit' );
    delete_post_meta( $id , 'bitly_link_stumbleupon' );
    delete_post_meta( $id , 'bitly_link_tumblr' );
    delete_post_meta( $id , 'bitly_link_twitter' );
    delete_post_meta( $id , 'bitly_link_whatsapp' );
    delete_post_meta( $id , 'bitly_link_yummly' );
    delete_post_meta( $id , 'bitly_link' );

    // Delete any custom options fields
    delete_post_meta( $id , 'nc_customTweet' );
    delete_post_meta( $id , 'nc_floatLocation' );
    delete_post_meta( $id , 'nc_ogDescription' );
    delete_post_meta( $id , 'nc_ogImage' );
    delete_post_meta( $id , 'nc_ogTitle' );
    delete_post_meta( $id , 'nc_pinterestDescription' );
    delete_post_meta( $id , 'nc_pinterestImage' );
    delete_post_meta( $id , 'nc_postLocation' );
    delete_post_meta( $id , 'sw_fb_author' );
    delete_post_meta( $id , 'sw_open_graph_image_data' );
    delete_post_meta( $id , 'sw_open_graph_image_url' );
    delete_post_meta( $id , 'sw_open_thumbnail_url' );
    delete_post_meta( $id , 'sw_pinterest_image_url' );
    delete_post_meta( $id , 'sw_twitter_username' );
    delete_post_meta( $id , 'swp_cache_timestamp' );
    delete_post_meta( $id , 'swp_open_graph_image_data' );
    delete_post_meta( $id , 'swp_open_graph_image_url' );
    delete_post_meta( $id , 'swp_open_thumbnail_url' );
    delete_post_meta( $id , 'swp_pinterest_image_url' );
    delete_post_meta( $id , 'swp_recovery_url' );

}
add_action( "dp_duplicate_post", "swp_remove_fields" );

/**
 * A function to fix the share recovery conflict with Really Simple SSL plugin
 * @param  string $html A string of html to be filtered
 * @return string $html The filtered string of html
 * @access public
 * @since 2.2.2
 *
 */
function swp_rsssl_fix_compatibility($html) {
    //replace the https back to http
    $html = str_replace(
         "swp_post_recovery_url = 'https://",
         "swp_post_recovery_url = 'http://",
         $html);
    return $html;
}
add_filter("rsssl_fixer_output","swp_rsssl_fix_compatibility");
