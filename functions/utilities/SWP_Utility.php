<?php

/**
 * SWP_Utility
 *
 * A collection of utility functions.
 *
 * All of the methods should be static. The class serves as tidy container
 * for various utility functions.
 *
 * The constructor serves only to set up hooks and filters.
 *
 * @package   SocialWarfare\Utilities
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     3.3.0 | 14 AUG 2018 | Created.
 * @access    public
 *
 */
class SWP_Utility {

    /**
     * Insantiates filterss and hooks, for admin and ajax.
     *
     * @since  3.3.0 \ 14 AUG 2018 | Created.
     *
     */
    public function __construct() {
        add_action( 'wp_ajax_swp_store_settings', array( self, 'store_settings' ) );

    }


    public static function get_option( $key = '' ) {
        if ( !isset( $key ) || !is_string( $key ) ) :
            return false;
        endif;

        global $swp_user_options;

        if ( array_key_exists( $key, $swp_user_options ) ) :
            return $swp_user_options[$key];
        endif;

        return false;
    }

    public static function store_settings() {
        if ( !check_ajax_referer( 'swp_plugin_options_save', 'security', false ) ) {
    		wp_send_json_error( esc_html__( 'Security failed.', 'social-warfare' ) );
    		die;
    	}

        $data = wp_unslash( $_POST );

    	if ( empty( $data['settings'] ) ) {
    		wp_send_json_error( esc_html__( 'No settings to save.', 'social-warfare' ) );
    		die;
    	}

        $settings = $data['settings'];

        // Loop and check for checkbox values, convert them to boolean.
    	foreach ( $data['settings'] as $key => $value ) {
    		if ( 'true' == $value ) {
    			$settings[$key] = true;
    		} elseif ( 'false' == $value ) {
    			$settings[$key] = false;
    		} else {
    			$settings[$key] = $value;
    		}
    	}

        echo json_encode( update_option( 'social_warfare_settings', $settings ) );

        wp_die();
    }



    public static function kilomega( $number = 0) {
        if ( empty( $number ) ) :
            return 0;
        endif;

        $decimal_separator = self::get_option( 'decimal_separator');

        if ( $number < 1000 ) :
            return $number;
        endif;

        if ( $number < 1000000 ) {
            $suffix = 'K';
            $value = $number / 1000;
        } else {
            $suffix = 'M';
            $value = $number / 1000000;
        }

        if ( 'period' == self::get_option( 'decimals' ) ) :
            $decimal_point = '.';
            $thousands_separator = ',';
        else :
            $decimal_point = ',';
            $thousands_separator = '.';
        endif;

        return number_format( $value, self::get_option( 'decimals' ), $decimal_point, $thousands_separator );

    }

    public static function get_the_excerpt( $post_id ) {
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

    // public static function get_excerpt_by_id() {}

    public static function debug( $key = '' ) {
        return !empty( $_GET['swp_debug'] ) && ( strtolower( $_GET['swp_debug'] ) == strtolower( $key ) );
    }

    public static function convert_smart_quotes( $content ) {
    	$content = str_replace( '"', '\'', $content );
    	$content = str_replace( '&#8220;', '\'', $content );
    	$content = str_replace( '&#8221;', '\'', $content );
    	$content = str_replace( '&#8216;', '\'', $content );
    	$content = str_replace( '&#8217;', '\'', $content );
    	return $content;
    }

    public static function get_post_types() {
		$types = get_post_types( array( 'public' => true, '_builtin' => false ), 'names' );

        $types = array_merge( $types, array( 'post', 'page' ) );

    	return apply_filters( 'swp_post_types', $types );
    }

    public static function remove_screen_options( $display, $wp_screen_object ){
     	$blacklist = array('admin.php?page=social-warfare');

     	if ( in_array( $GLOBALS['pagenow'], $blacklist ) ) {
     		$wp_screen_object->render_screen_layout();
     		$wp_screen_object->render_per_page_options();
     		return false;
         }

     	return $display;
     }

    public static function get_site_url() {
    	if( true == is_multisite() ) {
    		return network_site_url();
    	} else {
    		return get_site_url();
    	}
    }
}
