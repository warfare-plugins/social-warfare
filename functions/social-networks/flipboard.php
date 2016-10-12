<?php

/**

 * **************************************************************
 *                                                                *
 *   #1: Add the On / Off Switch	and Sortable Option				 *
 *                                                                *
 ******************************************************************/
	add_filter( 'swp_button_options', 'swp_flipboard_options_function',20 );
function swp_flipboard_options_function( $options ) {

	// Create the new option in a variable to be inserted
	$options['content']['flipboard'] = array(
	'type' => 'checkbox',
	'content' => 'Flipboard',
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
	add_filter( 'swp_add_networks', 'swp_flipboard_network' );

	// Create the function that will filter the options
function swp_flipboard_network( $networks ) {

	// Add your network to the existing network array
	$networks[] = 'flipboard';

	// Be sure to return the modified options array or the world will explode
	return $networks;
};
/**

 * **************************************************************
 *                                                                *
 *   #3: Generate the API Share Count Request URL	             *
 *                                                                *
 ******************************************************************/
function swp_flipboard_request_link( $url ) {
	return 0;
}
/**

 * **************************************************************
 *                                                                *
 *   #4: Parse the Response to get the share count	             *
 *                                                                *
 ******************************************************************/
function swp_format_flipboard_response( $response ) {
	return 0;
}
/**

***************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
*/
	add_filter( 'swp_network_buttons', 'swp_flipboard_button_html',10 );
function swp_flipboard_button_html( $array ) {

	if ( (isset( $array['options']['newOrderOfIcons']['flipboard'] ) && ! isset( $array['buttons'] )) || (isset( $array['buttons'] ) && isset( $array['buttons']['flipboard'] ))  ) :

		// Collect the Title
		$title = get_post_meta( $array['postID'] , 'nc_ogTitle' , true );
		if ( ! $title ) :
			$title = get_the_title();
			endif;

		// Collect the Description
		$description = get_post_meta( $array['postID'] , 'nc_ogDescription' , true );
		if ( ! $description ) :
			$description = swp_get_excerpt_by_id( $array['postID'] );
			endif;
		++$array['count'];

		$array['resource']['flipboard'] = '<div class="nc_tweetContainer swp_flipboard" data-id="' . $array['count'] . '" data-network="flipboard">';
		$link = urlencode( urldecode( swp_process_url( $array['url'] , 'flipboard' , $array['postID'] ) ) );
		$array['resource']['flipboard'] .= '<a href="https://share.flipboard.com/bookmarklet/popout?v=2&title=Tools%20-%20Flipboard&url=' . $link . '" data-link="https://share.flipboard.com/bookmarklet/popout?v=2&title=Tools%20-%20Flipboard&url=' . $link . '" class="nc_tweet flipboard">';
		$array['resource']['flipboard'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-flipboard"></i><span class="swp_share"> ' . __( 'Flip','social-warfare' ) . '</span></span></span></span>';
		$array['resource']['flipboard'] .= '</a>';
		$array['resource']['flipboard'] .= '</div>';

		endif;

	return $array;

};
