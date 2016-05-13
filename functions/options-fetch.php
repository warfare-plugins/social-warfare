<?php
	$sw_user_options = get_option('socialWarfareOptions');
	function sw_get_user_options() {
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

		// Set defaults for everthing
		if(!isset($sw_user_options['locationPost'])) 				{ $sw_user_options['locationPost'] 		= 'both'; 	};
		if(!isset($sw_user_options['locationPage'])) 				{ $sw_user_options['locationPage'] 		= 'both'; 	};
		if(!isset($sw_user_options['language'])) 					{ $sw_user_options['language'] 			= 'en'; 	};
		if(!isset($sw_user_options['locationSite'])) 				{ $sw_user_options['locationSite'] 		= 'both'; 	};

		// Default Buttons to Display
		if(!isset($sw_user_options['googlePlus'])) 					{ $sw_user_options['googlePlus'] 		= true; 	};
		if(!isset($sw_user_options['twitter'])) 					{ $sw_user_options['twitter'] 			= true; 	};
		if(!isset($sw_user_options['facebook'])) 					{ $sw_user_options['facebook'] 			= true; 	};
		if(!isset($sw_user_options['pinterest'])) 					{ $sw_user_options['pinterest'] 		= true; 	};
		if(!isset($sw_user_options['linkedIn'])) 					{ $sw_user_options['linkedIn'] 			= true; 	};
		if(!isset($sw_user_options['yummly'])) 						{ $sw_user_options['yummly'] 			= false; 	};
		if(!isset($sw_user_options['email'])) 						{ $sw_user_options['email'] 			= false; 	};
		if(!isset($sw_user_options['whatsapp'])) 					{ $sw_user_options['whatsapp'] 			= false; 	};
		if(!isset($sw_user_options['tumblr'])) 						{ $sw_user_options['tumblr'] 			= false; 	};
		if(!isset($sw_user_options['reddit'])) 						{ $sw_user_options['reddit'] 			= false; 	};
		if(!isset($sw_user_options['stumbleupon'])) 				{ $sw_user_options['stumbleupon'] 		= false; 	};
		if(!isset($sw_user_options['pocket'])) 						{ $sw_user_options['pocket'] 			= false; 	};
		if(!isset($sw_user_options['buffer'])) 						{ $sw_user_options['buffer'] 			= false; 	};

		if(!isset($sw_user_options['totes'])) 						{ $sw_user_options['totes'] 			= true; 	};
		if(!isset($sw_user_options['totesEach'])) 					{ $sw_user_options['totesEach'] 		= true; 	};
		if(!isset($sw_user_options['twitterID'])) 					{ $sw_user_options['twitterID'] 		= false; 	};
		if(!isset($sw_user_options['sw_twitter_card'])) 			{ $sw_user_options['sw_twitter_card'] 	= true; 	};
		if(!isset($sw_user_options['visualTheme'])) 				{ $sw_user_options['visualTheme'] 		= 'style1'; };
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
		if(!isset($sw_user_options['shorteningMethod'])) 			{ $sw_user_options['shorteningMethod'] 	= 'warfareLinks';	};
		if(!isset($sw_user_options['minTotes']))					{ $sw_user_options['minTotes']			= 0; };
		if(!isset($sw_user_options['cacheMethod']))					{ $sw_user_options['cacheMethod']		= 'advanced'; };
		if(!isset($sw_user_options['rawNumbers']))					{ $sw_user_options['rawNumbers']		= false; };
		if(!isset($sw_user_options['notShowing']))					{ $sw_user_options['notShowing']		= false; };
		if(!isset($sw_user_options['visualEditorBug']))				{ $sw_user_options['visualEditorBug']	= false; };
		if(!isset($sw_user_options['loopFix']))						{ $sw_user_options['loopFix']			= false; };
		if(!isset($sw_user_options['sniplyBuster']))				{ $sw_user_options['sniplyBuster']		= false; };
		if(!isset($sw_user_options['googleAnalytics']))				{ $sw_user_options['googleAnalytics']	= true; };
		if(!isset($sw_user_options['analyticsMedium']))				{ $sw_user_options['analyticsMedium']	= 'social'; };
		if(!isset($sw_user_options['analyticsCampaign']))			{ $sw_user_options['analyticsCampaign']	= 'SocialWarfare'; };
		if(!isset($sw_user_options['sw_click_tracking']))			{ $sw_user_options['sw_click_tracking']	= false; };
		if(!isset($sw_user_options['orderOfIconsSelect']))			{ $sw_user_options['orderOfIconsSelect']	= 'manual'; };
		if(!isset($sw_user_options['newOrderOfIcons'])) 			{ $sw_user_options['newOrderOfIcons'] 		= array(
				"twitter" => "Twitter",
				"linkedIn" => "LinkedIn",
				"pinterest" => "Pinterest",
				"facebook" => "Facebook",
				"googlePlus" => "Google Plus"
			);
		};
		return $sw_user_options;
	}
	function sw_get_single_option($key) {
		$option = sw_get_user_options();
		return $option[$key];
	}
