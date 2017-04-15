<?php

abstract class swp_custom_check
{
	public $name = "";
	public $whats_wrong = "";
	public $how_to_fix= "";
	public $check_passed = null;
	public $additional_message = null;

	/**
	 * Force children to have an executable run method.
	 */
	abstract public function run();
}

?>