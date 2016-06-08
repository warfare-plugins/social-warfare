<?php

/*****************************************************************
*                                                                *
*   #1: Add the On / Off Switch	and Sortable Option				 *
*                                                                *
******************************************************************/
	add_filter('sw_button_options', 'sw_whatsapp_options_function',20);
	function sw_whatsapp_options_function($options) {

		// Create the new option in a variable to be inserted
		$options['content']['whatsapp'] = array(
			'type' => 'checkbox',
			'content' => 'whatsapp',
			'default' => false
		);

		return $options;		 

	};
/*****************************************************************
*                                                                *
*   #2: Add it to global network array	         				 *
*                                                                *
******************************************************************/
	// Queue up your filter to be ran on the sw_options hook.
	add_filter('sw_add_networks', 'sw_whatsapp_network');

	// Create the function that will filter the options
	function sw_whatsapp_network($networks) {

		// Add your network to the existing network array
		$networks[] = 'whatsapp';

		// Be sure to return the modified options array or the world will explode
		return $networks;
	};
/*****************************************************************
*                                                                *
*   #3: Generate the API Share Count Request URL	             *
*                                                                *
******************************************************************/
	function sw_whatsapp_request_link($url) {
		$request_url = 'https://api.facebook.com/restserver.php?method=links.getStats&format=json&urls='.$url;
		return 0;
	}
/*****************************************************************
*                                                                *
*   #4: Parse the Response to get the share count	             *
*                                                                *
******************************************************************/
	function sw_format_whatsapp_response($response) {
		// $response = json_decode($response, true);
		// return isset($response[0]['total_count'])?intval($response[0]['total_count']):0;
		return 0;
	}
/*****************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
******************************************************************/
	add_filter('sw_network_buttons', 'sw_whatsapp_button_html',10);
	function sw_whatsapp_button_html($array) {

		// If we've already generated this button, just use our existing html
		if(isset($_GLOBALS['sw']['buttons'][$array['postID']]['whatsapp'])):
			$array['resource']['whatsapp'] = $_GLOBALS['sw']['buttons'][$array['postID']]['whatsapp'];

		// If not, let's check if WhatsApp is activated and create the button HTML
		elseif( ($array['options']['whatsapp'] && sw_mobile_detection() && !isset($array['buttons'])) 
				|| (isset($array['buttons']) && isset($array['buttons']['whatsapp']))  ):

			$array['totes'] += $array['shares']['whatsapp'];
			++$array['count'];

			$array['resource']['whatsapp'] = '<div class="nc_tweetContainer sw_whatsapp" data-id="'.$array['count'].'" data-network="whatsapp">';
			$link = urlencode(urldecode(sw_process_url( $array['url'] , 'whatsapp' , $array['postID'] )));
			$array['resource']['whatsapp'] .= '<a target="_blank" href="whatsapp://send?text='.$link.'" class="nc_tweet" data-action="share/whatsapp/share">';
			if($array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && $array['shares']['whatsapp'] > 0):
				$array['resource']['whatsapp'] .= '<span class="iconFiller">';
				$array['resource']['whatsapp'] .= '<span class="spaceManWilly">';
				$array['resource']['whatsapp'] .= '<i class="sw sw-whatsapp"></i>';
				$array['resource']['whatsapp'] .= '<span class="sw_share"> '.__('WhatsApp','social-warfare').'</span>';
				$array['resource']['whatsapp'] .= '</span></span>';
				$array['resource']['whatsapp'] .= '<span class="sw_count">'.kilomega($array['shares']['whatsapp']).'</span>';
			else:
				$array['resource']['whatsapp'] .= '<span class="sw_count sw_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-whatsapp"></i><span class="sw_share"> '.__('WhatsApp','social-warfare').'</span></span></span></span>';
			endif;
			$array['resource']['whatsapp'] .= '</a>';
			$array['resource']['whatsapp'] .= '</div>';

			// Store these buttons so that we don't have to generate them for each set
			$_GLOBALS['sw']['buttons'][$array['postID']]['whatsapp'] = $array['resource']['whatsapp'];

		endif;

		return $array;

	};
