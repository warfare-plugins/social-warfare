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

// Queue up our hook function
add_action( 'wp_footer' , 'swp_footer_functions' , 99 );

// Queue up our footer hook function
add_filter( 'swp_footer_scripts' , 'swp_output_cache_trigger' );
add_filter( 'swp_footer_scripts' , 'swp_click_tracking' );

function swp_footer_functions() {

	// Fetch a few variables.
	$info['postID']           = get_the_ID();
	$info['swp_user_options'] = swp_get_user_options();
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
 * Trigger cache rebuild.
 *
 * @since  unknown
 * @access public
 * @param  array $info An array of footer script information.
 * @return array $info A modified array of footer script information.
 */
function swp_output_cache_trigger( $info ) {
	// Bail early if we're not on a single page or we have fresh cache.
	if ( ! is_singular() || swp_is_cache_fresh( get_the_ID(), true ) ) {
		return $info;
	}

	// Bail if we're not using the newer cache method.
	if ( 'legacy' === $info['swp_user_options']['cacheMethod'] ) {
		return $info;
	}

	// Bail if we're on a WooCommerce account page.
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		return $info;
	}

	// Trigger the cache rebuild.
	if ( isset( $_GET['swp_cache'] ) && 'rebuild' === $_GET['swp_cache'] ) {
		$url = get_permalink();
		$admin_ajax = admin_url( 'admin-ajax.php' );
		$info['footer_output'] .= 'swp_admin_ajax = "' . $admin_ajax . '"; var swp_buttons_exist = !!document.getElementsByClassName("nc_socialPanel");if(swp_buttons_exist) {jQuery(document).ready( function() { var swp_cache_data = {"action":"swp_cache_trigger","post_id":' . $info['postID'] . '};jQuery.post(swp_admin_ajax, swp_cache_data, function(response) {console.log(response);});});} swp_post_id="' . $info['postID'] . '"; swp_post_url="' . $url . '"; socialWarfarePlugin.fetchFacebookShares();';
	}

	return $info;
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
		$info['footer_output'] .= 'if (typeof ga == "function") { jQuery(document).on("click",".nc_tweet",function(event) {var network = jQuery(this).parents(".nc_tweetContainer").attr("data-network");ga("send", "event", "social_media", "swp_" + network + "_share" );});}';
	}

	return $info;
}
