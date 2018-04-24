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
	if(empty($options['single_custom_color']) ):
		$options['single_custom_color'] = $options['custom_color'];
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
		$swp_registration = is_swp_addon_registered('pro');
	else:
		$swp_registration = false;
	endif;

	if ( $admin || true === $swp_registration ) :
		if ( 'total_shares' === $options['totals_alignment'] ) :
			$options['totals_alignment'] = 'totals_right';
		endif;
	else:
		$options['twitter_cards']                  = false;
		$options['button_shape']                       = 'flat_fresh';
		$options['default_colors']                         = 'full_color';
		$options['single_colors']                         = 'full_color';
		$options['hover_colors']                         = 'full_color';
		$options['float_default_colors']                     = 'full_color';
		$options['float_single_colors']                     = 'full_color';
		$options['float_hover_colors']                     = 'full_color';
		$options['float_style_source']                  = true;
		$options['button_size']                        = 1;
		$options['button_alignment']                       = 'full_width';
		$options['ctt_theme']                          = 'style1';
		$options['ctt_css'] 							  = "";
		$options['twitter_shares']                    = false;
		$options['recover_shares']                    = false;
		$options['google_analtyics']                   = false;
		$options['bitly_authentication']                    = false;
		$options['minimum_shares']                          = 0;
		$options['swp_click_tracking']                = false;
		$options['order_of_icons']                = 'manual';
		$options['pinit_toggle']                      = false;
		$options['pinit_location_horizontal']         = 'center';
		$options['pinit_location_vertical']           = 'top';
		$options['emphasize_icons']                   = 0;
		$options['float_mobile']                   = 'off';
		$options['pin_browser_extension']          = false;
		$options['pinterest_image_location'] = 'hidden';
		$options['pinterest_fallback']       = 'all';
	endif;

	if(isset($options['order_of_icons']['active'])) {
		unset($options['order_of_icons']['active']);
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
	$options = get_option( 'social_warfare_settings', array() );

	$options[ $key ] = $value;

	return swp_update_options( $options );
}
