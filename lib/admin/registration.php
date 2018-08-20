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
        $site_url = SWP_Utility::get_site_url();
        $store_url = 'https://warfareplugins.com';

        $api_params = array(
            'edd_action' => 'activate_license',
            'item_id' => $item_id,
            'license' => $license,
            'url' => $site_url
        );

        $response =  wp_remote_retrieve_body( wp_remote_post( $store_url, array( 'body' => $api_params, 'timeout' => 10 ) ) );

		// $url ='https://warfareplugins.com/?edd_action=activate_license&item_id='.$item_id.'&license='.$license.'&url='.SWP_Utility::get_site_url();
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
    $site_url = SWP_Utility::get_site_url();
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
