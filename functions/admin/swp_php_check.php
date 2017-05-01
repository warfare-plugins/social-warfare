<?php

class swp_php_check extends swp_custom_check
{
	public function __construct()
	{
		$this->name = 'PHP Check';
	}

	public function run()
	{
		if( version_compare( PHP_VERSION, '5.2.0' ) >= 0 )
		{
			$this->check_passed = true;
		}
		else
		{
			$this->check_passed = false;
			$this->whats_wrong = 'Your server is currently using PHP version 5.2 (or whatever version it is). In order for our plugin to fetch share counts properly, you must be using PHP 5.3 or newer.';
			$this->how_to_fix = 'To fix this, simply contact your hosting provider and ask them to update your server to the latest stable version of PHP.  If possible, test your website on a newer version before updating to avoid errors that could cause problems on a live site.';
		}

		return $this->check_passed;
	}
}

?>