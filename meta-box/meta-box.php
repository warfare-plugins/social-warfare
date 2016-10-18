<?php

if ( defined( 'ABSPATH' ) && ! class_exists( 'SWPMB_Loader' ) )
{
	require plugin_dir_path( __FILE__ ) . 'inc/loader.php';
	new SWPMB_Loader;
}
