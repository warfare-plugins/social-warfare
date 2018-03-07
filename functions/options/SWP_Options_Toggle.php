<?php

class SWP_Option_Toggle extends SWP_Option {

	public function __construct() {
		parent::__construct();
		$this->type = 'checkbox';
		$this->default = true;
		$this->size = 'two-thirds';
	}

	public function render_html() {
	    //* Render the HTML for the toggle. 
	}

}
