<?php

/**
 * Functions to add a LinkedIn share button to the available buttons
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2017, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | CREATED | Unknown
 * @since     2.2.4 | UPDATED | 2 MAY 2017 | Refactored functions & updated docblocking
 */

defined( 'WPINC' ) || die;

/**
 * #1: Add the On/Off Switch and Sortable Option
 *
 * @since  1.0.0
 * @access public
 * @param  array $options The array of available plugin options
 * @return array $options The modified array of available plugin options
 */
add_filter( 'swp_button_options', 'swp_linkedIn_options_function',20 );
function swp_linkedIn_options_function( $options ) {

	// Create the new option in a variable to be inserted
	$options['content']['linkedIn'] = array(
		'type' => 'checkbox',
		'content' => 'LinkedIn',
		'default' => true,
		'premium' => false,
	);

	return $options;
};

/**
 * #2: Add it to the global network array
 *
 * @since  1.0.0
 * @access public
 * @param  array $networks The array of available plugin social networks
 * @return array $networks The modified array of available plugin social networks
 */
add_filter( 'swp_add_networks', 'swp_linkedIn_network' );
function swp_linkedIn_network( $networks ) {
	$networks[] = 'linkedIn';
	return $networks;
};

/**
 * #3: Generate the API Share Count Request URL
 *
 * @since  1.0.0
 * @access public
 * @param  string $url The permalink of the page or post for which to fetch share counts
 * @return string $request_url The complete URL to be used to access share counts via the API
 */
function swp_linkedIn_request_link( $url ) {
	$request_url = 'https://www.linkedin.com/countserv/count/share?url=' . $url . '&format=json';
	return $request_url;
}

/**
 * #4: Parse the response to get the share count
 *
 * @since  1.0.0
 * @access public
 * @param  string $response The raw response returned from the API request
 * @return int $total_activity The number of shares reported from the API
 */
function swp_format_linkedIn_response( $response ) {
	$response = json_decode( $response, true );
	return isset( $response['count'] )?intval( $response['count'] ):0;
}

/**
 * #5: Create the HTML to display the share button
 *
 * @since  1.0.0
 * @access public
 * @param  array $array The array of information used to create and display each social panel of buttons
 * @return array $array The modified array which will now contain the html for this button
 */
add_filter( 'swp_network_buttons' , 'swp_linkedIn_button_html' , 10 );
function swp_linkedIn_button_html( $array ) {

	// If we've already generated this button, just use our existing html
	if ( isset( $_GLOBALS['sw']['buttons'][ $array['postID'] ]['linkedIn'] ) ) :
		$array['resource']['linkedIn'] = $_GLOBALS['sw']['buttons'][ $array['postID'] ]['linkedIn'];

		// If not, let's check if Facebook is activated and create the button HTML
		elseif ( (isset( $array['options']['newOrderOfIcons']['linkedIn'] ) && ! isset( $array['buttons'] )) || (isset( $array['buttons'] ) && isset( $array['buttons']['linkedIn'] ))  ) :

			$array['totes'] += intval( $array['shares']['linkedIn'] );
			++$array['count'];

			$array['resource']['linkedIn'] = '<div class="nc_tweetContainer linkedIn" data-id="' . $array['count'] . '" data-network="linked_in">';
			$link = urlencode( urldecode( swp_process_url( $array['url'] , 'linkedIn' , $array['postID'] ) ) );
			$array['resource']['linkedIn'] .= '<a rel="nofollow" target="_blank" href="https://www.linkedin.com/cws/share?url=' . $link . '" data-link="https://www.linkedin.com/cws/share?url=' . $link . '" class="nc_tweet">';
			if ( $array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && $array['shares']['linkedIn'] > 0 ) :
				$array['resource']['linkedIn'] .= '<span class="iconFiller">';
				$array['resource']['linkedIn'] .= '<span class="spaceManWilly">';
				$array['resource']['linkedIn'] .= '<i class="sw sw-linkedin"></i>';
				$array['resource']['linkedIn'] .= '<span class="swp_share"> ' . __( 'Share','social-warfare' ) . '</span>';
				$array['resource']['linkedIn'] .= '</span></span>';
				$array['resource']['linkedIn'] .= '<span class="swp_count">' . swp_kilomega( $array['shares']['linkedIn'] ) . '</span>';
			else :
				$array['resource']['linkedIn'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-linkedin"></i><span class="swp_share"> ' . __( 'Share','social-warfare' ) . '</span></span></span></span>';
			endif;
			$array['resource']['linkedIn'] .= '</a>';
			$array['resource']['linkedIn'] .= '</div>';

			// Store these buttons so that we don't have to generate them for each set
			$_GLOBALS['sw']['buttons'][ $array['postID'] ]['linkedIn'] = $array['resource']['linkedIn'];

		endif;

		return $array;

};
