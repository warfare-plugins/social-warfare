<?php

/**

 * **************************************************************
 *                                                                *
 *   #1: Add the On / Off Switch	and Sortable Option				 *
 *                                                                *
 ******************************************************************/
	add_filter( 'swp_button_options', 'swp_stumbleupon_options_function',20 );
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

***************************************************************
*                                                                *
*   #2: Add it to global network array	         				 *
*                                                                *
*/
	// Queue up your filter to be ran on the swp_options hook.
	add_filter( 'swp_add_networks', 'swp_stumbleupon_network' );

	// Create the function that will filter the options
function swp_stumbleupon_network( $networks ) {

	// Add your network to the existing network array
	$networks[] = 'stumbleupon';

	// Be sure to return the modified options array or the world will explode
	return $networks;
};
/**

 * **************************************************************
 *                                                                *
 *   #3: Generate the API Share Count Request URL	             *
 *                                                                *
 ******************************************************************/
function swp_stumbleupon_request_link( $url ) {
	$request_url = 'https://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $url;
	return $request_url;
}
/**

 * **************************************************************
 *                                                                *
 *   #4: Parse the Response to get the share count	             *
 *                                                                *
 ******************************************************************/
function swp_format_stumbleupon_response( $response ) {
	$response = json_decode( $response, true );
	return isset( $response['result']['views'] )?intval( $response['result']['views'] ):0;
}
/**

***************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
*/
	add_filter( 'swp_network_buttons', 'swp_stumbleupon_button_html',10 );
function swp_stumbleupon_button_html( $array ) {

	// If we've already generated this button, just use our existing html
	if ( isset( $_GLOBALS['sw']['buttons'][ $array['postID'] ]['stumbleupon'] ) ) :
		$array['resource']['stumbleupon'] = $_GLOBALS['sw']['buttons'][ $array['postID'] ]['stumbleupon'];

		// If not, let's check if Facebook is activated and create the button HTML
		elseif ( (isset( $array['options']['newOrderOfIcons']['stumbleupon'] ) && ! isset( $array['buttons'] )) || (isset( $array['buttons'] ) && isset( $array['buttons']['stumbleupon'] ))  ) :

			if ( isset( $array['shares']['stumbleupon'] ) ) :
				$array['totes'] += $array['shares']['stumbleupon'];
			endif;
			++$array['count'];

			// Collect the Title
			$title = get_post_meta( $array['postID'] , 'nc_ogTitle' , true );
			if ( ! $title ) :
				$title = get_the_title();
			endif;

			$array['resource']['stumbleupon'] = '<div class="nc_tweetContainer swp_stumbleupon" data-id="' . $array['count'] . '" data-network="stumble_upon">';
			$link = $array['url'];
			$array['resource']['stumbleupon'] .= '<a target="_blank" href="http://www.stumbleupon.com/submit?url=' . $link . '&title=' . urlencode( $title ) . '" data-link="http://www.stumbleupon.com/submit?url=' . $link . '&title=' . urlencode( $title ) . '" class="nc_tweet">';
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
