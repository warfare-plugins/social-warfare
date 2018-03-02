<?php

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
