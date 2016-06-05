<?php

/***************************************************************

	Enqueue the Menu Item

***************************************************************/

// Add the link to the WordPress menu
add_action( 'admin_menu', 'sw_admin_options_page' );
function sw_admin_options_page() {
	
	// Declare the menu link
	$sw_menu = add_menu_page( 
		'SW 2.0', 
		'SW 2.0', 
		'manage_options', 
		'social-warfare-2', 
		'sw_plugin_options',
		SW_PLUGIN_DIR.'/images/socialwarfare-20x20.png'
	);
	
	// Hook into the CSS and Javascript Enqueue process for this specific page
	add_action( 'admin_print_styles-' . $sw_menu, 'sw_admin_options_css' );
	add_action( 'admin_print_scripts-'. $sw_menu, 'sw_admin_options_js' );
}

/***************************************************************

	Enqueue the Settings Page CSS & Javascript

***************************************************************/

// Enqueue the Admin Options CSS
function sw_admin_options_css() {
    wp_enqueue_style( 'sw_admin_options_css', SW_PLUGIN_DIR.'/functions/admin-options-page/admin-options-page.css' , array() , SW_VERSION );
}

// Enqueue the Admin Options JS
function sw_admin_options_js() {
    wp_enqueue_script( 'sw_admin_options_js', SW_PLUGIN_DIR.'/functions/admin-options-page/admin-options-page.js' , array() , SW_VERSION );
}

/***************************************************************

	Build the Settings Page Form

***************************************************************/

// We'll build the form here
function sw_plugin_options() {
	
	// Make sure the person accessing this link has proper permissions to access it
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	echo '<div class="wrap">';
	echo '<p>Here is where the form would go if I actually had options.</p>';
	echo '</div>';
}
