<?php

/*****************************************************************
*                                                                *
*   #1: Add the On / Off Switch	and Sortable Option				 *
*                                                                *
******************************************************************/
	add_filter('swp_button_options', 'swp_email_options_function',20);
	function swp_email_options_function($options) {

		// Create the new option in a variable to be inserted
		$options['content']['email'] = array(
			'type' => 'checkbox',
			'content' => 'Email',
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
	add_filter('swp_add_networks', 'swp_email_network');

	// Create the function that will filter the options
	function swp_email_network($networks) {

		// Add your network to the existing network array
		$networks[] = 'email';

		// Be sure to return the modified options array or the world will explode
		return $networks;
	};
/*****************************************************************
*                                                                *
*   #3: Generate the API Share Count Request URL	             *
*                                                                *
******************************************************************/
	function swp_email_request_link($url) {
		return 0;
	}
/*****************************************************************
*                                                                *
*   #4: Parse the Response to get the share count	             *
*                                                                *
******************************************************************/
	function swp_format_email_response($response) {
		return 0;
	}
/*****************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
******************************************************************/
	add_filter('swp_network_buttons', 'swp_email_button_html',10);
	function swp_email_button_html($array) {

		if( ($array['options']['newOrderOfIcons']['email'] && !isset($array['buttons'])) || (isset($array['buttons']) && isset($array['buttons']['email']))  ):

			// Collect the Title
			$title = get_post_meta( $array['postID'] , 'nc_ogTitle' , true );
			if(!$title):
				$title = get_the_title();
			endif;

			// Collect the Description
			$description = get_post_meta( $array['postID'] , 'nc_ogDescription' , true );
			if(!$description):
				$description = swp_get_excerpt_by_id($array['postID']);
			endif;
			++$array['count'];

			$array['resource']['email'] = '<div class="nc_tweetContainer swp_email" data-id="'.$array['count'].'" data-network="email">';
			$link = urlencode(urldecode(swp_process_url( $array['url'] , 'email' , $array['postID'] )));
			$array['resource']['email'] .= '<a href="mailto:?subject='.str_replace('&amp;','%26',rawurlencode(html_entity_decode($title, ENT_COMPAT, 'UTF-8'))).'&body='.str_replace('&amp;','%26',rawurlencode(html_entity_decode($description, ENT_COMPAT, 'UTF-8'))).rawurlencode(' Read here: ') .$link.'" class="nc_tweet noPop">';
			$array['resource']['email'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-email"></i><span class="swp_share"> '.__('Email','social-warfare').'</span></span></span></span>';
			$array['resource']['email'] .= '</a>';
			$array['resource']['email'] .= '</div>';

		endif;

		return $array;

	};
