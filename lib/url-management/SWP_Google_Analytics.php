<?php

/**
 * SWP_Google_Analytics
 *
 * A class to manage the URL parameters added to the end of URL's that are
 * shared out the various social networks.
 *
 * @since  4.0.0 | 17 JUL 2019 | Created
 *
 */
class SWP_Google_Analytics {


	/**
	 * The Magic Constructor
	 *
	 * Just queue up our method to add the analytics parameters when the
	 * swp_analytics filter is applied.
	 *
	 * @since  4.0.0 | 17 JUL 2019 | Created
	 * @param  void
	 * @return void
	 *
	 */
	public function __construct() {
		add_filter( 'swp_analytics', array( $this, 'add_analytics_parameters' ) );
	}


	/**
	 * Google Analytics UTM Tracking Parameters
	 *
	 * This is the method used to add Google analytics UTM parameters to the links
	 * that are being shared on social media.
	 *
	 * @since  3.0.0 | 04 APR 2018 | Created
	 * @since  3.4.0 | 16 OCT 2018 | Refactored, Simplified, Docblocked.
	 * @since  4.0.0 | 17 JUL 2019 | Moved to SWP_Google_Analytics class.
	 * @param  array $args An array of arguments and data used in processing the URL.
	 *         $args['url']         = $url;              String (e.g. 'http://google.com')
	 *         $args['network']     = $network;          String (e.g. 'twitter')
	 *         $args['post_id']     = $post_id;          Interger (e.g 357)
	 *         $args['fresh_cache'] = $is_cache_fresh;   Boolean
	 * @return array $args The modified array.
	 *
	 */
	public function add_analytics_parameters( $args ) {


		/**
		 * If Google UTM Analytics are disabled, then bail out early and return
		 * the unmodified array.
		 *
		 */
		if ( false == SWP_Utility::get_option('google_analytics') ) {
			return $args;
		}


		/**
		 * If the network is Pinterest and UTM analtyics are disabled on
		 * Pinterest links, then simply bail out and return the unmodified array.
		 *
		 */
		if ( 'pinterest' === $args['network'] && false == SWP_Utility::get_option('utm_on_pins') ) {
			return $args;
		}


		/**
		 * If we're on a WordPress attachment, then bail out early and return
		 * the unmodified array.
		 *
		 */
		if ( true === is_attachment() ) {
			return $args;
		}


		/**
		 * If all of our checks are passed, then compile the UTM string, attach
		 * it to the end of the URL, and return the modified array.
		 *
		 */
		$args['url']  = add_query_arg( 'utm_source', $args['network'], $args['url'] );
		$args['url']  = add_query_arg( 'utm_medium', SWP_Utility::get_option( 'analytics_medium' ), $args['url'] );
		$args['url']  = add_query_arg( 'utm_campaign', SWP_Utility::get_option( 'analytics_campaign' ), $args['url'] );

		return $args;
	}
}
