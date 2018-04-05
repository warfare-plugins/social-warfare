<?php

/**
 * Google Plus
 *
 * Class to add a Google Plus share button to the available buttons
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2017, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | CREATED | Unknown
 * @since     2.2.4 | UPDATED | 2 MAY 2017 | Refactored functions & updated docblocking
 * @since     3.0.0 | Updated | 05 APR 2018 | Rebuilt into a class-based system.
 *
 */
class SWP_Google_Plus extends SWP_Social_Network {

	public $name    = 'Google Plus';
	public $key     = 'google_plus';
	public $default = 'true';

	public function __construct() {
		$this->add_to_global();
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
	 * #5: Create the HTML to display the share button
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $array The array of information used to create and display each social panel of buttons
	 * @return array $array The modified array which will now contain the html for this button
	 *
	 */
	public function render_html( $array ) {

		// If we've already generated this button, just use our existing html
		if ( isset( $_GLOBALS['sw']['buttons'][ $array['postID'] ][$this->key] ) ) :
			$html= $_GLOBALS['sw']['buttons'][ $array['postID'] ][$this->key];

		// If not, let's check if Facebook is activated and create the button HTML
		elseif ( (isset( $array['options']['order_of_icons'][$this->key] ) && !isset( $array['buttons'] )) || (isset( $array['buttons'] ) && isset( $array['buttons'][$this->key] ))  ) :

			$html= '<div class="nc_tweetContainer googlePlus" data-id="' . $array['count'] . '" data-network="'.$this->key.'">';
			$link = urlencode( urldecode( SWP_URL_Management::process_url( $array['url'] , 'googlePlus' , $array['postID'] ) ) );
			$html.= '<a rel="nofollow" target="_blank" href="https://plus.google.com/share?url=' . $link . '" data-link="https://plus.google.com/share?url=' . $link . '" class="nc_tweet">';
			if ( $array['options']['network_shares'] && $array['shares']['total_shares'] >= $array['options']['minimum_shares'] && $array['shares']['googlePlus'] > 0 ) :
				$html.= '<span class="iconFiller">';
				$html.= '<span class="spaceManWilly">';
				$html.= '<i class="sw sw-google-plus"></i>';
				$html.= '<span class="swp_share"> ' . __( '+1','social-warfare' ) . '</span>';
				$html.= '</span></span>';
				$html.= '<span class="swp_count">' . swp_kilomega( $array['shares']['googlePlus'] ) . '</span>';
			else :
				$html.= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-google-plus"></i><span class="swp_share"> ' . __( '+1','social-warfare' ) . '</span></span></span></span>';
			endif;
			$html.= '</a>';
			$html.= '</div>';

			// Store these buttons so that we don't have to generate them for each set
			$_GLOBALS['sw']['buttons'][ $array['postID'] ]['googlePlus'] = $array['html']['googlePlus'];

		endif;

		return $html;

	}
}
