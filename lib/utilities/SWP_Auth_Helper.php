<?php

/**
 * This serves as a collection of common methods for getting, fetching,
 * and storing client keys and secrets.
 *
 * Since all of the actual handshakes take place on the warfareplugins.com server,
 * SWP application keys and secrets live in those files.
 * The server is also where we keep the network-specific SDKs.
 *
 * This class only tells us if we have access for a network, and if not,
 * which link we should use to request access.
 *
 */
class SWP_Auth_Helper {

	/**
	 * The network this controller interfaces.
	 * @var string $network
	 *
	 */
	public $network = '';


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


	public function __construct( $network_key ) {
		if ( empty( $network_key ) ) {
			error_log('Please provide a network_key when constructing an SWP_Auth_Controller.');
			return;
		}

		$this->network = $network_key;
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
		$consumer_key = SWP_Utility::get_option( $this->network . '_access_token' );

		if ( false === $consumer_key || empty( $consumer_key ) ) {
			return false;
		}

		$this->consumer_key = $consumer_key;
		return $this->has_credentials = true;
	}


	public function add_to_authorizations( $network_keys ) {
		return array_merge( $network_keys, array( $this->network ) );
	}

	/**
	 * Prepares a Log In url for the requested network.
	 *
	 * @since 3.5.0 | 03 JAN 2018 | Created.
	 * @param void
	 * @return string The URL which handles the oAuth handshakes.
	 */
	public function get_authorization_link() {
		// die(var_dump('get_authorization_link  ' . $this->network));
		$request_url = 'https://warfareplugins.com/authorizations/' . $this->network . '/request_token.php';
		return add_query_arg('return_address', admin_url('?page=social-warfare'), $request_url);
	}


}
