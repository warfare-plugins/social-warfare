<?php
/**
 * A series of classes to check the user's system for minimum system requirements
 *
 * @package   social-warfare\functions\admin
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     2.2.4 | Created | 1 MAY 2017
 */

/**
 * A class for initializing the system checks
 *
 * @since  2.2.4 | Created | 1 MAY 2017
 * @access public
 */
abstract class SWP_Custom_Check {

	public $name               = '';
	public $whats_wrong        = '';
	public $how_to_fix         = '';
	public $check_passed       = null;
	public $additional_message = null;

	/**
	 * Force children to have an executable run method.
	 */
	abstract public function run();
}
