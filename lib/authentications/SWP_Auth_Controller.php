<?php

/**
 * This serves as a collection of common methods for getting, fetching,
 * and storing client keys and secrets.
 *
 */
abstract class SWP_Auth_Controller {


	/**
	 * Whether or not we have registered credentials for this network.
	 * @var bool $has_credentials;
	 *
	 */
	public $has_credentials = false;


	/**
	 * The Social Warfare API key.
	 *
	 * This must be manually set on a per-network basis in the
	 * SWFW_Follow_Network __construct() method.
	 *
	 * @var string $client_secret;
	 *
	 */
	protected $client_key = '';


	/**
	 * The Social Warfare API secret.
	 *
	 * This must be manually set on a per-network basis in the 
	 * SWFW_Follow_Network __construct() method.
	 *
	 * @var string $client_secret;
	 *
	 */
	protected $client_secret = '';


	/**
	 * The user's API key.
	 * @var string $consumer_key;
	 *
	 */
	protected $consumer_key = '';


	/**
	 * The user's API secret.
	 * @var string $consumer_secret;
	 *
	 */
	protected $consumer_secret = '';


	/**
	 * @TODO Start writing network-specific constructors, then abstract them here.
	 */
	public function __construct() {
		$this->establish_credentials();
	}


	/**
	 * Provides the SWP API key, if it exists.
	 *
	 * @since 3.5.0 | 03 JAN 2018 | Created.
	 * @param void
	 * @return mixed String if the key exists, else false.
	 *
	 */
	public function get_consumer_key() {
		return $this->has_credentials ? $this->consumer_key : false;
	}


	/**
	 * Provides the SWP API secret, if it exists.
	 *
	 * @since 3.5.0 | 03 JAN 2018 | Created.
	 * @param void
	 * @return mixed String if the key exists, else false.
	 *
	 */
	public function get_consumer_secret() {
		return $this->has_credentials ? $this->consumer_secret : false;
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
	 * @TODO Some networks may only have a key and no secret.
	 * Right now both are required, so find a way to distinguish the two.
	 *
	 */
	public function establish_credentials() {
		$consumer_key = SWP_Utility::get_option( $this->key . '_auth_token' );
		$consumer_secret = SWP_Utility::get_option( $this->key . '_auth_secret' );

		if ( false == $consumer_key || false ==  $consumer_secret ) {
			return false;
		}

		$this->consumer_key = $consumer_key;
		$this->consuemr_secret = $consumer_secret;
		return $this->has_credentials = true;
	}

	public function add_to_authorizations( $network_keys ) {
		// die(var_dump(' adding this to the list of things that need', $this->key));
		return array_merge( $network_keys, array( $this->key ) );
	}


}
