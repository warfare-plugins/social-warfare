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
	 * @var string $client_secret;
	 *
	 */
	protected $client_key = '';


	/**
	 * The Social Warfare API secret.
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
		return $this->has_credentials ? $this->consuemr_key : false;
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
		return $this->has_credentials ? $this->consuemr_secret : false;
	}


	/**
	 * Provides the user's API key, if it exists.
	 *
	 * @since 3.5.0 | 03 JAN 2018 | Created.
	 * @param void
	 * @return mixed String if the key exists, else false.
	 *
	 */
	public function get_client_key() {
		return $this->has_credentials ? $this->client_key : false;
	}


	/**
	 * Provides the user's API secret, if it exists.
	 *
	 * @since 3.5.0 | 03 JAN 2018 | Created.
	 * @param void
	 * @return mixed String if the key exists, else false.
	 *
	 */
	public function get_client_secret() {
		return $this->has_credentials ? $this->client_secret : false;
	}


}
