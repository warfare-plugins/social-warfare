<?php

/*****************************************************************
*                                                                *
*   #1: Add the On / Off Switch	and Sortable Option				 *
*                                                                *
******************************************************************/
	add_filter('sw_options', 'sw_email_options_function',20);
	function sw_email_options_function($sw_options) {
	
		// Create the new option in a variable to be inserted
		$email = array(
			'email' => array(
				 'type' => 'checkbox',
				 'content' => 'Email',
				 'default' => 0
			)
		);
	 
		// Call the function that adds the On / Off Switch and Sortable Option
		return sw_add_network_option($sw_options,$email);
	
	};
/*****************************************************************
*                                                                *
*   #2: Add it to global network array	         				 *
*                                                                *
******************************************************************/
	// Queue up your filter to be ran on the sw_options hook.
	add_filter('sw_add_networks', 'sw_email_network');
	
	// Create the function that will filter the options
	function sw_email_network($networks) {
		
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
	function sw_email_request_link($url) {
		return 0;
	}
/*****************************************************************
*                                                                *
*   #4: Parse the Response to get the share count	             *
*                                                                *
******************************************************************/	
	function sw_format_email_response($response) {	
		return 0;
	}
/*****************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
******************************************************************/
	add_filter('sw_network_buttons', 'sw_email_button_html',10);
	function sw_email_button_html($array) {
		
		if( $array['options']['email'] ):
		
			// Collect the Title
			$title = SW_META_meta( 'nc_ogTitle' );
			if(!$title):
				$title = get_the_title();
			endif;
			
			// Collect the Description
			$description = SW_META_meta( 'nc_ogDescription' );
			if(!$description):
				$description = sw_get_excerpt_by_id($array['postID']);
			endif;
			++$array['count'];
					
			$array['resource']['email'] = '<div class="nc_tweetContainer sw_email" data-id="'.$array['count'].'">';
			$link = urlencode(urldecode(sw_process_url( $array['url'] , 'email' , $array['postID'] )));
			$array['resource']['email'] .= '<a href="mailto:?subject='.rawurlencode($title).'&body='.rawurlencode($description).rawurlencode(' Read here: ') .$link.'" class="nc_tweet">';
			$array['resource']['email'] .= '<span class="sw_count sw_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-email"></i><span class="sw_share"> '.$array['language']['email'].'</span></span></span></span>';
			$array['resource']['email'] .= '</a>';
			$array['resource']['email'] .= '</div>';
			
		endif;
		
		return $array;
		
	};
		
		
		
		
		