<?php
/**
 * Wouldn't this be a much better case of using a trait?
 *
 * I don't feel like nothing more than an error handling method should qualify as the parent class
 * of the parent class of all of the SWP_Option classes.
 *
 * For example:
 */

trait SWP_Abstract {
    /**
     * Give chid classes an error handlingmethod.
     * @param  mixed $message The message to send as an error.
     * @return object Exception An           [description]
     */
	public function throw( $message ) {
		if ( is_string( $message ) ) {
            throw new Exception( $message );
        } else {
            throw new Exception( var_dump( $message) );
        }
	}
}
/* And then in the class declaration you simply add "use SWP_Error_Handling" and now you have access to
 * the throw() method and we can include this trait wherever the heck we may feel the need to have access.
 * class SWP_Option {
 *	use SWP_Abstract;
 * }
 */
class SWP_Abstract {
    /**
     * Give chid classes an error handlingmethod.
     * @param  mixed $message The message to send as an error.
     * @return object Exception An           [description]
     */
    public function throw( $message ) {
        if ( is_string( $message ) ) {
            throw new Exception( $message );
        } else {
            throw new Exception( var_dump( $message) );
        }
    }
}
