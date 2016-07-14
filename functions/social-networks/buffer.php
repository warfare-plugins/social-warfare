<?php

/*****************************************************************
*                                                                *
*   #1: Add the On / Off Switch	and Sortable Option				 *
*                                                                *
******************************************************************/
	add_filter('swp_button_options', 'swp_buffer_options_function',20);
	function swp_buffer_options_function($options) {

		// Create the new option in a variable to be inserted
		$options['content']['buffer'] = array(
			'type' => 'checkbox',
			'content' => 'Buffer',
			'default' => false,
			'premium' => true
		);

		return $options;		 

	};
/*****************************************************************
*                                                                *
*   #2: Add it to global network array	         				 *
*                                                                *
******************************************************************/
	// Queue up your filter to be ran on the swp_options hook.
	add_filter('swp_add_networks', 'swp_buffer_network');

	// Create the function that will filter the options
	function swp_buffer_network($networks) {

		// Add your network to the existing network array
		$networks[] = 'buffer';

		// Be sure to return the modified options array or the world will explode
		return $networks;
	};
/*****************************************************************
*                                                                *
*   #3: Generate the API Share Count Request URL	             *
*                                                                *
******************************************************************/
	function swp_buffer_request_link($url) {
		$request_url = 'https://api.bufferapp.com/1/links/shares.json?url='.$url;
		return $request_url;
	}
/*****************************************************************
*                                                                *
*   #4: Parse the Response to get the share count	             *
*                                                                *
******************************************************************/
	function swp_format_buffer_response($response) {
		$response = json_decode($response, true);
		return isset($response['shares'])?intval($response['shares']):0;
	}
/*****************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
******************************************************************/
	add_filter('swp_network_buttons', 'swp_buffer_button_html',10);
	function swp_buffer_button_html($array) {

		// If we've already generated this button, just use our existing html
		if(isset($_GLOBALS['sw']['buttons'][$array['postID']]['buffer'])):
			$array['resource']['buffer'] = $_GLOBALS['sw']['buttons'][$array['postID']]['buffer'];

		// If not, let's check if Buffer is activated and create the button HTML
		elseif( ($array['options']['newOrderOfIcons']['buffer'] && !isset($array['buttons'])) || (isset($array['buttons']) && isset($array['buttons']['buffer']))  ):

			// Collect the Title
			$title = get_post_meta( $array['postID'] , 'nc_ogTitle' , true );
			if(!$title):
				$title = get_the_title();
			endif;

			$array['totes'] += $array['shares']['buffer'];
			++$array['count'];

			$array['resource']['buffer'] = '<div class="nc_tweetContainer swp_buffer" data-id="'.$array['count'].'" data-network="buffer">';
			$link = urlencode(urldecode(swp_process_url( $array['url'] , 'buffer' , $array['postID'] )));
			$array['resource']['buffer'] .= '<a target="_blank" href="http://bufferapp.com/add?url='.$link.'&text='.urlencode(html_entity_decode($title, ENT_COMPAT, 'UTF-8')).'" data-link="http://bufferapp.com/add?url='.$link.'&text='.urlencode(html_entity_decode($title, ENT_COMPAT, 'UTF-8')).'" class="nc_tweet buffer_link">';
			if($array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && $array['shares']['buffer'] > 0):
				$array['resource']['buffer'] .= '<span class="iconFiller">';
				$array['resource']['buffer'] .= '<span class="spaceManWilly">';
				$array['resource']['buffer'] .= '<i class="sw sw-buffer"></i>';
				$array['resource']['buffer'] .= '<span class="swp_share"> '.__('Buffer','social-warfare').'</span>';
				$array['resource']['buffer'] .= '</span></span>';
				$array['resource']['buffer'] .= '<span class="swp_count">'.swp_kilomega($array['shares']['buffer']).'</span>';
			else:
				$array['resource']['buffer'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-buffer"></i><span class="swp_share"> '.__('Buffer','social-warfare').'</span></span></span></span>';
			endif;
			$array['resource']['buffer'] .= '</a>';
			$array['resource']['buffer'] .= '</div>';

			// Store these buttons so that we don't have to generate them for each set
			$_GLOBALS['sw']['buttons'][$array['postID']]['buffer'] = $array['resource']['buffer'];

		endif;

		return $array;

	};
