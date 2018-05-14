<?php
/**
 * Functions for getting and setting the plugin's registration status.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

 /**
  * A function to be used to make cURL requests
  * @param  string $url The URL to be fetched
  * @return string The response from the server
  *
  */
function swpp_file_get_contents_curl( $url ) {
 	$ch = curl_init();
 	curl_setopt( $ch, CURLOPT_URL, $url );
 	curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
 	curl_setopt( $ch, CURLOPT_FAILONERROR, 0 );
 	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 0 );
 	curl_setopt( $ch, CURLOPT_RETURNTRANSFER,1 );
 	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
 	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
 	curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
 	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5 );
 	curl_setopt( $ch, CURLOPT_NOSIGNAL, 1 );
 	curl_setopt( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
 	$cont = @curl_exec( $ch );
 	$curl_errno = curl_errno( $ch );
 	curl_close( $ch );
 	if ( $curl_errno > 0 ) {
 		return false;
 	}
 	return $cont;
}

/**
 * Check to see if the plugin has been registered once per page load.
 *
 * @since  2.1.0
 * @param  string $domain The current site's domain.
 * @param  string $context The context where the key will be used.
 * @return string A registration key based on the site's domain.
 */
function swp_get_registration_key( $domain, $context = 'api' ) {
	$key = md5( $domain );

	if ( 'db' === $context ) {
		$key = md5( $key );
	}

	return $key;
}

/**
 * Check to see if an addon has been registered once per page load.
 * Once per week, we'll ping our server to ask if the license key is still valid.
 *
 * @since  2.3.3 - Created the function to work for all addons, not just the pro addon
 * @since  3.0.6 | 14 MAY 2018 | Added check for array key to prevent undefined index notice.
 * @param string The unique key for the addon
 * @return bool True if the plugin is registered, false otherwise.
 */
function is_swp_addon_registered($key) {

	// Get the plugin options from the database
	$options = get_option( 'social_warfare_settings' );
	$is_registered = false;

	// Get the timestamps setup for comparison to see if a week has passed since our last check
	$current_time = time();
	if(!isset($options[$key.'_license_key_timestamp'])):
		$timestamp = 0;
	else:
		$timestamp = $options[$key.'_license_key_timestamp'];
	endif;
	$time_to_recheck = $timestamp + 604800;

	// If they have a key and a week hasn't passed since the last check, just return true...the plugin is registered.
	if( !empty($options[$key.'_license_key']) && $current_time < $time_to_recheck ) {

		$is_registered = true;

	// If a week has indeed passed since the last check, ping our API to check the validity of the license key
    } elseif( !empty($options[$key.'_license_key']) ){

		// Setup the API parameters
		$license = $options[$key.'_license_key'];
        $site_url = swp_get_site_url();
        $store_url = 'https://warfareplugins.com';
        $registration_array = array();
        $registration_array = apply_filters( 'swp_registrations' , $registration_array );

        if ( !array_key_exists( $key, $registration_array ) ) :
            return $is_registered;
        endif;

        $item_id = $registration_array[$key]['product_id'];


        $api_params = array(
            'edd_action' => 'check_license',
            'item_id' => $item_id,
            'license' => $license,
            'url' => $site_url,
        );

        $response = wp_remote_retrieve_body( wp_remote_post( $store_url , array('body' => $api_params, 'timeout' => 10 ) ) );

		if( false != $response ) {

			// Parse the response into an object
			$license_data = json_decode( $response );

			// If the license was invalid
			if( isset($license_data->license) && 'invalid' == $license_data->license) {
				$is_registered = false;
				$options[$key.'_license_key'] = '';
				$options[$key.'_license_key_timestamp'] = $current_time;
				update_option( 'social_warfare_settings' , $options );

			// If the property is some other status, just go with it.
			} else {
				$options[$key.'_license_key_timestamp'] = $current_time;
				update_option( 'social_warfare_settings' , $options );
				$is_registered = true;
			}

		// If we recieved no response from the server, we'll just check again next week
		} else {
			$options[$key.'_license_key_timestamp'] = $current_time;
			update_option( 'social_warfare_settings' , $options );
			$is_registered = true;
		}
	}

	// Return the registration value true/false
	return $is_registered;
}

/**
 * Check to see if the plugin has been registered once per page load.
 * Once per week, we'll ping our server to ask if the license key is still valid.
 *
 * @since  unknown
 * @since 2.3.3 Forward the request to the is_swp_addon_registered() function.
 * @return bool True if the plugin is registered, false otherwise.
 */
function is_swp_registered($timeline = false) {

    return is_swp_addon_registered('pro');

}

/**
 * Attempt to register the plugin.
 *
 * @since  2.1.0
 * @since  2.3.0 Hooked registration into the new EDD Software Licensing API
 * @param  none
 * @return JSON Encoded Array (Echoed) - The Response from the EDD API
 *
 */
add_action( 'wp_ajax_swp_register_plugin', 'swp_register_plugin' );
function swp_register_plugin() {

	// Check to ensure that license key was passed into the function
	if(!empty($_POST['license_key'])) {

		// Grab the license key so we can use it below
		$name_key = $_POST['name_key'];
		$license = $_POST['license_key'];
		$item_id = $_POST['item_id'];
        $site_url = swp_get_site_url();
        $store_url = 'https://warfareplugins.com';

        $api_params = array(
            'edd_action' => 'activate_license',
            'item_id' => $item_id,
            'license' => $license,
            'url' => $site_url
        );

        $response =  wp_remote_retrieve_body( wp_remote_post( $store_url, array( 'body' => $api_params, 'timeout' => 10 ) ) );

		// $url ='https://warfareplugins.com/?edd_action=activate_license&item_id='.$item_id.'&license='.$license.'&url='.swp_get_site_url();
		// $response = swpp_file_get_contents_curl( $url );

		if(false != $response){

			// Parse the response into an object
			$license_data = json_decode( $response );

			// If the license is valid store it in the database
			if( isset($license_data->license) && 'valid' == $license_data->license ) {

				$current_time = time();
				$options = get_option( 'social_warfare_settings' );
				$options[$name_key.'_license_key'] = $license;
				$options[$name_key.'_license_key_timestamp'] = $current_time;
				update_option( 'social_warfare_settings' , $options );

				echo json_encode($license_data);
				wp_die();

			// If the license is not valid
			} elseif( isset($license_data->license) &&  'invalid' == $license_data->license ) {
				echo json_encode($license_data);
				wp_die();

			// If some other status was returned
			} else {
				$license_data['success'] = false;
				$license_data['data'] = 'Invaid response from the registration server.';
				echo json_encode($license_data);
				wp_die();
			}

		// If we didn't get a response from the registration server
		} else {
			$license_data['success'] = false;
			$license_data['data'] = 'Failed to connect to registration server.';
			echo json_encode($license_data);
			wp_die();
		}
	} else {
		$license_data['success'] = false;
		$license_data['data'] = 'Admin Ajax did not receive valid POST data.';
		echo json_encode($license_data);
		wp_die();
	}

	wp_die();

}

/**
 * Attempt to unregister the plugin.
 *
 * @since  2.1.0
 * @since  2.3.0 Hooked into the EDD Software Licensing API
 * @param  none
 * @return JSON Encoded Array (Echoed) - The Response from the EDD API
 */
add_action( 'wp_ajax_swp_unregister_plugin', 'swp_unregister_plugin' );
function swp_unregister_plugin() {

    // Setup the variables needed for processing
	$options = get_option( 'social_warfare_settings' );
	$name_key = $_POST['name_key'];
	$item_id = $_POST['item_id'];
    $site_url = swp_get_site_url();
    $store_url = 'https://warfareplugins.com';

	// Check to see if the license key is even in the options
	if(empty($options[$name_key.'_license_key'])) {
		$response['success'] = true;
		echo json_encode($response);
	} else {

		// Grab the license key so we can use it below
		$license = $options[$name_key.'_license_key'];

        // Setup the API request parameters
        $api_params = array(
            'edd_action' => 'deactivate_license',
            'item_id' => $item_id,
            'license' => $license,
            'url' => $site_url,
        );

        $response =  wp_remote_retrieve_body( wp_remote_post( $store_url, array( 'body' => $api_params, 'timeout' => 10 ) ) );

        // Parse the response into an object
		$license_data = json_decode( $response );

		// If the deactivation was valid update the database
		if( isset($license_data->license) && $license_data->license == 'valid' ) {

			$options = get_option( 'social_warfare_settings' );
			$options[$name_key.'_license_key'] = '';
			update_option( 'social_warfare_settings' , $options );
			echo json_encode($license_data);
			wp_die();

		// If the API request didn't work, just deactivate locally anyways
		} else {

			$options = get_option( 'social_warfare_settings' );
			$options[$name_key.'_license_key'] = '';
			update_option( 'social_warfare_settings' , $options );
			echo json_encode($license_data);
			wp_die();
		}
	}

	wp_die();
}

add_action( 'wp_ajax_swp_ajax_passthrough', 'swp_ajax_passthrough' );
/**
 * Pass ajax responses to a remote HTTP request.
 *
 * @since  2.0.0
 * @return void
 */
function swp_ajax_passthrough() {
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
		$response = swp_register_plugin( $data['email'], swp_get_site_url() );

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
