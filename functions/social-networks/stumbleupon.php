<?php

/*****************************************************************
*                                                                *
*   #1: Add the On / Off Switch	and Sortable Option				 *
*                                                                *
******************************************************************/
	add_filter('sw_options', 'sw_stumbleupon_options_function',20);
	function sw_stumbleupon_options_function($sw_options) {
	
		// Create the new option in a variable to be inserted
		$stumbleupon = array(
			'stumbleupon' => array(
				 'type' => 'checkbox',
				 'content' => 'StumbleUpon',
				 'default' => 0
			)
		);
	 
		// Call the function that adds the On / Off Switch and Sortable Option
		return sw_add_network_option($sw_options,$stumbleupon);
	
	};
/*****************************************************************
*                                                                *
*   #2: Add it to global network array	         				 *
*                                                                *
******************************************************************/
	// Queue up your filter to be ran on the sw_options hook.
	add_filter('sw_add_networks', 'sw_stumbleupon_network');
	
	// Create the function that will filter the options
	function sw_stumbleupon_network($networks) {
		
		// Add your network to the existing network array
		$networks[] = 'stumbleupon';
	
		// Be sure to return the modified options array or the world will explode
		return $networks;
	};
/*****************************************************************
*                                                                *
*   #3: Generate the API Share Count Request URL	             *
*                                                                *
******************************************************************/
	function sw_stumbleupon_request_link($url) {
		$request_url = 'https://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $url;
		return $request_url;
	}
/*****************************************************************
*                                                                *
*   #4: Parse the Response to get the share count	             *
*                                                                *
******************************************************************/	
	function sw_format_stumbleupon_response($response) {
		$response = json_decode($response, true);	
		return isset($response['result']['views'])?intval($response['result']['views']):0;
	}
/*****************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
******************************************************************/
	add_filter('sw_network_buttons', 'sw_stumbleupon_button_html',10);
	function sw_stumbleupon_button_html($array) {
		
		// If we've already generated this button, just use our existing html
		if(isset($_GLOBALS['sw']['buttons'][$array['postID']]['stumbleupon'])):
			$array['resource']['stumbleupon'] = $_GLOBALS['sw']['buttons'][$array['postID']]['stumbleupon'];
			
		// If not, let's check if Facebook is activated and create the button HTML
		elseif( isset($array['options']['stumbleupon']) && $array['options']['stumbleupon'] ):
			
			if(isset($array['shares']['stumbleupon'])):
				$array['totes'] += $array['shares']['stumbleupon'];
			endif;
			++$array['count'];
		
			// Collect the Title
			$title = SW_META_meta( 'nc_ogTitle' );
			if(!$title):
				$title = get_the_title();
			endif;
			
			$array['resource']['stumbleupon'] = '<div class="nc_tweetContainer sw_stumbleupon" data-id="'.$array['count'].'">';
			$link = urlencode(urldecode(sw_process_url( $array['url'] , 'stumbleupon' , $array['postID'] )));
			$array['resource']['stumbleupon'] .= '<a target="_blank" href="http://www.stumbleupon.com/submit?url='.$link.'&title='.urlencode($title).'" data-link="http://www.stumbleupon.com/submit?url='.$link.'&title='.urlencode($title).'" class="nc_tweet">';
			if($array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && isset($array['shares']['stumbleupon']) && $array['shares']['stumbleupon'] > 0):
				$array['resource']['stumbleupon'] .= '<span class="iconFiller">';
				$array['resource']['stumbleupon'] .= '<span class="spaceManWilly">';
				$array['resource']['stumbleupon'] .= '<i class="sw sw-stumbleupon"></i>';
				$array['resource']['stumbleupon'] .= '<span class="sw_share"> '.$array['language']['stumbleupon'].'</span>';
				$array['resource']['stumbleupon'] .= '</span></span>';
				$array['resource']['stumbleupon'] .= '<span class="sw_count">'.kilomega($array['shares']['stumbleupon']).'</span>'; 
			else:
				$array['resource']['stumbleupon'] .= '<span class="sw_count sw_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-stumbleupon"></i><span class="sw_share"> '.$array['language']['stumbleupon'].'</span></span></span></span>';
			endif;
			$array['resource']['stumbleupon'] .= '</a>';
			$array['resource']['stumbleupon'] .= '</div>';
			
			// Store these buttons so that we don't have to generate them for each set
			$_GLOBALS['sw']['buttons'][$array['postID']]['stumbleupon'] = $array['resource']['stumbleupon'];
			
		endif;
		
		return $array;
		
	};
		
		
		
		
		