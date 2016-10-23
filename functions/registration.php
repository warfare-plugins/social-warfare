<?php
/**
 * Functions for getting and setting the plugin's registration status.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2016, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

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
 * Check to see if the plugin has been registered once per page load.
 *
 * @since  unknown
 * @return bool True if the plugin is registered, false otherwise.
 */
function is_swp_registered() {
	static $is_registered;

	if ( null === $is_registered ) {
		$options = get_option( 'socialWarfareOptions' );

		$domain = swp_get_site_url();
		$key = swp_get_registration_key( $domain, 'db' );

		$is_registered = false;

		// If the Premium Code is currently set....
		if ( isset( $options['premiumCode'] ) && $key === $options['premiumCode'] ) {
			$is_registered = true;
		}
	}

	return $is_registered;
}

/**
 * Get a response from the Social Warfare registration API.
 *
 * @since  2.1.0
 * @param  array $args Query arguments to be sent to the API.
 * @param  bool  $decode Whether or not to decode the API response.
 * @return array
 */
function swp_get_registration_api( $args = array(), $decode = true ) {
	$url = add_query_arg( $args, 'https://warfareplugins.com/registration-api/' );
	$response = wp_remote_get( esc_url_raw( $url ) );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$response = wp_remote_retrieve_body( $response );

	if ( $decode ) {
		$response = json_decode( $response, true );

		if ( isset( $response['status'] ) ) {
			$response['status'] = strtolower( $response['status'] );
		} else {
			$response['status'] = 'failure';
		}
	}

	return $response;
}

/**
 * Attempt to register the plugin.
 *
 * @since  2.1.0
 * @param  string $email The email to use during unregistration.
 * @param  string $domain The domain to use during unregistration.
 * @return bool
 */
function swp_register_plugin( $email, $domain ) {
	$response = swp_get_registration_api( array(
		'activity'         => 'register',
		'emailAddress'     => $email,
		'domain'           => $domain,
		'registrationCode' => swp_get_registration_key( $domain ),
	) );

	if ( ! $response ) {
		return false;
	}

	if ( 'success' === $response['status'] ) {
		swp_update_option( 'premiumCode', $response['premiumCode'] );
		return true;
	}

	return false;
}

/**
 * Attempt to unregister the plugin.
 *
 * @since  2.1.0
 * @param  string $email The email to use during unregistration.
 * @param  string $key The premium key code to be unregistered.
 * @return bool
 */
function swp_unregister_plugin( $email, $key ) {
	$response = swp_get_registration_api( array(
		'activity'     => 'unregister',
		'emailAddress' => $email,
		'premiumCode'  => $key,
	) );

	if ( ! $response ) {
		return false;
	}

	if ( 'success' === $response['status'] ) {
		swp_update_option( 'emailAddress', '' );
		swp_update_option( 'premiumCode', '' );
		return true;
	}

	return false;
}

/**
 * Check if the site is registered at our server.
 *
 * @since  unknown
 * @global $swp_user_options
 * @return bool
 */
function swp_check_registration_status() {
	global $swp_user_options;

	$options = $swp_user_options;

	// Bail early if no premium code exists.
	if ( empty( $options['premiumCode'] ) ) {
		return false;
	}

	$domain = swp_get_site_url();
	$email = $options['emailAddress'];

	$args = array(
		'activity'         => 'check_registration',
		'emailAddress'     => $email,
		'domain'           => $domain,
		'registrationCode' => swp_get_registration_key( $domain ),
	);

	$response = swp_get_registration_api( $args, false );

	$status = is_swp_registered();

	// If the response is negative, unregister the plugin....
	if ( ! $response || 'false' === $response ) {
		if ( swp_register_plugin( $email, $domain ) ) {
			$status = true;
		} else {
			swp_unregister_plugin( $email, $options['premiumCode'] );
			$status = false;
		}
	}

	return $status;
}

add_action( 'admin_init', 'swp_delete_cron_jobs' );
/**
 * Clear out any leftover cron jobs from previous plugin versions.
 *
 * @since  2.1.0
 * @return void
 */
function swp_delete_cron_jobs() {
	if ( wp_get_schedule( 'swp_check_registration_event' ) ) {
		wp_clear_scheduled_hook( 'swp_check_registration_event' );
	}
}

add_action( 'admin_init', 'swp_check_license' );
/**
 * Check to see if the license is valid once every month.
 *
 * @since  2.1.0
 * @return void
 */
function swp_check_license() {
	if ( 'checked' === get_transient( 'swp_check_license' ) ) {
		return;
	}

	if ( defined( 'MONTH_IN_SECONDS' ) ) {
		$month = MONTH_IN_SECONDS;
	} else {
		$month = 30 * DAY_IN_SECONDS;
	}

	swp_check_registration_status();

	set_transient( 'swp_check_license', 'checked', $month );
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

	$message = '';

	if ( 'register' === $data['activity'] && isset( $data['domain'] ) ) {
		$response = swp_register_plugin( $data['email'], $data['domain'] );

		if ( ! $response ) {
			wp_send_json_error( esc_html__( 'Plugin could not be registered.', 'social-warfare' ) );
			die;
		}

		$message = esc_html__( 'Plugin successfully registered!', 'social-warfare' );
	}

	if ( 'unregister' === $data['activity'] && isset( $data['key'] ) ) {
		$response = swp_unregister_plugin( $data['email'], $data['key'] );

		if ( ! $response ) {
			wp_send_json_error( esc_html__( 'Plugin could not be unregistered.', 'social-warfare' ) );
			die;
		}

		$message = esc_html__( 'Plugin successfully unregistered!', 'social-warfare' );
	}

	wp_send_json_success( array( 'message' => $message ) );

	die;
}
