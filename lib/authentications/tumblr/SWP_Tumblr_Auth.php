<?php

/**
 * Loads the official Tubmlr API library and uses it to process &
 * store oAuth handshakes.
 *
 */
class SWP_Tumblr_Auth {


	/**
	 * The user's API secret.
	 * @var string $client_secret;
	 *
	 */
	protected $client_key = '';


	/**
	 * The user's API secret.
	 * @var string $client_secret;
	 *
	 */
	protected $client_secret = '';

	public function __construct() {
		$this->load_files();

		/**
		 *  Dummy data for testing.
		 *  Two options for production.
		 *  1. Leave valid production API keys in plain text here.
		 *  2. Fetch keys from warfareplugins.com using the user's swp_license_key as a password to receive keys.
		 */
		$this->client_key = '417XX50OsviGipm7S0d3CoQq7tYI8pR2sDDXgOj6NPODxlTcU0';
		$this->client_secret = 'v00cOcheNGOrOoHzU6WnU1AbleQQZmGUSRr44rjJsSG3u6mUbg';

		if ( $this->establish_credentials() ) {
			$this->do_follower_count_request();
		}
		else {
			$this->do_authentication_request();
		}
	}


	public function load_files() {
		require_once('./Tumblr/API/Client.php');
		require_once('./Tumblr/API/RequestException.php');
		require_once('./Tumblr/API/RequestHandler.php');
	}


	/**
	 * Fetches stored client credentials.
	 *
	 * A returned value of `false` indicates that the user needs to
	 * go through the authentication process. Retun value of `true`
	 * means we can proceed with th request.
	 *
	 * @since 3.5.0 | 03 JAN 2018 | Created.
	 * @param void
	 * @return bool True iff the credentials exist, else false.
	 *
	 */
	public function establish_credentials() {
		$consumer_key = SWP_Utility::get_option('tumblr_auth_token');
		$consumer_secret = SWP_Utility::get_option('tumblr_auth_secret');

		if ( false == $consumer_key || false ==  $consumer_secret ) {
			return false;
		}

		$this->consumer_key = $consumer_key;
		$this->consuemr_secret = $consumer_secret;
	}


	/**
	 * Checks the cache to see if we need to make a new request.
	 * If so, run the request.
	 * Else return the cached value.
	 *
	 * @since 3.5.0 | 03 JAN 2018 | Created.
	 * @param void
	 * @return mixed True iff the credentials exist, else false.
	 */
	public function do_follower_count_request() {
		$this->client = new Tumblr\API\Client($this->$consumer_key, $this->$consumer_secret);
		$client->setToken($token, $tokenSecret);
	}


	/**
	 * Checks the cache to see if we need to make a new request.
	 * If so, run the request.
	 * Else return the cached value.
	 *
	 * @since 3.5.0 | 03 JAN 2018 | Created.
	 * @param void
	 * @return mixed True iff the credentials exist, else false.
	 */
	public function do_authentication_request() {

	}
}
