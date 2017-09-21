<?php

/**
 * Functions to add a Facebook share button to the available buttons
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
add_filter( 'swp_button_options', 'swp_facebook_options_function',20 );
function swp_facebook_options_function( $options ) {

	// Create the new option in a variable to be inserted
	$options['content']['facebook'] = array(
		'type' => 'checkbox',
		'content' => 'Facebook',
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
add_filter( 'swp_add_networks', 'swp_facebook_network' );
function swp_facebook_network( $networks ) {
	$networks[] = 'facebook';
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
function swp_facebook_request_link( $url ) {
	$link = 'https://graph.facebook.com/?fields=og_object{likes.summary(true).limit(0)},share&id='.$url;
	return $link;
}

/**
 * #4: Parse the response to get the share count
 *
 * @since  1.0.0
 * @access public
 * @param  string $response The raw response returned from the API request
 * @return int $total_activity The number of shares reported from the API
 */
function swp_format_facebook_response( $response ) {
	$formatted_response = json_decode( $response , true);

	if( !empty( $formatted_response['og_object'] ) ) {
		$likes = $formatted_response['og_object']['likes']['summary']['total_count'];
	} else {
		$likes = 0;
	}

	if( !empty( $formatted_response['share'] ) ){
		$comments = $formatted_response['share']['comment_count'];
		$shares = $formatted_response['share']['share_count'];
	} else {
		$comments = 0;
		$shares = 0;
	}

	$total = $likes + $comments + $shares;
	return $total;
}

/**
 * #5: Create the HTML to display the share button
 *
 * @since  1.0.0
 * @access public
 * @param  array $array The array of information used to create and display each social panel of buttons
 * @return array $array The modified array which will now contain the html for this button
 */
add_filter( 'swp_network_buttons', 'swp_facebook_button_html',10 );
function swp_facebook_button_html( $array ) {

	// If we've already generated this button, just use our existing html
	if ( isset( $_GLOBALS['sw']['buttons'][ $array['postID'] ]['facebook'] ) ) :
		$array['resource']['facebook'] = $_GLOBALS['sw']['buttons'][ $array['postID'] ]['facebook'];

		// If not, let's check if Facebook is activated and create the button HTML
		elseif ( (isset( $array['options']['newOrderOfIcons']['facebook'] ) && ! isset( $array['buttons'] )) || (isset( $array['buttons'] ) && isset( $array['buttons']['facebook'] ))  ) :

			$array['totes'] += intval( $array['shares']['facebook'] );
			++$array['count'];

			$array['resource']['facebook'] = '<div class="nc_tweetContainer swp_fb" data-id="' . $array['count'] . '" data-network="facebook">';
			$link = urlencode( urldecode( swp_process_url( $array['url'] , 'facebook' , $array['postID'] ) ) );
			$array['resource']['facebook'] .= '<a rel="nofollow" target="_blank" href="https://www.facebook.com/share.php?u=' . $link . '" data-link="http://www.facebook.com/share.php?u=' . $link . '" class="nc_tweet">';
			if ( $array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && $array['shares']['facebook'] > 0 ) :
				$array['resource']['facebook'] .= '<span class="iconFiller">';
				$array['resource']['facebook'] .= '<span class="spaceManWilly">';
				$array['resource']['facebook'] .= '<i class="sw sw-facebook"></i>';
				$array['resource']['facebook'] .= '<span class="swp_share"> ' . __( 'Share','social-warfare' ) . '</span>';
				$array['resource']['facebook'] .= '</span></span>';
				$array['resource']['facebook'] .= '<span class="swp_count">' . swp_kilomega( $array['shares']['facebook'] ) . '</span>';
			else :
				$array['resource']['facebook'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-facebook"></i><span class="swp_share"> ' . __( 'Share','social-warfare' ) . '</span></span></span></span>';
			endif;
			$array['resource']['facebook'] .= '</a>';
			$array['resource']['facebook'] .= '</div>';

			// Store these buttons so that we don't have to generate them for each set
			$_GLOBALS['sw']['buttons'][ $array['postID'] ]['facebook'] = $array['resource']['facebook'];

		endif;

		return $array;

};
