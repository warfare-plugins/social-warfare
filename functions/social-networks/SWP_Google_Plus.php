<?php

/**
 * Google Plus
 *
 * Class to add a Google Plus share button to the available buttons
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | Created | Unknown
 * @since     2.2.4 | Updated | 2 MAY 2017 | Refactored functions & updated docblocking
 * @since     3.0.0 | Updated | 05 APR 2018 | Rebuilt into a class-based system.
 *
 */
class SWP_Google_Plus extends SWP_Social_Network {


	/**
	 * The Magic __construct Method
	 *
	 * This method is used to instantiate the social network object. It does three things.
	 * First it sets the object properties for each network. Then it adds this object to
	 * the globally accessible swp_social_networks array. Finally, it fetches the active
	 * state (does the user have this button turned on?) so that it can be accessed directly
	 * within the object.
	 *
	 * @since  3.0.0 | CREATED | 06 APR 2018
	 * @param  none
	 * @return none
	 * @access public
	 *
	 */
	public function __construct() {

		// Update the class properties for this network
		$this->name    = __( 'Google Plus','social-warfare' );
		$this->cta     = __( '+1','social-warfare' );
		$this->key     = 'google_plus';
		$this->default = 'true';

		$this->init_social_network();
	}

	/**
	 * Generate the API Share Count Request URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $url The permalink of the page or post for which to fetch share counts
	 * @return string $request_url The complete URL to be used to access share counts via the API
	 *
	 */
	public function get_api_link( $url ) {
		return $url;
	}


	/**
	 * Parse the response to get the share count
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $response The raw response returned from the API request
	 * @return int $total_activity The number of shares reported from the API
	 *
	 */
	public function parse_api_response( $response ) {
		$response = json_decode( $response, true );
		return isset( $response[0]['result']['metadata']['globalCounts']['count'] )?intval( $response[0]['result']['metadata']['globalCounts']['count'] ):0;
	}


	/**
	 * Generate the share link
	 *
	 * This is the link that is being clicked on which will open up the share
	 * dialogue.
	 *
	 * @since  3.0.0 | Created | 06 APR 2018
	 * @param  array $array The array of information passed in from the buttons panel.
	 * @return string The generated link
	 * @access public
	 *
	 */
	public function generate_share_link( $array ) {
		$share_link = 'https://plus.google.com/share?url=' . $this->get_shareable_permalink( $array );
		return $share_link;
	}

}
