<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

// Script version, used to add version for scripts and styles
define( 'SW_META_VER', '4.4.1' );

// Define plugin URLs, for fast enqueuing scripts and styles
if ( ! defined( 'SW_META_URL' ) )
	define( 'SW_META_URL', plugin_dir_url( __FILE__ ) );
define( 'SW_META_JS_URL', trailingslashit( SW_META_URL . 'js' ) );
define( 'SW_META_CSS_URL', trailingslashit( SW_META_URL . 'css' ) );

// Plugin paths, for including files
if ( ! defined( 'SW_META_DIR' ) )
	define( 'SW_META_DIR', plugin_dir_path( __FILE__ ) );
define( 'SW_META_INC_DIR', trailingslashit( SW_META_DIR . 'inc' ) );
define( 'SW_META_FIELDS_DIR', trailingslashit( SW_META_INC_DIR . 'fields' ) );

// Optimize code for loading plugin files ONLY on admin side
// @see http://www.deluxeblogtips.com/?p=345

// Helper function to retrieve meta value
require_once SW_META_INC_DIR . 'helpers.php';

if ( is_admin() )
{
	require_once SW_META_INC_DIR . 'common.php';
	require_once SW_META_INC_DIR . 'field.php';

	// Field classes
	foreach ( glob( SW_META_FIELDS_DIR . '*.php' ) as $file )
	{
		require_once $file;
	}

	// Main file
	require_once SW_META_INC_DIR . 'meta-box.php';
	require_once SW_META_INC_DIR . 'init.php';
}
