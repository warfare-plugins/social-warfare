<?php

/**
 * SWP_Requests: A class to process API share count requests via WordPress
 * built in HTTP Request mechanisms.
 *
 * We no longer actually use cURL, so the old SWP_CURL class will simply act as
 * passthrough functions forwarding all calls to this class and just returning
 * what is provided.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 * @since     4.3.0 | 05 JAN 2023 | Created
 *
 */
class SWP_Requests {


	/**
	 * SWP_Requests::fetch_shares_via_wordpress_multi()
	 *
	 * This function allows us to use concurrent calls to fetch up all of the
	 * share counts in one swift call to all of the API's.
	 *
	 * @param  array $links An associative array of links
	 *         key = network name
	 *         value = http link where we can fetch shares for a given post
	 * @return array associative array of responses for each network
	 * @since  4.3.0 | 05 JAN 2023 | Created
	 *
	 */
	public static function fetch_shares_via_wordpress_multi( $links ) {
		if ( SWP_Utility::debug( 'is_cache_fresh' ) ) :
				$started = time();
				echo 'Starting multi curl request at : ' . $started;
		endif;

		// Build out the array that can be passed into request_multiple()
		$request_multi = array();
		foreach ( $links as $key => $value ) {

			// If its not zero, we use it.
			if ( 0 !== $value ) {
				$request_multi[ $key ] = array(
					'url' => $value,
				);

				// If it is zero, we'll just re-add it later.
			} else {
				$discards[ $key ] = 0;
			}
		}

		// Perform the HTTP Request
		$responses = Requests::request_multiple( $request_multi );

		// Loop through and parse the response
		$response = array();
		foreach ( $responses as $key => $object ) {
			$response[ $key ] = $object->body;
		}

		// Merge the discarded zeroes back into it.
		if ( false === empty( $discards ) ) {
			$response = array_merge( $response, $discards );
		}

		// Return the response.
		return $response;
	}

	public static function file_get_contents_http( $url, $headers = null ) {
		$response = wp_remote_get( $url );
		if ( false === is_array( $response ) ) {
			return false;
		}
		return $response['body'];
	}

	/**
	 * SWP_CURL::post_json()
	 *
	 * This function will make an API request using the cURL library instead of WordPress's built-in wp_remote_post()
	 * to resolve issues with the Bitly API's response. This function ensures compatibility and more direct control over the HTTP request.
	 *
	 * @param  string $url The endpoint URL for the API request.
	 * @param  array $fields The body data (usually parameters or data payload) to be sent in the API request.
	 * @param  array $headers Headers to be sent with the API request. Default is JSON content type.
	 * @return string Response from the API request.
	 *
	 * @since  4.4.6 | 21 FEB 2024 | Created as a direct cURL alternative to wp_remote_post() for Bitly API.
	 */
	public static function post_json( $url, $fields, $headers = array() ) {
		if ( ! in_array( 'Content-Type: application/json; charset=utf-8', $headers, true ) ) {
			$headers[] = 'Content-Type: application/json; charset=utf-8';
		}

		$curl = curl_init( $url );

		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $fields ) );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );

		$resp = curl_exec( $curl );

		curl_close( $curl );

		return $resp;
	}
}
