<?php

/**
 * Loads the official Facebook API library and uses it to process &
 * store oAuth handshakes.
 *
 */
class SWP_Facebook_Auth extends SWP_Auth_Controller {


	public function __construct() {
		$this->key = 'facebook';
		$this->load_files();

		if ( !$this->establish_credentials() ) {
			$this->do_authentication_request();
		}
	}


	/**
	 * Loads the SDK assets as provided by host.
	 *
	 * @since 3.5.0 | 03 JAN 2018 | Created.
	 * @param void
	 * @return void
	 */
	public function load_files() {
		require_once('./Facebook/autoload.php');
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
		$fb = new Facebook\Facebook([
			'app_id' => '{app-id}',
			'app_secret' => '{app-secret}',
			'default_graph_version' => 'v2.10',
		]);

		$helper = $fb->getRedirectLoginHelper();

		$permissions = ['email']; // Optional permissions
		$loginUrl = $helper->getLoginUrl('https://warfareplugins.com/authentications/facebook', $permissions);

		echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
	}


}
