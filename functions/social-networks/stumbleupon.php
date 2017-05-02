<?php

/**
 * Functions to add a StumbleUpon share button to the available buttons
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
add_filter( 'swp_button_options' , 'swp_stumbleupon_options_function' , 20 );
function swp_stumbleupon_options_function( $options ) {

	// Create the new option in a variable to be inserted
	$options['content']['stumbleupon'] = array(
		'type' => 'checkbox',
		'content' => 'StumbleUpon',
		'default' => false,
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
add_filter( 'swp_add_networks', 'swp_stumbleupon_network' );
function swp_stumbleupon_network( $networks ) {
	$networks[] = 'stumbleupon';
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
function swp_stumbleupon_request_link( $url ) {
	$request_url = 'https://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $url;
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
function swp_format_stumbleupon_response( $response ) {
	$response = json_decode( $response, true );
	return isset( $response['result']['views'] )?intval( $response['result']['views'] ):0;
}

/**
 * #5: Create the HTML to display the share button
 *
 * @since  1.0.0
 * @access public
 * @param  array $array The array of information used to create and display each social panel of buttons
 * @return array $array The modified array which will now contain the html for this button
 */
add_filter( 'swp_network_buttons', 'swp_stumbleupon_button_html',10 );
function swp_stumbleupon_button_html( $array ) {

	// If we've already generated this button, just use our existing html
	if ( isset( $_GLOBALS['sw']['buttons'][ $array['postID'] ]['stumbleupon'] ) ) :
		$array['resource']['stumbleupon'] = $_GLOBALS['sw']['buttons'][ $array['postID'] ]['stumbleupon'];

	// If not, let's check if Facebook is activated and create the button HTML
	elseif ( (isset( $array['options']['newOrderOfIcons']['stumbleupon'] ) && ! isset( $array['buttons'] )) || (isset( $array['buttons'] ) && isset( $array['buttons']['stumbleupon'] ))  ) :

		if ( isset( $array['shares']['stumbleupon'] ) ) :
			$array['totes'] += intval( $array['shares']['stumbleupon'] );
		endif;
		++$array['count'];

		// Collect the Title
		$title = get_post_meta( $array['postID'] , 'nc_ogTitle' , true );
		if ( ! $title ) :
			$title = get_the_title();
		endif;

		$array['resource']['stumbleupon'] = '<div class="nc_tweetContainer swp_stumbleupon" data-id="' . $array['count'] . '" data-network="stumble_upon">';
		$link = $array['url'];
		$array['resource']['stumbleupon'] .= '<a rel="nofollow" target="_blank" href="http://www.stumbleupon.com/submit?url=' . $link . '&title=' . urlencode( $title ) . '" data-link="http://www.stumbleupon.com/submit?url=' . $link . '&title=' . urlencode( $title ) . '" class="nc_tweet">';
		if ( $array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && isset( $array['shares']['stumbleupon'] ) && $array['shares']['stumbleupon'] > 0 ) :
			$array['resource']['stumbleupon'] .= '<span class="iconFiller">';
			$array['resource']['stumbleupon'] .= '<span class="spaceManWilly">';
			$array['resource']['stumbleupon'] .= '<i class="sw sw-stumbleupon"></i>';
			$array['resource']['stumbleupon'] .= '<span class="swp_share"> ' . __( 'Stumble','social-warfare' ) . '</span>';
			$array['resource']['stumbleupon'] .= '</span></span>';
			$array['resource']['stumbleupon'] .= '<span class="swp_count">' . swp_kilomega( $array['shares']['stumbleupon'] ) . '</span>';
		else :
			$array['resource']['stumbleupon'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-stumbleupon"></i><span class="swp_share"> ' . __( 'Stumble','social-warfare' ) . '</span></span></span></span>';
		endif;
		$array['resource']['stumbleupon'] .= '</a>';
		$array['resource']['stumbleupon'] .= '</div>';

		// Store these buttons so that we don't have to generate them for each set
		$_GLOBALS['sw']['buttons'][ $array['postID'] ]['stumbleupon'] = $array['resource']['stumbleupon'];

	endif;

	return $array;

};
