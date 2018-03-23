<?php
/**
 * Functions for getting and setting the plugin's options.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
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
	$options = get_option( 'social_warfare_settings', array() );
	return $options;


	if(isset($options['activate_tweet_counts']) && true == $options['activate_tweet_counts'] && !isset($options['tweet_count_source']) ) {
		$options['tweet_count_source'] = 'newsharecounts';
	}

	/**
	 * Set the default og:type values for each post type
	 *
	 */
	$swp_post_types = swp_get_post_types();

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
		$options['cttCSS'] 							  = "";
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

	return update_option( 'social_warfare_settings', $options );
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
