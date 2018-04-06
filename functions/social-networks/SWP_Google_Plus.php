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

	public $name    = __( 'Google Plus','social-warfare' );
	public $cta     = __( '+1','social-warfare' );
	public $key     = 'google_plus';
	public $default = 'true';

	public function __construct() {
		$this->add_to_global();
		$this->set_active_state();
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
	 * @since  3.0.0 | 06 APR 2018 | Created
	 * @param  array $array The array of information passed in from the buttons panel.
	 * @return string The generated link
	 * @access public
	 *
	 */
	public function generate_share_link( $array ) {

		$permalink = urlencode( urldecode( SWP_URL_Management::process_url( $array['url'] , $this->key , $array['postID'] ) ) );
		$share_link = 'https://plus.google.com/share?url=' . $permalink;
		return $share_link;
	}


	/**
	 * Create the HTML to display the share button
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $array The array of information used to create and display each social panel of buttons
	 * @return array $array The modified array which will now contain the html for this button
	 * TODO: Clean up those conditionals, maybe even put some of that into another method.
	 *
	 */
	public function render_html( $array ) {

		// If we've already generated this button, just use our existing html
		if ( isset( $this->html[$array['postID']] ) ) :
			return $this->html[$array['postID']]

		// If not, let's check if this network is activated and create the button HTML
		else:

			$share_link = $this->generate_share_link( $array );

			$html= '<div class="nc_tweetContainer '.$this->key.'" data-id="' . $array['count'] . '" data-network="'.$this->key.'">';
			$html.= '<a rel="nofollow" target="_blank" href="https://plus.google.com/share?url=' . $link . '" data-link="https://plus.google.com/share?url=' . $link . '" class="nc_tweet">';
			if ( true === $this->show_share_count( $array ) ) :
				$html.= '<span class="iconFiller">';
				$html.= '<span class="spaceManWilly">';
				$html.= '<i class="sw sw-'.$this->key.'"></i>';
				$html.= '<span class="swp_share"> ' . $this->cta . '</span>';
				$html.= '</span></span>';
				$html.= '<span class="swp_count">' . swp_kilomega( $array['shares'][$this->key] ) . '</span>';
			else :
				$html.= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-google-plus"></i><span class="swp_share"> ' . __( '+1','social-warfare' ) . '</span></span></span></span>';
			endif;
			$html.= '</a>';
			$html.= '</div>';

			// Store these buttons so that we don't have to generate them for each set
			$this->save_html( $html , $array['postID'] );

		endif;

		return $html;

	}
}
