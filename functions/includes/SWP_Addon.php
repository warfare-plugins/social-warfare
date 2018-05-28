<?php

class SWP_Addon extends Social_Warfare {
    public function __construct() {
        parent::__construct();
        $this->name = '';
        $this->product_id = 0;
        $this->key = '';
        $this->version = '';
        $this->core_required = '3.0.0';
        $this->store_url = 'https://warfareplugins.com';
        $this->site_url = swp_get_site_url();
        add_action( 'wp_ajax_swp_register_plugin', [$this, 'register_plugin'] );
        add_action( 'wp_ajax_swp_unregister_plugin', [$this, 'unregister_plugin'] );
        add_action( 'wp_ajax_swp_ajax_passthrough', [$this, 'ajax_passthrough'] );
    }


    /**
     * The callback function used to add a new instance of this /**
      * to our swp_registrations filter.
      *
      * This should be the last item called in an addon's main file.
      *
     * @param array $addons The array of addons currently activated.
     */
    public function add_self( $addons ) {
        $this->establish_license_key();
        $this->registered = $this->is_registered();

        $addons[] = $this;

        return $addons;
    }

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

    public function establish_license_key() {
        $options = get_option( 'social_warfare_settings' );

        if ( isset ( $options[ $this->key . '_license_key'] ) ) :
            $this->license_key = $options[ $this->key . '_license_key'];
        endif;

        $this->license_key = '';
    }

    public function is_registered() {
        // Get the plugin options from the database
    	$options = get_option( 'social_warfare_settings', false );
        $old_options = get_option( 'socialWarfareOptions', false );

        if ( isset( $options[$this->key . '_license_key'] ) ) :
            $this->license_key = $options[$this->key . '_license_key'];
        elseif ( isset( $old_options[$this->key . '_license_key'] ) ) :
            $this->license_key = $old_options[$this->key . '_license_key'];
        else:
            $this->license_key = '';
        endif;


    	// Get the timestamps setup for comparison to see if a week has passed since our last check
    	$current_time = time();

        if ( isset($options[$this->key.'_license_key_timestamp'] ) ) {
            $timestamp = $options[$this->key . '_license_key_timestamp'];
        }

    	$timestamp = isset ( $timestamp ) ? $timestamp  : 0;

    	$time_to_recheck = $timestamp + 604800;

    	// If they have a key and a week hasn't passed since the last check, just return true...the plugin is registered.
    	if( !empty( $this->license_key)  && $current_time < $time_to_recheck ) :
    		return true;
        endif;

        // If a week has passed since the last check, ping our API to check the validity of the license key
        if ( !empty( $this->license_key) ) :

            $data = array(
                'edd_action' => 'check_license',
                'item_id' => $this->product_id,
                'license' => $this->license_key,
                'url' => $this->site_url,
            );

            $response = wp_remote_retrieve_body( wp_remote_post( $this->store_url , array('body' => $data, 'timeout' => 10 ) ) );

    		if( false !== $response ) :

    			// Parse the response into an object
    			$license_data = json_decode( $response );

                $options[$this->key . '_license_key_timestamp'] = $current_time;

    			// If the license was invalid
    			if ( isset( $license_data->license ) && 'invalid' === $license_data->license ) :
    				$is_registered = false;
    				$this->license_key = '';

                    $options[$this->key . '_license_key'] = '';
					
    				update_option( 'social_warfare_settings' , $options );

                    return false;

    			// If the property is some other status, just go with it.
    			else :
                    $is_registered = true;
    				update_option( 'social_warfare_settings' , $options );

                    return true;

    			endif;

    		// If we recieved no response from the server, we'll just check again next week
    		else :
    			$options[$key.'_license_key_timestamp'] = $current_time;
    			update_option( 'social_warfare_settings' , $options );

                return true;
    		endif;
    	endif;


    	return false;
    }

    public function check_for_updates() {
        if ( version_compare(SWP_VERSION, $this->core_required) >= 0 ) :

        endif;
    }

    public function unregister_plugin() {
        // Setup the variables needed for processing
    	$options = get_option( 'social_warfare_settings' );
    	$key = $_POST['name_key'];
    	$item_id = $_POST['item_id'];

    	// Check to see if the license key is even in the options
    	if ( empty( $options[$key.'_license_key'] ) ) :
    		$response['success'] = true;
    		echo json_encode($response);
    	else :
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

			$options = get_option( 'social_warfare_settings' );
			$options[$key.'_license_key'] = '';
			update_option( 'social_warfare_settings' , $options );
			echo json_encode($license_data);
    	endif;

    	wp_die();
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
}
