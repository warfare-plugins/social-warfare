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
		swp_PLUGIN_DIR . "/css/style{$suffix}.css",
		array(),
		swp_VERSION
	);

	wp_enqueue_script(
		'social_warfare_script',
		swp_PLUGIN_DIR . "/js/script{$suffix}.js",
		array( 'jquery' ),
		swp_VERSION,
		true
	);
}

add_action( 'admin_enqueue_scripts', 'enqueueSocialWarfareAdminScripts' );
/**
 * Load admin scripts and styles.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function enqueueSocialWarfareAdminScripts() {
	enqueueSocialWarfareScripts();

	$suffix = swp_get_suffix();

	wp_enqueue_style(
		'social_warfare_admin',
		swp_PLUGIN_DIR . "/css/admin{$suffix}.css",
		array(),
		swp_VERSION
	);

	wp_enqueue_script(
		'social_warfare_admin_script',
		swp_PLUGIN_DIR . "/js/admin{$suffix}.js",
		array( 'jquery' ),
		swp_VERSION
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
