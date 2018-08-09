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
		// Retrieve the user's set options from the database.
		$this->user_options = get_option( 'social_warfare_settings', false );

        //* No options have been stored yet.
        if ( false === $this->user_options ) {
            return;
        }

		// Get the options data used to filter the user options.
		$this->registered_options = get_option( 'swp_registered_options', false );

		add_action( 'wp_loaded', array( $this, 'filter_options'), 100 );

		// Assign the user options to a globally accessible array.
        global $swp_user_options;
        $swp_user_options = $this->user_options;

		// Add all relevant option info to the database.
		add_action( 'admin_footer', array( $this , 'store_registered_options_data' ) );
        add_action( 'admin_footer', array( $this, 'debug' ) );

		// $this->debug();?
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
		if( true === _swp_is_debug( 'swp_user_options' ) ) {
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
		$new_registered_options = array(
            'defaults'  => apply_filters( 'swp_options_page_defaults', array() ),
            'values'    => apply_filters( 'swp_options_page_values', array() )
        );

		if( $new_registered_options != $this->registered_options ) {
			update_option( 'swp_registered_options', $new_registered_options );
		}
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
        $defaults = $this->registered_options['defaults'];

        foreach( $this->user_options as $key => $value) {
            if ( !array_key_exists( $key, $defaults ) ) :
                unset( $this->user_options[$key] );
            endif;
        }
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
		$values = $this->registered_options['values'];
		$defaults = $this->registered_options['defaults'];

		foreach( $this->user_options as $key => $value ) {
			if( $values[$key]['type'] == 'select' && !in_array( $value, $values[$key]['values']) ) {
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
