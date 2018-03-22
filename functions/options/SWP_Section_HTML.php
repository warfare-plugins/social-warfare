<?php

/**
* For creating markup that does not fit into the exiting options.
*
* This extends SWP_Option rather than _Section because it uses
* many of the same methods as an option and is a child of a
* section, even though this is not necessarily an option.
*
* @since 3.0.0
*/

class SWP_Section_HTML extends SWP_Option {
    public function __construct( $name ) {
        //* This does not have a key, so pass an empty string
        //* as the second arg.
        parent::__construct( $name, $name );
    }

    public function add_HTML( $html ) {
        if ( !is_string( $html) ) :
            $this->_throw( 'This requires a string of HTML!' );
        endif;

        $this->html .= $html;

        return $this;
    }

    public function render_html() {
        return $this->html;
    }
}