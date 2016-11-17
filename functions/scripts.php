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
}

/**
 * Queue up our javscript for options and whatnot
 * @since 1.4.0
 * @param Void
 * @return Void. Echo results directly to the screen.
 */
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

// Queue up our hook function
add_action( 'wp_footer' , 'swp_footer_functions' , 99 );

/**
 * Enable click tracking in Google Analytics.
 *
 * @since  1.4
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

// Queue up our footer hook function
add_filter( 'swp_footer_scripts' , 'swp_click_tracking' );

/**
 * Create a nonce for added security
 *
 * @since  2.1.4
 * @access public
 * @param  array $info An array of footer script information.
 * @return array $info A modified array of footer script information.
 */
function swp_nonce( $info ) {

	// Create a nonce
	$info['footer_output'] .= ' var swp_nonce = "'.wp_create_nonce().'";';
	return $info;
}

// Queue up our footer hook function
add_filter( 'swp_footer_scripts' , 'swp_nonce' );

/**
 * The Frame Buster Option
 *
 * @since  1.4.0
 * @access public
 * @param  array $info An array of footer script information.
 * @return array $info A modified array of footer script information.
 */
function swp_frame_buster( $info ) {

	global $swp_user_options;

	if ( true === $swp_user_options['sniplyBuster'] ) :
		$info['footer_output'] .= PHP_EOL . 'function parentIsEvil() { var html = null; try { var doc = top.location.pathname; } catch(err){ }; if(typeof doc === "undefined") { return true } else { return false }; }; if (parentIsEvil()) { top.location = self.location.href; };var url = "' . get_permalink() . '";if(url.indexOf("stfi.re") != -1) { var canonical = ""; var links = document.getElementsByTagName("link"); for (var i = 0; i < links.length; i ++) { if (links[i].getAttribute("rel") === "canonical") { canonical = links[i].getAttribute("href")}}; canonical = canonical.replace("?sfr=1", "");top.location = canonical; console.log(canonical);};';
	endif;

	return $info;
}

add_filter( 'swp_footer_scripts' , 'swp_frame_buster' );
