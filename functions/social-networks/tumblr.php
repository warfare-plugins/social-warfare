<?php

/*****************************************************************
*                                                                *
*   #1: Add the On / Off Switch	and Sortable Option				 *
*                                                                *
******************************************************************/
	add_filter('sw_options', 'sw_tumblr_options_function',20);
	function sw_tumblr_options_function($sw_options) {

		// Create the new option in a variable to be inserted
		$tumblr = array(
			'tumblr' => array(
				 'type' => 'checkbox',
				 'content' => 'Tumblr',
				 'default' => false
			)
		);
		// Call the function that adds the On / Off Switch and Sortable Option
		return sw_add_network_option($sw_options,$tumblr);
	};
/*****************************************************************
*                                                                *
*   #2: Add it to global network array	         				 *
*                                                                *
******************************************************************/
	// Queue up your filter to be ran on the sw_options hook.
	add_filter('sw_add_networks', 'sw_tumblr_network');

	// Create the function that will filter the options
	function sw_tumblr_network($networks) {

		// Add your network to the existing network array
		$networks[] = 'tumblr';

		// Be sure to return the modified options array or the world will explode
		return $networks;
	};
/*****************************************************************
*                                                                *
*   #3: Generate the API Share Count Request URL	             *
*                                                                *
******************************************************************/
	function sw_tumblr_request_link($url) {
		$request_url = 'https://api.tumblr.com/v2/share/stats?url=' . $url;
		return $request_url;
	}
/*****************************************************************
*                                                                *
*   #4: Parse the Response to get the share count	             *
*                                                                *
******************************************************************/
	function sw_format_tumblr_response($response) {
		$response = json_decode($response, true);
		return isset($response['response']['note_count'])?intval($response['response']['note_count']):0;
	}
/*****************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
******************************************************************/
	add_filter('sw_network_buttons', 'sw_tumblr_button_html',10);
	function sw_tumblr_button_html($array) {

		// If we've already generated this button, just use our existing html
		if(isset($_GLOBALS['sw']['buttons'][$array['postID']]['tumblr'])):
			$array['resource']['tumblr'] = $_GLOBALS['sw']['buttons'][$array['postID']]['tumblr'];

		// If not, let's check if Facebook is activated and create the button HTML
		elseif( ($array['options']['tumblr'] && !isset($array['buttons'])) || (isset($array['buttons']) && isset($array['buttons']['tumblr']))  ):

			$array['totes'] += $array['shares']['tumblr'];
			++$array['count'];

			// Collect the Title
			$title = get_post_meta( $array['postID'] , 'nc_ogTitle' , true );
			if(!$title):
				$title = get_the_title();
			endif;

			// Collect the Description
			$description = get_post_meta( $array['postID'] , 'nc_ogDescription' , true );

			$array['resource']['tumblr'] = '<div class="nc_tweetContainer sw_tumblr" data-id="'.$array['count'].'" data-network="tumblr">';
			$link = urlencode(urldecode(sw_process_url( $array['url'] , 'tumblr' , $array['postID'] )));
			$array['resource']['tumblr'] .= '<a target="_blank" href="http://www.tumblr.com/share/link?url='.$link.'&name='.urlencode($title).($description ? '&description=' : '').urlencode($description).'" data-link="http://www.tumblr.com/share/link?url='.$link.'&name='.urlencode($title).($description ? '&description=' : '').urlencode($description).'" class="nc_tweet">';
			if($array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && $array['shares']['tumblr'] > 0):
				$array['resource']['tumblr'] .= '<span class="iconFiller">';
				$array['resource']['tumblr'] .= '<span class="spaceManWilly">';
				$array['resource']['tumblr'] .= '<i class="sw sw-tumblr"></i>';
				$array['resource']['tumblr'] .= '<span class="sw_share"> '.__('Share','social-warfare').'</span>';
				$array['resource']['tumblr'] .= '</span></span>';
				$array['resource']['tumblr'] .= '<span class="sw_count">'.kilomega($array['shares']['tumblr']).'</span>';
			else:
				$array['resource']['tumblr'] .= '<span class="sw_count sw_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-tumblr"></i><span class="sw_share"> '.__('Share','social-warfare').'</span></span></span></span>';
			endif;
			$array['resource']['tumblr'] .= '</a>';
			$array['resource']['tumblr'] .= '</div>';

			// Store these buttons so that we don't have to generate them for each set
			$_GLOBALS['sw']['buttons'][$array['postID']]['tumblr'] = $array['resource']['tumblr'];

		endif;

		return $array;

	};
