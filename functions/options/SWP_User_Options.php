<?php

class SWP_User_options {


	public function __construct() {
		get_option('Our_Options_In_The_Database');
		$this->remove_unavailable_options();
		$this->set_defaults();
	}

	/**
	 * This will compare the User Options in the database against the SWP_Options_Page object. If it does
	 * not exist in the SWP_Options_Page object, that means that the addon that offered this option is not
	 * active or not registered so delete it from SWP_User_Options.
	 *
	 * But DO NOT remove registration keys or registration timestamps.
	 *
	 */
	public function remove_unavailable_options() {

	}

	/**
	 * Instead of a giant array of defaults like we have now, have it sort the options against the SWP_Options_Page object.
	 * Any User Option that isn't set, simply set it to it's default value.
	 *
	 */
	public function set_defaults() {

	}


}
