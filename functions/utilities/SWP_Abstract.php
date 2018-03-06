<?php

class SWP_Abstract {
    public $name, $premium, $addon, $default;

    public function __construct( $name ) {
        $this->set_name( $name );
    }

    public function get_property( $property ) {
        if ( property_exists( __CLASS__, $property ) ) {
            return $this->$property;
        }

        $this->throw("Property $property does not exist in " . __CLASS__ "." );
    }

    /**
     * Give chid classes an error handlingmethod.
     * @param  mixed $message The message to send as an error.
     * @return object Exception An exception with the passed in message.
     */
    public function throw( $message ) {
        if ( is_string( $message ) ) {
            throw new Exception( __METHOD__ . PHP_EOL . $message );
        } else {
            throw new Exception( var_dump( $message) );
        }
    }

    public function set_name( $name ) {
        if ( !is_string($name) ) {
            $this->throw("Please provide a string for your object's name." );
        }

        $this->name = __( $name, 'social-warfare' );

        return $this;
    }

    public function set_priority( $priority ) {
        if ( ! intval( $priority ) || $priority < 1) {
            $this->throw("Requires an integer greater than 0.");
        }

        $this->priority = $priority;

        return $this;
    }

    /**
     * Set whether a feature is premium (paid).
     *
     * If the feature is paid, it comes as an addon and has an associated
     * addon code (such as 'pro').
     *
     * @param boolean $bool True iff the feature requires payment.
     * @param string $addon The code for the addon.
     *
     * @return object $this The object calling the methods.
     *
     */
    public function set_premium( $bool, $addon) {
        if ( !is_boolean( $bool ) ) {
            $this->throw( 'First argument must be a boolean value.' );
        };

        if ( $bool && empty( $addon ) ) {
            $this->throw ( 'Please provide an addon code with the premium setting.' );
        }

        $this->is_premium = $bool;

        if ( $bool ) {
            if ( !is_string( $addon ) ) {
                $this->throw( 'Addon must be passed as a string.');
            }

            $this->addon = $addon;
        }

        return $this;
    }
}
