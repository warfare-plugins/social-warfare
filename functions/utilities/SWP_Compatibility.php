<?php

/**
 * SWP_Compatibility: A class to enhance compatibility with other plugins
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 * @since     3.0.0 | 22 FEB 2018 | Refactored into a class-based system.
 *
 */
class SWP_Compatibility {


	/**
	 * The magic method used to insantiate this class.
	 *
	 * This adds compatibility with Simple Podcast Press, the Duplicate Posts
	 * plugin, and Really Simple SSL.
	 *
	 * @since  2.1.4
	 * @access public
	 * @param  integer $id The post ID
	 * @return none
	 *
	 */
	public function __construct() {
		// Disabe Open Graph tags on Simple Podcast Press Pages
		if ( is_plugin_active( 'simple-podcast-press/simple-podcast-press.php' ) ) {
			global $ob_wp_simplepodcastpress;
			remove_action( 'wp_head' , array( $ob_wp_simplepodcastpress, 'spp_open_graph' ) , 1 );
		}

		// Remove our custom fields when a post is duplicated via the Duplicate Post plugin.
		add_action( 'dp_duplicate_post', array( $this , 'remove_fields' ) );

		// Fix the links that are modified by the Really Simple SSL plugin.
		add_filter("rsssl_fixer_output", [$this, 'rsssl_fix_compatibility']   );

	}


	/**
	 * A function to remove all Social Warfare fields when a post
	 * is duplicated using the duplicate post plugin.
	 *
	 * @since  2.1.4
	 * @access public
	 * @param  integer $id The post ID
	 * @return none
	 *
	 */
	function remove_fields($id) {

	    // Delete any share count fields
	    delete_post_meta( $id , '_buffer_shares' );
	    delete_post_meta( $id , '_facebook_shares' );
	    delete_post_meta( $id , '_flipboard_shares' );
	    delete_post_meta( $id , '_google_plus_shares' );
	    delete_post_meta( $id , '_hacker_news_shares' );
	    delete_post_meta( $id , '_linkedin_shares' );
	    delete_post_meta( $id , '_pinterest_shares' );
	    delete_post_meta( $id , '_pocket_shares' );
	    delete_post_meta( $id , '_reddit_shares' );
	    delete_post_meta( $id , '_stumbleupon_shares' );
	    delete_post_meta( $id , '_tumblr_shares' );
	    delete_post_meta( $id , '_twitter_shares' );
	    delete_post_meta( $id , '_whatsapp_shares' );
	    delete_post_meta( $id , '_yummly_shares' );
	    delete_post_meta( $id , '_total_shares' );

	    // Delete any bitly links
	    delete_post_meta( $id , 'bitly_link_buffer' );
	    delete_post_meta( $id , 'bitly_link_facebook' );
	    delete_post_meta( $id , 'bitly_link_flipboard' );
	    delete_post_meta( $id , 'bitly_link_google_plus' );
	    delete_post_meta( $id , 'bitly_link_hacker_news' );
	    delete_post_meta( $id , 'bitly_link_linkedin' );
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
	    delete_post_meta( $id , 'nc_float_location' );
	    delete_post_meta( $id , 'nc_ogDescription' );
	    delete_post_meta( $id , 'swp_og_image' );
	    delete_post_meta( $id , 'nc_ogTitle' );
	    delete_post_meta( $id , 'nc_pinterest_description' );
	    delete_post_meta( $id , 'swp_pinterest_image' );
	    delete_post_meta( $id , 'swp_post_location' );
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

        $new_meta_fields = [
            'swp_og_image',
            'swp_og_title',
            'swp_pinterest_image',
            'swp_custom_tweet',
            'swp_post_location',
            'swp_float_location',
            'swp_pinterest_description',
            'swp_twitter_id',
            'swp_og_description',
            'swp_pinterest_description',
            'swp_cache_timestamp',
            'swp_pin_browser_extension',
            'swp_pin_browser_extension_location',
            'swp_pin_browser_extension_url'
        ];

        foreach($new_meta_fields as $field) {
            delete_post_meta( $id, $field );
        }

	}


	/**
	 * A function to fix the share recovery conflict with Really Simple SSL plugin
	 * @param  string $html A string of html to be filtered
	 * @return string $html The filtered string of html
	 * @access public
	 * @since 2.2.2
	 *
	 */
	function rsssl_fix_compatibility($html) {
	    //replace the https back to http
	    $html = str_replace( "swp_post_recovery_url = 'https://" , "swp_post_recovery_url = 'http://" , $html);
	    return $html;
	}

}
