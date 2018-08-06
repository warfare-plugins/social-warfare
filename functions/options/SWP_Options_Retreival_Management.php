<?php

class SWP_Options_Retrieval_Management {


	public function __construct() {

		// Retrieve the user's set options from the database.
		$this->user_options = get_option( 'social_warfare_settings', false );

        if ( false === $this->user_options ) {
            return;
        }

		// Get the options data used to filter the user options.
		$this->registered_options = get_option( 'swp_registered_option_values' );

		if( false === $this->registered_options ):
			return;
		endif;

		// Filter the user options based on options, values, and defaults.
		$this->filter_options();
		$this->filter_defaults();

		// Assign the user options to a globally accessible array.
		global $swp_user_options;
		$swp_user_options = $this->user_options;

		// Add all relevant option info to the database.
		add_action( 'plugins_loaded', array( $this , 'store_options_data' ) , 1000 );

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
	 * @return [type] [description]
	 */
	public function store_options_data() {
        global $swp_user_options;

		$new_registered_options  = apply_filters( 'swp_options_page_values' , array() );

        foreach( $swp_user_options as $key => $value ) {
            //* Compare the stored registred available values against the freshly loaded available values.
            if ( $this->registered_options[$key]!= $new_registered_options[$key] ) {
                $this->registered_options[$key] = $new_registered_options[$key];
            }
        }

		if( $new_registered_options != $this->registered_options ) {
			update_option( 'swp_registered_option_values' , $new_registered_options );
		}
	}


	/**
	 * TODO: This needs to check if an option exists and if the value that it is
	 * set to still exists.
	 *
	 * @return [type] [description]
	 */
	private function filter_options() {
        global $swp_user_options;

        $defaults = apply_filters( 'swp_options_page_defaults', array() );

        foreach( $swp_user_options as $key => $value) {
            if ( !in_array( $key, $defaults ) ) :
                unset( $swp_user_options[$key] );
            endif;
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
	private function filter_defaults() {
		global $swp_user_options;

		$updated = false;
		$defaults = $this->options_data['defaults'];

		// Manually set the order_of_icons default.
		$defaults['order_of_icons'] = array(
			'google_plus' => 'google_plus',
			'twitter'     => 'twitter',
			'facebook'    => 'facebook',
			'linkedin'    => 'linkedin',
			'pinterest'   => 'pinterest'
		);

		foreach ($defaults as $key => $value ) {
			 if ( !array_key_exists( $key, $swp_user_options) ) :
				 $swp_user_options[$key] = $value;
				 $updated = true;
			 endif;
		}

		if ( $updated ) {
			update_option( 'social_warfare_settings', $swp_user_options );
		}
}
