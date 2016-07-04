<?php
	$sw_user_options = get_option('socialWarfareOptions');
	function sw_get_user_options( $admin = false ) {
		
		// Fetch the global options array
		global $sw_user_options;

		// Reset the Order of Icons Options
		if (isset($sw_user_options['orderOfIcons'])):
		 	unset($sw_user_options['orderOfIcons']);
			update_option('socialWarfareOptions',$sw_user_options);
		endif;

		// Force the plugin off on certain post types
		$sw_user_options['locationattachment'] 		= 'none';
		$sw_user_options['locationrevision'] 		= 'none';
		$sw_user_options['nav_menu_item'] 			= 'none';
		$sw_user_options['shop_order'] 				= 'none';
		$sw_user_options['shop_order_refund'] 		= 'none';
		$sw_user_options['shop_coupon'] 			= 'none';
		$sw_user_options['shop_webhook'] 			= 'none';

		// If this is the admin page or if the plugin is registered
		if( $admin == true || is_sw_registered() == true):
			if(!isset($sw_user_options['locationSite'])) 				{ $sw_user_options['locationSite'] 		= 'both'; 	};
			if(!isset($sw_user_options['totes'])) 						{ $sw_user_options['totes'] 			= true; 	};
			if(!isset($sw_user_options['totesEach'])) 					{ $sw_user_options['totesEach'] 		= true; 	};
			if(!isset($sw_user_options['twitterID'])) 					{ $sw_user_options['twitterID'] 		= false; 	};
			if(!isset($sw_user_options['sw_twitter_card'])) 			{ $sw_user_options['sw_twitter_card'] 	= true; 	};
			if(!isset($sw_user_options['visualTheme'])) 				{ $sw_user_options['visualTheme'] 		= 'flatFresh'; };
			if(!isset($sw_user_options['dColorSet'])) 					{ $sw_user_options['dColorSet'] 		= 'fullColor';};
			if(!isset($sw_user_options['iColorSet'])) 					{ $sw_user_options['iColorSet'] 		= 'fullColor';};
			if(!isset($sw_user_options['oColorSet'])) 					{ $sw_user_options['oColorSet'] 		= 'fullColor';};
			if(!isset($sw_user_options['sideDColorSet'])) 				{ $sw_user_options['sideDColorSet'] 	= 'fullColor';};
			if(!isset($sw_user_options['sideIColorSet'])) 				{ $sw_user_options['sideIColorSet'] 	= 'fullColor';};
			if(!isset($sw_user_options['sideOColorSet'])) 				{ $sw_user_options['sideOColorSet'] 	= 'fullColor';};
			if(!isset($sw_user_options['floatStyleSource'])) 			{ $sw_user_options['floatStyleSource'] 	= true;};
			if(!isset($sw_user_options['buttonSize'])) 					{ $sw_user_options['buttonSize'] 		= 1;};
			if(!isset($sw_user_options['buttonFloat'])) 				{ $sw_user_options['buttonFloat'] 		= 'fullWidth';};
			if(!isset($sw_user_options['sideReveal'])) 					{ $sw_user_options['sideReveal'] 		= 'slide';};
			if(!isset($sw_user_options['sw_float_scr_sz'])) 			{ $sw_user_options['sw_float_scr_sz'] 	= 1100;};
			if(!isset($sw_user_options['cttTheme'])) 					{ $sw_user_options['cttTheme'] 			= 'style1'; };
			if(!isset($sw_user_options['twitter_shares'])) 				{ $sw_user_options['twitter_shares'] 	= false; };
			if(!isset($sw_user_options['float'])) 						{ $sw_user_options['float'] 			= true; 	};
			if(!isset($sw_user_options['floatOption'])) 				{ $sw_user_options['floatOption'] 		= 'bottom'; };
			if(!isset($sw_user_options['floatBgColor'])) 				{ $sw_user_options['floatBgColor'] 		= '#ffffff';};
			if(!isset($sw_user_options['floatStyle'])) 					{ $sw_user_options['floatStyle'] 		= 'default';};
			if(!isset($sw_user_options['customColor'])) 				{ $sw_user_options['customColor'] 		= '#000000';};
			if(!isset($sw_user_options['recover_shares'])) 				{ $sw_user_options['recover_shares'] 	= false;	};
			if(!isset($sw_user_options['recovery_format'])) 			{ $sw_user_options['recovery_format'] 	= 'unchanged';	};
			if(!isset($sw_user_options['recovery_protocol'])) 			{ $sw_user_options['recovery_protocol'] = 'unchanged';	};
			if(!isset($sw_user_options['recovery_prefix'])) 			{ $sw_user_options['recovery_prefix'] 	= 'unchanged';	};
			if(!isset($sw_user_options['swDecimals'])) 					{ $sw_user_options['swDecimals'] 		= 0;		};
			if(!isset($sw_user_options['sw_decimal_separator'])) 		{ $sw_user_options['sw_decimal_separator'] = 'period';};
			if(!isset($sw_user_options['swTotesFormat'])) 				{ $sw_user_options['swTotesFormat'] 	= 'totesalt';	};
			if($sw_user_options['swTotesFormat'] == 'totes')			{ $sw_user_options['swTotesFormat']		= 'totesalt';	};
			if(!isset($sw_user_options['googleAnalytics'])) 			{ $sw_user_options['googleAnalytics'] 	= false;	};
			if(!isset($sw_user_options['dashboardShares'])) 			{ $sw_user_options['dashboardShares'] 	= true;	};
			if(!isset($sw_user_options['linkShortening'])) 				{ $sw_user_options['linkShortening'] 	= false;	};
			if(!isset($sw_user_options['minTotes']))					{ $sw_user_options['minTotes']			= 0; };
			if(!isset($sw_user_options['cacheMethod']))					{ $sw_user_options['cacheMethod']		= 'advanced'; };
			if(!isset($sw_user_options['rawNumbers']))					{ $sw_user_options['rawNumbers']		= false; };
			if(!isset($sw_user_options['notShowing']))					{ $sw_user_options['notShowing']		= false; };
			if(!isset($sw_user_options['visualEditorBug']))				{ $sw_user_options['visualEditorBug']	= false; };
			if(!isset($sw_user_options['loopFix']))						{ $sw_user_options['loopFix']			= false; };
			if(!isset($sw_user_options['sniplyBuster']))				{ $sw_user_options['sniplyBuster']		= false; };
			if(!isset($sw_user_options['analyticsMedium']))				{ $sw_user_options['analyticsMedium']	= 'social'; };
			if(!isset($sw_user_options['analyticsCampaign']))			{ $sw_user_options['analyticsCampaign']	= 'SocialWarfare'; };
			if(!isset($sw_user_options['sw_click_tracking']))			{ $sw_user_options['sw_click_tracking']	= false; };
			if(!isset($sw_user_options['orderOfIconsSelect']))			{ $sw_user_options['orderOfIconsSelect']	= 'manual'; };
			if(!isset($sw_user_options['pinit_toggle']))				{ $sw_user_options['pinit_toggle']		= false; };
			if(!isset($sw_user_options['pinit_location_horizontal']))	{ $sw_user_options['pinit_location_horizontal']	= 'center'; };
			if(!isset($sw_user_options['pinit_location_vertical']))		{ $sw_user_options['pinit_location_vertical'] = 'top'; };
		
		// If it's not registered
		else:
			if(!isset($sw_user_options['locationSite'])) 				{ $sw_user_options['locationSite'] 		= 'both'; 	};
			if(!isset($sw_user_options['totes'])) 						{ $sw_user_options['totes'] 			= true; 	};
			if(!isset($sw_user_options['totesEach'])) 					{ $sw_user_options['totesEach'] 		= true; 	};
			if(!isset($sw_user_options['twitterID'])) 					{ $sw_user_options['twitterID'] 		= false; 	};
			$sw_user_options['sw_twitter_card'] = false;
			$sw_user_options['visualTheme'] = 'flatFresh';
			$sw_user_options['dColorSet'] 	= 'fullColor';
			$sw_user_options['iColorSet'] 	= 'fullColor';
			$sw_user_options['oColorSet'] 	= 'fullColor';
			$sw_user_options['sideDColorSet'] 	= 'fullColor';
			$sw_user_options['sideIColorSet'] 	= 'fullColor';
			$sw_user_options['sideOColorSet'] 	= 'fullColor';
			$sw_user_options['floatStyleSource'] = true;
			$sw_user_options['buttonSize'] = 1;
			$sw_user_options['buttonFloat'] = 'fullWidth';
			if(!isset($sw_user_options['sideReveal'])) 					{ $sw_user_options['sideReveal'] 		= 'slide';};
			if(!isset($sw_user_options['sw_float_scr_sz'])) 			{ $sw_user_options['sw_float_scr_sz'] 	= 1100;};
			$sw_user_options['cttTheme'] = 'style1';
			$sw_user_options['twitter_shares'] = false;
			if(!isset($sw_user_options['float'])) 						{ $sw_user_options['float'] 			= true; 	};
			if(!isset($sw_user_options['floatOption'])) 				{ $sw_user_options['floatOption'] 		= 'bottom'; };
			if(!isset($sw_user_options['floatBgColor'])) 				{ $sw_user_options['floatBgColor'] 		= '#ffffff';};
			if(!isset($sw_user_options['floatStyle'])) 					{ $sw_user_options['floatStyle'] 		= 'default';};
			if(!isset($sw_user_options['customColor'])) 				{ $sw_user_options['customColor'] 		= '#000000';};
			$sw_user_options['recover_shares'] = false;
			if(!isset($sw_user_options['recovery_format'])) 			{ $sw_user_options['recovery_format'] 	= 'unchanged';	};
			if(!isset($sw_user_options['recovery_protocol'])) 			{ $sw_user_options['recovery_protocol'] = 'unchanged';	};
			if(!isset($sw_user_options['recovery_prefix'])) 			{ $sw_user_options['recovery_prefix'] 	= 'unchanged';	};
			if(!isset($sw_user_options['swDecimals'])) 					{ $sw_user_options['swDecimals'] 		= 0;		};
			if(!isset($sw_user_options['sw_decimal_separator'])) 		{ $sw_user_options['sw_decimal_separator'] = 'period';};
			if(!isset($sw_user_options['swTotesFormat'])) 				{ $sw_user_options['swTotesFormat'] 	= 'totesalt';	};
			if($sw_user_options['swTotesFormat'] == 'totes')			{ $sw_user_options['swTotesFormat']		= 'totesalt';	};
			$sw_user_options['googleAnalytics'] = false;
			if(!isset($sw_user_options['dashboardShares'])) 			{ $sw_user_options['dashboardShares'] 	= true;	};
			$sw_user_options['linkShortening'] = false;
			$sw_user_options['minTotes'] = 0;
			if(!isset($sw_user_options['cacheMethod']))					{ $sw_user_options['cacheMethod']		= 'advanced'; };
			if(!isset($sw_user_options['rawNumbers']))					{ $sw_user_options['rawNumbers']		= false; };
			if(!isset($sw_user_options['notShowing']))					{ $sw_user_options['notShowing']		= false; };
			if(!isset($sw_user_options['visualEditorBug']))				{ $sw_user_options['visualEditorBug']	= false; };
			if(!isset($sw_user_options['loopFix']))						{ $sw_user_options['loopFix']			= false; };
			if(!isset($sw_user_options['sniplyBuster']))				{ $sw_user_options['sniplyBuster']		= false; };
			if(!isset($sw_user_options['analyticsMedium']))				{ $sw_user_options['analyticsMedium']	= 'social'; };
			if(!isset($sw_user_options['analyticsCampaign']))			{ $sw_user_options['analyticsCampaign']	= 'SocialWarfare'; };
			$sw_user_options['sw_click_tracking'] = false;
			$sw_user_options['orderOfIconsSelect'] = 'manual';
			$sw_user_options['pinit_toggle'] = false;
			$sw_user_options['pinit_location_horizontal'] = 'center';
			$sw_user_options['pinit_location_vertical'] = 'top';
		
		endif;
		
		if(!isset($sw_user_options['newOrderOfIcons'])):
			$sw_user_options['newOrderOfIcons']['active'] = array(
				"twitter" => "Twitter",
				"linkedIn" => "LinkedIn",
				"pinterest" => "Pinterest",
				"facebook" => "Facebook",
				"googlePlus" => "Google Plus"
			);
		elseif(isset($sw_user_options['newOrderOfIcons']) && is_sw_registered() == false):
			$sw_options_page = array(
				'tabs' => array(
					'links' => array(
					)
				),
				'options' => array()
			);
			$sw_options_page = apply_filters( 'sw_options' , $sw_options_page );
			foreach($sw_options_page['options']['sw_display']['buttons']['content'] as $key => $value):
				if(isset($sw_user_options['newOrderOfIcons'][$key]) && $value['premium'] == true):
					unset($sw_user_options['newOrderOfIcons'][$key]);
				endif;
			endforeach;
		endif;
		
		return $sw_user_options;
	}
	
	function sw_get_single_option($key) {
		$option = sw_get_user_options();
		return $option[$key];
	}
