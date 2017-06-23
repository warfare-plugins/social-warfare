<?php
/**
 * Register and output open graph tags, Twitter cards, custom color CSS, and the icon fonts.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2016, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

defined( 'WPINC' ) || die;

/**
 *  Queue up our hook function which will in turn call all of Social Warfare's custom hooks
 */
add_action( 'wp_head'           , 'swp_add_header_meta'      , 1 );
add_filter( 'swp_header_html'   , 'swp_output_font_css'      , 20 );
add_action( 'admin_head'        , 'swp_output_font_css'      , 20 );

/**
 * The function that we're hooking into the header
 *
 * All other items being added to the header will be hooked into
 * swp_meta_tags which we will call and print via this function.
 *
 * @since 1.4.0
 * @access public
 * @param  none
 * @return none
 */
function swp_add_header_meta() {

	// Get the global options and the post ID
	global $swp_user_options;
	$info['postID'] = get_the_ID();
	$info['html_output'] = '';

	/**
	 * Create and return the values to be used in the header meta tags
	 *
	 * @since 2.1.4
	 * @access public
	 * @var array $info An array of information
	 * @return array $info The modified array with the 'meta_tag_values' index populated
	 */
	$info = apply_filters( 'swp_header_values' , $info );

	/**
	 * A filter to take the values from above and compile them into their html format
	 *
	 * This filter will take the values from $info['meta_tag_values'] and compile
	 * them into html stored in the $info['html_output'] index. This index will be
	 * a string that gets added to by each hook (.=), not an array.
	 *
	 * Note: Each meta tag should begin with PHP_EOL for clean structured HTML output
	 *
	 * @since 2.1.4
	 * @access public
	 * @var array $info An array of information
	 * @return array $info The modified array with the 'html_output' index populated.
	 */
	$info = apply_filters( 'swp_header_html' , $info );

	if ( $info['html_output'] ) :
		echo PHP_EOL . '<!-- Social Warfare v' . SWP_VERSION . ' https://warfareplugins.com -->';
		echo $info['html_output'];
		echo PHP_EOL . '<!-- Social Warfare v' . SWP_VERSION . ' https://warfareplugins.com -->' . PHP_EOL . PHP_EOL;
	endif;
}

/**
 * Output the CSS to include the icon font.
 *
 * Note: This is done in the header rather than in a CSS file to
 * avoid having the fonts called from a CDN, 95% of which do not
 * support the necessary mime & cross-origin access types to deliver them.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $info An array of information about the post
 * @return array  $info The modified array
 */
function swp_output_font_css( $info = array() ) {
	if ( is_admin() ) :

		// Echo it if we're using the Admin Head Hook
		echo '<style>@font-face {font-family: "sw-icon-font";src:url("' . SWP_PLUGIN_URL . '/fonts/sw-icon-font.eot?ver=' . SWP_VERSION . '");src:url("' . SWP_PLUGIN_URL . '/fonts/sw-icon-font.eot?ver=' . SWP_VERSION . '#iefix") format("embedded-opentype"),url("' . SWP_PLUGIN_URL . '/fonts/sw-icon-font.woff?ver=' . SWP_VERSION . '") format("woff"),
	url("' . SWP_PLUGIN_URL . '/fonts/sw-icon-font.ttf?ver=' . SWP_VERSION . '") format("truetype"),url("' . SWP_PLUGIN_URL . '/fonts/sw-icon-font.svg?ver=' . SWP_VERSION . '#1445203416") format("svg");font-weight: normal;font-style: normal;}</style>';
	else :

		// Add it to our array if we're using the frontend Head Hook
		$info['html_output'] .= PHP_EOL . '<style>@font-face {font-family: "sw-icon-font";src:url("' . SWP_PLUGIN_URL . '/fonts/sw-icon-font.eot?ver=' . SWP_VERSION . '");src:url("' . SWP_PLUGIN_URL . '/fonts/sw-icon-font.eot?ver=' . SWP_VERSION . '#iefix") format("embedded-opentype"),url("' . SWP_PLUGIN_URL . '/fonts/sw-icon-font.woff?ver=' . SWP_VERSION . '") format("woff"), url("' . SWP_PLUGIN_URL . '/fonts/sw-icon-font.ttf?ver=' . SWP_VERSION . '") format("truetype"),url("' . SWP_PLUGIN_URL . '/fonts/sw-icon-font.svg?ver=' . SWP_VERSION . '#1445203416") format("svg");font-weight: normal;font-style: normal;}</style>';

		return $info;
	endif;
}
