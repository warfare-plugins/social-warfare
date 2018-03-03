<?php

/**
 * SWP_Options_Abstract
 *
 * A series of useful methods that can be pulled in and easily used in options-
 * related classes throughout the plugin.
 *
 * @since 2.4.0 | 02 MAR 2018 | Created
 *
 */
trait SWP_Options_Abstract {


    /**
     * Give classes an error handlingmethod.
     * @param  mixed $message The message to send as an error.
     * @return object Exception
     *
     */
	public function throw( $message ) {
		if ( is_string( $message ) ) {
            throw new Exception( $message );
        } else {
            throw new Exception( var_dump( $message) );
        }
	}


	/**
	 * Set's the name of this option. The name is what the plugin user will see as the label.
	 *
	 * @since  2.4.0 | 02 MAR 2018 | Created
	 * @param string The name of this option
	 * @return object $this Allows for method chaining
	 *
	 */
	public function set_name( $name ) {

        if ( !is_string( $name ) ) {
            $this->throw(__CLASS__ . " method set_name() requires a string. {$name} is not a string." );
        }

        $this->name = $name;

		return $this;
	}


	/**
	 * Set the priority to determine where this option appears in relation to other
	 * options in the same section.
	 *
	 * @since  2.4.0 | 02 MAR 2018 | Created
	 * @param integer $priority The priority of this option.
	 * @return object $this Allows for method chaining.
	 * @TODO: Sanitize for integers only.
	 *
	 */
	public function set_priority( $priority ) {

		if ( !is_int( $priority ) ) {
			$this->throw(__CLASS__ . " method set_priority() requires an integer. {$priority} is not an integer." );
		}

		$this->priority = $priority;
		return $this;
	}


	/**
	 * A method used within sorting functions to compare two object properties
	 * based on their "priority" property evaluated and compared as an integer.
	 *
	 * @since  2.4.0 | 02 MAR 2018 | Created
	 * @param  int $a The first integer being compared.
	 * @param  int $b The second integer being compared.
	 * @return int 1, 0, or -1
	 *
	 */
	public function compare($a, $b) {
    	return $this->intcmp($a->priority, $b->priority);
	}


	/**
	 * A method to replace strcmp which compares integers instead of strings.
	 *
	 * @since  2.4.0 | 2 MAR 2018 | Created
	 * @param  int $a The first integer being compared.
	 * @param  int $b The second integer being compared.
	 * @return int 1, 0, or -1
	 *
	 */
	public function intcmp($a,$b) {
	    if((int)$a == (int)$b)return 0;
	    if((int)$a  > (int)$b)return 1;
	    if((int)$a  < (int)$b)return -1;
	}

}
