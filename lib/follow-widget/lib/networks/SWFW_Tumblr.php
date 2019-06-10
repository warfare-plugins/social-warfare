<?php

/**
 * SWFW_Tumblr
 *
 * This provides an interface for creating a follow button for Pinterst.
 *
 * @package   social-follow-widget
 * @copyright Copyright (c) 2019, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | Created
 *
 */
class SWFW_Tumblr extends SWFW_Follow_Network {


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
			'key' => 'tumblr',
			'name' => 'Tumblr',
			'cta' => 'Follow',
			'follow_description' => 'Followers',
			'color_primary' => '#39475D',
			'color_accent' => '#27313F',
			'url'	=> 'https://swfw_username.tumblr.com',
			'needs_authorization' => true
		);

		parent::__construct( $network );
	}


	/**
	 * Tumblr-specific request_url.
	 *
	 * @since 1.0.0 | 15 JAN 2019 | Created.
	 * @param void
	 * @return mixed The request URL if credenetials exist, else bool `false`.
	 *
	 */
	public function do_api_request() {
		require_once __DIR__ . '/../vendor/Tumblr/vendor/autoload.php';
		$access_token = $this->auth_helper->get_access_token();
		$access_secret = $this->auth_helper->get_access_secret();

		if ( empty( $access_token )  ) {
			return false;
		}

		if (  empty( $access_secret )) {
			return false;
		}

		$swp_key = '417XX50OsviGipm7S0d3CoQq7tYI8pR2sDDXgOj6NPODxlTcU0';
		$swp_secret = 'v00cOcheNGOrOoHzU6WnU1AbleQQZmGUSRr44rjJsSG3u6mUbg';

		$tumblr = new Tumblr\API\Client( $swp_key, $swp_secret );
		$tumblr->setToken( $access_token, $access_secret );

		$response = $tumblr->getUserInfo();
		if ( !empty( $response ) ) {
			$this->response = $response;
		} else {
			$this->response = false;
		}
	}


	/**
	 * Tumblr-specific response handling.
	 *
	 * @since 1.0.0 | 15 JAN 2019 | Created.
	 * @param void
	 * @return int The follow count provided by Pinterest, or 0.
	 *
	 */
	public function parse_api_response() {
		// $response is already formatted as object thanks to the Tumblr client.

		if ( empty( $this->response ) ) {
			return "0";
		}

		if ( empty( $this->response->user ) || empty( $this->response->user->blogs ) ) {
			return "0";
		}

		/**
		 * A user may have multiple blogs. Let's iterate over each of them
		 * and sum the total followers for all blogs.
		 *
		 */
		foreach( $this->response->user->blogs as $blog ) {
			if ( is_numeric ( $blog->followers ) ) {
				$this->follow_count += (int) $blog->followers;
			}
		}

		return $this->follow_count;
	}
}
