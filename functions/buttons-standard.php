<?php

/*****************************************************************
*                                                                *
*          THE SOCIAL WARFARE WRAPPER FUNCTION         			 *
*                                                                *
******************************************************************/
function socialWarfare( $content = false , $where = 'default' , $echo = true ) {

	// Collect the deprecated fields and place them into an array
	$array['content'] 	= $content;
	$array['where'] 	= $where;
	$array['echo'] 		= $echo;
	$array['devs']		= true;

	// Pass the array into the new function
	return social_warfare($array);
}
/*****************************************************************
*                                                                *
*          A RECURSIVE ARRAY SEARCH FUNCTION         			 *
*                                                                *
******************************************************************/
function recursive_array_search($needle,$haystack) {
    foreach($haystack as $key=>$value) {
        $current_key=$key;
        if($needle===$value OR (is_array($value) && recursive_array_search($needle,$value) !== false)) {
            return $current_key;
        }
    }
    return false;
}
/*****************************************************************
*                                                                *
*          CACHe CHECKING FUNCTION         			 			 *
*                                                                *
******************************************************************/
function sw_is_cache_fresh( $postID , $output=false , $ajax=false ) {

	// Fetch the Options
	$options 			= sw_get_user_options();

	// Check if output is being forced or if legacy caching is enabled
	if($output == false && $options['cacheMethod'] != 'legacy'):
		if(isset($_GET['sw_cache']) && $_GET['sw_cache'] == 'rebuild'):
			$freshCache = false;
		else:
			$freshCache = true;
		endif;

	else:
		$postAge = floor(date('U') - get_post_time( 'U' , false , $postID));
		if($postAge < (21 * 86400)){ $hours = 1; }
		elseif($postAge < (60 * 86400)) { $hours = 4; }
		else { $hours = 12; }

		$time = floor(((date('U')/60)/60));
		$lastChecked = get_post_meta($postID,'sw_cache_timestamp',true);

		// Check if it's a crawl bot. If so, ONLY SERVE CACHED RESULTS FOR MAXIMUM SPEED
		if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])):
			$freshCache = true;

		// Next, check if the cache is fresh or needs rebuilt
		// Always be TRUE if we're not on a single.php otherwise we could end up
		// Rebuilding multiple page caches which will cost a lot of time.
		elseif(($lastChecked > ($time - $hours) && $lastChecked > 390000) || (!is_singular() && $ajax == false) ) :
			$freshCache = true;
		else:
			$freshCache = false;
		endif;
	endif;
	return $freshCache;
}


function sw_disable_subs() { return false; };
/*****************************************************************

THE SHARE BUTTONS FUNCTION:

This function accepts an array of parameters resulting in the
outputting of the Social Warfare Buttons.


ACCEPTED PARAMETERS :

content 	: The post content to which we append the buttons
			: (string)

where   	: Used to overwrite the default location in relation to the content
			: ( above | below | both | none )

echo    	: Used to print or store the variables.
			: ( true | false )



******************************************************************/
function social_warfare_buttons($array = array()) {

	// Setup the default Array parameters
	if(!isset($array['where'])) { $array['where'] = 'default'; }
	if(!isset($array['echo'])) { $array['echo'] = true; }
	if(!isset($array['content'])) { $array['content'] = false; }

	// Get the options...or create them if they don't exist
	if(isset($array['post_id'])):
		$postID = $array['post_id'];
	else:
		$postID = get_the_ID();
	endif;

	$options = sw_get_user_options();

	// Check to see if display location was specifically defined for this post
	$specWhere = get_post_meta($postID,'nc_postLocation',true);
	if( !$specWhere ) { $specWhere = 'default'; };
	
	if($array['where'] == 'default'):
		if($specWhere == 'default' || $specWhere == ''):
			if( is_singular('post') ):
				$array['where'] = $options['locationPost'];
			elseif( is_singular('page') ):
				$array['where'] = $options['locationPage'];
			elseif( is_singular() ):
				$postType = get_post_type($postID);
				if(isset($options['location'.$postType])):
					$array['where'] = $options['location'.$postType];
				else:
					$array['where'] = 'none';
				endif;
			else:
				$postType = get_post_type($postID);
				if(isset($options['location'.$postType])):
					$array['where'] = $options['location'.$postType];
				elseif($postType == 'post' || $postType == 'page'):
					$array['where'] = $options['locationSite'];
				else:
					$array['where'] = 'none';
				endif;
			endif;
		else:
			$array['where'] = $specWhere;
		endif;
	endif;

	// Disable the buttons on Buddy Press pages
	if(function_exists('is_buddypress') && is_buddypress()):
		return $array['content'];

	// Disable the buttons if the location is set to "None / Manual"
	elseif($array['where'] == 'none' && !isset($array['devs'])):
		return $array['content'];

	// Disable the button if we're not in the loop, unless there is no content which means the function was called by a developer.
	elseif( (!is_main_query() || !in_the_loop()) && !isset($array['devs']) ):
		return $array['content'];

	// Don't do anything if we're in the admin section
	elseif( is_admin() ):
		return $array['content'];

	// If all the checks pass, let's make us some buttons!
	else:

		// Set the options for the horizontal floating bar
		if($options['float'] && is_singular()):
			$floatOption = 'float'.ucfirst($options['floatOption']);
		else:
			$floatOption = 'floatNone';
		endif;

		// Disable the plugin on feeds, search results, and non-published content
		if (!is_feed() && !is_search() && get_post_status($postID) == 'publish' ):

			// Acquire the social stats from the networks
			if(isset($array['url'])):
				$buttonsArray['url'] = $array['url'];
			else:
				$buttonsArray['url'] = get_permalink( $postID );
			endif;

			// Fetch the share counts
			$buttonsArray['shares'] = get_social_warfare_shares($postID);

			// Pass the sw_options into the array so we can pass it into the filter
			$buttonsArray['options'] = $options;

			// Customize which buttosn we're going to display
			if( isset ( $array['buttons'] ) ):
			
				// Fetch the global names and keys
				$sw_options = array();
				$sw_available_options = apply_filters('sw_options',$sw_options);
				$available_buttons = $sw_available_options['options']['displaySettings'];
				
				// Split the comma separated list into an array
				$button_set_array = explode(',', $array['buttons']);
				
				// Match the names in the list to their appropriate system-wide keys
				foreach($button_set_array as $button):
					if( recursive_array_search( $button , $available_buttons ) ) :
						$key = recursive_array_search( $button , $available_buttons );
						
						// Store the result in the array that gets passed to the HTML generator
						$buttonsArray['buttons'][$key] = $button;

						// Declare a default share count of zero. This will be overriden later
						if(!isset($buttonsArray['shares'][$key])):
							$buttonsArray['shares'][$key] = 0;
						endif;
						
					endif;
				endforeach;
				
				// Manually turn the total shares on or off
				if(array_search('Total',$button_set_array)) $buttonsArray['buttons']['totes'] = 'Total';
			endif;

			// Setup the buttons array to pass into the 'sw_network_buttons' hook
			$buttonsArray['count'] = 0;
			$buttonsArray['totes'] = 0;
			if( 	( $buttonsArray['options']['totes'] && $buttonsArray['shares']['totes'] >= $buttonsArray['options']['minTotes'] && !isset($array['buttons']) )
				|| 	( isset($buttonsArray['buttons']) && isset($buttonsArray['buttons']['totes']) && $buttonsArray['totes'] >= $options['minTotes'] ) ) :
				++$buttonsArray['count'];
			endif;
			$buttonsArray['resource'] = array();
			$buttonsArray['postID'] = $postID;

			// Disable the subtitles plugin to avoid letting them inject their subtitle into our share titles
			if ( is_plugin_active( 'subtitles/subtitles.php' ) && class_exists ( 'Subtitles' ) ) :
				remove_filter( 'the_title', array( Subtitles::getinstance() , 'the_subtitle' ), 10, 2 );
			endif;

			// This array will contain the HTML for all of the individual buttons
			$buttonsArray = apply_filters( 'sw_network_buttons' , $buttonsArray );

			// Create the social panel
			$assets = '<div class="nc_socialPanel sw_'.$options['visualTheme'].' sw_d_'.$options['dColorSet'].' sw_i_'.$options['iColorSet'].' sw_o_'.$options['oColorSet'].'" data-position="'.$options['locationPost'].'" data-float="'.$floatOption.'" data-count="'.$buttonsArray['count'].'" data-floatColor="'.$options['floatBgColor'].'" data-scale="'.$options['buttonSize'].'" data-align="'.$options['buttonFloat'].'">';

			// Setup the total shares count if it's on the left
			if( ( $options['totes'] && $options['swTotesFormat'] == 'totesAltLeft' && $buttonsArray['totes'] >= $options['minTotes'] && !isset($array['buttons'] ) ) 
			|| 	($options['swTotesFormat'] == 'totesAltLeft' && isset($array['buttons']) && isset($array['buttons']['totes']) && $buttonsArray['totes'] >= $options['minTotes'] ) ):
				++$buttonsArray['count'];
				$assets .= '<div class="nc_tweetContainer totes totesalt" data-id="'.$buttonsArray['count'].'" >';
				$assets .= '<span class="sw_count">'.kilomega($buttonsArray['totes']).' <span class="sw_label">'.__('Shares','social-warfare').'</span></span>';
				$assets .= '</div>';
			endif;

			// Sort the buttons according to the user's preferences
			if($options['orderOfIconsSelect'] == 'manual'):
				foreach($options['newOrderOfIcons'] as $thisIcon => $status):
					if(isset($buttonsArray['resource'][$thisIcon])):
						$assets .= $buttonsArray['resource'][$thisIcon];
					endif;
				endforeach;
			elseif($options['orderOfIconsSelect'] == 'dynamicCount'):
				arsort($buttonsArray['shares']);
				foreach($buttonsArray['shares'] as $thisIcon => $status):
					if(isset($buttonsArray['resource'][$thisIcon])):
						$assets .= $buttonsArray['resource'][$thisIcon];
					endif;
				endforeach;
			endif;

			// Create the Total Shares Box if it's on the right
			if( ( $options['totes'] && $options['swTotesFormat'] != 'totesAltLeft' && $buttonsArray['totes'] >= $options['minTotes'] && !isset($buttonsArray['buttons'] ) ) 
			|| 	( $options['swTotesFormat'] != 'totesAltLeft' && isset($buttonsArray['buttons']) && isset($buttonsArray['buttons']['totes']) && $buttonsArray['totes'] >= $options['minTotes'] ) ):
				++$buttonsArray['count'];
				if($options['swTotesFormat'] == 'totes'):
					$assets .= '<div class="nc_tweetContainer totes" data-id="'.$buttonsArray['count'].'" >';
					$assets .= '<span class="sw_count">'.kilomega($buttonsArray['totes']).' <span class="sw_label">'.__('Shares','social-warfare').'</span></span>';
					$assets .= '</div>';
				else:
					$assets .= '<div class="nc_tweetContainer totes totesalt" data-id="'.$buttonsArray['count'].'" >';
					$assets .= '<span class="sw_count"><span class="sw_label">'.__('Shares','social-warfare').'</span> '.kilomega($buttonsArray['totes']).'</span>';
					$assets .= '</div>';
				endif;
			endif;

			// Close the Social Panel
			$assets .= '</div>';

			// Reset the cache timestamp if needed
			if(sw_is_cache_fresh($postID) == false):
				delete_post_meta($postID,'sw_cache_timestamp');
				update_post_meta($postID,'sw_cache_timestamp',floor(((date('U')/60)/60)));
			endif;

			if($array['echo'] == false && $array['where'] != 'none'):
				return $assets;
			elseif($array['content'] === false):
				echo $assets;
			elseif($array['where']	== 'below'):
				$content = $array['content'].''.$assets;
				return $content;
			elseif($array['where'] 	== 'above'):
				$content = $assets.''.$array['content'];
				return $content;
			elseif($array['where'] 	== 'both'):
				$content = $assets.''.$array['content'].''.$assets;
				return $content;
			elseif($array['where']  	== 'none'):
				return $array['content'];
			endif;
		else:
			return $array['content'];
		endif;

	endif;
}
