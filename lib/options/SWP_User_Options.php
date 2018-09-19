<?php

/**
* A Class to create and filter the global $swp_user_options;
*
* This class ensures that if options have been added via updates or by installing
* new addons that they are added to the user options array. Conversely, if
* available options have disappeared from deactivating an addon, those options
* will be removed from the global user options array.
*
* @package   SocialWarfare\Functions\Options
* @copyright Copyright (c) 2018, Warfare Plugins, LLC
* @license   GPL-3.0+
* @since     3.3.0   | Created | 06 AUG 2018
* @access    public
*
*/
class SWP_User_options {


	public function __construct() {
        $this->unfiltered_options = get_option( 'social_warfare_settings', false );
		// Fetch the current options and the available options data.
		$this->registered_options = get_option( 'swp_registered_options', false );
		$this->user_options = $this->unfiltered_options;

		// Filter the options.
		$this->filter_options();

		// Assign the options to the global.
		global $swp_user_options;
		$swp_user_options = $this->user_options;

		// Defered to End of Cycle: Add all relevant option info to the database.
		add_action( 'wp_loaded', array( $this , 'store_registered_options_data' ), 10000 );

		// Debug
        add_action( 'admin_footer', array( $this, 'debug' ) );
	}


    /**
     * Compares what the admin wants to what is available to the admin.
     *
     * @return void
     *
     */
    public function filter_options() {
        if( false !== $this->registered_options ) :
            $this->remove_unavailable_options();
			$this->correct_invalid_values();
    		$this->add_option_defaults();
		endif;
    }


	/**
	 * A function for debugging this class.
	 *
	 * @since  3.3.0 | 07 AUG 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	public function debug() {
		if( true === SWP_Utility::debug( 'swp_user_options' ) ) {
			echo "<pre>", var_export($this), "</pre>";
		}
	}


	/**
	 * Store the options data in the database.
	 *
	 * This will be an array of all available options, all of their available
	 * values, and all of their defaults.
	 *
	 * This is loaded super late to ensure that all available options will have
	 * already been added to the filter so that we can access them here.
	 *
	 * By loading late, it will not be available on this same page load for use
	 * by the filters. It will be available on the next available page load.
	 * However, this should only have to run on the page load when an addon is
	 * activated or deactivated as they won't change any other time so this won't
	 * be an issue.
	 *
	 * @since  3.3.0 | 06 AUG 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	public function store_registered_options_data() {
        $whitelist = $this->generate_whitelist();

        $new_registered_options = array(
            'defaults'  => apply_filters( 'swp_options_page_defaults', array() ),
            'values'    => apply_filters( 'swp_options_page_values', array() )
        );
        $registrations = apply_filters('swp_registrations', []);

        foreach($whitelist as $key) {
            if (isset( $this->unfiltered_options[$key] ) ) :
                $new_registered_options["defaults"][$key] = $this->unfiltered_options[$key];
                $new_registered_options["values"][$key]["type"] = "none";
                $new_registered_options["values"][$key]["values"] = $this->unfiltered_options[$key];
            endif;
        }

		if( $new_registered_options != $this->registered_options ) {
			update_option( 'swp_registered_options', $new_registered_options );
		}
	}


	public function generate_whitelist() {
        $addons = apply_filters( 'swp_registrations', array() );
        $whitelist = array('last_migrated', 'bitly_access_token', 'bitly_access_login');

		$post_types = get_post_types();
		foreach( $post_types as $post_type ) {
			$whitelist[] = 'swp_og_type_' . $post_type;
		}

        if ( empty( $addons) ) {
            return $whitelist;
        }

        foreach( $addons as $addon ) {
            $whitelist[] = $addon->key . '_license_key';
            $whitelist[] = $addon->key . '_license_key_timestamp';
        }

		return $whitelist;
	}


	/**
	 * Filter out non-existent options.
	 *
	 * This checks if an option is still registered and removes it from the user
	 * options if it does not exist.
	 *
	 * @since  3.3.0 | 06 AUG 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	private function remove_unavailable_options() {
        $defaults = array_keys( $this->registered_options['defaults'] );
        $options = array_keys ( $this->user_options );



        $available_options = array_intersect( $defaults, $options );

        foreach( $this->user_options as $key => $value ) {

            //* Manually filter the order of icons.
            if ( $key == 'order_of_icons' ) :
                $value = $this->filter_order_of_icons( $value );
                $this->user_icons[$key] = $value;
                continue;
            endif;

            if ( !in_array( $key, $available_options ) ) :
                unset( $this->user_options[$key] );
            endif;
        }
	}


    private function filter_order_of_icons( $user_icons = array() ) {
        $networks = $this->registered_options['values']['order_of_icons']['values'];
        $user_icons = $this->user_options['order_of_icons'];

        foreach( $user_icons as $network_key ) {
            if ( !array_key_exists( $network_key, $networks ) ) :
                unset( $user_icons[$network_key] );
            endif;
        }

        //* They did not have any Core icons set. Return to default icons.
        if ( empty ( $user_icons ) ) :
            $user_icons = $this->registered_options['defaults']['order_of_icons'];
        endif;

        return $user_icons;
    }


	/**
	 * Correct any values that may be invalid.
	 *
	 * @since  3.3.0 | 06 AUG 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	private function correct_invalid_values() {
        $defaults = $this->registered_options['defaults'];
		$values = $this->registered_options['values'];

		foreach( $this->user_options as $key => $value ) {
			if( $values[$key]['type'] == 'select' && !array_key_exists( $value, $values[$key]['values']) ) {
				$this->user_options[$key] = $defaults[$key];
			}
		}
	}


	/**
	 * Creates the default value for any new keys.
	 *
	 * @since  3.0.8  | 16 MAY 2018 | Created the method.
	 * @since  3.0.8  | 24 MAY 2018 | Added check for order_of_icons
	 * @since  3.1.0  | 13 JUN 2018 | Replaced array bracket notation.
	 * @since  3.3.0  | 06 AUG 2018 | Moved from database migration class.
	 * @param  void
	 * @return void
	 *
	 */
	private function add_option_defaults() {
		$defaults = $this->registered_options['defaults'];

		foreach ( $defaults as $key => $value ) {
			 if ( !array_key_exists( $key, $this->user_options ) ) :
				 $this->user_options[$key] = $value;
			 endif;
		}
    }
}
