<?php

class SWP_Options_Page_Option {

	public $type;
	public $size;
	public $content;
	public $default;
	public $premium;
	public $priority;

	public function __construct( $attributes ) {

		/**
		 * Cycle through each attribute and set it. Not all attributes apply to all option types so we need
		 * to check if each one is set before setting the property. I'd rather use a class-object so that if
		 * I want to add a method or something to it later, I will be able to do so easily just like with the
		 * tabs and sections.
		 *
		 */

        $whitelist = ['type', 'size', 'content', 'default', 'premium', 'priority'];

		foreach($whitelist as $key ) {
            $this->$key = $attributes[$key];
        }

	}


	/**
	 * Useful for adding new available choices to a dropdown item that already exists. For example, if Pro
	 * adds additional choices to an option that's already in core.
	 *
	 */
	public function add_choice() {

	}

	/**
	 * What if the cool new choice that we just added above is so cool that we want it to be the default?
	 *
	 */
	public function set_default() {

	}

}
