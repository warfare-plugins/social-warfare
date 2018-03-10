<?php

class SWP_Option_Toggle extends SWP_Option {

	public function __construct() {
		parent::__construct();
		$this->type = 'checkbox';
		$this->default = true;
		$this->size = 'two-thirds';
	}

    public function set_default( $value ) {
        if ( !is_boolean( $value ) ||  empty( $value ) ) {
            $this->_throw( 'Please provide a default value as a boolean.' );
        }

        $this->default = $value;

        return $this;
    }

	public function render_html( $echo = false ) {
	    //* Render the HTML for the toggle.
	}

}
