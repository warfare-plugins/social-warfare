<?php

/**
 * #1: Add the On / Off Switch	and Sortable Option
 */
add_filter( 'swp_button_options', 'swp_whatsapp_options_function',20 );
function swp_whatsapp_options_function( $options ) {

	// Create the new option in a variable to be inserted
	$options['content']['whatsapp'] = array(
	'type' => 'checkbox',
	'content' => 'WhatsApp',
	'default' => false,
	'premium' => true,
	);

	return $options;

};

/**
 * #2: Add it to global network array
 */
// Queue up your filter to be ran on the swp_options hook.
add_filter( 'swp_add_networks', 'swp_whatsapp_network' );

// Create the function that will filter the options
function swp_whatsapp_network( $networks ) {

	// Add your network to the existing network array
	$networks[] = 'whatsapp';

	// Be sure to return the modified options array or the world will explode
	return $networks;
};

/**
 *  #3: Generate the API Share Count Request URL
 */
function swp_whatsapp_request_link( $url ) {
	$request_url = 'https://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' . $url;
	return 0;
}

/**
 * #4: Parse the Response to get the share count
 */
function swp_format_whatsapp_response( $response ) {
	// $response = json_decode($response, true);
	// return isset($response[0]['total_count'])?intval($response[0]['total_count']):0;
	return 0;
}
/**
 * #5: Create the Button HTML
 */
add_filter( 'swp_network_buttons', 'swp_whatsapp_button_html',10 );
function swp_whatsapp_button_html( $array ) {

	// If we've already generated this button, just use our existing html
	if ( isset( $_GLOBALS['sw']['buttons'][ $array['postID'] ]['whatsapp'] ) ) :
		$array['resource']['whatsapp'] = $_GLOBALS['sw']['buttons'][ $array['postID'] ]['whatsapp'];

		// If not, let's check if WhatsApp is activated and create the button HTML
		elseif ( (isset( $array['options']['newOrderOfIcons']['whatsapp'] ) && ! isset( $array['buttons'] ))
		|| (isset( $array['buttons'] ) && isset( $array['buttons']['whatsapp'] ))  ) :

			$array['totes'] += $array['shares']['whatsapp'];
			++$array['count'];

			$array['resource']['whatsapp'] = '<div class="nc_tweetContainer swp_whatsapp" data-id="' . $array['count'] . '" data-network="whatsapp">';
			$link = urlencode( urldecode( swp_process_url( $array['url'] , 'whatsapp' , $array['postID'] ) ) );
			$array['resource']['whatsapp'] .= '<a rel="nofollow" target="_blank" href="whatsapp://send?text=' . $link . '" class="nc_tweet" data-action="share/whatsapp/share">';
			if ( $array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && $array['shares']['whatsapp'] > 0 ) :
				$array['resource']['whatsapp'] .= '<span class="iconFiller">';
				$array['resource']['whatsapp'] .= '<span class="spaceManWilly">';
				$array['resource']['whatsapp'] .= '<i class="sw sw-whatsapp"></i>';
				$array['resource']['whatsapp'] .= '<span class="swp_share"> ' . __( 'WhatsApp','social-warfare' ) . '</span>';
				$array['resource']['whatsapp'] .= '</span></span>';
				$array['resource']['whatsapp'] .= '<span class="swp_count">' . swp_kilomega( $array['shares']['whatsapp'] ) . '</span>';
			else :
				$array['resource']['whatsapp'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-whatsapp"></i><span class="swp_share"> ' . __( 'WhatsApp','social-warfare' ) . '</span></span></span></span>';
			endif;
			$array['resource']['whatsapp'] .= '</a>';
			$array['resource']['whatsapp'] .= '</div>';

			// Store these buttons so that we don't have to generate them for each set
			$_GLOBALS['sw']['buttons'][ $array['postID'] ]['whatsapp'] = $array['resource']['whatsapp'];

		endif;

		return $array;

};
