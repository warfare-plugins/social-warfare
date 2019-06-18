<?php

/**
 * The Social Warfare Addon Class
 *
 * This class serves as the parent class for all addons to the Social Warfare
 * framework. This class allows the setting of certain properties (like the
 * addon key, product ID, etc.) that will allow the addon to properly control
 * the registration of the product, the validity of the license key used for the
 * addon, and other necessary checks that allow the addons to integrate
 * seemlessly with the core plugin.
 *
 * @package Social Warfar / Lib
 * @since  3.0.0 | 01 MAR 2018 | Created
 * @since  4.0.0 | 13 JUN 2019 | Updated, refactored, documentation added.
 *
 */
class Social_Warfare_Addon {


	/**
	 * The Magic Constructor Method
	 *
	 * This method instantiates the addon object and sets up all the object
	 * properties that will be needed.
	 *
	 * @since  3.0.0 | 01 MAR 2018 | Created
	 * @since  4.0.0 | 13 JUN 2019 | Updated, refactored, documentation added.
	 * @param  array $args [description]
	 * @return void
	 *
	 */
	public function __construct( $args = array() ) {

		$this->establish_class_properties( $args );
		$this->establish_license_key();
		$this->is_registered = $this->establish_resgistration();


		/**
		 * This queues up our register and unregister hooks that will be sent
		 * from the settings page to admin-ajax.php.
		 *
		 */
		add_action( 'wp_ajax_swp_register_plugin', array( $this, 'register_plugin' ) );
		add_action( 'wp_ajax_swp_unregister_plugin', array( $this, 'unregister_plugin' ) );
		add_action( 'wp_ajax_swp_ajax_passthrough', array( $this, 'ajax_passthrough' ) );


		/**
		 * This is a custom filter hook that gets called in core that fetches
		 * all of the addons so that we can have the key for each addon in a
		 * nice, neat array for easy access.
		 *
		 */
		add_filter( 'swp_registrations', array( $this, 'add_self' ) );
	}


	/**
	 * Establish the Class Properties for this Addon
	 *
	 * Each addon that extends this class will be required to pass in three
	 * required class properties in order to instantiate without errors. The
	 * following is an index of required properties:
	 *
	 * 1. name: This is the nice/pretty name of the addon.
	 * 2. key: This is the snake_cased name of the addon.
	 * 3. version: The current version number of the addon.
	 *
	 * NOTE: These can be passed in as an associative array diring insantation
	 * of your class or they can be set directly as class properties in your
	 * class constructor prior to calling parent__construct();
	 *
	 * Example:
	 *     $this->name    = 'Social Warfare - Pro';
	 *     $this->key     = 'pro';
	 *     $this->version = 3.6.1;
	 *     parent::__contruct();
	 *
	 * @since  3.0.0 | 01 MAR 2019 | Created
	 * @since  4.0.0 | 13 JUN 2019 | Updated, refactored, documentation added.
	 * @param  array  $args An associative array of class properties.
	 * @return void
	 *
	 */
	private function establish_class_properties( $args = array () ) {

		// Migrate all passed $args into local class properties.
		foreach($args as $key => $value) {
			$this->$key = $value;
		}

		// Mandatory class properties for an addon to function properly.
		$required = array( 'name', 'key', 'version' );


		/**
		 * Check to ensure that all required properties have been passed in.
		 * If a required field hasn't been passed in from the addon, we'll
		 * manually trigger an exception here to notify the developer.
		 *
		 */
		foreach($required as $key) {
			if ( !isset( $this->$key ) ) :
				$message = "Hey developer, you are attempting to instantiate a class that extends the Social_Warfare_Addon class. In order to do this, you must provide the following argument for your class: $key => \$value. You can read more about required class properties for this class in the docblock provided in /lib/Social_Warfare_Addon.php for the establish_class_properties() method.";
				throw new Exception($message);
			endif;
		}


		/**
		 * This is the store URL used by Easy Digital Downloads to ping our site
		 * in order to check the validity of the the registration license key.
		 *
		 * In third party addons, vendors can set this to their own websites. If
		 * not, we'll assume it's one of our own addons and ping our site to
		 * check the license key.
		 *
		 */
		if ( isset( $this->product_id ) && empty ( $this->store_url ) ) {
			$this->store_url = 'https://warfareplugins.com';
		}
	}


	/**
	 * The callback function used to add a new instance of this
	 * to our swp_registrations filter.
	 *
	 * See above: add_filter( 'swp_registrations', array( $this, 'add_self' ) );
	 * This should be the last item called in an addon's main class.
	 *
	 * @since  3.0.0 | 01 MAR 2018 | Created
	 * @since  4.0.0 | 13 JUN 2019 | Updated, refactored, documentation added.
	 * @param  array $addons The array of addons currently activated.
	 * @return array $addons The modified array of addons currently activated.
	 *
	 */
	public function add_self( $addons ) {
		$addons[] = $this;
		return $addons;
	}


	/**
	 * A method to fetch the license key from the database and store it in a
	 * local class property for the Addon object.
	 *
	 * @since  3.0.0 | 01 MAR 2018 | Created
	 * @since  4.0.0 | 13 JUN 2019 | Updated, refactored, documentation added.
	 * @param  void
	 * @return void Processed values are stored in $this->license_key.
	 *
	 */
	public function establish_license_key() {


		/**
		 * The license key is stored in our options set in the database. This
		 * utility method allows us to easily retrieve it.
		 *
		 */
		$key = SWP_Utility::get_option( $this->key . '_license_key' );


		/**
		 * This check exists to retrieve the license key from the old, legacy
		 * Social Warfare options set in the database. This was changed to a new
		 * set when 3.0.0 rolled out. This is most likely no longer needed, but
		 * we'll leave it in there for any stragglers who are updating from 2.x
		 * to a current version.
		 *
		 */
		if ( !$key ) {
			$old_options = get_option( 'socialWarfareOptions', false );
			if ( isset( $old_options[$this->key . '_license_key']) ) {
				$key = $old_options[$this->key . '_license_key'];
			}
		}


		/**
		 * If we were able to find a license key, then we'll go ahead and store
		 * it in a local class property for this addon.
		 *
		 */
		$this->license_key = $key ? $key : '';
	}


	/**
	 * A method to ping our EDD storefront API and verify the validity of a
	 * registration license key.
	 *
	 * We recheck the license key to see if it is STILL valid once per week.
	 * This is because if soemone cancels their subscription, files for a refund,
	 * or in some other way brings their license key to the end of it's life,
	 * the plugin will need to be able to detect this and deactivate the premium,
	 * registration locked features.
	 *
	 * @since  3.0.0 | 01 MAR 2018 | Created
	 * @param  void
	 * @return bool The current registration status
	 *
	 */
	public function establish_resgistration() {


		/**
		 * The timestamp in the database will represent the unix time of the
		 * last time that the license key was checked to see if it is still valid.
		 *
		 */
		$timestamp = SWP_Utility::get_option( $this->key . '_license_key_timestamp' );
		if ( empty( $timestamp ) ) {
			$timestamp =  0;
		}

		$time_to_recheck = $timestamp + 604800;
		$current_time    = time();


		/**
		 * If they have a key and a week hasn't passed since the last check,
		 * just return true...the plugin is registered.
		 *
		 */
		if( !empty( $this->license_key)  && $current_time < $time_to_recheck ) {
			return true;
		}


		// If a week has passed since the last check, ping our API to check the validity of the license key
		if ( !empty( $this->license_key) ) :
			global $swp_user_options;

			$data = array(
				'edd_action' => 'check_license',
				'item_id' => $this->product_id,
				'license' => $this->license_key,
				'url' => $this->site_url,
			);

			$response = wp_remote_retrieve_body( wp_remote_post( $this->store_url , array('body' => $data, 'timeout' => 10 ) ) );

			if( false !== $response ) :
				$license_data = json_decode( $response );

				$swp_user_options[$this->key . '_license_key_timestamp'] = $current_time;

				// If the license was invalid
				if ( isset( $license_data->license ) && 'invalid' === $license_data->license ) :
					$this->license_key = '';

					$swp_user_options[$this->key . '_license_key'] = '';

					update_option( 'social_warfare_settings' , $swp_user_options );

					return false;

				// If the property is some other status, just go with it.
				else :
					update_option( 'social_warfare_settings' , $swp_user_options );

					return true;
				endif;

			// If we recieved no response from the server, we'll just check again next week
			else :
				$swp_user_options[$key.'_license_key_timestamp'] = $current_time;
				update_option( 'social_warfare_settings' , $swp_user_options );

				return true;
			endif;
		endif;

		return false;
	}

	public function check_for_updates() {
		if ( version_compare(SWP_VERSION, $this->core_required) >= 0 ) :

		endif;
	}

	/**
	 * Request to EDD to activate the licence.
	 *
	 * @since  2.1.0
	 * @since  2.3.0 Hooked registration into the new EDD Software Licensing API
	 * @param  none
	 * @return JSON Encoded Array (Echoed) - The Response from the EDD API
	 *
	 */
	public function register_plugin() {
		// Check to ensure that license key was passed into the function
		if ( !empty( $_POST['license_key'] ) ) :

			// Grab the license key so we can use it below
			$key = $_POST['name_key'];
			$license = $_POST['license_key'];
			$item_id = $_POST['item_id'];
			$this->store_url = 'https://warfareplugins.com';

			$api_params = array(
				'edd_action' => 'activate_license',
				'item_id' => $item_id,
				'license' => $license,
				'url' => $this->site_url
			);

			$response =  wp_remote_retrieve_body( wp_remote_post( $this->store_url, array( 'body' => $api_params, 'timeout' => 10 ) ) );

			if ( false != $response ) :

				// Parse the response into an object
				$license_data = json_decode( $response );

				// If the license is valid store it in the database
				if( isset($license_data->license) && 'valid' == $license_data->license ) :

					$current_time = time();
					$options = get_option( 'social_warfare_settings' );
					$options[$key.'_license_key'] = $license;
					$options[$key.'_license_key_timestamp'] = $current_time;
					update_option( 'social_warfare_settings' , $options );

					echo json_encode($license_data);
					wp_die();

				// If the license is not valid
				elseif( isset($license_data->license) &&  'invalid' == $license_data->license ) :
					echo json_encode($license_data);
					wp_die();

				// If some other status was returned
				else :
					$license_data['success'] = false;
					$license_data['data'] = 'Invaid response from the registration server.';
					echo json_encode($license_data);
					wp_die();
				endif;

			// If we didn't get a response from the registration server
			else :
				$license_data['success'] = false;
				$license_data['data'] = 'Failed to connect to registration server.';
				echo json_encode($license_data);
				wp_die();
			endif;
		endif;

		$license_data['success'] = false;
		$license_data['data'] = 'Admin Ajax did not receive valid POST data.';
		echo json_encode($license_data);
		wp_die();

	}


	/**
	 * Request to EDD to deactivate the licence.
	 *
	 * @since  2.1.0
	 * @since  2.3.0 Hooked into the EDD Software Licensing API
	 * @param  none
	 * @return JSON Encoded Array (Echoed) - The Response from the EDD API
	 *
	 */
	public function unregister_plugin() {
		// Setup the variables needed for processing
		$options = get_option( 'social_warfare_settings' );
		$key = $_POST['name_key'];
		$item_id = $_POST['item_id'];
		$response = array('success' => false);

		// Check to see if the license key is even in the options
		if ( !SWP_Utility::get_option( $key . '_license_key' ) ) :
			$response['success'] = true;
			wp_die(json_encode($response));
		endif;

		// Grab the license key so we can use it below
		$license = $options[$key.'_license_key'];

		// Setup the API request parameters
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'item_id' => $item_id,
			'license' => $license,
			'url' => $this->site_url,
		);

		$response =  wp_remote_retrieve_body( wp_remote_post( $this->store_url, array( 'body' => $api_params, 'timeout' => 10 ) ) );
		if ( empty( $response ) ) {
			$response['success'] = false;
			$response['message'] = 'Error making deactivation request to ' . $this->store_url;
			wp_die( json_encode( $response ) );
		}

		$response = json_decode( $response );

		if ( $response->license == 'deactivated' || $response->license == 'failed' ) {
			$options = get_option( 'social_warfare_settings' );
			$options[$key.'_license_key'] = '';
			update_option( 'social_warfare_settings' , $options );
		}

		wp_die(json_encode($response));
	}

	public function ajax_passthrough() {

		if ( ! check_ajax_referer( 'swp_plugin_registration', 'security', false ) ) {
			wp_send_json_error( esc_html__( 'Security failed.', 'social-warfare' ) );
			die;
		}

		$data = wp_unslash( $_POST ); // Input var okay.

		if ( ! isset( $data['activity'], $data['email'] ) ) {
			wp_send_json_error( esc_html__( 'Required fields missing.', 'social-warfare' ) );
			die;
		}

		if ( 'register' === $data['activity'] ) {
			$response = swp_register_plugin( $data['email'], SWP_Utility::get_site_url() );

			if ( ! $response ) {
				wp_send_json_error( esc_html__( 'Plugin could not be registered.', 'social-warfare' ) );
				die;
			}

			$response['message'] = esc_html__( 'Plugin successfully registered!', 'social-warfare' );
		}

		if ( 'unregister' === $data['activity'] && isset( $data['key'] ) ) {
			$response = swp_unregister_plugin( $data['email'], $data['key'] );

			if ( ! $response ) {
				wp_send_json_error( esc_html__( 'Plugin could not be unregistered.', 'social-warfare' ) );
				die;
			}

			$response['message'] = esc_html__( 'Plugin successfully unregistered!', 'social-warfare' );
		}

		wp_send_json_success( $response );

		die;
	}
}
