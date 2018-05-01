<?php

/**
 * Twitter
 *
 * Class to add a Twitter share button to the available buttons
 *
 * @package   SocialWarfare\Functions\Social-Networks
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | Unknown     | Created
 * @since     2.2.4 | 02 MAY 2017 | Refactored functions & updated docblocking
 * @since     3.0.0 | 07 APR 2018 | Rebuilt into a class-based system.
 *
 */
class SWP_Twitter extends SWP_Social_Network {


	/**
	 * The Magic __construct Method
	 *
	 * This method is used to instantiate the social network object. It does three things.
	 * First it sets the object properties for each network. Then it adds this object to
	 * the globally accessible swp_social_networks array. Finally, it fetches the active
	 * state (does the user have this button turned on?) so that it can be accessed directly
	 * within the object.
	 *
	 * @since  3.0.0 | 07 APR 2018 | Created
	 * @param  none
	 * @return none
	 * @access public
	 *
	 */
	public function __construct() {

		// Update the class properties for this network
		$this->name    = __( 'Twitter','social-warfare' );
		$this->cta     = __( 'Tweet','social-warfare' );
		$this->key     = 'twitter';
		$this->default = 'true';

		$this->init_social_network();
	}

	/**
	 * Generate the API Share Count Request URL
	 *
	 * @since  3.0.0 | 07 APR 2018 | Created
	 * @access public
	 * @param  string $url The permalink of the page or post for which to fetch share counts
	 * @return string $request_url The complete URL to be used to access share counts via the API
	 *
	 */
	public function get_api_link( $url ) {

		// Fetch the user's options
		global $swp_user_options;

		// If the user has enabled Twitter shares....
		if ( $swp_user_options['twitter_shares'] ) :

			// Return the correct Twitter JSON endpoint URL
			if('opensharecount' == $swp_user_options['tweet_count_source']){
				$request_url = 'https://opensharecount.com/count.json?url='. $url;
			} else {
				$request_url = 'http://public.newsharecounts.com/count.json?url=' . $url;
			}

			// Debugging
			if ( _swp_is_debug( 'twitter' ) ) {
				echo '<b>Request URL:</b> ' . $request_url . '<br />';
			}

			return $request_url;

			// If the user has not enabled Twitter shares....
			else :

				// Return nothing so we don't run an API call
				return 0;

			endif;
	}


	/**
	 * Parse the response to get the share count
	 *
	 * @since  3.0.0 | 07 APR 2018 | Created
	 * @access public
	 * @param  string $response The raw response returned from the API request
	 * @return int $total_activity The number of shares reported from the API
	 *
	 */
	public function parse_api_response( $response ) {

		// Fetch the user's options
		global $swp_user_options;

		// If the user has enabled Twitter shares....
		if ( $swp_user_options['twitter_shares'] ) :

			// Debugging
			if ( _swp_is_debug( 'twitter' ) ) :
				echo '<b>Response:</b> ' . $response . '<br />';
			endif;

			// Parse the response to get the actual number
			$response = json_decode( $response, true );

			return isset( $response['count'] )?intval( $response['count'] ):0;

		// If the user has not enabled Twitter shares....
		else :

			// Return the number 0
			return 0;

		endif;
	}


	/**
	 * Generate the share link
	 *
	 * This is the link that is being clicked on which will open up the share
	 * dialogue.
	 *
	 * @since  3.0.0 | 07 APR 2018 | Created
	 * @param  array $post_data The array of information passed in from the buttons panel.
	 * @return string The generated link
	 * @access public
	 *
	 */
	public function generate_share_link( $post_data ) {

		// Generate a title for the share.
		$title = str_replace( '|', '', strip_tags( $post_data['post_title'] ) );

		// Check for a custom tweet from the post options.
		$custom_tweet = get_post_meta( $post_data['ID'] , 'nc_customTweet' , true );

		$custom_tweet = $custom_tweet !== '' ? urlencode( html_entity_decode( $custom_tweet, ENT_COMPAT, 'UTF-8' ) ) : urlencode( html_entity_decode( $title, ENT_COMPAT, 'UTF-8' ) );
		$twitter_link = $this->get_shareable_permalink( $post_data );

		// If the custom tweet contains a link, block Twitter for auto adding another one.
		if ( false !== strpos( $custom_tweet , 'http' ) ) :
			$url_parameter = '&url=/';
		else :
			$url_parameter = '&url=' . $twitter_link;
		endif;

		$twitter_mention = get_post_meta( $post_data['ID'] , 'swp_twitter_mention' , true );

		if (false != $twitter_mention):
			$custom_tweet .= ' @'.str_replace('@','',$twitter_mention);
		endif;

		$user_twitter_handle 	= get_the_author_meta( 'swp_twitter' , SWP_User_Profile::get_author( $post_data['ID'] ) );

        if ( $user_twitter_handle ) :
			$via_parameter = '&via=' . str_replace( '@','',$user_twitter_handle );
		elseif ( $post_data['options']['twitter_id'] ) :
			$via_parameter = '&via=' . str_replace( '@','',$post_data['options']['twitter_id'] );
		else :
			$via_parameter = '';
		endif;

        $parameters = $custom_tweet . $url_parameter . $via_parameter;

        $intent_link = "https://twitter.com/intent/tweet?text=$parameters";

		return $intent_link;

	}

}
