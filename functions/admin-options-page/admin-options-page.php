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
	?>

	<div class="sw-grid sw-col-940 sw-top-menu">
    	<img class="sw-header-logo" src="<?php echo SW_PLUGIN_DIR ?>/functions/admin-options-page/images/social-warfare-light.png" />
		<ul class="sw-header-menu">
        	<li class="sw-active-tab"><a class="sw-tab-selector" href="#" data-link="sw-display-tab"><span>Display</span></a></li>
        	<li><a class="sw-tab-selector" href="#" data-link="sw-styles-tab"><span>Styles</span></a></li>
        	<li><a class="sw-tab-selector" href="#" data-link="sw-social-identity-tab"><span>Social Identity</span></a></li>
        	<li><a class="sw-tab-selector" href="#" data-link="sw-advanced-tab"><span>Advanced</span></a></li>
        	<li><a class="sw-tab-selector" href="#" data-link="sw-registration-tab"><span>Registration</span></a></li>
		</ul>
	</div>
	<div class="sw-clearfix"></div>
	<div class="sw-admin-wrapper">
		<!-- Begin Tabs Container -->
    	<div class="sw-tabs-container sw-grid sw-col-700">
        	
            <!-- Begin Display Tab -->
            <div id="sw-display-tab" class="sw-admin-tab sw-grid sw-col-940">
        		
                <!-- Begin The Social Networks -->
                <h2>Social Networks</h2>
                <p class="sw-subtitle">Drag & Drop to activate and order your share buttons.</p>
				<img src="<?php echo SW_PLUGIN_DIR ?>/functions/admin-options-page/images/social-networks.png">
                <div class="sw-admin-divider sw-clearfix"></div>
                <!-- End Social Networks -->

                <!-- Begin Share Counts -->
                <h2>Share Counts</h2>
                <p class="sw-subtitle">Use the toggles below to determine how to display your social proof.</p>

                <div class="sw-grid sw-col-300">
                	<p class="sw-checkbox-label">Button Counts</p>
                </div>

                <div class="sw-grid sw-col-300">
                	<div class="sw-checkbox-toggle"></div>
                </div>
                
                <div class="sw-grid sw-col-300 sw-fit">
                </div>
                
                <div class="sw-clearfix"></div>
                
                <div class="sw-grid sw-col-300">
                	<p class="sw-checkbox-label">Total Counts</p>
                </div>

                <div class="sw-grid sw-col-300">
                	<div class="sw-checkbox-toggle"></div>
                </div>
                
                <div class="sw-grid sw-col-300 sw-fit">
                </div>
                
                <div class="sw-clearfix"></div>
                
                <div class="sw-grid sw-col-300">
                	<p class="sw-checkbox-label">Minimum Shares</p>
                </div>

                <div class="sw-grid sw-col-300">
                	<input type="text" class="sw-admin-input" placeholder="0"/>
                </div>
                
                <div class="sw-grid sw-col-300 sw-fit">
                </div>
                
                <div class="sw-admin-divider sw-clearfix"></div>
                <!-- End Share Counts -->
                
			<!-- End Display Tab -->
			</div>
            
            <!-- Begin Display Tab -->
			<div id="sw-styles-tab" class="sw-admin-tab sw-grid sw-col-940">
			
            	Styles Tab
            
            <!-- End Display Tab -->
            </div>
            
        
        <!-- End Tabs Container -->
        </div>
        
        
		<!-- Begin Admin Sidebar -->        
        <div class="sw-admin-sidebar sw-grid sw-col-220 sw-fit">
            <img src="<?php echo SW_PLUGIN_DIR ?>/functions/admin-options-page/images/sidebar-button-save-changes.jpg">
            <img src="<?php echo SW_PLUGIN_DIR ?>/functions/admin-options-page/images/sidebar-images.jpg">
            <p class="sw-support-notice">Need help? Check out our Knowledgebase.</p>
            <p class="sw-support-notice">Opening a support ticket? Copy your System Status by clicking the button below.</p>
            <img src="<?php echo SW_PLUGIN_DIR ?>/functions/admin-options-page/images/sidebar-button-support.jpg">
        
        <!-- End Admin Sidebar -->
        </div>
	
    
    </div>
    
    
	<?php
}













