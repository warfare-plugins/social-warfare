<?php

/**
* SWP_Link_Manager
*
* A class engineered to manage the links that are shared out to the various social
* networks. This class will shorten them via Bitly or add Google Analytics tracking
* parameters if the user has either of these options enabled and configured.
*
* Organization - This class has one primary function: It provides a publicly
* accessable method that applies the link-modifying filters.
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
