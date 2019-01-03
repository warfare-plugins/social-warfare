<?php

class SWP_Vimeo_Auth extends SWP_Auth_Controller {


	public function __construct() {
		$this->key = 'vimeo';
		$this->load_files();
	}


	public function load_files() {
		require_once('./Vimeo/autoload.php');
	}


}
