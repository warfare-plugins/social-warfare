<?php

class SWP_Option {

	use SWP_Abstract;

	public $type;
	public $default;
	public $premium = false;
	public $priority;
	public $name;

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
	 * false. Instead, it will be false by default. Unless this method is called and a string corresponding
	 * the registration key of the corresponding premium addon is passed. Example: $SWP_Option->set_premium('pro');
	 *
	 * This will then set the premium property to true and place the registration key into the premium_addon property.
	 *
	 * @since 2.4.0 | 02 MAR 2018 | Created
	 * @param string String corresponding to the registration key of premium plugin if true.
	 * @return $this Return the object to allow method chaining.
	 */
	public function set_premium( $premium_addon ) {
		if ( !is_string( $premium_addon ) ) {
			$this->throw(__CLASS__ . " method set_premium() requires a string. {$premium_addon} is not acceptable." );
		}

		$this->premium = true;
		$this->premium_addon = $premium_addon;

		return $this;
	}

	public function set_name( $name ) {
        if ( !is_string( $name ) ) {
            $this->throw(__CLASS__ . " method set_name() requires a string. {$name} is not acceptable." );
        }

        $this->name = __( $name, 'social-warfare' );

		return $this;
	}

	public function set_size( $size ) {
		$this->size = $size;
		return $this;
	}

    public function set_divider( $bool ) {

        $this->divider = !!$bool;

        return $this;
    }

}
