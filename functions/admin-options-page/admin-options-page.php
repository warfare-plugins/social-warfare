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
						
				// Check for four-fourths size
				if($option['size'] == 'four-fourths'):

					echo '<div class="sw-grid sw-col-620"><h2 class="sw-h-label">'.$option['title'].'</h2><p class="sw-subtext-label">'.$option['description'].'</p></div>';
					echo '<div class="sw-grid sw-col-300 sw-fit">';
					echo '<div class="sw-checkbox-toggle" status="'.$status.'" field="#'.$key.'"></div>';
					echo '<input type="checkbox" class="sw-hidden" name="'.$key.'" id="sw_twitter_card" '.$selected.'>';
					echo '</div>';
				
				// Check for three-fourths-advanced size
				elseif($option['size'] == 'two-thirds-advanced'):

					echo '<div class="two-thirds-advanced">';
					echo '<div class="sw-grid sw-col-300"><h2 class="sw-h-label">'.$option['title'].'</h2><p class="sw-subtext-label">'.$option['description'].'</p></div>';
					echo '<div class="sw-grid sw-col-300">';
					echo '<div class="sw-checkbox-toggle" status="'.$status.'" field="#'.$key.'"></div>';
					echo '<input type="checkbox" class="sw-hidden" name="'.$key.'" id="sw_twitter_card" '.$selected.'>';
					echo '</div>';
					echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
					echo '</div>';
					echo '<div class="sw-clearfix"></div>';
				
				// Check for two-fourths size
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
				
				// All others
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
				echo '<div class="sw-grid sw-col-300"><input name="'.$key.'" type="text" class="sw-admin-input" placeholder="'.$option['default'].'" value="'.$option['default'].'" /></div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
				echo '<div class="sw-clearfix"></div>';
				
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
				
			elseif( $option['type'] == 'select' && $option['size'] == 'two-thirds' ):

				echo '<div class="sw-grid sw-col-300"><p class="sw-checkbox-label">'.$option['name'].'</p></div>';
				echo '<div class="sw-grid sw-col-300"><select name="'.$key.'">';
				if(!isset($option['default'])):
					echo '<option value="">Select...</option>';					
				endif;
				foreach( $option['content'] as $select_key => $select_value ) :
					echo '<option value="'.$select_key.'" '.($option['default'] == $select_key ? 'selected' :'').'>'.$select_value.'</option>';
				endforeach;
				echo '</select></div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
			
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

	Authentication / Button Module

***************************************************************/

			if($option['type'] == 'authentication'):
				echo '<div class="sw-grid sw-col-300"><p class="sw-authenticate-label">'.$option['name'].'</p></div>';
				echo '<div class="sw-grid sw-col-300">';
				echo '<a class="button sw-navy-button">Authenticate</a>';
				echo '</div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
			endif;

/***************************************************************

	Plugin Registration Module

***************************************************************/

			if($option['type'] == 'plugin_registration'):
				
				// Begin Registration Wrapper
				echo '<div class="registration-wrapper" registration="false">';
				
				// Registration Title
				echo '<h2>Premium Registration</h2>';
				
				// Open the IS NOT REGISTERED container
				echo '<div class="sw-grid sw-col-940 sw_is_not_registered">';
				
				// The Warning Notice & Instructions
				echo '<div class="sw-red-notice">This copy of Social Warfare is not registered. Let\'s fix it below.</div>';
				echo '<p class="sw-subtitle sw-registration-text">Follow these simple steps to register your Premium License and access all features.</p>';
				echo '<p class="sw-subtitle sw-registration-text sw-italic">Step 1: Enter your email.<br />Step 2: Click the "Register Plugin" button.<br />Step 3: Watch the magic.</p>';
				
				// Email Input Module
				echo '<div class="sw-grid sw-col-300"><p class="sw-input-label">Email Address</p></div>';
				echo '<div class="sw-grid sw-col-300"><input name="emailAddress" type="text" class="sw-admin-input" placeholder="email@domain.com" value="" /></div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
				echo '<div class="sw-clearfix"></div>';

				// Activate Plugin Module
				echo '<div class="sw-grid sw-col-300"><p class="sw-authenticate-label">Activate Registration</p></div>';
				echo '<div class="sw-grid sw-col-300">';
				echo '<a class="button sw-navy-button">Register Plugin</a>';
				echo '</div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
				
				// Close the IS NOT REGISTERED container
				echo '</div>';


				// Open the IS NOT REGISTERED container
				echo '<div class="sw-grid sw-col-940 sw_is_registered">';
				
				// The Warning Notice & Instructions
				echo '<div class="sw-green-notice">This copy of Social Warfare is registered. Wah-hoo!</div>';
				echo '<p class="sw-subtitle sw-registration-text">To unregister your license click the button below to free it up for use on another domain.</p>';
				
				// Deactivate Plugin Module
				echo '<div class="sw-grid sw-col-300"><p class="sw-authenticate-label">Deactivate Registration</p></div>';
				echo '<div class="sw-grid sw-col-300">';
				echo '<a class="button sw-navy-button">Unregister Plugin</a>';
				echo '</div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
				
				// Close the IS NOT REGISTERED container
				echo '</div>';



				
				// Close the Registration Wrapper
				echo '</div>';
				
			endif;

/***************************************************************

	Plugin Registration Module

***************************************************************/

			if($option['type'] == 'tweet_counts'):
				
				// Begin Registration Wrapper
				echo '<div class="tweet-count-wrapper" registration="false">';
				
				// Registration Title
				echo '<h2>Tweet Count Registration</h2>';
				
				// Open the IS NOT Activated container
				echo '<div class="sw-grid sw-col-940 sw_tweets_not_activated">';
				
				// The Warning Notice & Instructions
				echo '<p class="sw-subtitle sw-registration-text">In order to allow Social Warfare to track tweet counts, we\'ve partnered with NewShareCounts.com. Follow the steps below to register with NewShareCounts and allow us to track your Twitter shares.</p>';
				echo '<p class="sw-subtitle sw-registration-text sw-italic">Step 1: <a style="float:none;" class="button sw-navy-button" href="http://newsharecounts.com" target="_blank">Click here to visit NewShareCounts.com</a><br />Step 2: At NewShareCounts.com, Enter your domain and click the "Sign In With Twitter" button.<img class="sw-tweet-count-demo" src="'.SW_PLUGIN_DIR.'/functions/admin-options-page/images/new_share_counts.png" /><br />Step 3: Flip the switch below to "ON" and then save changes.</p>';
				
				
				// Close the IS NOT ACTIVATED container
				echo '</div>';

				// Checkbox Module
				echo '<div class="sw-grid sw-col-300"><p class="sw-checkbox-label">Tweet Counts</p></div>';					
				echo '<div class="sw-grid sw-col-300">';
				echo '<div class="sw-checkbox-toggle" status="false" field="#twitter_shares"></div>';
				echo '<input type="checkbox" class="sw-hidden" name="twitter_shares" id="twitter_shares">';
				echo '</div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
				
				// Close the Registration Wrapper
				echo '</div>';
				
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
	echo '<a href="#"><img src="'.SW_PLUGIN_DIR.'/functions/admin-options-page/images/affiliate-300x150.jpg"></a>';
	echo '<a href="#"><img src="'.SW_PLUGIN_DIR.'/functions/admin-options-page/images/starter-guide-300x150.jpg"></a>';
	echo '<a href="#"><img src="'.SW_PLUGIN_DIR.'/functions/admin-options-page/images/measure-roi-300x150.jpg"></a>';
	echo '<a href="#"><img src="'.SW_PLUGIN_DIR.'/functions/admin-options-page/images/paradox-of-choice-300x150.jpg"></a>';
	echo '<p class="sw-support-notice">Need help? Check out our Knowledgebase.</p>';
	echo '<p class="sw-support-notice">Opening a support ticket? Copy your System Status by clicking the button below.</p>';
	echo '<a href="#" class="button sw-blue-button">Get System Status</a>';
	echo '</div>';
	
	echo '</div>';

}











