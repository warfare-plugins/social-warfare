<?php
/**
 * General utility helper functions.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     2.1.0
 */


/**
 * Writes data to the wordpress log.
 *
 * @var mixed $log The data you want to print.
 * @var string optional $context Context about the data you are writing.
 */
 if ( ! function_exists('write_log')) {
    function write_log ( $log, $context = '' )  {
       error_log( "Context: " . $context );
       if ( is_array( $log ) || is_object( $log ) ) {
          error_log( print_r( $log, true ) );
       } else {
          error_log( $log );
       }
    }
 }


/**
 * A global function to ensure that we always have a value for
 * every option. Grab and use the default value if it hasn't been
 * set.
 *
 * @since  3.0.0 | 24 APR 2018 | Created
 * @since  3.0.8 | 16 MAY 2018 | Added $options parameter.
 * @param  string $key   The key associated with the option we want.
 * @param  optional array $optoins A set of options to read from.
 * @return mixed  $value The value of the option.
 *
 */
function swp_get_option( $key, $options = null ){
  	global $swp_user_options;

    if ( null === $options ) {
        $options = $swp_user_options;
    }

  	$defaults = array();
  	$defaults = apply_filters('swp_options_page_defaults' , $defaults );

  	// If the options exists, return it.
  	if( !empty( $options[$key] ) ):
  		return $options[$key];

  	// Else check if we have a default to use:
  	elseif( !empty($defaults[$key]) ):
          // init_default( $key );
		return $defaults[$key];

  	// If neither, just return false.
  	else:
  		return false;
  	endif;
}

add_action( 'wp_ajax_swp_store_settings', 'swp_store_the_settings' );
/**
 * Handle the options save request inside of admin-ajax.php
 *
 * @since  unknown
 * @return void
 * @since  3.0.9 | 31 MAY 2018 | Added call to wp_cache_delete to make sure settings save
 */
function swp_store_the_settings() {
	global $swp_user_options;

	if ( ! check_ajax_referer( 'swp_plugin_options_save', 'security', false ) ) {
		wp_send_json_error( esc_html__( 'Security failed.', 'social-warfare' ) );
		die;
	}

	$data = wp_unslash( $_POST );

	if ( empty( $data['settings'] ) ) {
		wp_send_json_error( esc_html__( 'No settings to save.', 'social-warfare' ) );
		die;
	}

	$settings = $data['settings'];
	$options = $swp_user_options;

	unset( $options['order_of_icons']['active'] );
	unset( $options['order_of_icons']['inactive'] );

	// Loop and check for checkbox values, convert them to boolean.
	foreach ( $settings as $key => $value ) {
		if ( 'true' == $value ) {
			$options[ $key ] = true;
		} elseif ( 'false' == $value ) {
			$options[ $key ] = false;
		} else {
			$options[ $key ] = $value;
		}
	}

  wp_cache_delete ( 'social_warfare_settings', 'options' );
	echo json_encode(swp_update_options( $options ));

	die;
}


 /**
  * A wrapper for the legacy version of the function
  *
  * This version accepted 3 parameters, but was scrapped for a
  * new version that now accepts an array of unlimited parameters
  *
  * @since  1.4.0
  * @access public
  * @param  boolean $content The content to which the buttons will be added
  * @param  string  $where   Where the buttons should appear (above, below, both, none)
  * @param  boolean $echo    Echo the content or return it
  * @return string 			Returns the modified content
  */
 function socialWarfare( $content = false, $where = 'default', $echo = true ) {

    // Collect the deprecated fields and place them into an array
    $array['content'] 	= $content;
    $array['where'] 	= $where;
    $array['echo'] 		= $echo;
    $array['devs']		= true;

    // Pass the array into the new function
    return social_warfare( $array );
 }


/**
 * The primary social_warfare function
 *
 * This is the function that we encourage users to use in their themes and whatnot
 * if they want to be able to directly output the buttons in their theme. It accepts
 * an array of arguments that correspond to the arguments that the SWP_Buttons_Panel
 * accepts.
 *
 * @since  1.0.0 | Unknown | Created
 * @since  3.0.0 | 08 MAY 2018 | Converted to a passthrough function that calls
 *                               the buttons panel class.
 * @param  array $args An array of arguments to manipulate the output of the buttons.
 * @return none        The html of the buttons will be output to the screen.
 *
 */
 function social_warfare( $args = array() ) {

	 if( !is_array($args) ):
		 $args = array();
	 endif;

    $buttons_panel = new SWP_Buttons_Panel( $args );
	echo $buttons_panel->render_HTML();
 }


/**
 *  Round a number to the appropriate thousands.
 *
 * @since  unknown
 * @access public
 * @param  float $val The float to be rounded.
 * @return float A rounded number.
 */
function swp_kilomega( $val ) {
	global $swp_user_options;

	// Fetch the user assigned options
	$options = $swp_user_options;

	// Check if we even have a value to format
	if ( $val ) :

		// Check if the value is less than 1,000....
		if ( $val < 1000 ) :

			// If less than 1,000 just format and kick it back
			return number_format( $val );

			// Check if the value is greater than 1,000 and less than 1,000,000....
		elseif ( $val < 1000000 ) :

			// Start by deviding the value by 1,000
			$val = intval( $val ) / 1000;

			// If the decimal separator is a period
			if ( $options['decimal_separator'] == 'period' ) :

				// Then format the number to the appropriate number of decimals
				return number_format( $val,$options['decimals'],'.',',' ) . 'K';

				// If the decimal separator is a comma
			else :

				// Then format the number to the appropriate number of decimals
				return number_format( $val,$options['decimals'],',','.' ) . 'K';

			endif;

			// Check if the value is greater than 1,000,000....
		else :

			// Start by deviding the value by 1,000,000
			$val = intval( $val ) / 1000000;

			// If the decimal separator is a period
			if ( $options['decimal_separator'] == 'period' ) :

				// Then format the number to the appropriate number of decimals
				return number_format( $val,$options['decimals'],'.',',' ) . 'M';

				// If the decimal separator is a comma
			else :

				// Then format the number to the appropriate number of decimals
				return number_format( $val,$options['decimals'],',','.' ) . 'M';

			endif;

		endif;

	endif;

	return 0;
}
/**
 * This is a function for removing tags and all of their containing html like inline style or script tags in the excerpt.
 * @since  2.2.4 | Created | 6 March 2017
 * @access public
 * @param  string $tag_name The name of the tag to search and destroy
 * @param  object $document The HTML dom object
 * @return none
 */
function swp_remove_elements_by_tag_name($tag_name, $document) {
	$nodeList = $document->getElementsByTagName($tag_name);
	for ($nodeIdx = $nodeList->length; --$nodeIdx >= 0; ) {
		$node = $nodeList->item($nodeIdx);
		$node->parentNode->removeChild($node);
	}
}

/**
 *  Process the excerpts for descriptions
 *
 * @since  1.0.0 | Created | Unknown
 * @since  2.2.4 | Updated | 6 March 2017 | Added the filter to remove the script and style tags
 * @access public
 * @param  int $post_id The post ID to use when getting an exceprt.
 * @return string The excerpt.
 */
function swp_get_excerpt_by_id( $post_id ) {

	// Check if the post has an excerpt
	if ( has_excerpt() ) :
		$the_post = get_post( $post_id ); // Gets post ID
		$the_excerpt = $the_post->post_excerpt;

	// If not, let's create an excerpt
	else :
		$the_post = get_post( $post_id ); // Gets post ID
		$the_excerpt = $the_post->post_content; // Gets post_content to be used as a basis for the excerpt
	endif;

	$excerpt_length = 100; // Sets excerpt length by word count

	// Filter out any inline script or style tags as well as their content
	if( !empty( $the_excerpt ) ):
		$the_excerpt = preg_replace('/(<script[^>]*>.+?<\/script>|<style[^>]*>.+?<\/style>)/s', '', $the_excerpt);
	endif;

	$the_excerpt = strip_tags( strip_shortcodes( $the_excerpt ) ); // Strips tags and images
	$the_excerpt = preg_replace( '/\[[^\]]+\]/', '', $the_excerpt );
	$the_excerpt = str_replace( ']]>', ']]&gt;', $the_excerpt );
	$the_excerpt = strip_tags( $the_excerpt );
	$excerpt_length = apply_filters( 'excerpt_length', 100 );
	$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[...]' );
	$words = preg_split( "/[\n\r\t ]+/", $the_excerpt, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );

	if ( count( $words ) > $excerpt_length ) :
		array_pop( $words );
		// array_push($words, '…');
		$the_excerpt = implode( ' ', $words );
	endif;

	$the_excerpt = preg_replace( "/\r|\n/", '', $the_excerpt );

	return $the_excerpt;
}

if ( ! function_exists( 'swp_mobile_detection' ) ) {
	/**
	 * Check to see if the user is using a mobile device.
	 *
	 * @since  unknown
	 * @access public
	 * @todo   Replace this with a more reliable method, probably client-side.
	 * @return bool true if a mobile user agent.
	 */
	function swp_mobile_detection() {
		return preg_match( '/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i', $_SERVER['HTTP_USER_AGENT'] );
	}
}

/**
 * Check to see if debugging has been enabled.
 *
 * @since  2.1.0
 * @access private
 * @param  string $type The type of debugging to check for.
 * @return bool true if debugging is enabled.
 */
function _swp_is_debug( $type = 'all' ) {
	$debug = false;

	if ( ! empty( $_GET['swp_debug'] ) ) {
		$debug = sanitize_key( $_GET['swp_debug'] );
	}

	if ( ( $debug && 'all' === $type ) || $debug === $type ) {
		$debug = true;
	} else {
		$debug = false;
	}

	return (bool) apply_filters( 'swp_is_debug', $debug );
}

/**
 * A function to clean up the available buttons Array
 *
 * @since 2.1.4
 * @param Array $options The options Array
 * @return Array $options The modified options array
 */
// DEPRECATED
// function swp_buttons_cleanup( $options ) {
// 	if(isset($options['icons']['active'])) {
// 		unset($options['icons']['active']);
// 	}
// 	return $options;
// }
// add_filter( 'swp_button_options', 'swp_buttons_cleanup', 999 );

/**
 * A function to recursively search arrays
 *
 * @since  1.0.0
 * @access public
 * @param  string $needle   The needle
 * @param  string $haystack The haystack
 * @return string/bool Return the key if found or false if nothing is found
 */
function swp_recursive_array_search( $needle, $haystack ) {
	foreach ( $haystack as $key => $value ) {
		$current_key = $key;
		if ( $needle === $value or (is_array( $value ) && swp_recursive_array_search( $needle,$value ) !== false) ) {
			return $current_key;
		}
	}
	return false;
}

/**
 * A function to gethe current URL of a page
 *
 * @since  1.0.0
 * @return string The URL of the current page
 */
function swp_get_current_url() {
	$page_url = 'http';
	if ( $_SERVER['HTTPS'] == 'on' ) {$page_url .= 's';}
	$page_url .= '://';
	$page_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	$page_url = strtok( $page_url, '?' );

	return $page_url;
}
/**
 * A function to disable the buttons on subtitles
 *
 * @return bool false
 */
function swp_disable_subs() {
	return false;
}

/**
 * Convert curly quotes to straight quotes
 *
 * @since  1.4.0
 * @param  string $content A string of text to be filtered
 * @return string $content The modified string of text
 */
function convert_smart_quotes( $content ) {
	$content = str_replace( '"', '\'', $content );
	$content = str_replace( '&#8220;', '\'', $content );
	$content = str_replace( '&#8221;', '\'', $content );
	$content = str_replace( '&#8216;', '\'', $content );
	$content = str_replace( '&#8217;', '\'', $content );
	return $content;
}

/**
 * A function to make removing hooks easier
 *
 * @since  1.4.0
 * @access public
 * @param  string  $hook_name   The name of the hook
 * @param  string  $method_name The name of the method
 * @param  integer $priority    The hook priority
 * @return boolean false
 */
function swp_remove_filter( $hook_name = '', $method_name = '', $priority = 0 ) {
	global $wp_filter;

	// Take only filters on right hook name and priority
	if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
		return false;
	}

	// Loop on filters registered
	foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
		// Test if filter is an array ! (always for class/method)
		if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
			// Test if object is a class and method is equal to param !
			if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && $filter_array['function'][1] == $method_name ) {
				unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
			}
		}
	}

	return false;
}

/**
 * Get the SWP supported post types.
 *
 * @return array $types A list of post type names.
 */
function swp_get_post_types() {
	$types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		),
		'names'
	);

	$types[] = 'page';
	$types[] = 'post';

	return (array) apply_filters( 'swp_post_types', $types );
}

/**
 * A function to add options to the admin options page
 *
 * @since  2.1.4
 * @access public
 * @param  array  $sw_options The array of current options
 * @param  string $tabName    The name of the tab being modified
 * @param  string $optionName The name of the option that the new option will be inserted next to.
 * @param  array  $option     The array of information about the new option being added
 * @param  string $position   Add the new option 'before' or 'after' the needle. Default => 'after'
 * @return array  $sw_options The modified array of options
 */
 function swp_insert_option( $sw_options , $tabName , $optionName , $newOptionArray , $position = 'after' ) {

 	// Locate the index of the option you want to insert next to
     $keyIndex = array_search( $optionName, array_keys( $sw_options['options'][$tabName] ) );

	 if('after' === $position) {
		 ++$keyIndex;
	 }

     // Split the array at the location of the option above
     $first_array = array_splice ( $sw_options['options'][$tabName] , 0 , $keyIndex );

     // Merge the two parts of the split array with your option added in the middle
     $sw_options['options'][$tabName] = array_merge (
         $first_array,
         $newOptionArray,
         $sw_options['options'][$tabName]
     );

     // Return the option array or the world will explode
     return $sw_options;

 }

/**
 * A function to remove the screen options tab from our admin page
 * @since 2.2.1
 */
function swp_remove_screen_options( $display_boolean, $wp_screen_object ){
 	$blacklist = array('admin.php?page=social-warfare');
 	if ( in_array( $GLOBALS['pagenow'], $blacklist ) ) {
 		$wp_screen_object->render_screen_layout();
 		$wp_screen_object->render_per_page_options();
 		return false;
     }
 	return $display_boolean;
 }
 add_filter( 'screen_options_show_screen', 'swp_remove_screen_options', 10, 2 );


function swp_get_license_key($key) {

	if(is_swp_addon_registered($key)):

		$options = get_option( 'social_warfare_settings' );
		$license = $options[$key.'_license_key'];
		return $license;

	else:

		return false;

	endif;
}

/**
 * A function to return the URL of the website or network
 *
 * @since 2.3.3 | 25 SEP 2017 | Created
 * @return String The URL of the site
 *
 */
function swp_get_site_url() {
	if( true == is_multisite() ) {
		return network_site_url();
	} else {
		return get_site_url();
	}
}


/**
 * A function to convert strings into snake_case
 *
 * @since  3.0.0 | 02 MAY 2018 | Created
 * @param  string $string The string to be modified.
 * @return string         The snake_case modified string.
 *
 */
function swp_snake_case( $string ) {
	$snake_case = str_replace( ' ' , '_' , strtolower($string) );
	return $snake_case;
}

add_action( 'wp_ajax_swp_fetch_user_options', 'swp_fetch_user_options' );
function swp_fetch_user_options() {
  echo json_encode( get_option( 'social_warfare_settings', [] ) );
  wp_die();
}
