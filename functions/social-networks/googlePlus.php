<?php

/**
 * Functions to add a Google Plus share button to the available buttons
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
add_filter( 'swp_button_options', 'swp_googlePlus_options_function',20 );
function swp_googlePlus_options_function( $options ) {

	// Create the new option in a variable to be inserted
	$options['content']['googlePlus'] = array(
		'type' => 'checkbox',
		'content' => 'Google Plus',
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
add_filter( 'swp_add_networks', 'swp_googlePlus_network' );
function swp_googlePlus_network( $networks ) {
	$networks[] = 'googlePlus';
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
function swp_googlePlus_request_link( $url ) {
	return $url;
}

/**
 * #4: Parse the response to get the share count
 *
 * @since  1.0.0
 * @access public
 * @param  string $response The raw response returned from the API request
 * @return int $total_activity The number of shares reported from the API
 */
function swp_format_googlePlus_response( $response ) {
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
 */
add_filter( 'swp_network_buttons', 'swp_googlePlus_button_html',10 );
function swp_googlePlus_button_html( $array ) {

	// If we've already generated this button, just use our existing html
	if ( isset( $_GLOBALS['sw']['buttons'][ $array['postID'] ]['googlePlus'] ) ) :
		$array['resource']['googlePlus'] = $_GLOBALS['sw']['buttons'][ $array['postID'] ]['googlePlus'];

		// If not, let's check if Facebook is activated and create the button HTML
		elseif ( (isset( $array['options']['newOrderOfIcons']['googlePlus'] ) && ! isset( $array['buttons'] )) || (isset( $array['buttons'] ) && isset( $array['buttons']['googlePlus'] ))  ) :

			$array['totes'] += intval( $array['shares']['googlePlus'] );
			++$array['count'];

			$array['resource']['googlePlus'] = '<div class="nc_tweetContainer googlePlus" data-id="' . $array['count'] . '" data-network="google_plus">';
			$link = urlencode( urldecode( swp_process_url( $array['url'] , 'googlePlus' , $array['postID'] ) ) );
			$array['resource']['googlePlus'] .= '<a rel="nofollow" target="_blank" href="https://plus.google.com/share?url=' . $link . '" data-link="https://plus.google.com/share?url=' . $link . '" class="nc_tweet">';
			if ( $array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && $array['shares']['googlePlus'] > 0 ) :
				$array['resource']['googlePlus'] .= '<span class="iconFiller">';
				$array['resource']['googlePlus'] .= '<span class="spaceManWilly">';
				$array['resource']['googlePlus'] .= '<i class="sw sw-google-plus"></i>';
				$array['resource']['googlePlus'] .= '<span class="swp_share"> ' . __( '+1','social-warfare' ) . '</span>';
				$array['resource']['googlePlus'] .= '</span></span>';
				$array['resource']['googlePlus'] .= '<span class="swp_count">' . swp_kilomega( $array['shares']['googlePlus'] ) . '</span>';
			else :
				$array['resource']['googlePlus'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-google-plus"></i><span class="swp_share"> ' . __( '+1','social-warfare' ) . '</span></span></span></span>';
			endif;
			$array['resource']['googlePlus'] .= '</a>';
			$array['resource']['googlePlus'] .= '</div>';

			// Store these buttons so that we don't have to generate them for each set
			$_GLOBALS['sw']['buttons'][ $array['postID'] ]['googlePlus'] = $array['resource']['googlePlus'];

		endif;

		return $array;

};
