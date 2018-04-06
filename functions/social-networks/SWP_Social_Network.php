<?php

/**
 * SWP_Social_Network
 *
 * This is the class that is used for adding new social networks to the
 * buttons which can be selected on the options page and rendered in the
 * panel of buttons.
 *
 * @since 3.0.0 | 05 APR 2018
 *
 */
class SWP_Social_Network {


	/**
	 * The display name of the social network
	 *
	 * This is the 'pretty name' that users will see. It should generally
	 * reflect the official name of the network according to the way that
	 * network is publicly branded.
	 *
	 * @var string
	 *
	 */
	public $name = '';


	/**
	 * The snake_case name of the social network
	 *
	 * This is 'ugly name' of the network. This a snake_case key used for
	 * the purpose of eliminating spaces so that we can save things in the
	 * database and other such cool things.
	 *
	 * @var string
	 *
	 */
	public $key = '';


	/**
	 * The default state of this network
	 *
	 * This property will determine where the icon appears in the options page
 	 * prior to the user setting and saving it. If true, it will appear in the
 	 * active section. If false, it will appear in the inactive section. Once
 	 * the user has updated/saved their preferences, this property will no
 	 * longer do anything.
	 *
	 * @var bool If true, the button is turned on by default.
	 *
	 */
	public $default = true;


	/**
	 * The premium status of this network
	 *
	 * Whether this button is a premium network. An empty string refers to a
	 * non-premium network. A string containing the key of the premium addon
	 * to which this is a member is used for premium networks. For example,
	 * setting this to 'pro' means that it is a premium network dependant on
	 * the Social Warfare - Pro addon being installed and registered.
	 *
	 * @var string
	 *
	 */
	public $premium = '';


	/**
	 * The active status of this network
	 *
	 * If the user has this network activated on the options page, then this
	 * property will be set to true. If not, it will be set to false.
	 *
	 * @var bool
	 *
	 */
	public $active = false;


	public function add_to_global() {

		global $swp_social_networks;
		$swp_social_networks[$this->key] = $this;

	}


	/**
	 * A method for providing the object with a name.
	 *
	 * @since 3.0.0 | 05 APR 2018 | Created
	 * @param string $value The name of the object.
	 * @return object $this Allows chaining of methods.
	 * @access public
	 *
	 */
	public function set_name( $value ) {

        if ( !is_string( $value )  ||  empty( $value ) ) {
            $this->_throw("Please provide a string for your object's name." );
        }

        $this->name = $value;

        return $this;
    }


	/**
	 * A method for updating this network's default property.
	 *
	 * @since 3.0.0 | 05 APR 2018 | Created
	 * @param bool $value The default status of the network.
	 * @return object $this Allows chaining of methods.
	 * @access public
	 *
	 */
	public function set_default( $value ) {
		if ( !is_bool( $value ) || empty( $value ) ) {
			$this->_throw("Please provide a boolean value for your object's default state." );
		}

		$this->default = $value;

		return $this;
	}


	/**
	 * A method for updating this network's key property.
	 *
	 * @since 3.0.0 | 05 APR 2018 | Created
	 * @param string $value The key for the network.
	 * @return object $this Allows chaining of methods.
	 * @access public
	 *
	 */
	public function set_key( $value ) {

		if ( !is_string( $value ) ||  empty( $value ) ) {
			$this->_throw( 'Please provide a snake_case string for the key value.' );
		}

		$this->key = $value;
		return $this;
	}


	/**
	 * A method for updating this network's premium property.
	 *
	 * @since 3.0.0 | 05 APR 2018 | Created
	 * @param string $value A string corresponding to the key of the dependant premium addon.
	 * @return object $this Allows chaining of methods.
	 * @access public
	 *
	 */
	public function set_premium( $value ) {

		if ( !is_string( $value ) ||  empty( $value ) ) {
			$this->_throw( 'Please provide a string corresponding to the premium addon to which this network depends.' );
		}

		$this->premium = $value;
		return $this;
	}


	/**
	 * A method to return the 'active' status of this network.
	 *
	 * @since 3.0.0 | 06 APR 2018 | Created
	 * @param none
	 * @return bool
	 * @access public
	 *
	 */
	public function is_active() {
		return $this->active;
	}


	/**
	 * A method to set the 'active' status of this network.
	 *
	 * @since 3.0.0 | 06 APR 2018 | Created
	 * @param none
	 * @return none
	 * @access public
	 *
	 */
	public function set_active_state() {
		global $swp_user_options;
		if ( isset( $swp_user_options['order_of_icons'][$this->key] ) ) {
			$this->active = true;
		}
	}

}
