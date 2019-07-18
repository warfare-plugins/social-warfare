<?php

/**
* SWP_Link_Manager
*
* A class engineered to manage the links that are shared out to the various social
* networks. This class will shorten them via Bitly or add Google Analytics tracking
* parameters if the user has either of these options enabled and configured.
*
* Organization - This class has two primary functions:
*
* 1. It instantiates all link-modifying classes.
*
*    These classes will enqueue their functionality via the swp_link_shortening
*    filter and the swp_analytics filters. This allows us to add as many modifiers
*    as people want and simply add them to the filter.
*
* 2. It provides a publicly accessable method that applies the link-modifying filters.
*
*    This will provides the plugin with an easy to access, static method which
*    can be called from anywhere with minimal properties to quickly fetch a
*    fully processed sharable link for the buttons.
*
* @since 3.0.0 | 14 FEB 2018 | Added check for is_attachment() to swp_google_analytics
* @since 3.0.0 | 04 APR 2018 | Converted to class-based, object-oriented system.
* @since 4.0.0 | 17 JUL 2019 | Refactored into a more expandable system so that
*                              we can easily add multiple link shortening services.
*
*/
class SWP_Link_Manager {


	/**
	 * The magic __construct method.
	 *
	 * This method instantiates the SWP_Link_Manager object. It's primary function
	 * is to add the various methods to their approprate hooks for use later on in
	 * modifying the links.
	 *
	 * @since  3.0.0 | 04 APR 2018 | Created
	 * @since  4.0.0 | 17 JUL 2019 | Migrating link modifying filters into their
	 *                               own classes for more specialized management.
	 * @param  void
	 * @return void
	 *
	 */
	public function __construct() {


		/**
		 * This class will manage and add the URL parameters for shared links
		 * using the Google Analytics UTM format. The link modifications made by
		 * this class are added via filter and will be accessed by applying the
		 * swp_analytics filter.
		 *
		 */
		new SWP_Google_UTM_Tracking();


		/**
		 * This class will manage and process the shortened URLs for shared links
		 * if the user has shortlinks enabled and if they have Bitly selected as
		 * their link shortening integration of choice. The link modifications
		 * made by this class are added via filter and will be accessed by
		 * applying the swp_link_shortening filter.
		 *
		 */
		new SWP_Bitly();
	}


	/**
	 * The function that processes a URL
	 *
	 * This method is used throughout the plugin to fetch URL's that have been
	 * processed with the link shorteners and UTM parameters. It processes an
	 * array of information through the swp_analytics filter and the
	 * swp_link_shortening filter.
	 *
	 * @since  3.0.0 | 04 APR 2018 | Created
	 * @param  string $url     The URL to be modified.
	 * @param  string $network The network on which the URL is being shared.
	 * @param  int    $post_id The post ID.
	 * @return string          The modified URL.
	 *
	 */
	public static function process_url( $url, $network, $post_id, $is_cache_fresh = true ) {


		/**
		 * Bail out if this is an attachment page. We had reports of short links
		 * being created on these.
		 *
		 */
		if( is_attachment() ) {
			return $url;
		}


		/**
		 * Compile all of the parameters passed in into an array so that we can
		 * pass it through our custom filters (which only accept one paramter).
		 *
		 */
		$array['url']         = $url;
		$array['network']     = $network;
		$array['post_id']     = $post_id;
		$array['fresh_cache'] = $is_cache_fresh;
		$array                = apply_filters( 'swp_analytics' , $array );
		$array                = apply_filters( 'swp_link_shortening', $array);

		return $array['url'];
	}
}
