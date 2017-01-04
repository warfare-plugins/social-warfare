<?php

/**

 * **************************************************************
 *                                                                *
 *   #1: Add the On / Off Switch	and Sortable Option				 *
 *                                                                *
 ******************************************************************/
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

***************************************************************
*                                                                *
*   #2: Add it to global network array	         				 *
*                                                                *
*/
	// Queue up your filter to be ran on the swp_options hook.
	add_filter( 'swp_add_networks', 'swp_linkedIn_network' );

	// Create the function that will filter the options
function swp_linkedIn_network( $networks ) {

	// Add your network to the existing network array
	$networks[] = 'linkedIn';

	// Be sure to return the modified options array or the world will explode
	return $networks;
};
/**

 * **************************************************************
 *                                                                *
 *   #3: Generate the API Share Count Request URL	             *
 *                                                                *
 ******************************************************************/
function swp_linkedIn_request_link( $url ) {
	$request_url = 'https://www.linkedin.com/countserv/count/share?url=' . $url . '&format=json';
	return $request_url;
}
/**

 * **************************************************************
 *                                                                *
 *   #4: Parse the Response to get the share count	             *
 *                                                                *
 ******************************************************************/
function swp_format_linkedIn_response( $response ) {
	$response = json_decode( $response, true );
	return isset( $response['count'] )?intval( $response['count'] ):0;
}
/**

***************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
*/
	add_filter( 'swp_network_buttons', 'swp_linkedIn_button_html',10 );
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
