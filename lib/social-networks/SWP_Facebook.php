<?php

/**
 * Facebook
 *
 * Class to add a Facebook share button to the available buttons
 *
 * @package   SocialWarfare\Functions\Social-Networks
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | Unknown     | CREATED
 * @since     2.2.4 | 02 MAY 2017 | Refactored functions & updated docblocking
 * @since     3.0.0 | 05 APR 2018 | Rebuilt into a class-based system.
 * @since     3.6.0 | 22 APR 2018 | Removed all Javascript related functions for
 *                                  fetching share counts. This includes:
 *                                      register_cache_processes()
 *                                      add_facebook_footer_hook()
 *                                      print_facebook_script()
 *                                      facebook_shares_update()
 *                                 Shares are now fetched using the same two
 *                                 method process that are used by all other
 *                                 social networks in the plugin.
 *
 */
class SWP_Facebook extends SWP_Social_Network {


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
		$this->name           = __( 'Facebook','social-warfare' );
		$this->cta            = __( 'Share','social-warfare' );
		$this->key            = 'facebook';
		$this->default        = 'true';
		$this->base_share_url = 'https://www.facebook.com/share.php?u=';

		$this->init_social_network();
	}


	/**
	 * Generate the API Share Count Request URL
	 *
	 * @since  1.0.0 | 06 APR 2018 | Created
	 * @since  3.6.0 | 22 APR 2019 | Updated API to v3.2.
	 * @access public
	 * @param  string $url The permalink of the page or post for which to fetch share counts
	 * @return string $request_url The complete URL to be used to access share counts via the API
	 *
	 */
	public function get_api_link( $url ) {
		$access_token = base64_decode('MTc5NjYwNzk4Nzc0Mjk2fGZld2FfS0VPUzBwZWxzcFBPZndfanFsanFUaw==');
		return 'https://graph.facebook.com/v3.2/?id='.$url.'&fields=engagement&access_token=' . $access_token;
	}


	/**
	 * Parse the response to get the share count
	 *
	 * @since  1.0.0 | 06 APR 2018 | Created
	 * @since  3.6.0 | 22 APR 2019 | Updated to parse API v. 3.2.
	 * @access public
	 * @param  string  $response The raw response returned from the API request
	 * @return integer $total_activity The number of shares reported from the API
	 *
	 */
	public function parse_api_response( $response ) {

		// JSON decode the response.
		$formatted_response = json_decode( $response , true);

		// Parse the response to get integers.
		if( !empty( $formatted_response['engagement'] ) ) {
			$reaction_count = $formatted_response['engagement']['reaction_count'];
			$comment_count  = $formatted_response['engagement']['comment_count'];
			$share_count    = $formatted_response['engagement']['share_count'];
		} else {
			$reaction_count = $comment_count = $share_count = 0;
		}

		// Add up the integers and return the total.
		$total = $reaction_count + $comment_count + $share_count;
		return $total;
	}

}
