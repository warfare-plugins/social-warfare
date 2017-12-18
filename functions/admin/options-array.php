<?php

/**
 * An Array of options to pass over to the option page
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2017, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

defined( 'WPINC' ) || die;

/**
 * swp_options_display An array of options for the display tab of the options page
 * @since 	2.0.0
 * @param  	array $swp_options The array of options
 * @return 	array $swp_options The modified array of options
 */
function swp_options_display($swp_options) {

	$icons_array = array(
		'type'		=> 'buttons'
	);
	$icons_array = apply_filters( 'swp_button_options' , $icons_array );

	// Declare the Options Tab and Tab Name
	$swp_options['tabs']['links']['swp_display'] = __( 'Display' ,'social-warfare' );

	// Declare the content that goes on this options page
	$swp_options['options']['swp_display'] = array(
		'social_networks_title' => array(
			'type' 		=> 'title',
			'content' 	=> __( 'Social Networks' ,'social-warfare' )
		),
		'social_networks_subtitle' => array(
			'type'		=> 'paragraph',
			'content'	=> __( 'Drag & Drop to activate and order your share buttons.' ,'social-warfare' )
		),
		'buttons' => $icons_array,
		'buttons_divider' => array(
			'type'		=> 'divider'
		),
/*
		'emphasize_icons' => array(
			'type'		=> 'select',
			'size'		=> 'two-thirds',
			'content'	=> array(
				'0' 	=> __( 'Don\'t Emphasize Any Buttons' ,'social-warfare' ),
				'1' 	=> __( 'Emphasize the First Button' ,'social-warfare' ),
				'2' 	=> __( 'Emphasize the First Two Buttons' ,'social-warfare' )
			),
			'default'	=> '0',
			'name'		=> __( 'Emphasize Buttons' ,'social-warfare' ),
			'divider'	=> true,
			'premium'	=> true
		),
*/
		'share_counts_title' => array(
			'type' 		=> 'title',
			'content' 	=> __( 'Share Counts' ,'social-warfare' )
		),
		'share_counts_subtitle' => array(
			'type'		=> 'paragraph',
			'content'	=> __( 'Use the toggles below to determine how to display your social proof.' ,'social-warfare' )
		),
		'totesEach' => array(
			'type'		=> 'checkbox',
			'size'		=> 'two-thirds',
			'content'	=> __( 'Button Counts' ,'social-warfare' ),
			'default'	=> true,
			'premium'	=> false
		),
		'totes' => array(
			'type'		=> 'checkbox',
			'size'		=> 'two-thirds',
			'content'	=> __( 'Total Counts' ,'social-warfare' ),
			'default'	=> true,
			'premium'	=> false
		),
		'totes_divider' => array(
			'type'		=> 'divider'
		),
		'position_title' => array(
			'type' 		=> 'title',
			'content' 	=> __( 'Position Share Buttons' ,'social-warfare' )
		),
		'position_subtitle' => array(
			'type'		=> 'paragraph',
			'content'	=> __( 'The options below will allow you to customize the positioning of share buttons for each post type.' ,'social-warfare' )
		),
		'location_column_labels' => array(
			'type'		=> 'column_labels',
			'columns'	=> 3,
			'column_1'	=> __( 'Post Type' ,'social-warfare' ),
			'column_2'	=> __( 'Static Buttons' ,'social-warfare' ),
			'column_3'	=> __( 'Floating Buttons (If Activated)' ,'social-warfare' )
		)
	);

	// Create the content locations
	$contentLocations = array(
		'above'=> __( 'Above the Content' ,'social-warfare' ),
		'below' => __( 'Below the Content' ,'social-warfare' ),
		'both' => __( 'Both Above and Below the Content' ,'social-warfare' ),
		'none' => __( 'None/Manual Placement' ,'social-warfare' )
	);

	// Get the public post Types
	$postTypes = swp_get_post_types();

	if(!empty($postTypes)):

		// Loop through the Custom Post Type Options
		foreach($postTypes as $postType):
			$swp_options['options']['swp_display']['location_'.$postType] = array(
				'type'		=> 'select',
				'name'		=> ucfirst($postType),
				'primary'	=> 'location_'.$postType,
				'secondary' => 'float_location_'.$postType,
				'content'	=> $contentLocations,
				'content_2'	=> array(
					'on'	=> __( 'On' ,'social-warfare' ),
					'off'	=> __( 'Off' ,'social-warfare' ),
				),
				'default'	=> 'both',
				'default_2' => 'on'
			);
		endforeach;
	endif;

	$swp_options['options']['swp_display']['locationHome'] = array(
		'type'		=> 'select',
		'name'		=> __( 'Home Page' ,'social-warfare' ),
		'content'	=> $contentLocations,
		'default'	=> 'none',
		'size'		=> 'two-thirds'
	);

	$swp_options['options']['swp_display']['locationSite'] = array(
		'type'		=> 'select',
		'name'		=> __( 'Archive & Categories' ,'social-warfare' ),
		'content'	=> $contentLocations,
		'default'	=> 'below',
		'size'		=> 'two-thirds'
	);

	return $swp_options;
}

/**
 * swp_options_styles An array of options for the styles tab of the options page
 * @since 	2.0.0
 * @param  	array $swp_options The array of options
 * @return 	array $swp_options The modified array of options
 */
function swp_options_styles($swp_options) {

	// Declare the Display Settings tab and tab name
	$swp_options['tabs']['links']['swp_styles'] = __( 'Styles' ,'social-warfare' );

	$swp_options['options']['swp_styles'] = array(

		'total_counts_title' => array(
			'type'	=> 'title',
			'content' => __( 'Total Counts' ,'social-warfare' )
		),
		'total_counts_description' => array(
			'type' 		=> 'paragraph',
			'content' 	=> __( 'Customize how the "Total Shares" section of your share buttons look.' ,'social-warfare' )
		),
		'swDecimals' => array(
			'type' => 'select',
			'name' => __( 'Decimal Places' ,'social-warfare' ),
			'size' => 'two-fourths',
			'content' => array(
				'0' => __( 'Zero' ,'social-warfare' ),
				'1' => __( 'One' ,'social-warfare' ),
				'2' => __( 'Two' ,'social-warfare' )
			),
			'default' => '0'
		 ),
		'swp_decimal_separator' => array(
			'type' => 'select',
			'size' => 'two-fourths',
			'name' => __( 'Decimal Separator' ,'social-warfare' ),
			'content' => array(
				'period' => __( 'Period' ,'social-warfare' ),
				'comma' => __( 'Comma' ,'social-warfare' )
			),
			'default' => 'period'
		 ),
		 'swTotesFormat' => array(
			'type' => 'select',
			'size' => 'two-fourths',
			'name' => __( 'Alignment' ,'social-warfare' ),
			'content' => array(
				'totesAlt'		=>	__( 'Right' ,'social-warfare' ),
				'totesAltLeft'	=>	__( 'Left' ,'social-warfare' )
			),
			'default' => 'totesAlt'
		),
		'total_counts_divider' => array(
			'type' => 'divider'
		),
		'float' => array(
			'type'			=> 'checkbox',
			'title' 		=> __( 'Floating Share Buttons' ,'social-warfare' ),
			'description' 	=> __( 'If you would like to activate floating share buttons, turn this on.' ,'social-warfare' ),
			'size'			=> 'four-fourths'
		),
		'floatOption' => array(
			'type' => 'select',
			'size' => 'two-fourths',
			'name' => __( 'Float Position' ,'social-warfare' ),
			'content' => array(
				'top' => __( 'Top of the Page' ,'social-warfare' ),
				'bottom' => __( 'Bottom of the Page' ,'social-warfare' ),
				'left' => __( 'On the left side of the page' ,'social-warfare' )
			),
			'default' => 'bottom',
			'dep' 		=> 'float',
			'dep_val'	=> array(true)
		),
		'swp_float_scr_sz' => array(
			'type' => 'input',
			'size' => 'two-fourths',
			'name' => __( 'Minimum Screen Width' ,'social-warfare' ),
			'default' => '1100',
			'dep' 		=> 'floatOption',
			'dep_val'	=> array('left')
		),
		'sideReveal' => array(
			'type' => 'select',
			'size' => 'two-fourths',
			'name' => __( 'Transition', 'social-warfare' ),
			'content' => array(
				'slide' 			=> __( 'Slide In / Slide Out' ,'social-warfare' ),
				'fade' 				=> __( 'Fade In / Fade Out' ,'social-warfare' )
			),
			'default' => 'slide',
			'dep' 		=> 'floatOption',
			'dep_val'	=> array('left')
		),
		'floatBgColor' => array(
			'type' => 'input',
			'size' => 'two-fourths',
			'name' => __( 'Background Color' ,'social-warfare' ),
			'default' => '#ffffff',
			'dep' 		=> 'floatOption',
			'dep_val'	=> array('top','bottom')
		)
	);

	// Return the options value
	return $swp_options;

}


/**
 * swp_options_social_identity An array of options for the social identity tab of the options page
 * @since 	2.0.0
 * @param  	array $swp_options The array of options
 * @return 	array $swp_options The modified array of options
 */

function swp_options_social_identity($swp_options) {

	// Declare the Display Settings tab and tab name
	$swp_options['tabs']['links']['swp_social_identity'] = __( 'Social Identity' , 'social-warfare' );

	$swp_options['options']['swp_social_identity'] = array(
		'social_identity_title' => array(
			'type' 		=> 'title',
			'content' 	=> __( 'Sitewide Identity' , 'social-warfare' )
		),
		'social_identity_description' => array(
			'type' 		=> 'paragraph',
			'content' 	=> __( 'If you would like to set sitewide defaults for your social identity, add them below.' , 'social-warfare' )
		),
		'twitterID' => array(
			'type'		=> 'input',
			'size'		=> 'two-thirds',
			'name'		=> __( 'Twitter Username' , 'social-warfare' )
		),
		'pinterestID' => array(
			'type'		=> 'input',
			'size'		=> 'two-thirds',
			'name'		=> __( 'Pinterest Username' , 'social-warfare' )
		),
		'facebookPublisherUrl' => array(
			'type'		=> 'input',
			'size'		=> 'two-thirds',
			'name'		=> __( 'Facebook Page URL'  , 'social-warfare' )
		),
		'facebookAppID' => array(
			'type'		=> 'input',
			'size'		=> 'two-thirds',
			'name'		=> __( 'Facebook App ID' , 'social-warfare' )
		),
		'social_identity_divider' => array(
			'type'		=> 'divider'
		)

	);

	return $swp_options;

}

/**
 * swp_options_advanced An array of options for the advanced tab of the options page
 * @since 	2.0.0
 * @param  	array $swp_options The array of options
 * @return 	array $swp_options The modified array of options
 */

function swp_options_advanced($swp_options) {

	// Declare the Display Settings tab and tab name
	$swp_options['tabs']['links']['swp_advanced'] = __( 'Advanced' , 'social-warfare' );

	$swp_options['options']['swp_advanced'] = array(
		'sniplyBuster' => array(
			'type'			=> 'checkbox',
			'title' 		=> __( 'Frame Buster' , 'social-warfare' ),
			'description' 	=> __( 'If you want to stop content pirates from framing your content, turn this on.' , 'social-warfare' ),
			'size'			=> 'two-thirds-advanced',
			'default'		=> true,
			'divider'		=> true
		),
		'caching_method_title' => array(
			'type'		=> 'title',
			'content'	=> __( 'Caching Method' , 'social-warfare' )
		),
		'caching_method_description' => array(
			'type'		=> 'paragraph',
			'content'	=> __( 'If you have advanced knowledge of caching configurations, you may want to choose your own below.' , 'social-warfare' )
		),
		'cacheMethod'		=> array(
			'type'				=> 'select',
			'size'				=> 'two-thirds',
			'name'				=> __( 'Cache Rebuild Method' , 'social-warfare' ),
			'content'			=> array(
				'advanced'			=> __( 'Advanced Cache Triggering' , 'social-warfare' ),
				'legacy'			=> __( 'Legacy Cache Rebuilding during Page Loads' , 'social-warfare' )
			),
			'default'			=> 'advanced',
			'divider'			=> true,
			'premium'			=> false
		),
		'full_content_title' => array(
			'type'		=> 'title',
			'content'	=> __( 'Full Content vs. Excerpts' , 'social-warfare' )
		),
		'full_content_description' => array(
			'type'		=> 'paragraph',
			'content'	=> __( 'If your theme does not use excerpts, but instead displays the full post content on archive, category, and home pages, activate this toggle to allow the buttons to appear in those areas.' , 'social-warfare' )
		),
		'full_content'		=> array(
			'type'				=> 'checkbox',
			'size'				=> 'two-thirds',
			'content'				=> __( 'Full Content?' , 'social-warfare' ),
			'default'			=> false,
			'premium'			=> false,
			'divider'			=> true
		),
		'force_new_shares_title' => array(
			'type'		=> 'title',
			'content'	=> __( 'Force New Shares' , 'social-warfare' )
		),
		'force_new_shares_description' => array(
			'type'		=> 'paragraph',
			'content'	=> __( 'If the API share count request returns a lower number than previously recorded, we ignore the new number and retain the original higher number from the previous request. Activating this will force the new share number to be accepted even if it is a lower number than previously recorded.' , 'social-warfare' )
		),
		'force_new_shares'		=> array(
			'type'				=> 'checkbox',
			'size'				=> 'two-thirds',
			'content'				=> __( 'Force New Shares?' , 'social-warfare' ),
			'default'			=> false,
			'premium'			=> false
		)
	);

	return $swp_options;
};

/**
 * swp_options_registration An array of options for the registration tab of the options page
 * @since 	2.0.0
 * @since   2.3.5 | DEC 18, 2017 | Moved this function into core so all premium addons can activate and use the Registration tab
 * @param  	array $swp_options The array of options
 * @return 	array $swp_options The modified array of options
 */

function swp_options_registration_tab($swp_options) {

	// Declare the Display Settings tab and tab name
	$swp_options['tabs']['links']['swp_registration'] = __( 'Registration' , 'social-warfare' );

	// The registration section/data will be filled dynamically via a hook
	$swp_options['options']['swp_registration']['plugin_registration'] = array(
		'type'			=> 'plugin_registration'
	);
	$swp_options['options']['swp_registration']['registration_divider'] = array(
		'type'	  => 'divider',
		'premium' => true
	);

	return $swp_options;

}

/**
 * Queue up the options filter functions
 * @since 2.0.0
 */

add_filter('swp_options', 'swp_options_display' 			, 1 );
add_filter('swp_options', 'swp_options_styles' 				, 2 );
add_filter('swp_options', 'swp_options_social_identity'		, 3 );
add_filter('swp_options', 'swp_options_advanced'			, 4 );

// SWP_ACTIVATE_REGISTRATION_TAB - Only queue the registration tab if a plugin has defined this constant
if( defined( 'SWP_ACTIVATE_REGISTRATION_TAB' ) ) {
	add_filter('swp_options', 'swp_options_registration_tab'    , 5 );
}

/**
 * swp_add_network_option A function for easily adding networks to the avialable options
 * @since 2.0.0
 * @param array $swp_options 	The array of available options
 * @param array $newOptionArray An array containing information about the new option we're adding
 * @return array $swp_options 	The modified array of available options
 */
function swp_add_network_option($swp_options,$newOptionArray) {

	$swp_options['options']['swp_display']['newOrderOfIcons']['content'][$key] = $newOptionArray[$key]['content'];

	// Return the option array or the world will explode
	return $swp_options;
}
