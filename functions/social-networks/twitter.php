<?php

/*****************************************************************
*                                                                *
*   #1: Add the On / Off Switch	and Sortable Option				 *
*                                                                *
******************************************************************/
	add_filter('sw_options', 'sw_twitter_options_function',20);
	function sw_twitter_options_function($sw_options) {
	
		// Create the new option in a variable to be inserted
		$twitter = array(
			'twitter' => array(
				 'type' => 'checkbox',
				 'content' => 'Twitter',
				 'default' => 1
			)
		);
	 
		// Call the function that adds the On / Off Switch and Sortable Option
		return sw_add_network_option($sw_options,$twitter);
	
	};
/*****************************************************************
*                                                                *
*   #2: Add it to global network array	         				 *
*                                                                *
******************************************************************/
	// Queue up your filter to be ran on the sw_options hook.
	add_filter('sw_add_networks', 'sw_twitter_network');
	
	// Create the function that will filter the options
	function sw_twitter_network($networks) {
		
		// Add your network to the existing network array
		$networks[] = 'twitter';
	
		// Be sure to return the modified options array or the world will explode
		return $networks;
	};
/*****************************************************************
*                                                                *
*   #3: Generate the API Share Count Request URL	             *
*                                                                *
******************************************************************/
	function sw_twitter_request_link($url) {
		
		// Fetch the user's options
		$sw_user_options = sw_get_user_options();
		
		// If the user has enabled Twitter shares....
		if($sw_user_options['twitter_shares']):
		
			// Return the correct Twitter JSON endpoint URL
			$request_url = 'http://public.newsharecounts.com/count.json?url=' . $url;
			
			// Debugging
			if(isset($_GET['sw_twitter_debug']) && $_GET['sw_twitter_debug'] == true):
				echo '<b>Request URL:</b> '.$request_url.'<br />';
			endif;
			
			return $request_url;
			
		// If the user has not enabled Twitter shares....
		else:
		
			// Return nothing so we don't run an API call 
			return 0;
			
		endif;
	}
/*****************************************************************
*                                                                *
*   #4: Parse the Response to get the share count	             *
*                                                                *
******************************************************************/
	function sw_format_twitter_response($response) {
		
		// Fetch the user's options
		$sw_user_options = sw_get_user_options();
		
		// If the user has enabled Twitter shares....
		if($sw_user_options['twitter_shares']):
			
			// Debugging
			if(isset($_GET['sw_twitter_debug']) && $_GET['sw_twitter_debug'] == true):
				echo '<b>Response:</b> '.$response.'<br />';
			endif;

			// Parse the response to get the actual number		
			$response = json_decode($response, true);
			
			return isset($response['count'])?intval($response['count']):0;
		
		// If the user has not enabled Twitter shares....
		else:
		
			// Return the number 0
			return 0;
		
		endif;
	}
/*****************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
******************************************************************/
	add_filter('sw_network_buttons', 'sw_twitter_button_html',10);
	function sw_twitter_button_html($array) {

		// If we've already generated this button, just use our existing html
		if(isset($_GLOBALS['sw']['buttons'][$array['postID']]['twitter'])):
			$array['resource']['twitter'] = $_GLOBALS['sw']['buttons'][$array['postID']]['twitter'];
			
		// If not, let's check if Facebook is activated and create the button HTML
		elseif( isset($array['options']['twitter']) && $array['options']['twitter'] ):
			
			$array['totes'] += $array['shares']['twitter'];
			++$array['count'];
			
			$title = strip_tags(get_the_title($array['postID']));
			$title = str_replace('|','',$title);
			$ct = get_post_meta( $array['postID'] , 'nc_customTweet' , true );

			
			$ct = ($ct != '' ? urlencode(html_entity_decode($ct, ENT_COMPAT, 'UTF-8')) : urlencode(html_entity_decode($title, ENT_COMPAT, 'UTF-8')));
			$twitterLink = sw_process_url( $array['url'] , 'twitter' , $array['postID'] );
			if (strpos($ct,'http') !== false) : $urlParam = '&url=/'; else: $urlParam = '&url='.$twitterLink; endif;
			
			if(sw_is_cache_fresh($array['postID']) == false):
				$user_twitter_handle 	= get_the_author_meta( 'sw_twitter' , sw_get_author($array['postID']));
				if($user_twitter_handle):
					delete_post_meta($array['postID'],'sw_twitter_username');
					update_post_meta($array['postID'],'sw_twitter_username',$user_twitter_handle);
				else:
					delete_post_meta($array['postID'],'sw_twitter_username');
				endif;
			else:
				$user_twitter_handle = get_post_meta( $array['postID'] , 'sw_twitter_username' , true );
			endif;
			
			if($user_twitter_handle):
				$viaText = '&via='.str_replace('@','',$user_twitter_handle);
			elseif($array['options']['twitterID']): 
				$viaText = '&via='.str_replace('@','',$array['options']['twitterID']); 
			else: 
				$viaText = ''; 
			endif;
			
			$array['resource']['twitter'] = '<div class="nc_tweetContainer twitter" data-id="'.$array['count'].'">';
			$array['resource']['twitter'] .= '<a href="https://twitter.com/share?original_referer=/&text='.$ct.''.$urlParam.''.$viaText.'" data-link="https://twitter.com/share?original_referer=/&text='.$ct.''.$urlParam.''.$viaText.'" class="nc_tweet">';
			if($array['options']['totesEach'] && $array['totes'] >= $array['options']['minTotes'] && $array['shares']['twitter'] > 0):
				$array['resource']['twitter'] .= '<span class="iconFiller">';
				$array['resource']['twitter'] .= '<span class="spaceManWilly">';
				$array['resource']['twitter'] .= '<i class="sw sw-twitter"></i>';
				$array['resource']['twitter'] .= '<span class="sw_share"> '.$array['language']['twitter'].'</span>';
				$array['resource']['twitter'] .= '</span></span>';
				$array['resource']['twitter'] .= '<span class="sw_count">'.kilomega($array['shares']['twitter']).'</span>';
			else:
				$array['resource']['twitter'] .= '<span class="sw_count sw_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-twitter"></i><span class="sw_share"> '.$array['language']['twitter'].'</span></span></span></span>';
			endif;
			$array['resource']['twitter'] .= '</a>';
			$array['resource']['twitter'] .= '</div>';		
			
			// Store these buttons so that we don't have to generate them for each set
			$_GLOBALS['sw']['buttons'][$array['postID']]['twitter'] = $array['resource']['twitter'];
			
		endif;

		return $array;

	};




