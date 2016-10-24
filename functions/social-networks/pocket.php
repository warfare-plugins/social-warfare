<?php

/**

 * **************************************************************
 *                                                                *
 *   #1: Add the On / Off Switch	and Sortable Option				 *
 *                                                                *
 ******************************************************************/
	add_filter( 'swp_button_options', 'swp_pocket_options_function',20 );
function swp_pocket_options_function( $options ) {

	// Create the new option in a variable to be inserted
	$options['content']['pocket'] = array(
	'type' => 'checkbox',
	'content' => 'Pocket',
	'default' => false,
	'premium' => true,
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
	add_filter( 'swp_add_networks', 'swp_pocket_network' );

	// Create the function that will filter the options
function swp_pocket_network( $networks ) {

	// Add your network to the existing network array
	$networks[] = 'pocket';

	// Be sure to return the modified options array or the world will explode
	return $networks;
};
/**

 * **************************************************************
 *                                                                *
 *   #3: Generate the API Share Count Request URL	             *
 *                                                                *
 ******************************************************************/
function swp_pocket_request_link( $url ) {
	return 0;
}
/**

 * **************************************************************
 *                                                                *
 *   #4: Parse the Response to get the share count	             *
 *                                                                *
 ******************************************************************/
function swp_format_pocket_response( $response ) {
	return 0;
}
/**

***************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
*/
	add_filter( 'swp_network_buttons', 'swp_pocket_button_html',10 );
function swp_pocket_button_html( $array ) {

	if ( (isset( $array['options']['newOrderOfIcons']['pocket'] ) && ! isset( $array['buttons'] )) || (isset( $array['buttons'] ) && isset( $array['buttons']['pocket'] ))  ) :

		// Collect the Title
		$title = get_post_meta( $array['postID'] , 'nc_ogTitle' , true );
		if ( ! $title ) :
			$title = get_the_title();
			endif;

		++$array['count'];

		$array['resource']['pocket'] = '<div class="nc_tweetContainer swp_pocket" data-id="' . $array['count'] . '" data-network="pocket">';
		$link = urlencode( urldecode( swp_process_url( $array['url'] , 'pocket' , $array['postID'] ) ) );
		$array['resource']['pocket'] .= '<a rel="nofollow" href="https://getpocket.com/save?url=' . $link . '&title=' . $title . '" data-link="https://getpocket.com/save?url=' . $link . '&title=' . $title . '" class="nc_tweet">';
		$array['resource']['pocket'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-pocket"></i><span class="swp_share"> ' . __( 'Pocket','social-warfare' ) . '</span></span></span></span>';
		$array['resource']['pocket'] .= '</a>';
		$array['resource']['pocket'] .= '</div>';

		endif;

	return $array;

};
