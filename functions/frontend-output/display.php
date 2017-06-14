<?php
/**
 * Functions to load the front end display for the plugin.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2016, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

/**
 * A global for storing post ID's to prevent duplicate processing on the same posts
 * @since 2.1.4
 * @var array $swp_already_print Array of post ID's that have been processed during this pageload.
 */
global $swp_already_print;
$swp_already_print = array();

/**
 * A function to add the buttons
 *
 * @since 2.1.4
 * @param none
 * @return none
 */
function swp_activate_buttons() {

	// Fetch the user's settings
	global $swp_user_options;

	// Only hook into the_content filter if we're is_singular() is true or they don't use excerpts
    if( true === is_singular() || true === $swp_user_options['full_content'] ):
        add_filter( 'the_content','social_warfare_wrapper', 10 );
    endif;

	// Add the buttons to the excerpts
	add_filter( 'the_excerpt','social_warfare_wrapper' );

}

// Hook into the template_redirect so that is_singular() conditionals will be ready
add_action('template_redirect', 'swp_activate_buttons');


/**
 * A wrapper function for adding the buttons the content or excerpt.
 *
 * @since  1.0.0
 * @param  string $content The content.
 * @return String $content The modified content
 */
function social_warfare_wrapper( $content ) {

	// Fetch our global variables to ensure we haven't already processed this post
	global $post, $swp_already_print;
	$post_id = $post->ID;

	// Check if it's already been processed
	if( in_array( $post_id, $swp_already_print) ){
		return $content;
	}

	// Ensure it's not an embedded post
	if(true === is_singular() && $post_id != get_queried_object_id()) {
		return $content;
	}

	// Pass the content (in an array) into the buttons function to add the buttons
	$array['content'] = $content;
	$content = social_warfare_buttons( $array );

	// Add an invisible div to the content so the image hover pin button finds the content container area
	if( false === is_admin() && false == is_feed() ):
		$content .= '<div class="swp-content-locator"></div>';
	endif;

	return $content;
}

/**
 * The main social_warfare function used to create the buttons.
 *
 * @since  1.4.0
 * @param  array $array An array of options and information to pass into the buttons function.
 * @return string $content The modified content
 */
function social_warfare( $array = array() ) {
	$array['devs'] = true;
	$content = social_warfare_buttons( $array );
	if( false === is_admin() ):
		$content .= '<div class="swp-content-locator"></div>';
	endif;
	return $content;
}

/**
 * Add the side floating buttons to the footer if they are activated
 *
 * @since 1.4.0
 */
if ( in_array( $swp_user_options['floatOption'], array( 'left', 'right' ), true ) ) {
	add_action( 'wp_footer', 'socialWarfareSideFloat' );
}
