<?php

class swp_system_checker
{
    public static $custom_checks = array();
	public static $has_error = FALSE;

    public static function full_system_check()
    {
    	swp_system_checker::load_all_checks();
    	swp_system_checker::run_all_checks();
    	swp_system_checker::print_all_errors();

    }

    public static function load_all_checks()
    {
    	swp_system_checker::$custom_checks[ 'swp_php_check' ]  = new swp_php_check();
    	swp_system_checker::$custom_checks[ 'swp_curl_check' ] = new swp_curl_check();
    }

    public static function run_all_checks()
    {
		foreach( swp_system_checker::$custom_checks as $custom_check )
		{
			if( method_exists( $custom_check, 'run' ) )
				if( !$custom_check->run() && !$custom_check->check_passed )
					swp_system_checker::$has_error = true;
		}
    }

    public static function print_all_errors()
    {
    	if( !isset( swp_system_checker::$has_error ) || empty( swp_system_checker::$has_error ) )
    		return FALSE;

    	echo '<div class="sw-red-notice">';

    	foreach( swp_system_checker::$custom_checks as $custom_check )
    	{
    		if( $custom_check->check_passed )
    			continue;

    		echo '<br><p style="font-weight:bold">A problem has been found:</p>' . $custom_check->whats_wrong;
    		echo '<br><p style="font-weight:bold">Here is how to resolve the problem:</p>' . $custom_check->how_to_fix;
    	}

    	echo '</div>';
    }
}

?>