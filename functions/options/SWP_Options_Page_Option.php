<?php

class SWP_Options_Page_Option {

	public $type;
	public $size;
	public $content;
	public $default;
	public $premium;
	public $priority;

	public function __construct( ) {

	}

	public function set_priority( $priority ) {
		$this->priority = $priority;
		return $this;
	}

	/**
	 * Useful for adding new available choices to a dropdown item that already exists. For example, if Pro
	 * adds additional choices to an option that's already in core.
	 *
	 */
	public function add_choice() {

	}

	/**
	 * Add all the methods that will commonly be used across child classes.
	 *
	 */
	public function set_default( $default ) {
		$this->default = $default;
		return $this;
	}

	/**
	 * Set the premium status of this option.
	 *
	 * Since there are going to be multiple addons, it's not sufficient to set premium to simply true or
	 * false. Instead, it will be false, if it is a core (free) options and the registration key of the
	 * premium plugin to which it belongs (e.g. "pro") if it is premium. This will allow us to use the
	 * is_swp_addon_registered() function to check if it is allowed on output.
	 *
	 * @since 2.4.0 | 02 MAR 2018 | Created
	 * @param bool/str False if not premium, string corresponding to the registration key of premium plugin
	 * 				   if true.
	 * @return $this Return the object to allow method chaining.
	 */
	public function set_premium( $premium ) {
		$this->premium = $value;
		return $this;
	}

	public function set_name( $name ) {
		$this->name = $name;
		return $this;
	}

	public function set_size( $size ) {
		$this->size = $size;
		return $this;
	}

}
