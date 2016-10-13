<?php

/**

 * **************************************************************
 *                                                                *
 *   #1: Add the On / Off Switch	and Sortable Option				 *
 *                                                                *
 ******************************************************************/
	add_filter( 'swp_button_options', 'swp_yummly_options_function',20 );
function swp_yummly_options_function( $options ) {

	// Create the new option in a variable to be inserted
	$options['content']['yummly'] = array(
	'type' => 'checkbox',
	'content' => 'Yummly',
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
	add_filter( 'swp_add_networks', 'swp_yummly_network' );

	// Create the function that will filter the options
function swp_yummly_network( $networks ) {

	// Add your network to the existing network array
	$networks[] = 'yummly';

	// Be sure to return the modified options array or the world will explode
	return $networks;
};
/**

 * **************************************************************
 *                                                                *
 *   #3: Generate the API Share Count Request URL	             *
 *                                                                *
 ******************************************************************/
function swp_yummly_request_link( $url ) {
	$request_url = 'http://www.yummly.com/services/yum-count?url=' . $url;
	return $request_url;
}
/**

 * **************************************************************
 *                                                                *
 *   #4: Parse the Response to get the share count	             *
 *                                                                *
 ******************************************************************/
function swp_format_yummly_response( $response ) {
	$response = json_decode( $response, true );
	return isset( $response['count'] )?intval( $response['count'] ):0;
}
/**

***************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
*/
	add_filter( 'swp_network_buttons', 'swp_yummly_button_html',10 );
function swp_yummly_button_html( $array ) {

	// If we've already generated this button, just use our existing html
	if ( isset( $_GLOBALS['sw']['buttons'][ $array['postID'] ]['yummly'] ) ) :
		$array['resource']['yummly'] = $_GLOBALS['sw']['buttons'][ $array['postID'] ]['yummly'];

		// If not, let's check if Yummly is activated and create the button HTML
		elseif ( (isset( $array['options']['newOrderOfIcons']['yummly'] ) && ! isset( $array['buttons'] )) || (isset( $array['buttons'] ) && isset( $array['buttons']['yummly'] ))  ) :

			if (

				// If a category is set and this post is in that category
				(
					isset( $array['options']['yummly_categories'] )
					&& $array['options']['yummly_categories'] != ''
					&& in_category( $array['options']['yummly_categories'] , $array['postID'] )
				)

				||

				// If a tag is set and this post is in that tag
				(
					isset( $array['options']['yummly_tags'] )
					&& $array['options']['yummly_tags'] != ''
					&& has_tag( $array['options']['yummly_tags'] , $array['postID'] )
				)

				||

				// If no tags or categories have been set
				(
					! isset( $array['options']['yummly_tags'] ) && ! isset( $array['options']['yummly_categories'] ) ||
					 $array['options']['yummly_categories'] == '' && $array['options']['yummly_tags'] == ''
				)

				) :

				$array['totes'] += $array['shares']['yummly'];
				++$array['count'];

				// Let's create a title
				if ( get_post_meta( $array['postID'] , 'nc_ogTitle' , true ) ) :

					// If the user defined an social media title, let's use it.
					$title = urlencode( urldecode( get_post_meta( $array['postID'] , 'nc_ogTitle' , true ) ) );

				else :

					// Otherwise we'll use the default post title
					$title = urlencode( urldecode( get_the_title() ) );

				endif;

				if ( get_post_meta( $array['postID'],'swp_open_graph_image_url' ) ) :
					$image = urlencode( urldecode( get_post_meta( $array['postID'],'swp_open_graph_image_url',true ) ) );
				else :
					$image = urlencode( urldecode( get_post_meta( $array['postID'],'swp_open_thumbnail_url',true ) ) );
				endif;

				$array['resource']['yummly'] = '<div class="nc_tweetContainer swp_yummly" data-id="' . $array['count'] . '" data-network="yummly">';
				// $link = urlencode(urldecode(swp_process_url( $array['url'] , 'yummly' , $array['postID'] )));
				$link = $array['url'];
				$array['resource']['yummly'] .= '<a target="_blank" href="http://www.yummly.com/urb/verify?url=' . $link . '&title=' . $title . '&image=' . $image . '&yumtype=button" data-link="http://www.yummly.com/urb/verify?url=' . $link . '&title=' . $title . '&image=' . $image . '&yumtype=button" class="nc_tweet">';
				if ( $array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && $array['shares']['yummly'] > 0 ) :
					$array['resource']['yummly'] .= '<span class="iconFiller">';
					$array['resource']['yummly'] .= '<span class="spaceManWilly">';
					$array['resource']['yummly'] .= '<i class="sw sw-yummly"></i>';
					$array['resource']['yummly'] .= '<span class="swp_share"> ' . __( 'Yum','social-warfare' ) . '</span>';
					$array['resource']['yummly'] .= '</span></span>';
					$array['resource']['yummly'] .= '<span class="swp_count">' . swp_kilomega( $array['shares']['yummly'] ) . '</span>';
				else :
					$array['resource']['yummly'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-yummly"></i><span class="swp_share"> ' . __( 'Yum','social-warfare' ) . '</span></span></span></span>';
				endif;
				$array['resource']['yummly'] .= '</a>';
				$array['resource']['yummly'] .= '</div>';

				// Store these buttons so that we don't have to generate them for each set
				$_GLOBALS['sw']['buttons'][ $array['postID'] ]['yummly'] = $array['resource']['yummly'];

			endif;

		endif;

		return $array;

};
