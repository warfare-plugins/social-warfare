<?php

/**
 * Functions to add a Twitter share button to the available buttons
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2017, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | CREATED | Unknown
 * @since     2.2.4 | UPDATED | 3 MAY 2017 | Refactored functions & updated docblocking
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
add_filter( 'swp_button_options', 'swp_twitter_options_function',20 );
function swp_twitter_options_function( $options ) {

	// Create the new option in a variable to be inserted
	$options['content']['twitter'] = array(
		'type' => 'checkbox',
		'content' => 'Twitter',
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
add_filter( 'swp_add_networks', 'swp_twitter_network' );
function swp_twitter_network( $networks ) {
	$networks[] = 'twitter';
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
function swp_twitter_request_link( $url ) {

	// Fetch the user's options
	global $swp_user_options;

	// If the user has enabled Twitter shares....
	if ( $swp_user_options['twitter_shares'] ) :

		// Return the correct Twitter JSON endpoint URL
		$request_url = 'http://public.newsharecounts.com/count.json?url=' . $url;

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
 * #4: Parse the response to get the share count
 *
 * @since  1.0.0
 * @access public
 * @param  string $response The raw response returned from the API request
 * @return int $total_activity The number of shares reported from the API
 */
function swp_format_twitter_response( $response ) {

	// Fetch the user's options
	global $swp_user_options;

	// If the user has enabled Twitter shares....
	if ( $swp_user_options['twitter_shares'] ) :

		// Debugging
		if ( _swp_is_debug( 'twitter' ) ) {
			echo '<b>Response:</b> ' . $response . '<br />';
		}

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
 * #5: Create the HTML to display the share button
 *
 * @since  1.0.0
 * @access public
 * @param  array $array The array of information used to create and display each social panel of buttons
 * @return array $array The modified array which will now contain the html for this button
 */
add_filter( 'swp_network_buttons', 'swp_twitter_button_html',10 );
function swp_twitter_button_html( $array ) {

	// If we've already generated this button, just use our existing html
	if ( isset( $_GLOBALS['sw']['buttons'][ $array['postID'] ]['twitter'] ) ) :
		$array['resource']['twitter'] = $_GLOBALS['sw']['buttons'][ $array['postID'] ]['twitter'];

	// If not, let's check if Facebook is activated and create the button HTML
	elseif ( (isset( $array['options']['newOrderOfIcons']['twitter'] ) && ! isset( $array['buttons'] )) || (isset( $array['buttons'] ) && isset( $array['buttons']['twitter'] ))  ) :

		$array['totes'] += intval( $array['shares']['twitter'] );
		++$array['count'];

		$title = strip_tags( get_the_title( $array['postID'] ) );
		$title = str_replace( '|','',$title );
		$ct = get_post_meta( $array['postID'] , 'nc_customTweet' , true );

		$ct = ($ct != '' ? urlencode( html_entity_decode( $ct, ENT_COMPAT, 'UTF-8' ) ) : urlencode( html_entity_decode( $title, ENT_COMPAT, 'UTF-8' ) ));
		$twitterLink = swp_process_url( $array['url'] , 'twitter' , $array['postID'] );
		if ( strpos( $ct,'http' ) !== false ) :
			$urlParam = '&url=/';
		else :
			$urlParam = '&url=' . $twitterLink;
		endif;

		/**
		 * A function to allow custom mentions of a Twitter user when link is shared on Twitter
		 */
		$twitter_mention = get_post_meta( $array['postID'] , 'swp_twitter_mention' , true );
		if(false != $twitter_mention):
			$ct .= ' @'.str_replace('@','',$twitter_mention);
		endif;

		$user_twitter_handle 	= get_the_author_meta( 'swp_twitter' , swp_get_author( $array['postID'] ) );
		if ( $user_twitter_handle ) :
			$viaText = '&via=' . str_replace( '@','',$user_twitter_handle );
		elseif ( $array['options']['twitterID'] ) :
			$viaText = '&via=' . str_replace( '@','',$array['options']['twitterID'] );
		else :
			$viaText = '';
		endif;

		$array['resource']['twitter'] = '<div class="nc_tweetContainer twitter" data-id="' . $array['count'] . '" data-network="twitter">';
		$array['resource']['twitter'] .= '<a rel="nofollow" target="_blank" href="https://twitter.com/share?original_referer=/&text=' . $ct . '' . $urlParam . '' . $viaText . '" data-link="https://twitter.com/share?original_referer=/&text=' . $ct . '' . $urlParam . '' . $viaText . '" class="nc_tweet">';
		if ( $array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && $array['shares']['twitter'] > 0 ) :
			$array['resource']['twitter'] .= '<span class="iconFiller">';
			$array['resource']['twitter'] .= '<span class="spaceManWilly">';
			$array['resource']['twitter'] .= '<i class="sw sw-twitter"></i>';
			$array['resource']['twitter'] .= '<span class="swp_share"> ' . __( 'Tweet','social-warfare' ) . '</span>';
			$array['resource']['twitter'] .= '</span></span>';
			$array['resource']['twitter'] .= '<span class="swp_count">' . swp_kilomega( $array['shares']['twitter'] ) . '</span>';
		else :
			$array['resource']['twitter'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-twitter"></i><span class="swp_share"> ' . __( 'Tweet','social-warfare' ) . '</span></span></span></span>';
		endif;
		$array['resource']['twitter'] .= '</a>';
		$array['resource']['twitter'] .= '</div>';

		// Store these buttons so that we don't have to generate them for each set
		$_GLOBALS['sw']['buttons'][ $array['postID'] ]['twitter'] = $array['resource']['twitter'];

	endif;

	return $array;

};
