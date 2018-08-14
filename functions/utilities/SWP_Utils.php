<?php

/**
 * SWP_Utils
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
class SWP_Utils {

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

    public static function get_excerpt_by_id() {}

    public static function debug() {}

    public static function convert_smart_quotes() {}

    public static function get_post_types() {}

    public static function remove_screen_options() {}

    public static function get_site_url() {}


}
