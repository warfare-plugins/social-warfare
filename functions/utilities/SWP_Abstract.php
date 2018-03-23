<?php

class SWP_Abstract {
    /**
    * Name
    *
    * The name of this option. This is a "pretty" name that the plugin user will see.
    *
    * @var string
    *
    */
	public $name;


	/**
    * Type
    *
    * The type property refers to the type of option this represents (e.g. input,
    * textarea, checkbox, etc.)
    *
    * @var string
    *
    */
	public $type;


	/**
    * Default
    *
    * The default property refers to the default value for this option. This is
    * what the option will be set to until the user changes it.
    *
    * @var mixed This var is dependant on what type of option is being generated.
    *
    */
	public $default;


	/**
    * Premium
    *
    * This property determines whether or not this option is a premium option. By
    * default this property is set to false. The set_premium() method can be called
    * to change this property. When called, the set_premium() method will accept a
    * string corresponding to the registration key of the premium plugin on which
    * this option relies. It will set the $premium_addon property to that string and
    * switch this property to true.
    *
    * @var bool
    *
    */
	public $premium = false;

    /**
     *  Addon
     *
     * This propety is set iff $premium === true. The value of $addon is the
     * code for the corresponding addon. Permissable values are:
     *
     * pro
     *
     * @var string
     *
     */
    public $addon = '';


	/**
    * Priority
    *
    * The priority property is used to determine the order in which the options are
    * presented to the user. These options will be sorted prior to the rendering of
    * the HTML in ascending order. That is to say, an option with a priority of 10
    * will appear before an option with a priority of 20.
    *
    * @var integer
    *
    */
	public $priority;

    public function __construct( $name ) {
        $this->set_name( $name );
        $this->user_options = get_option( 'social_warfare_settings' );
        // echo "<pre>";
        // var_export( $this->user_options); die();

    }

    public function get_property( $property ) {
        if ( property_exists( __CLASS__, $property ) ) {
            return $this->$property;
        }

        $this->_throw("Property $property does not exist in " . __CLASS__ . "." );
    }

    /**
    * Give chid classes an error handling method.
    *
    * @param  mixed $message The message to send as an error.
    * @return object Exception An exception with the passed in message.
    */
    public function _throw( $message ) {
        ob_start();
        print_r( debug_backtrace()[1]['args'] );
        $dump = ob_get_clean();

        if ( is_string( $message ) ) {
            throw new Exception( get_class( $this ) . '->' . debug_backtrace()[1]['function'] . '() ' . $message . ' Here is what I received: ' . $dump );
        } else {
            throw new Exception( get_class( $this ) . '->' . debug_backtrace()[1]['function'] . '() ' . PHP_EOL . var_dump( $message ) );
        }
    }

    public function set_name( $name ) {
        if ( !is_string($name) ) {
            $this->_throw("Please provide a string for your object's name." );
        }

        $this->name = __( $name, 'social-warfare' );

        return $this;
    }

    public function set_priority( $priority ) {
        if ( ! intval( $priority ) || $priority < 1) {
            $this->_throw("Requires an integer greater than 0.");
        }

        $this->priority = $priority;

        return $this;
    }

    protected function render_dependency() {
        if ( !empty( $this->dependency) ) :
            return ' data-dep="' . $this->dependency->parent . '" data-dep_val=\'' . json_encode($this->dependency->values) . '\'';
        endif;

        return ' ';
    }

    protected function render_premium() {
        return;
        if ( isset( $this->premium ) ) :
            return ' premium="true" ';
        endif;

        return ' ';
    }

    /**
    * Creates a Javscript selector keyname  based on the object's name.
    *
    * @param string $name The name to be converted to a key. Usually the objects name.
    * @return string $key A valid PHP and jQuery target keyname.
    */
    public function name_to_key( $name ) {
        if ( !is_string( $name ) ) :
            $this->_throw( 'Please provide a string to get a key.' );
        endif;

        //* Remove all non-word character symbols.
        $key = preg_replace( '#[^\w\s]#i', '', $name );

        //* Replace spaces with underscores.
        $key = preg_replace( '/\s+/', '_', $name );


        return strtolower( $key );
    }

    /**
    * Set the premium status of the object.
    *
    * Since there are going to be multiple addons, it's not sufficient to set premium to simply true or
    * false. Instead, it will be false by default. Unless this method is called and a string corresponding
    * the registration key of the corresponding premium addon is passed. Example: $SWP_Option->set_premium('pro');
    *
    * This will then set the premium property to true and place the registration key into the premium_addon property.
    *
    * This method does not need to be called unless it is a premium option.
    *
    * @since 2.4.0 | 02 MAR 2018 | Created
    * @param string String corresponding to the registration key of premium plugin if true.
    * @return $this Return the object to allow method chaining.
    *
    */
	public function set_premium( $premium_addon ) {
		if ( !is_string( $premium_addon ) ) {
            $addons = [ 'pro' ];
            $addon_string = PHP_EOL;

            foreach( $addons as $addon ) {
                $addon_string . $addon . PHP_EOL;
            }
			$this->_throw( "Please provide a string that is one of the following: " . var_export($addons ) );
		}

		$this->premium = $premium_addon;

		return $this;
	}
}
