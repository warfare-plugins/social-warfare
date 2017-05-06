<?php
/**
 * Functions for getting and setting the plugin's options.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2017, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

defined( 'WPINC' ) || die;

// Set the global options variable
global $swp_user_options;

/**
 * $swp_user_options Fetch the available options that the user has set
 * @var array An array of available options from the options page
 */
$swp_user_options = swp_get_user_options( is_admin() );

/**
 * A function to adjust the options and ensure that defaults are set
 *
 * @param  boolean $admin A boolean value to determine if it's being called in the admin or elsewhere
 * @return array $options The modified options array
 */
function swp_get_user_options( $admin = false ) {
	$options = get_option( 'socialWarfareOptions', array() );

	$defaults = array(
		'locationSite'                      => 'below',
		'locationHome'				        => 'none',
		'location_post'				        => 'below',
		'location_page'				        => 'below',
		'totes'                             => true,
		'totesEach'                         => true,
		'twitterID'                         => false,
		'swp_twitter_card'                  => true,
		'visualTheme'                       => 'flatFresh',
		'dColorSet'                         => 'fullColor',
		'iColorSet'                         => 'fullColor',
		'oColorSet'                         => 'fullColor',
		'sideDColorSet'                     => 'fullColor',
		'sideIColorSet'                     => 'fullColor',
		'sideOColorSet'                     => 'fullColor',
		'floatStyleSource'                  => true,
		'buttonSize'                        => 1,
		'buttonFloat'                       => 'fullWidth',
		'sideReveal'                        => 'slide',
		'swp_float_scr_sz'                  => 1100,
		'cttTheme'                          => 'style1',
		'twitter_shares'                    => false,
		'float'                             => true,
		'floatOption'                       => 'bottom',
		'floatBgColor'                      => '#ffffff',
		'floatStyle'                        => 'default',
		'customColor'                       => '#000000',
		'recover_shares'                    => false,
		'recovery_format'                   => 'unchanged',
		'recovery_protocol'                 => 'unchanged',
		'recovery_prefix'                   => 'unchanged',
		'swDecimals'                        => 0,
		'swp_decimal_separator'             => 'period',
		'swTotesFormat'                     => 'totesalt',
		'googleAnalytics'                   => false,
		'dashboardShares'                   => true,
		'linkShortening'                    => false,
		'minTotes'                          => 0,
		'cacheMethod'                       => 'advanced',
		'full_content'				        => false,
		'rawNumbers'                        => false,
		'notShowing'                        => false,
		'visualEditorBug'                   => false,
		'loopFix'                           => false,
		'sniplyBuster'                      => false,
		'analyticsMedium'                   => 'social',
		'analyticsCampaign'                 => 'SocialWarfare',
		'swp_click_tracking'                => false,
		'orderOfIconsSelect'                => 'manual',
		'pinit_toggle'                      => false,
		'pinit_location_horizontal'         => 'center',
		'pinit_location_vertical'           => 'top',
		'pinit_min_width'                   => '200',
		'pinit_min_height'                  => '200',
		'advanced_pinterest_image'          => false,
		'advanced_pinterest_image_location' => 'hidden',
		'advanced_pinterest_fallback'       => 'all',
		'emphasize_icons'                   => 0,
		'floatLeftMobile'                   => 'bottom',
		'newOrderOfIcons' => array(
			'active' => array(
				'twitter'    => 'Twitter',
				'linkedIn'   => 'LinkedIn',
				'pinterest'  => 'Pinterest',
				'facebook'   => 'Facebook',
				'googlePlus' => 'Google Plus',
			),
		),
	);

	/**
	 * Set the default og:type values for each post type
	 *
	 */
	$swp_post_types = swp_get_post_types();
	if(!empty($swp_post_types)):
		foreach($swp_post_types as $swp_post_type):
			$defaults['swp_og_type_'.$swp_post_type] = 'article';
		endforeach;
	endif;

	$options = array_merge( $defaults, $options );

	// Make the side custom absorb the main custom color if they haven't set one yet.
	if(empty($options['sideCustomColor']) ):
		$options['sideCustomColor'] = $options['customColor'];
	endif;

	// Force the plugin off on certain post types.
	$options['locationattachment'] = 'none';
	$options['locationrevision']   = 'none';
	$options['nav_menu_item']      = 'none';
	$options['shop_order']         = 'none';
	$options['shop_order_refund']  = 'none';
	$options['shop_coupon']        = 'none';
	$options['shop_webhook']       = 'none';

	if( function_exists('is_swp_registered') ):
		$swp_registration = is_swp_registered();
	else:
		$swp_registration = false;
	endif;

	if ( $admin || true === $swp_registration ) :
		if ( 'totes' === $options['swTotesFormat'] ) :
			$options['swTotesFormat'] = 'totesalt';
		endif;
	else:
		$options['swp_twitter_card']                  = false;
		$options['visualTheme']                       = 'flatFresh';
		$options['dColorSet']                         = 'fullColor';
		$options['iColorSet']                         = 'fullColor';
		$options['oColorSet']                         = 'fullColor';
		$options['sideDColorSet']                     = 'fullColor';
		$options['sideIColorSet']                     = 'fullColor';
		$options['sideOColorSet']                     = 'fullColor';
		$options['floatStyleSource']                  = true;
		$options['buttonSize']                        = 1;
		$options['buttonFloat']                       = 'fullWidth';
		$options['cttTheme']                          = 'style1';
		$options['twitter_shares']                    = false;
		$options['recover_shares']                    = false;
		$options['googleAnalytics']                   = false;
		$options['linkShortening']                    = false;
		$options['minTotes']                          = 0;
		$options['swp_click_tracking']                = false;
		$options['orderOfIconsSelect']                = 'manual';
		$options['pinit_toggle']                      = false;
		$options['pinit_location_horizontal']         = 'center';
		$options['pinit_location_vertical']           = 'top';
		$options['emphasize_icons']                   = 0;
		$options['floatLeftMobile']                   = 'off';
		$options['advanced_pinterest_image']          = false;
		$options['advanced_pinterest_image_location'] = 'hidden';
		$options['advanced_pinterest_fallback']       = 'all';
	endif;

	if(isset($options['newOrderOfIcons']['active'])) {
		unset($options['newOrderOfIcons']['active']);
	}

	/**
	 * Unset any buttons that may have been put into the options but are no longer actually available
	 *
	 */
	$icons_array = array(
		'type'		=> 'buttons'
	);
	$icons_array = apply_filters( 'swp_button_options' , $icons_array );
	foreach($options['newOrderOfIcons'] as $icon):
		if(!isset($icons_array['content'][$icon])):
			unset($options['newOrderOfIcons'][$icon]);
		endif;
	endforeach;

	return $options;
}

/**
 * Fetch a single option
 *
 * @since  unknown
 * @param  string $key The key to pull from the array of options.
 * @return mixed $options The value of the desired option
 */
function swp_get_single_option( $key ) {
	global $swp_user_options;

	if ( isset( $swp_user_options[ $key ] ) ) {
		return $swp_user_options[ $key ];
	}

	return false;
}

/**
 * Update the main plugin options.
 *
 * @since  2.1.0
 * @param  array $options The option values to be set.
 * @return bool True if the option has been updated.
 */
function swp_update_options( $options ) {
	if ( ! is_array( $options ) ) {
		return false;
	}

	unset( $options['orderOfIcons'] );

	return update_option( 'socialWarfareOptions', $options );
}

/**
 * Update a single option.
 *
 * @since  2.1.0
 * @param  string $key The key to set in the array of options.
 * @param  mixed  $value The option value to be set.
 * @return bool True if the option has been updated.
 */
function swp_update_option( $key, $value ) {
	$options = get_option( 'socialWarfareOptions', array() );

	$options[ $key ] = $value;

	return swp_update_options( $options );
}
