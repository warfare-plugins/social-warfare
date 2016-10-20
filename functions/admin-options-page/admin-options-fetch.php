<?php

/**
 * $swp_user_options Fetch the available options that the user has set
 * @var array An array of available options from the options page
 */
$swp_user_options = get_option( 'socialWarfareOptions' );

/**
 * swp_get_user_options A function to adjust the options and ensure that defaults are set
 * @param  boolean $admin A boolean value to determine if it's being called in the admin or elsewhere
 * @return array $swp_user_options The modified options array
 */
function swp_get_user_options( $admin = false ) {

	// Fetch the global options array
	global $swp_user_options;

	// Reset the Order of Icons Options
	if ( isset( $swp_user_options['orderOfIcons'] ) ) :
		unset( $swp_user_options['orderOfIcons'] );
		update_option( 'socialWarfareOptions',$swp_user_options );
	endif;

	// Force the plugin off on certain post types
	$swp_user_options['locationattachment'] 		= 'none';
	$swp_user_options['locationrevision'] 			= 'none';
	$swp_user_options['nav_menu_item'] 				= 'none';
	$swp_user_options['shop_order'] 				= 'none';
	$swp_user_options['shop_order_refund'] 			= 'none';
	$swp_user_options['shop_coupon'] 				= 'none';
	$swp_user_options['shop_webhook'] 				= 'none';

	// If this is the admin page or if the plugin is registered
	if ( $admin == true || is_swp_registered() == true ) :
		if ( ! isset( $swp_user_options['locationSite'] ) ) { $swp_user_options['locationSite'] 		= 'both';
		};
		if ( ! isset( $swp_user_options['totes'] ) ) { $swp_user_options['totes'] 				= true;
		};
		if ( ! isset( $swp_user_options['totesEach'] ) ) { $swp_user_options['totesEach'] 			= true;
		};
		if ( ! isset( $swp_user_options['twitterID'] ) ) { $swp_user_options['twitterID'] 			= false;
		};
		if ( ! isset( $swp_user_options['swp_twitter_card'] ) ) { $swp_user_options['swp_twitter_card'] 	= true;
		};
		if ( ! isset( $swp_user_options['visualTheme'] ) ) { $swp_user_options['visualTheme'] 			= 'flatFresh';
		};
		if ( ! isset( $swp_user_options['dColorSet'] ) ) { $swp_user_options['dColorSet'] 			= 'fullColor';
		};
		if ( ! isset( $swp_user_options['iColorSet'] ) ) { $swp_user_options['iColorSet'] 			= 'fullColor';
		};
		if ( ! isset( $swp_user_options['oColorSet'] ) ) { $swp_user_options['oColorSet'] 			= 'fullColor';
		};
		if ( ! isset( $swp_user_options['sideDColorSet'] ) ) { $swp_user_options['sideDColorSet'] 		= 'fullColor';
		};
		if ( ! isset( $swp_user_options['sideIColorSet'] ) ) { $swp_user_options['sideIColorSet'] 		= 'fullColor';
		};
		if ( ! isset( $swp_user_options['sideOColorSet'] ) ) { $swp_user_options['sideOColorSet'] 		= 'fullColor';
		};
		if ( ! isset( $swp_user_options['floatStyleSource'] ) ) { $swp_user_options['floatStyleSource'] 	= true;
		};
		if ( ! isset( $swp_user_options['buttonSize'] ) ) { $swp_user_options['buttonSize'] 			= 1;
		};
		if ( ! isset( $swp_user_options['buttonFloat'] ) ) { $swp_user_options['buttonFloat'] 			= 'fullWidth';
		};
		if ( ! isset( $swp_user_options['sideReveal'] ) ) { $swp_user_options['sideReveal'] 			= 'slide';
		};
		if ( ! isset( $swp_user_options['swp_float_scr_sz'] ) ) { $swp_user_options['swp_float_scr_sz'] 	= 1100;
		};
		if ( ! isset( $swp_user_options['cttTheme'] ) ) { $swp_user_options['cttTheme'] 			= 'style1';
		};
		if ( ! isset( $swp_user_options['twitter_shares'] ) ) { $swp_user_options['twitter_shares'] 		= false;
		};
		if ( ! isset( $swp_user_options['float'] ) ) { $swp_user_options['float'] 				= true;
		};
		if ( ! isset( $swp_user_options['floatOption'] ) ) { $swp_user_options['floatOption'] 			= 'bottom';
		};
		if ( ! isset( $swp_user_options['floatBgColor'] ) ) { $swp_user_options['floatBgColor'] 		= '#ffffff';
		};
		if ( ! isset( $swp_user_options['floatStyle'] ) ) { $swp_user_options['floatStyle'] 			= 'default';
		};
		if ( ! isset( $swp_user_options['customColor'] ) ) { $swp_user_options['customColor'] 			= '#000000';
		};
		if ( ! isset( $swp_user_options['recover_shares'] ) ) { $swp_user_options['recover_shares'] 		= false;
		};
		if ( ! isset( $swp_user_options['recovery_format'] ) ) { $swp_user_options['recovery_format'] 		= 'unchanged';
		};
		if ( ! isset( $swp_user_options['recovery_protocol'] ) ) { $swp_user_options['recovery_protocol'] 	= 'unchanged';
		};
		if ( ! isset( $swp_user_options['recovery_prefix'] ) ) { $swp_user_options['recovery_prefix'] 		= 'unchanged';
		};
		if ( ! isset( $swp_user_options['swDecimals'] ) ) { $swp_user_options['swDecimals'] 			= 0;
		};
		if ( ! isset( $swp_user_options['swp_decimal_separator'] ) ) { $swp_user_options['swp_decimal_separator'] = 'period';
		};
		if ( ! isset( $swp_user_options['swTotesFormat'] ) ) { $swp_user_options['swTotesFormat'] 		= 'totesalt';
		};
		if ( $swp_user_options['swTotesFormat'] == 'totes' ) { $swp_user_options['swTotesFormat']		= 'totesalt';
		};
		if ( ! isset( $swp_user_options['googleAnalytics'] ) ) { $swp_user_options['googleAnalytics'] 		= false;
		};
		if ( ! isset( $swp_user_options['dashboardShares'] ) ) { $swp_user_options['dashboardShares'] 		= true;
		};
		if ( ! isset( $swp_user_options['linkShortening'] ) ) { $swp_user_options['linkShortening'] 		= false;
		};
		if ( ! isset( $swp_user_options['minTotes'] ) ) { $swp_user_options['minTotes']				= 0;
		};
		if ( ! isset( $swp_user_options['cacheMethod'] ) ) { $swp_user_options['cacheMethod']			= 'advanced';
		};
		if ( ! isset( $swp_user_options['rawNumbers'] ) ) { $swp_user_options['rawNumbers']			= false;
		};
		if ( ! isset( $swp_user_options['notShowing'] ) ) { $swp_user_options['notShowing']			= false;
		};
		if ( ! isset( $swp_user_options['visualEditorBug'] ) ) { $swp_user_options['visualEditorBug']		= false;
		};
		if ( ! isset( $swp_user_options['loopFix'] ) ) { $swp_user_options['loopFix']				= false;
		};
		if ( ! isset( $swp_user_options['sniplyBuster'] ) ) { $swp_user_options['sniplyBuster']			= false;
		};
		if ( ! isset( $swp_user_options['analyticsMedium'] ) ) { $swp_user_options['analyticsMedium']		= 'social';
		};
		if ( ! isset( $swp_user_options['analyticsCampaign'] ) ) { $swp_user_options['analyticsCampaign']	= 'SocialWarfare';
		};
		if ( ! isset( $swp_user_options['swp_click_tracking'] ) ) { $swp_user_options['swp_click_tracking']	= false;
		};
		if ( ! isset( $swp_user_options['orderOfIconsSelect'] ) ) { $swp_user_options['orderOfIconsSelect']	= 'manual';
		};
		if ( ! isset( $swp_user_options['pinit_toggle'] ) ) { $swp_user_options['pinit_toggle']			= false;
		};
		if ( ! isset( $swp_user_options['pinit_location_horizontal'] ) ) { $swp_user_options['pinit_location_horizontal'] = 'center';
		};
		if ( ! isset( $swp_user_options['pinit_location_vertical'] ) ) { $swp_user_options['pinit_location_vertical'] = 'top';
		};
		if ( ! isset( $swp_user_options['pinit_min_width'] ) ) { $swp_user_options['pinit_min_width'] 		= '200';
		};
		if ( ! isset( $swp_user_options['pinit_min_height'] ) ) { $swp_user_options['pinit_min_height'] 	= '200';
		};
		if ( ! isset( $swp_user_options['emphasize_icons'] ) ) { $swp_user_options['emphasize_icons'] 		= 0;
		};
		if ( ! isset( $swp_user_options['floatLeftMobile'] ) ) { $swp_user_options['floatLeftMobile'] 		= 'bottom';
		};

		// If it's not registered
		else :
			if ( ! isset( $swp_user_options['locationSite'] ) ) { $swp_user_options['locationSite'] 		= 'both';
			};
			if ( ! isset( $swp_user_options['totes'] ) ) { $swp_user_options['totes'] 				= true;
			};
			if ( ! isset( $swp_user_options['totesEach'] ) ) { $swp_user_options['totesEach'] 			= true;
			};
			if ( ! isset( $swp_user_options['twitterID'] ) ) { $swp_user_options['twitterID'] 			= false;
			};
			$swp_user_options['swp_twitter_card'] = false;
			$swp_user_options['visualTheme'] = 'flatFresh';
			$swp_user_options['dColorSet'] 	= 'fullColor';
			$swp_user_options['iColorSet'] 	= 'fullColor';
			$swp_user_options['oColorSet'] 	= 'fullColor';
			$swp_user_options['sideDColorSet'] 	= 'fullColor';
			$swp_user_options['sideIColorSet'] 	= 'fullColor';
			$swp_user_options['sideOColorSet'] 	= 'fullColor';
			$swp_user_options['floatStyleSource'] = true;
			$swp_user_options['buttonSize'] = 1;
			$swp_user_options['buttonFloat'] = 'fullWidth';
			if ( ! isset( $swp_user_options['sideReveal'] ) ) { $swp_user_options['sideReveal'] 			= 'slide';
			};
			if ( ! isset( $swp_user_options['swp_float_scr_sz'] ) ) { $swp_user_options['swp_float_scr_sz'] 	= 1100;
			};
			$swp_user_options['cttTheme'] = 'style1';
			$swp_user_options['twitter_shares'] = false;
			if ( ! isset( $swp_user_options['float'] ) ) { $swp_user_options['float'] 				= true;
			};
			if ( ! isset( $swp_user_options['floatOption'] ) ) { $swp_user_options['floatOption'] 			= 'bottom';
			};
			if ( ! isset( $swp_user_options['floatBgColor'] ) ) { $swp_user_options['floatBgColor'] 		= '#ffffff';
			};
			if ( ! isset( $swp_user_options['floatStyle'] ) ) { $swp_user_options['floatStyle'] 			= 'default';
			};
			if ( ! isset( $swp_user_options['customColor'] ) ) { $swp_user_options['customColor'] 			= '#000000';
			};
			$swp_user_options['recover_shares'] = false;
			if ( ! isset( $swp_user_options['recovery_format'] ) ) { $swp_user_options['recovery_format'] 		= 'unchanged';
			};
			if ( ! isset( $swp_user_options['recovery_protocol'] ) ) { $swp_user_options['recovery_protocol'] 	= 'unchanged';
			};
			if ( ! isset( $swp_user_options['recovery_prefix'] ) ) { $swp_user_options['recovery_prefix'] 		= 'unchanged';
			};
			if ( ! isset( $swp_user_options['swDecimals'] ) ) { $swp_user_options['swDecimals'] 			= 0;
			};
			if ( ! isset( $swp_user_options['swp_decimal_separator'] ) ) { $swp_user_options['swp_decimal_separator'] = 'period';
			};
			if ( ! isset( $swp_user_options['swTotesFormat'] ) ) { $swp_user_options['swTotesFormat'] 		= 'totesalt';
			};
			if ( $swp_user_options['swTotesFormat'] == 'totes' ) { $swp_user_options['swTotesFormat']		= 'totesalt';
			};
			$swp_user_options['googleAnalytics'] = false;
			if ( ! isset( $swp_user_options['dashboardShares'] ) ) { $swp_user_options['dashboardShares'] 		= true;
			};
			$swp_user_options['linkShortening'] = false;
			$swp_user_options['minTotes'] = 0;
			if ( ! isset( $swp_user_options['cacheMethod'] ) ) { $swp_user_options['cacheMethod']			= 'advanced';
			};
			if ( ! isset( $swp_user_options['rawNumbers'] ) ) { $swp_user_options['rawNumbers']			= false;
			};
			if ( ! isset( $swp_user_options['notShowing'] ) ) { $swp_user_options['notShowing']			= false;
			};
			if ( ! isset( $swp_user_options['visualEditorBug'] ) ) { $swp_user_options['visualEditorBug']		= false;
			};
			if ( ! isset( $swp_user_options['loopFix'] ) ) { $swp_user_options['loopFix']				= false;
			};
			if ( ! isset( $swp_user_options['sniplyBuster'] ) ) { $swp_user_options['sniplyBuster']			= false;
			};
			if ( ! isset( $swp_user_options['analyticsMedium'] ) ) { $swp_user_options['analyticsMedium']		= 'social';
			};
			if ( ! isset( $swp_user_options['analyticsCampaign'] ) ) { $swp_user_options['analyticsCampaign']	= 'SocialWarfare';
			};
			$swp_user_options['swp_click_tracking'] = false;
			$swp_user_options['orderOfIconsSelect'] = 'manual';
			$swp_user_options['pinit_toggle'] = false;
			$swp_user_options['pinit_location_horizontal'] = 'center';
			$swp_user_options['pinit_location_vertical'] = 'top';
			$swp_user_options['emphasize_icons'] = 0;
			$swp_user_options['floatLeftMobile'] = 'off';

		endif;

		if ( ! isset( $swp_user_options['newOrderOfIcons'] ) ) :
			$swp_user_options['newOrderOfIcons']['active'] = array(
				'twitter' => 'Twitter',
				'linkedIn' => 'LinkedIn',
				'pinterest' => 'Pinterest',
				'facebook' => 'Facebook',
				'googlePlus' => 'Google Plus',
			);
		elseif ( isset( $swp_user_options['newOrderOfIcons'] ) && is_swp_registered() == false ) :
			$swp_options_page = array(
				'tabs' => array(
					'links' => array(),
				),
				'options' => array(),
			);
			$swp_options_page = apply_filters( 'swp_options' , $swp_options_page );
			foreach ( $swp_options_page['options']['swp_display']['buttons']['content'] as $key => $value ) :
				if ( isset( $swp_user_options['newOrderOfIcons'][ $key ] ) && $value['premium'] == true ) :
					unset( $swp_user_options['newOrderOfIcons'][ $key ] );
				endif;
			endforeach;
		endif;

		return $swp_user_options;
}
/**
 * swp_get_single_option A function for fetching a single option
 * @param  string $key 		The key to pull from the array of options
 * @return mixed $options 	The value of the desired option
 */
function swp_get_single_option( $key ) {
	$option = swp_get_user_options();
	return $option[ $key ];
}
