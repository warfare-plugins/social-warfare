<?php

class SWP_Option_Checkbox extends SWP_Option {

	public function __construct() {
		parent::__construct();
		$this->type = 'checkbox';
		$this->default = true;
		$this->size = 'two-thirds';
	}

	public function output_html() {
		// Stuff goes here
	}

}
