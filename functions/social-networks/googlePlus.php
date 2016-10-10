<?php

/*****************************************************************
*                                                                *
*   #1: Add the On / Off Switch	and Sortable Option				 *
*                                                                *
******************************************************************/
	add_filter('swp_button_options', 'swp_googlePlus_options_function',20);
	function swp_googlePlus_options_function($options) {

		// Create the new option in a variable to be inserted
		$options['content']['googlePlus'] = array(
			'type' => 'checkbox',
			'content' => 'Google Plus',
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
	add_filter('swp_add_networks', 'swp_googlePlus_network');

	// Create the function that will filter the options
	function swp_googlePlus_network($networks) {

		// Add your network to the existing network array
		$networks[] = 'googlePlus';

		// Be sure to return the modified options array or the world will explode
		return $networks;
	};
/*****************************************************************
*                                                                *
*   #3: Generate the API Share Count Request URL	             *
*                                                                *
******************************************************************/
	function swp_googlePlus_request_link($url) {
		return $url;
	}
/*****************************************************************
*                                                                *
*   #4: Parse the Response to get the share count	             *
*                                                                *
******************************************************************/
	function swp_format_googlePlus_response($response) {
		$response = json_decode($response, true);
		return isset($response[0]['result']['metadata']['globalCounts']['count'])?intval( $response[0]['result']['metadata']['globalCounts']['count'] ):0;
	}
/*****************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
******************************************************************/
	add_filter('swp_network_buttons', 'swp_googlePlus_button_html',10);
	function swp_googlePlus_button_html($array) {

		// If we've already generated this button, just use our existing html
		if(isset($_GLOBALS['sw']['buttons'][$array['postID']]['googlePlus'])):
			$array['resource']['googlePlus'] = $_GLOBALS['sw']['buttons'][$array['postID']]['googlePlus'];

		// If not, let's check if Facebook is activated and create the button HTML
		elseif( (isset($array['options']['newOrderOfIcons']['googlePlus']) && !isset($array['buttons'])) || (isset($array['buttons']) && isset($array['buttons']['googlePlus']))  ):

			$array['totes'] += $array['shares']['googlePlus'];
			++$array['count'];

			$array['resource']['googlePlus'] = '<div class="nc_tweetContainer googlePlus" data-id="'.$array['count'].'" data-network="google_plus">';
			$link = urlencode(urldecode(swp_process_url( $array['url'] , 'googlePlus' , $array['postID'] )));
			$array['resource']['googlePlus'] .= '<a target="_blank" href="https://plus.google.com/share?url='.$link.'" data-link="https://plus.google.com/share?url='.$link.'" class="nc_tweet">';
			if($array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && $array['shares']['googlePlus'] > 0):
				$array['resource']['googlePlus'] .= '<span class="iconFiller">';
				$array['resource']['googlePlus'] .= '<span class="spaceManWilly">';
				$array['resource']['googlePlus'] .= '<i class="sw sw-google-plus"></i>';
				$array['resource']['googlePlus'] .= '<span class="swp_share"> '.__('+1','social-warfare').'</span>';
				$array['resource']['googlePlus'] .= '</span></span>';
				$array['resource']['googlePlus'] .= '<span class="swp_count">'.swp_kilomega($array['shares']['googlePlus']).'</span>';
			else:
				$array['resource']['googlePlus'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-google-plus"></i><span class="swp_share"> '.__('+1','social-warfare').'</span></span></span></span>';
			endif;
			$array['resource']['googlePlus'] .= '</a>';
			$array['resource']['googlePlus'] .= '</div>';

			// Store these buttons so that we don't have to generate them for each set
			$_GLOBALS['sw']['buttons'][$array['postID']]['googlePlus'] = $array['resource']['googlePlus'];

		endif;

		return $array;

	};
