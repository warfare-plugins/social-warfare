<?php

class SWP_Registration_Tab_Template extends SWP_Option {
    public $key = '';
    public $license_key = '';
    public $product_id = 0;
    public $registered = 0;

    public function __construct( $addon ) {
        parent::__construct( $addon->name, $addon->key );
        $this->display_name = $addon->name;
        $this->key = $addon->key;
        $this->license_key = $this->get_license_key();
        $this->product_id = $addon->product_id;
        $this->version = $addon->version;

        //* TODO These methods exist in Social_Warfare_Addon. See which ones are better and use those.


        // add_action( 'wp_ajax_swp_register_plugin',   array( $this, 'register_plugin' ) );
        // add_action( 'wp_ajax_swp_unregister_plugin', array( $this, 'unregister_plugin' ) );
        // add_action( 'wp_ajax_swp_ajax_passthrough',  array( $this, 'ajax_passthrough' ) );
    }

    public function render_HTML() {
        if ( !empty( $this->license_key ) ) :
            $this->registered = 1;
        endif;

        $html = '<div class="registration-wrapper '. $this->key . '" registration="' . $this->registered . '">';
            $html .= '<h2>' . __($this->name . ' Registration', 'social-warfare') . '</h2>';

            //* Print both types of HTML. Javascript determines which to display.
            $html .= $this->not_registered_HTML();
            $html .= $this->is_registered_HTML();

        $html .= '</div>';

        $this->html = $html;

        return $html;
    }

    public function get_license_key() {
        $license = $this->key . '_license_key';

        if ( isset( $this->user_options[$license] ) ) {
            return $this->user_options[$license];
        }

        return '';
    }


    /**
     * Pass ajax responses to a remote HTTP request.
     *
     * @since  2.0.0
     * @return void
     */
    function ajax_passthrough() {
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
    		$response = $this->register_plugin( $data['email'], SWP_Utility::get_site_url() );

    		if ( ! $response ) {
    			wp_send_json_error( esc_html__( 'Plugin could not be registered.', 'social-warfare' ) );
    			die;
    		}

    		$response['message'] = esc_html__( 'Plugin successfully registered!', 'social-warfare' );
    	}

    	if ( 'unregister' === $data['activity'] && isset( $data['key'] ) ) {
    		$response = $this->unregister_plugin( $data['email'], $data['key'] );

    		if ( ! $response ) {
    			wp_send_json_error( esc_html__( 'Plugin could not be unregistered.', 'social-warfare' ) );
    			die;
    		}

    		$response['message'] = esc_html__( 'Plugin successfully unregistered!', 'social-warfare' );
    	}

    	wp_send_json_success( $response );

    	die;
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

    function register_plugin() {

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

    function unregister_plugin() {
        echo json_encode(['success' => true]);
        wp_die();
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
        $response['success'] = true;
        echo json_encode( $response );

    	wp_die();
    }


    protected function not_registered_HTML() {
        $html = '<div class="sw-grid sw-col-940 swp_is_not_registered">';

            $html .= '<div class="sw-red-notice">';
                $html .=  __( 'This copy of '. $this->name .' is NOT registered. <a target="_blank" href="https://warfareplugins.com">Click here</a> to purchase a license or add your account info below.' , 'social-warfare' );
            $html .= '</div>';

            $html .= '<p class="sw-subtitle sw-registration-text">';
                $html .= __( 'Enter your registration key for '. $this->name .' and then click Register Plugin.' , 'social-warfare' );
            $html .= '</p>';

            $html .= '<div class="sw-grid sw-col-300">';
                $html .= '<p class="sw-input-label">';
                    $html .= __( $this->name . ' License Key' , 'social-warfare' );
                $html .= '</p>';
            $html .= '</div>';

            $html .= '<div class="sw-grid sw-col-300">';
                $html .= '<input name="' . $this->key . '_license_key" type="text" class="sw-admin-input" placeholder="License Key" value="' . $this->license_key . '" />';
            $html .= '</div>';

            $html .= '<div class="sw-grid sw-col-300 sw-fit register_button_grid">';
                $html .= '<a href="#" class="register-plugin button sw-navy-button" swp-addon="' . $this->key . '"  swp-item-id="' . $this->product_id . '">';
                    $html .= __( 'Register Plugin' , 'social-warfare' );
                $html .= '</a>';
            $html .= '</div>';

            $html .= '<div class="sw-clearfix"></div>';
        $html .= '</div>';

        return $html;
    }

    protected function is_registered_HTML() {
        ob_start();

        ?>

        <div class="sw-grid sw-col-940 swp_is_registered">

            <div class="sw-green-notice">
                <?php _e( 'This copy of '. $this->name .' is registered. Wah-hoo!', 'social-warfare' ); ?>
            </div>

            <p class="sw-subtitle sw-registration-text">
                <?php _e( 'To unregister your license click the button below to free it up for use on another domain.' , 'social-warfare' ); ?>
            </p>

            <div class="sw-grid sw-col-300">
                <p class="sw-authenticate-label">
                    <?php _e( 'Deactivate Registration' , 'social-warfare' ); ?>
                </p>
            </div>

            <div class="sw-grid sw-col-300">
                <a href="#" class="unregister-plugin button sw-navy-button" swp-addon="<?php echo $this->key ?>"  swp-item-id="<?php echo $this->product_id; ?>">
                    <?php _e( 'Unregister Plugin' , 'social-warfare' ); ?>
                </a>
            </div>
            <div class="sw-grid sw-col-300 sw-fit"></div>

        </div>

        <?php

        $html = ob_get_clean();

        return $html;
    }
}
