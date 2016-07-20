<?php

/*****************************************************************
*                                                                *
*   #1: Add the On / Off Switch	and Sortable Option				 *
*                                                                *
******************************************************************/
	add_filter('swp_button_options', 'swp_facebook_options_function',20);
	function swp_facebook_options_function($options) {

		// Create the new option in a variable to be inserted
		$options['content']['facebook'] = array(
			'type' => 'checkbox',
			'content' => 'Facebook',
			'default' => true,
			'premium' => false
		);

		return $options;		 

	};
/*****************************************************************
*                                                                *
*   #2: Add it to global network array	         				 *
*                                                                *
******************************************************************/
	// Queue up your filter to be ran on the swp_options hook.
	add_filter('swp_add_networks', 'swp_facebook_network');

	// Create the function that will filter the options
	function swp_facebook_network($networks) {

		// Add your network to the existing network array
		$networks[] = 'facebook';

		// Be sure to return the modified options array or the world will explode
		return $networks;
	};
/*****************************************************************
*                                                                *
*   #3: Generate the API Share Count Request URL	             *
*                                                                *
******************************************************************/
	function swp_facebook_request_link($url) {
		$request_url = 'https://api.facebook.com/restserver.php?method=links.getStats&format=json&urls='.$url;
		return $request_url;
	}
/*****************************************************************
*                                                                *
*   #4: Parse the Response to get the share count	             *
*                                                                *
******************************************************************/
	function swp_format_facebook_response($response) {
		$response = json_decode($response, true);
		return isset($response[0]['total_count'])?intval($response[0]['total_count']):0;
	}
/*****************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
******************************************************************/
	add_filter('swp_network_buttons', 'swp_facebook_button_html',10);
	function swp_facebook_button_html($array) {

		// If we've already generated this button, just use our existing html
		if(isset($_GLOBALS['sw']['buttons'][$array['postID']]['facebook'])):
			$array['resource']['facebook'] = $_GLOBALS['sw']['buttons'][$array['postID']]['facebook'];

		// If not, let's check if Facebook is activated and create the button HTML
		elseif( (isset($array['options']['newOrderOfIcons']['facebook']) && !isset($array['buttons'])) || (isset($array['buttons']) && isset($array['buttons']['facebook']))  ):

			$array['totes'] += $array['shares']['facebook'];
			++$array['count'];

			$array['resource']['facebook'] = '<div class="nc_tweetContainer swp_fb" data-id="'.$array['count'].'" data-network="facebook">';
			$link = urlencode(urldecode(swp_process_url( $array['url'] , 'facebook' , $array['postID'] )));
			$array['resource']['facebook'] .= '<a target="_blank" href="http://www.facebook.com/share.php?u='.$link.'" data-link="http://www.facebook.com/share.php?u='.$link.'" class="nc_tweet">';
			if($array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && $array['shares']['facebook'] > 0):
				$array['resource']['facebook'] .= '<span class="iconFiller">';
				$array['resource']['facebook'] .= '<span class="spaceManWilly">';
				$array['resource']['facebook'] .= '<i class="sw sw-facebook"></i>';
				$array['resource']['facebook'] .= '<span class="swp_share"> '.__('Share','social-warfare').'</span>';
				$array['resource']['facebook'] .= '</span></span>';
				$array['resource']['facebook'] .= '<span class="swp_count">'.swp_kilomega($array['shares']['facebook']).'</span>';
			else:
				$array['resource']['facebook'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-facebook"></i><span class="swp_share"> '.__('Share','social-warfare').'</span></span></span></span>';
			endif;
			$array['resource']['facebook'] .= '</a>';
			$array['resource']['facebook'] .= '</div>';

			// Store these buttons so that we don't have to generate them for each set
			$_GLOBALS['sw']['buttons'][$array['postID']]['facebook'] = $array['resource']['facebook'];

		endif;

		return $array;

	};
