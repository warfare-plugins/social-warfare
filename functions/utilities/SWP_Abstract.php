<?php

/**
 * SWP_Abstract
 *
 * A series of useful methods that can be pulled in and easily used in classes
 * throughout the plugin.
 *
 * @since 2.4.0 | 02 MAR 2018 | Created
 *
 */
trait SWP_Abstract {


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
	 * A method used within sorting functions to compare two integers.
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
