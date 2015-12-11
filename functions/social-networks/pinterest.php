<?php

/*****************************************************************
*                                                                *
*   #1: Add the On / Off Switch	and Sortable Option				 *
*                                                                *
******************************************************************/
	add_filter('sw_options', 'sw_pinterest_options_function',20);
	function sw_pinterest_options_function($sw_options) {
	
		// Create the new option in a variable to be inserted
		$pinterest = array(
			'pinterest' => array(
				 'type' => 'checkbox',
				 'content' => 'Pinterest',
				 'default' => 1
			)
		);
	 
		// Call the function that adds the On / Off Switch and Sortable Option
		return sw_add_network_option($sw_options,$pinterest);
	
	};
/*****************************************************************
*                                                                *
*   #2: Add it to global network array	         				 *
*                                                                *
******************************************************************/
	// Queue up your filter to be ran on the sw_options hook.
	add_filter('sw_add_networks', 'sw_pinterest_network');
	
	// Create the function that will filter the options
	function sw_pinterest_network($networks) {
		
		// Add your network to the existing network array
		$networks[] = 'pinterest';
	
		// Be sure to return the modified options array or the world will explode
		return $networks;
	};
/*****************************************************************
*                                                                *
*   #3: Generate the API Share Count Request URL	             *
*                                                                *
******************************************************************/
	function sw_pinterest_request_link($url) {
		$url = rawurlencode($url);
		$request_url = 'https://api.pinterest.com/v1/urls/count.json?url='.$url;
		return $request_url;
	}	
/*****************************************************************
*                                                                *
*   #4: Parse the Response to get the share count	             *
*                                                                *
******************************************************************/	
	function sw_format_pinterest_response($response) {
		$response = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $response);
		$response = json_decode($response,true);
		return isset($response['count'])?intval($response['count']):0;	
	}
/*****************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
******************************************************************/
	add_filter('sw_network_buttons', 'sw_pinterest_button_html',10);
	function sw_pinterest_button_html($array) {
		
		// If we've already generated this button, just use our existing html
		if(isset($_GLOBALS['sw']['buttons'][$array['postID']]['pinterest'])):
			$array['resource']['pinterest'] = $_GLOBALS['sw']['buttons'][$array['postID']]['pinterest'];
			
		// If not, let's check if Facebook is activated and create the button HTML
		elseif( $array['options']['pinterest'] ):
			
			$array['totes'] += $array['shares']['pinterest'];
			++$array['count'];
		
			
			$pi = get_post_meta( $array['postID'] , 'nc_pinterestImage' , true);
			
			if(sw_is_cache_fresh($array['postID']) == false):
		
				// Check if an image ID has been provided
				$array['imageID'] = get_post_meta( $array['postID'] , 'nc_pinterestImage' , true );
				if($array['imageID']):
					$array['imageURL'] = wp_get_attachment_url( $array['imageID'] );
					delete_post_meta($array['postID'],'sw_pinterest_image_url');
					update_post_meta($array['postID'],'sw_pinterest_image_url',$array['imageURL']);
				else:
					$array['imageURL'] = wp_get_attachment_url( get_post_thumbnail_id( $array['postID'] ) );
					delete_post_meta($array['postID'],'sw_pinterest_image_url');
				endif;
			
			else:
			
				// Check if we have a cached Open Graph Image URL
				$array['imageURL'] = get_post_meta( $array['postID'] , 'sw_pinterest_image_url' , true );
				
				// If not, let's check to see if we have an ID to generate one
				if(!$array['imageURL']):
					
					// Check for an Open Graph Image ID
					$array['imageID'] = get_post_meta( $array['postID'] , 'nc_pinterestImage' , true );
					if($array['imageID']):
					
						// If we find one, let's convert it to a link and cache it for next time
						$array['imageURL'] = wp_get_attachment_url( $array['imageID'] );
						delete_post_meta($array['postID'],'sw_pinterest_image_url');
						update_post_meta($array['postID'],'sw_pinterest_image_url',$array['imageURL']);
					else:
					
						// If we don't find one, let's see if we can use a post thumbnail
						$array['imageURL'] = wp_get_attachment_url( get_post_thumbnail_id( $array['postID'] ) );
					endif;
				endif;			
			endif;		
		
			$pd	= get_post_meta( $array['postID'] , 'nc_pinterestDescription' , true );
			if($array['imageURL']): 
				$pi 	= '&media='.urlencode(html_entity_decode($array['imageURL'],ENT_COMPAT, 'UTF-8'));
			else: 
				$pi		= '';
			endif;
		
			$pinterestLink = $array['url'];
			$title = strip_tags(get_the_title($array['postID']));
			$title = str_replace('|','',$title);
			
			if($pi != ''): 
				$a = '<a data-link="https://pinterest.com/pin/create/button/?url='.$pinterestLink.''.$pi.'&description='.($pd != '' ? urlencode(html_entity_decode($pd, ENT_COMPAT, 'UTF-8')) : urlencode(html_entity_decode($title, ENT_COMPAT, 'UTF-8'))).'" class="nc_tweet" data-count="0">';
			else: 
				$a = '<a onClick="var e=document.createElement(\'script\');e.setAttribute(\'type\',\'text/javascript\');e.setAttribute(\'charset\',\'UTF-8\');e.setAttribute(\'src\',\'//assets.pinterest.com/js/pinmarklet.js?r=\'+Math.random()*99999999);document.body.appendChild(e);" class="nc_tweet noPop">';
			endif;
			$array['resource']['pinterest'] = '<div class="nc_tweetContainer nc_pinterest" data-id="'.$array['count'].'">';
			$array['resource']['pinterest'] .= $a;
			if($array['options']['totesEach'] && $array['totes'] >= $array['options']['minTotes'] && $array['shares']['pinterest'] > 0):
				$array['resource']['pinterest'] .= '<span class="iconFiller">';
				$array['resource']['pinterest'] .= '<span class="spaceManWilly" style="width:55px;">';
				$array['resource']['pinterest'] .= '<i class="sw sw-pinterest"></i>';
				$array['resource']['pinterest'] .= '<span class="sw_share"> '.$array['language']['pinterest'].'</span>';
				$array['resource']['pinterest'] .= '</span></span>';
				$array['resource']['pinterest'] .= '<span class="sw_count">'.kilomega($array['shares']['pinterest']).'</span>'; 
			else:
				$array['resource']['pinterest'] .= '<span class="sw_count sw_hide"><span class="iconFiller"><span class="spaceManWilly" style="width:55px;"><i class="sw sw-pinterest"></i><span class="sw_share"> '.$array['language']['pinterest'].'</span></span></span></span>';
			endif;
			$array['resource']['pinterest'] .= '</a>';
			$array['resource']['pinterest'] .= '</div>';
			
			// Store these buttons so that we don't have to generate them for each set
			$_GLOBALS['sw']['buttons'][$array['postID']]['pinterest'] = $array['resource']['pinterest'];
			
		endif;
		
		return $array;
		
	};
		
		
		
		
		