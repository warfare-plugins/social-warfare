<?php

/*****************************************************************
*                                                                *
*          Side Fixed Floater Function				             *
*                                                                *
******************************************************************/

	function socialWarfareSideFloat() {

		$postID = get_the_ID();
		$options = sw_get_user_options();
		$postType = get_post_type($postID);
		
		if( is_singular('post') ):
			$visibility = $options['floatLocationPost'];
		elseif( is_singular('page') ):
			$visibility = $options['floatlocationPage'];
		elseif( is_singular() ):
			$postType = get_post_type($postID);
			if(isset($options['floatLocation'.$postType])):
				$visilibity = $options['floatLocation'.$postType];
			else:
				$visibility = 'on';
			endif;
		else:
			$visibility = 'on';
		endif;
		
		if( is_singular() && get_post_status($postID) == 'publish' && get_post_meta( $postID , 'nc_floatLocation' , true ) != 'off' && $visibility == 'on' && !is_home()):			
			
			// Get the options...or create them if they don't exist
			wp_reset_query();
			
			// Acquire the social stats from the networks
			// Acquire the social stats from the networks
			if(isset($array['url'])): 
				$buttonsArray['url'] = $array['url'];
			else: 
				$buttonsArray['url'] = get_permalink( $postID );
			endif;

			if($options['float'] && is_singular()):
				$floatOption = 'float'.ucfirst($options['floatOption']);
			else:
				$floatOption = 'floatNone';
			endif;

			$language = array();
			$language = apply_filters('sw_languages',$language);
			
			if($options['floatStyleSource'] == true):
				$options['sideDColorSet'] = $options['dColorSet'];
				$options['sideIColorSet'] = $options['iColorSet'];
				$options['sideOColorSet'] = $options['oColorSet'];
			endif;
			
			// Setup the buttons array to pass into the 'sw_network_buttons' hook
			$buttonsArray['shares'] = get_social_warfare_shares($postID);
			$buttonsArray['language'] = apply_filters( 'sw_languages' , $language );
			$buttonsArray['count'] = 0;
			$buttonsArray['totes'] = 0;
			$buttonsArray['options'] = $options;
			if( $buttonsArray['options']['totes'] && $buttonsArray['shares']['totes'] >= $buttonsArray['options']['minTotes'] ) ++$buttonsArray['count'];
			$buttonsArray['resource'] = array();
			$buttonsArray['postID'] = $postID;
			$buttonsArray = apply_filters( 'sw_network_buttons' , $buttonsArray );
			
			// Create the social panel
			$assets 		= '<div class="nc_socialPanelSide nc_socialPanel sw_'.$options['floatStyle'].' sw_d_'.$options['sideDColorSet'].' sw_i_'.$options['sideIColorSet'].' sw_o_'.$options['sideOColorSet'].'" data-position="'.$options['locationPost'].'" data-float="'.$floatOption.'" data-count="'.$buttonsArray['count'].'" data-floatColor="'.$options['floatBgColor'].'">';
			
			// Display Total Shares if the Threshold has been met
			if($options['totes'] && $buttonsArray['totes'] >= $options['minTotes']):
				$assets .= '<div class="nc_tweetContainer totes totesalt" data-id="6" >';
				$assets .= '<span class="sw_count">'.kilomega($buttonsArray['totes']).'</span><span class="sw_label"> '.$language['total'].'</span>'; 
				$assets .= '</div>';
			endif;
			
			$i = 0;
			if($options['orderOfIconsSelect'] == 'manual'):
				foreach($options['newOrderOfIcons'] as $thisIcon => $status):
					if(isset($buttonsArray['resource'][$thisIcon])):
						$assets .= $buttonsArray['resource'][$thisIcon];
						++$i;
					endif;
					if ($i == 5) break;
				endforeach;
			elseif($options['orderOfIconsSelect'] == 'dynamicCount'):
				arsort($buttonsArray['shares']);
				foreach($buttonsArray['shares'] as $thisIcon => $status):
					if(isset($buttonsArray['resource'][$thisIcon])):
						$assets .= $buttonsArray['resource'][$thisIcon];
						++$i;
					endif;
					if ($i == 5) break;
				endforeach;
			endif;
				
			// Close the Social Panel
			$assets .= '</div>';
			
			echo $assets;
			
		endif;
	}
