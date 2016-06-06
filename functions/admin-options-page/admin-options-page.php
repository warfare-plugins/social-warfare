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
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'jquery-effects-core' );
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
	
	sw_build_options_page();
	
}

/***************************************************************

	A Function to Parse the Array & Builg the Options Page

***************************************************************/
function sw_build_options_page() {
	
	// Create all of the options in one giant array
	$sw_options_page = array(

		// A List of Options Page Tabs and Their Titles
		'tabs' => array(
			'links' => array(
			)
		),

		// A list of options in each of the options tabs
		'options' => array()
	);


	// Fetch the global options array
	// global $sw_options_page;
	$sw_options_page = apply_filters( 'sw_options_page' , $sw_options_page );
	
/***************************************************************

	Build the header menu

***************************************************************/
	
	echo '<div class="sw-grid sw-col-940 sw-top-menu">';
    echo '<img class="sw-header-logo" src="'.SW_PLUGIN_DIR.'/functions/admin-options-page/images/social-warfare-light.png" />';
	echo '<ul class="sw-header-menu">';
	$i=0; foreach ( $sw_options_page['tabs']['links'] as $key => $value): ++$i;
		echo '<li'.($i == 1 ? ' class="sw-active-tab"' : '').'><a class="sw-tab-selector" href="#" data-link="'.$key.'"><span>'.$value.'</span></a></li>';
	endforeach;
	echo '</ul>';
	echo '</div>';
	echo '<div class="sw-clearfix"></div>';

/***************************************************************

	Build the Tab Container

***************************************************************/

	// Wrapper for the entire content area
	echo '<div class="sw-admin-wrapper">';
	
	// Wrapper for the left 3/4 non-sidebar content
	echo '<div class="sw-tabs-container sw-grid sw-col-700">';

	// Loop through the options tabs and build the options page
	foreach($sw_options_page['options'] as $tab_name => $tab_options):
		
		// Individual Tab Container - Full Width
		echo '<div id="'.$tab_name.'" class="sw-admin-tab sw-grid sw-col-940">';
		
		// Loop through and output each option module for this tab
		foreach($tab_options as $key => $option):

/***************************************************************

	Title Module

***************************************************************/
			
			if($option['type'] == 'title'):
				echo '<h2>'.$option['content'].'</h2>';
			endif;

/***************************************************************

	Description Module

***************************************************************/

			if($option['type'] == 'paragraph'):
				echo '<p class="sw-subtitle">'.$option['content'].'</p>';
			endif;

/***************************************************************

	Image Module

***************************************************************/

			if($option['type'] == 'image'):
				echo '<img src="'.$option['content'].'">';
			endif;

/***************************************************************

	Checkbox Module

***************************************************************/

			if($option['type'] == 'checkbox'):
			
				// Check for a default value
				if($option['default'] == true):
					$status = 'on'; $selected = 'selected';
				else:
					$status = 'off'; $selected = '';
				endif;
						
				if($option['size'] == 'four-fourths'):

					echo '<div class="sw-grid sw-col-620"><h2 class="sw-h-label">'.$option['title'].'</h2><p class="sw-subtext-label">'.$option['description'].'</p></div>';
					echo '<div class="sw-grid sw-col-300 sw-fit">';
					echo '<div class="sw-checkbox-toggle" status="'.$status.'" field="#'.$key.'"></div>';
					echo '<input type="checkbox" class="sw-hidden" name="'.$key.'" id="sw_twitter_card" '.$selected.'>';
					echo '</div>';
				
				elseif( $option['size'] == 'two-fourths'):
				
					if($last_size == 'two-fourths'):
						$last_size = '';
						$fit = 'sw-fit';
					else:
						$last_size = 'two-fourths';
						$fit = '';
					endif;

					echo '<div class="sw-grid sw-col-460 '.$fit.'">';
					echo '<div class="sw-grid sw-col-460"><p class="sw-checkbox-label">'.$option['content'].'</p></div>';				
					echo '<div class="sw-grid sw-col-460 sw-fit">';
					echo '<div class="sw-checkbox-toggle" status="'.$status.'" field="#'.$key.'"></div>';
					echo '<input type="checkbox" class="sw-hidden" name="'.$key.'" id="sw_twitter_card" '.$selected.'>';
					echo '</div></div>';
				
				else:
								
					if($options['header'] == true):
						echo '<div class="sw-grid sw-col-300"><h2 class="sw-h-label">'.$option['content'].'</h2></div>';
					else:
						echo '<div class="sw-grid sw-col-300"><p class="sw-checkbox-label">'.$option['content'].'</p></div>';
					endif;
					
					echo '<div class="sw-grid sw-col-300">';
					echo '<div class="sw-checkbox-toggle" status="'.$status.'" field="#'.$key.'"></div>';
					echo '<input type="checkbox" class="sw-hidden" name="'.$key.'" id="sw_twitter_card" '.$selected.'>';
					echo '</div>';
					echo '<div class="sw-grid sw-col-300 sw-fit"></div>';

				endif;
			endif;

/***************************************************************

	Input Module

***************************************************************/

			if($option['type'] == 'input' && $option['size'] == 'two-thirds'):
				echo '<div class="sw-grid sw-col-300"><p class="sw-input-label">'.$option['name'].'</p></div>';
				echo '<div class="sw-grid sw-col-300"><input name="'.$key.'" type="text" class="sw-admin-input" placeholder="0" value="'.$option['default'].'" /></div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
				
			elseif($option['type'] == 'input' && $option['size'] == 'two-fourths'):
				if($last_size == 'two-fourths'):
					$last_size = '';
					$fit = 'sw-fit';
				else:
					$last_size = 'two-fourths';
					$fit = '';
				endif;

				echo '<div class="sw-grid sw-col-460 '.$fit.'">';
				echo '<div class="sw-grid sw-col-460"><p class="sw-input-label">'.$option['name'].'</p></div>';
				echo '<div class="sw-grid sw-col-460 sw-fit"><input name="'.$key.'" type="text" class="sw-admin-input" placeholder="0" value="'.$option['default'].'" /></div>';
				echo '</div>';
			endif;

/***************************************************************

	Select Module

***************************************************************/

			if($option['type'] == 'select' && isset($option['secondary'])):
							
				echo '<div class="sw-grid sw-col-300"><p class="sw-input-label">'.$option['name'].'</p></div>';
				echo '<div class="sw-grid sw-col-300">';
				echo '<select name="'.$option['primary'].'">';
				if(!isset($option['default'])):
					echo '<option value="">Select...</option>';					
				endif;
				foreach( $option['content'] as $select_key => $select_value ) :
					echo '<option value="'.$select_key.'" '.($option['default'] == $select_key ? 'selected' :'').'>'.$select_value.'</option>';
				endforeach;
				echo '</select>';
				echo '</div>';
				echo '<div class="sw-grid sw-col-300 sw-fit">';
				echo '<select name="'.$option['secondary'].'">';
				if(!isset($options['default_2'])):
					echo '<option value="">Select...</option>';					
				endif;
				foreach( $option['content_2'] as $select_key => $select_value ) :
					echo '<option value="'.$select_key.'" '.($option['default_2'] == $select_key ? 'selected' :'').'>'.$select_value.'</option>';
				endforeach;
				echo '</select>';
				echo '</div>';
				
			elseif( $option['type'] == 'select' && $option['size'] == 'two-fourths' ):
			
				if($last_size == 'two-fourths'):
					$last_size = '';
					$fit = 'sw-fit';
				else:
					$last_size = 'two-fourths';
					$fit = '';
				endif;
			
				echo '<div class="sw-grid sw-col-460 '.$fit.'">';
				echo '<div class="sw-grid sw-col-460"><p class="sw-checkbox-label">'.$option['name'].'</p></div>';
				echo '<div class="sw-grid sw-col-460 sw-fit"><select name="'.$key.'">';
				if(!isset($option['default'])):
					echo '<option value="">Select...</option>';					
				endif;
				foreach( $option['content'] as $select_key => $select_value ) :
					echo '<option value="'.$select_key.'" '.($option['default'] == $select_key ? 'selected' :'').'>'.$select_value.'</option>';
				endforeach;
				echo '</select></div>';
				echo '</div>';
			
			endif;

/***************************************************************

	Three-Wide Column Labels Module

***************************************************************/

			if($option['type'] == 'column_labels'):
				if($option['columns'] == 3):
					echo '<div class="sw-grid sw-col-300"><p class="sw-select-label sw-short">'.$option['column_1'].'</p></div>';
					echo '<div class="sw-grid sw-col-300"><p class="sw-select-label sw-short">'.$option['column_2'].'</p></div>';
					echo '<div class="sw-grid sw-col-300 sw-fit"><p class="sw-select-label sw-short">'.$option['column_3'].'</p></div>';
				endif;
			endif;

/***************************************************************

	Divider Module

***************************************************************/

			if($option['type'] == 'divider'):
				echo '<div class="sw-clearfix"></div><div class="sw-admin-divider sw-clearfix"></div>';
			endif;

/***************************************************************

	HTML Module

***************************************************************/

			if($option['type'] == 'html'):
				echo '<div class="sw-grid sw-col-940">';
				echo $option['content'];
				echo '<div class="sw-clearfix"></div></div>';
			endif;

/***************************************************************

	Close the Tab Container

***************************************************************/

			// Add a divider element if necessary
			if($option['divider'] == true):
				echo '<div class="sw-clearfix"></div><div class="sw-admin-divider sw-clearfix"></div>';
			endif;

			if($option['size'] != 'two-fourths'):
				$last_size = '';
			endif;

		// Close the loop
		endforeach;

		// Close the tab container
		echo '</div>';
		
	endforeach;

	echo '</div>';

/***************************************************************

	The Right Sidebar

***************************************************************/

	echo '<div class="sw-admin-sidebar sw-grid sw-col-220 sw-fit">';
	echo '<a href="#" class="button sw-navy-button">Save Changes</a>';
	echo '<img src="'.SW_PLUGIN_DIR.'/functions/admin-options-page/images/sidebar-images.jpg">';
	echo '<p class="sw-support-notice">Need help? Check out our Knowledgebase.</p>';
	echo '<p class="sw-support-notice">Opening a support ticket? Copy your System Status by clicking the button below.</p>';
	echo '<a href="#" class="button sw-blue-button">Get System Status</a>';
	echo '</div>';
	
	echo '</div>';

}











