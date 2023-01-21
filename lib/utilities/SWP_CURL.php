<?php

/**
 * SWP_CURL: A class process API share count requests via cURL
 *
 * We no longer actually use cURL, but we're leaving the name of the function
 * the same so that any calls throughout the plugin don't break. However, it
 * is now just a passthrough function in order to call WordPress's own built in
 * requests::request_multiple();
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 * @since     3.0.0 | 22 FEB 2018 | Refactored into a class-based system.
 *
 */
class SWP_CURL {

	/**
	 * SWP_CURL::fetch_shares_via_curl_multi()
	 *
	 * We no longer use curl, so this is just a wrapper function that forwards
	 * the request on to our new WordPress HTTP class.
	 *
	 * @param  array $links An associative array of links
	 *         key = network name
	 *         value = http link where we can fetch shares for a given post
	 * @return array associative array of responses for each network
	 * @since  4.3.0 | 05 JAN 2023 | Created
	 *
	 */
	public static function fetch_shares_via_curl_multi( $links ) {
		return SWP_Requests::fetch_shares_via_wordpress_multi( $links );
	}

	public static function file_get_contents_curl( $url, $headers = null) {
		return SWP_Requests::file_get_contents_http( $url, $headers );
	}

	public static function post_json( $url, $fields, $headers = array() ) {
		return SWP_Requests::post_json( $url, $fields, $headers );
	}
}
