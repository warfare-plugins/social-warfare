<?php

class SWP_Linkedin extends SWP_Social_Network {
    	/**
    	 * The Magic __construct Method
    	 *
    	 * This method is used to instantiate the social network object. It does three things.
    	 * First it sets the object properties for each network. Then it adds this object to
    	 * the globally accessible swp_social_networks array. Finally, it fetches the active
    	 * state (does the user have this button turned on?) so that it can be accessed directly
    	 * within the object.
    	 *
    	 * @since  3.0.0 | 06 APR 2018 | Created
    	 * @param  none
    	 * @return none
    	 * @access public
    	 *
    	 */
    	public function __construct() {

    		// Update the class properties for this network
    		$this->name           = __( 'LinkedIn','social-warfare' );
    		$this->cta            = __( 'share','social-warfare' );
    		$this->key            = 'linkedin';
    		$this->default        = 'true';
    		$this->base_share_url = 'https://www.linkedin.com/countserv/count/share?url=';

    		$this->init_social_network();
    	}


        /**
         * Generate the API Share Count Request URL
         *
         * @since  1.0.0
         * @access public
         * @param  string $url The permalink of the page or post for which to fetch share counts
         * @return string $request_url The complete URL to be used to access share counts via the API
         */
        function get_api_link( $url ) {
        	$request_url = 'https://www.linkedin.com/countserv/count/share?url=' . $url . '&format=json';
        	return $request_url;
        }


        /**
         * Parse the response to get the share count
         *
         * @since  1.0.0
         * @access public
         * @param  string $response The raw response returned from the API request
         * @return int $total_activity The number of shares reported from the API
         */
        public function parse_api_response( $response ) {
        	$response = json_decode( $response, true );
        	return isset( $response['count'] )?intval( $response['count'] ):0;
        }
}
