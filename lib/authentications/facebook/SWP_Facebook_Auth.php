<?php

/**
 * Loads the official Facebook API library and uses it to process &
 * store oAuth handshakes.
 *
 */
class SWP_Facebook_Auth extends SWP_Auth_Controller {


	public function __construct() {
		$this->key = 'facebook';
		$this->client_key = '2194481457470892';
		$this->client_secret = '6ba6cfd0e6f5930a7578f110baefc178';
		$this->load_files();

		$this->establish_credentials();

		parent::__construct();


	}


	/**
	 * Loads the SDK assets as provided by host.
	 *
	 * @since 3.5.0 | 03 JAN 2018 | Created.
	 * @param void
	 * @return void
	 */
	public function load_files() {
		require_once(__DIR__ . '/Facebook/autoload.php');
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
	public function get_authorization_link() {
		$fb = new Facebook\Facebook(array(
			'app_id' => $this->client_key,
			'app_secret' => $this->client_secret,
			'default_graph_version' => 'v3.2',
		));

		$helper = $fb->getRedirectLoginHelper();

		$permissions = ['email', 'manage_pages']; // Optional permissions
		$login_url = $helper->getLoginUrl('https://warfareplugins.com/authentications/facebook', $permissions);

		return $login_url;
	}


}
