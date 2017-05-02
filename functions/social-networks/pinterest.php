<?php

/**
 * Functions to add a Pinterest share button to the available buttons
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
add_filter( 'swp_button_options', 'swp_pinterest_options_function',20 );
function swp_pinterest_options_function( $options ) {

	// Create the new option in a variable to be inserted
	$options['content']['pinterest'] = array(
		'type' => 'checkbox',
		'content' => 'Pinterest',
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
add_filter( 'swp_add_networks', 'swp_pinterest_network' );
function swp_pinterest_network( $networks ) {
	$networks[] = 'pinterest';
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
function swp_pinterest_request_link( $url ) {
	$url = rawurlencode( $url );
	$request_url = 'https://api.pinterest.com/v1/urls/count.json?url=' . $url;
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
function swp_format_pinterest_response( $response ) {
	$response = preg_replace( '/^receiveCount\((.*)\)$/', "\\1", $response );
	$response = json_decode( $response,true );
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
add_filter( 'swp_network_buttons', 'swp_pinterest_button_html',10 );
function swp_pinterest_button_html( $array ) {

	// If we've already generated this button, just use our existing html
	if ( isset( $_GLOBALS['sw']['buttons'][ $array['postID'] ]['pinterest'] ) ) :
		$array['resource']['pinterest'] = $_GLOBALS['sw']['buttons'][ $array['postID'] ]['pinterest'];

	// If not, let's check if Facebook is activated and create the button HTML
	elseif ( (isset( $array['options']['newOrderOfIcons']['pinterest'] ) && ! isset( $array['buttons'] )) || (isset( $array['buttons'] ) && isset( $array['buttons']['pinterest'] ))  ) :

		$array['totes'] += intval( $array['shares']['pinterest'] );
		++$array['count'];

		$pi = get_post_meta( $array['postID'] , 'nc_pinterestImage' , true );

		// Pinterest Username
		if ( !empty( $array['options']['pinterestID'] ) ) :
			$pu = ' via @' . str_replace( '@' , '' , $array['options']['pinterestID'] );
		else :
			$pu = '';
		endif;

		$array['imageURL'] = false;
		$image_url = get_post_meta( $array['postID'] , 'swp_pinterest_image_url' , true );
		if( !empty( $image_url ) ):
			$array['imageURL'] = $image_url;
		elseif(isset($array['options']['advanced_pinterest_fallback']) && $array['options']['advanced_pinterest_fallback'] == 'featured'):
			$thumbnail_url = wp_get_attachment_url( get_post_thumbnail_id( $array['postID'] ) );
			if( !empty( $thumbnail_url ) ):
				$array['imageURL'] = $thumbnail_url;
			endif;
		endif;

		$pd	= get_post_meta( $array['postID'] , 'nc_pinterestDescription' , true );
		if ( $array['imageURL'] ) :
			$pi 	= '&media=' . urlencode( html_entity_decode( $array['imageURL'],ENT_COMPAT, 'UTF-8' ) );
		else :
			$pi		= '';
		endif;

		$pinterest_link = urlencode( urldecode( swp_process_url( $array['url'] , 'pinterest' , $array['postID'] ) ) );
		$title = strip_tags( get_the_title( $array['postID'] ) );
		$title = str_replace( '|','',$title );

		if( function_exists('is_swp_registered') ):
			$swp_registration = is_swp_registered();
		else:
			$swp_registration = false;
		endif;

		if ( $pi != '' && true === $swp_registration ) :
			$a = '<a rel="nofollow" data-link="https://pinterest.com/pin/create/button/?url=' . $pinterest_link . '' . $pi . '&description=' . ($pd != '' ? urlencode( html_entity_decode( $pd . $pu, ENT_COMPAT, 'UTF-8' ) ) : urlencode( html_entity_decode( $title . $pu, ENT_COMPAT, 'UTF-8' ) )) . '" class="nc_tweet" data-count="0">';
		else :
			$a = '<a rel="nofollow" onClick="var e=document.createElement(\'script\');e.setAttribute(\'type\',\'text/javascript\');e.setAttribute(\'charset\',\'UTF-8\');e.setAttribute(\'src\',\'//assets.pinterest.com/js/pinmarklet.js?r=\'+Math.random()*99999999);document.body.appendChild(e);" class="nc_tweet noPop">';
		endif;
		$array['resource']['pinterest'] = '<div class="nc_tweetContainer nc_pinterest" data-id="' . $array['count'] . '" data-network="pinterest">';
		$array['resource']['pinterest'] .= $a;
		if ( $array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && $array['shares']['pinterest'] > 0 ) :
			$array['resource']['pinterest'] .= '<span class="iconFiller">';
			$array['resource']['pinterest'] .= '<span class="spaceManWilly">';
			$array['resource']['pinterest'] .= '<i class="sw sw-pinterest"></i>';
			$array['resource']['pinterest'] .= '<span class="swp_share"> ' . __( 'Pin','social-warfare' ) . '</span>';
			$array['resource']['pinterest'] .= '</span></span>';
			$array['resource']['pinterest'] .= '<span class="swp_count">' . swp_kilomega( $array['shares']['pinterest'] ) . '</span>';
		else :
			$array['resource']['pinterest'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-pinterest"></i><span class="swp_share"> ' . __( 'Pin','social-warfare' ) . '</span></span></span></span>';
		endif;
		$array['resource']['pinterest'] .= '</a>';
		$array['resource']['pinterest'] .= '</div>';

		// Store these buttons so that we don't have to generate them for each set
		$_GLOBALS['sw']['buttons'][ $array['postID'] ]['pinterest'] = $array['resource']['pinterest'];

	endif;

	return $array;

};
