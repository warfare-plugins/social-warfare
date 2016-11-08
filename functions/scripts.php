<?php
/**
 * Register and enqueue plugin scripts and styles.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2016, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

defined( 'WPINC' ) || die;

/**
 * Helper function for getting the script/style `.min` suffix for minified files.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function swp_get_suffix() {
	$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

	if ( _swp_is_debug( 'script' ) ) {
		$debug = true;
	}

	$enabled = (bool) apply_filters( 'swp_enable_suffix', ! $debug );

	return $enabled ? '.min' : '';
}

add_action( 'wp_enqueue_scripts', 'enqueueSocialWarfareScripts' );
/**
 * Load front end scripts and styles.
 *
 * @since  1.0.0
 * @access public
 * @global $swp_user_options
 * @return void
 */
function enqueueSocialWarfareScripts() {
	global $swp_user_options;

	$suffix = swp_get_suffix();

	wp_enqueue_style(
		'social_warfare',
		SWP_PLUGIN_URL . "/css/style{$suffix}.css",
		array(),
		SWP_VERSION
	);

	wp_enqueue_script(
		'social_warfare_script',
		SWP_PLUGIN_URL . "/js/script{$suffix}.js",
		array( 'jquery' ),
		SWP_VERSION,
		true
	);

	$pin_vars = array(
		'enabled' => false,
	);

	if ( is_swp_registered() ) {

		if ( $swp_user_options['pinit_toggle'] ) {
			$pin_vars['enabled']   = true;
			$pin_vars['hLocation'] = $swp_user_options['pinit_location_horizontal'];
			$pin_vars['vLocation'] = $swp_user_options['pinit_location_vertical'];
			$pin_vars['minWidth']  = str_replace( 'px', '', $swp_user_options['pinit_min_width'] );
			$pin_vars['minHeight'] = str_replace( 'px', '', $swp_user_options['pinit_min_height'] );
		}
	}

	wp_localize_script( 'social_warfare_script', 'swpPinIt', $pin_vars );
}

add_action( 'admin_enqueue_scripts', 'enqueueSocialWarfareAdminScripts' );
/**
 * Load admin scripts and styles.
 *
 * @since  1.0.0
 * @access public
 * @param  string $screen The ID of the current admin screen.
 * @return void
 */
function enqueueSocialWarfareAdminScripts( $screen ) {
	$screens = array(
		'toplevel_page_social-warfare',
		'post.php',
		'post-new.php',
	);

	if ( ! in_array( $screen, $screens, true ) ) {
		return;
	}

	enqueueSocialWarfareScripts();

	$suffix = swp_get_suffix();

	wp_enqueue_style(
		'social_warfare_admin',
		SWP_PLUGIN_URL . "/css/admin{$suffix}.css",
		array(),
		SWP_VERSION
	);

	wp_enqueue_script(
		'social_warfare_admin_script',
		SWP_PLUGIN_URL . "/js/admin{$suffix}.js",
		array( 'jquery' ),
		SWP_VERSION
	);

	wp_localize_script( 'social_warfare_admin_script', 'swp_localize_admin',
		array(
			'swp_characters_remaining' => __( 'Characters Remaining', 'social-warfare' ),
		)
	);

	if ( ! is_swp_registered() ) {
		wp_enqueue_script( 'jquery-ui-tooltip' );

		wp_enqueue_style(
			'jquery-ui-tooltip-css',
			'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css',
			array()
		);
	}
}

// Queue up our hook function
add_action( 'wp_footer' , 'swp_footer_functions' , 99 );

// Queue up our footer hook function
add_filter( 'swp_footer_scripts' , 'swp_click_tracking' );

function swp_footer_functions() {
	global $swp_user_options;

	// Fetch a few variables.
	$info['postID']           = get_the_ID();
	$info['swp_user_options'] = $swp_user_options;
	$info['footer_output']    = '';

	// Pass the array through our custom filters.
	$info = apply_filters( 'swp_footer_scripts' , $info );

	// If we have output, output it.
	if ( $info['footer_output'] ) {
		echo '<script type="text/javascript">';
		echo $info['footer_output'];
		echo '</script>';
	}
}

/**
 * Enable click tracking in Google Analytics.
 *
 * @since  unknown
 * @access public
 * @param  array $info An array of footer script information.
 * @return array $info A modified array of footer script information.
 */
function swp_click_tracking( $info ) {
	if ( $info['swp_user_options']['swp_click_tracking'] ) {
		$info['footer_output'] .= 'var swpClickTracking = true;';
	} else {
		$info['footer_output'] .= 'var swpClickTracking = false;';
	}

	return $info;
}
