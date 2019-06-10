<?php
/**
 * SWFW_Facebook
 *
 * This provides an interface for creating a follow button for Facebook.
 *
 * @package   social-follow-widget
 * @copyright Copyright (c) 2019, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | 15 DEC 2018 | Created.
 *
 */
class SWFW_Facebook extends SWFW_Follow_Network {


	/**
	 * Applies network-specific data to the SWFW_Follow_Network
	 *
	 * @since 1.0.0 | 03 DEC 2018 | Created.
	 * @see SWFW_Follow_Network
	 * @param void
	 * @return void
	 */
	public function __construct() {
		$network = array(
			'key'                 => 'facebook',
			'name'                => 'Facebook',
			'cta'                 => 'Like',
			'follow_description'  => 'Fans',
			'color_primary'       => '#3A589E',
			'color_accent'        => '#314E84',
			'url'                 => 'https://facebook.com/swfw_username',
			'placeholder'		  => 'pageID',
			'needs_authorization' => true
		);

		parent::__construct( $network );
		$this->establish_client();

	}


	/**
	 * Facebook-specific request_url.
	 *
	 * @since 1.0.0 | 15 JAN 2019 | Created.
	 * @param void
	 * @return mixed The request URL if credentials exist, else bool `false`.
	 *
	 */
	public function do_api_request() {
		if ( !$this->auth_helper->has_credentials ) {
			return false;
		}

		$page_access_token = SWP_Credential_Helper::get_token( 'facebook', 'page_access_token' );

		if ( false == $page_access_token ) {
			$page_access_token = $this->do_page_token_request();

			if ( false == $page_access_token ) {
				// Bad request, error logged in do_page_token_request().
				return $this->response = false;
			}
		}

		try {
		  $pageID   = $this->username;
		  $endpoint = "/$pageID/?fields=fan_count";
		  $response = $this->client->get($endpoint, $page_access_token);
		  $this->response = $response->getGraphNode();

		} catch( Facebook\Exceptions\FacebookResponseException $e ) {
			$message = 'Graph returned an error: ' . $e->getMessage();
			error_log($message);
		} catch( Facebook\Exceptions\FacebookSDKException $e ) {
			$message = 'Facebook SDK returned an error: ' . $e->getMessage();
			error_log($message);
		}
	}

	public function parse_api_response() {
		if ( empty ( $this->response ) ) {
			return $this->follow_count = "000";
		}

		$fan_count = $this->response->getField('fan_count');

		if ( !empty( $fan_count ) ) {
			$this->follow_count = $fan_count;
		}

		return $this->follow_count;
	}

	protected function establish_client() {
		require_once __DIR__ . '/../vendor/Facebook/autoload.php';
		session_start();

		$this->client = new Facebook\Facebook(array(
			'app_id'     => '2194481457470892',
			'app_secret' => '8d3ffda53c0fca343a4d0932eb006037',
		));

		return $this->client;
	}


	// Uses the $user_access_token to get the page_access_token.
	// return false or $page_access_token
	protected function do_page_token_request() {
		$endpoint = "/{$this->username}?fields=access_token";

		try {
			$access_token = $this->auth_helper->get_access_token();
			$response = $this->client->get( $endpoint, $access_token );
			$node = $response->getGraphNode();
			$page_access_token = $node->getField('access_token');
			if ( !empty( $page_access_token ) ) {
				SWP_Credential_Helper::store_data( 'facebook', 'page_access_token', $page_access_token );
				return $page_access_token;
			}

		   $message = 'SWFW_Facebook::do_page_token_request() could not successfully request a token from Facebook for username ' . $this->username . '.';
		}

		catch( Facebook\Exceptions\FacebookResponseException $e ) {
			$message = 'Graph returned an error: ' . $e->getMessage();
		}

		catch( Facebook\Exceptions\FacebookSDKException $e ) {
			$message = 'Facebook SDK returned an error: ' . $e->getMessage();
		}

		error_log($message);
		return false;
	}
}
