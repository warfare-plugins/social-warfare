<?php

/**
 * SWFW_Pinterest
 *
 * This provides an interface for creating a follow button for Pinterst.
 *
 * @package   social-follow-widget
 * @copyright Copyright (c) 2019, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | Created
 *
 */
class SWFW_Pinterest extends SWFW_Follow_Network {


	/**
	 * Applies network-specific data to the SWFW_Follow_Network
	 *
	 * @since 1.0.0 | 03 DEC 2018 | Created.
	 * @see SWFW_Follow_Network
	 * @param void
	 * @return void
	 *
	 */
	public function __construct() {
		$network = array(
			'key'                 => 'pinterest',
			'name'                => 'Pinterest',
			'cta'                 => 'Follow',
			'follow_description'  => 'Followers',
			'color_primary'       => '#CC2029',
			'color_accent'        => '#AB1F25',
			'url'                 => 'https://pinterest.com/swfw_username',
			'placeholder'         => 'username',
			'needs_authorization' => true
		);

		parent::__construct( $network );

	}


	/**
	 * Pinterest-specific request_url.
	 *
	 * @since 1.0.0 | 15 JAN 2019 | Created.
	 * @param void
	 * @return mixed The request URL if credenetials exist, else bool `false`.
	 *
	 */
	public function do_api_request() {
		$access_token = $this->auth_helper->get_access_token();
		if ( false == $access_token ) {
			return false;
		}

		$follow_count = "000";

		/**
		 *  Only pass in `id` for `fields` parameter to reduce the Pinterest
		 *  query, and bump the default `limit` from 25 to 100.
		 *
		 */
		$url = 'https://api.pinterest.com/v1/me/followers/?access_token='.$access_token.'&fields=id&limit=100';

		// If there are more than 100 followers, keep requesting until we get them all.
		do {
			$encoded_response = SWP_CURL::file_get_contents_curl( $url );
			$response = json_decode( $encoded_response );

			if ( empty( $response->data ) || null == $response->data || 'NULL' == $response->data ) {
				$this->follow_count = 0;
				return;
			}

			if ( is_array( $response->data) ) {
				$follow_count += count( $response->data );
			}

			// Get the next URL for the next 100 followers.
			if ( !empty ($response->page ) && !empty( $response->page->next ) ) {
				$url = $response->page->next;
			}
		} while ( !empty( $response->page ) && !empty( $response->page->next ) );

		$this->follow_count = $follow_count;
	}


	/**
	 * Pinterest-specific response handling.
	 *
	 * Since the API requests may be run in multiple calls, they are all handled
	 * in teh do_api_request method and the sum of counts already processed.
	 *
	 * @since 1.0.0 | 15 JAN 2019 | Created.
	 * @param void
	 * @return int The follow count provided by Pinterest, or 0.
	 *
	 */
	public function parse_api_response() {
		return $this->follow_count;
	}
}
