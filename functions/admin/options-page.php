<?php
/**
 * Functions for loading the admin options page.
 *
 * @package   SocialWarfare\Admin\Functions
 * @copyright Copyright (c) 2016, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

/**
 * Enqueue the admin menu page
 * @since 	1.0.0
 * @param 	none
 * @return 	none
 */
add_action( 'admin_menu', 'swp_admin_options_page' );
function swp_admin_options_page() {

	// Declare the menu link
	$swp_menu = add_menu_page(
		'Social Warfare',
		'Social Warfare',
		'manage_options',
		'social-warfare',
		'swp_plugin_options',
		SWP_PLUGIN_URL . '/images/admin-options-page/socialwarfare-20x20.png'
	);

	// Hook into the CSS and Javascript Enqueue process for this specific page
	add_action( 'admin_print_styles-' . $swp_menu, 'swp_admin_options_css' );
	add_action( 'admin_print_scripts-' . $swp_menu, 'swp_admin_options_js' );
}

/**
 * Enqueue the Settings Page CSS & Javascript
 */
function swp_admin_options_css() {
	$suffix = swp_get_suffix();

	wp_enqueue_style(
		'swp_admin_options_css',
		SWP_PLUGIN_URL . "/css/admin-options-page{$suffix}.css",
		array(),
		SWP_VERSION
	);
}

/**
 * Enqueue the admin javascript
 *
 * @since  2.0.0
 * @return void
 */
function swp_admin_options_js() {
	$suffix = swp_get_suffix();

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-effects-core' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery-ui-tooltip' );
	wp_enqueue_media();
	wp_enqueue_script(
		'swp_admin_options_js',
		SWP_PLUGIN_URL . "/js/admin-options-page{$suffix}.js",
		array( 'jquery' ),
		SWP_VERSION
	);

	wp_localize_script( 'swp_admin_options_js', 'swpAdminOptionsData', array(
		'registerNonce' => wp_create_nonce( 'swp_plugin_registration' ),
		'optionsNonce'  => wp_create_nonce( 'swp_plugin_options_save' ),
	));
}

/**
 * Build the Settings Page Form
 */
function swp_plugin_options() {
	if ( current_user_can( 'manage_options' ) ) {
		swp_build_options_page();
	}
}

/**
 * A Function to Parse the Array & Build the Options Page
 */
function swp_build_options_page() {
	global $swp_user_options;

	// Create all of the options in one giant array.
	$swp_options_page = array(
		// A List of Options Page Tabs and Their Titles.
		'tabs' => array(
			'links' => array(),
		),
		// A list of options in each of the options tabs.
		'options' => array(),
	);

	// Fetch the global options array
	// global $swp_options_page;
	$swp_options_page = apply_filters( 'swp_options' , $swp_options_page );

	// Unset the 'active' index if it's set
	if( isset(	$swp_user_options['newOrderOfIcons']['active'] )):
		unset(	$swp_user_options['newOrderOfIcons']['active'] );
	endif;

	/**
	 * Build the header menu
	 */

	// Wrapper for the entire content area
	echo '<div class="sw-header-wrapper">';

	echo '<div class="sw-grid sw-col-940 sw-top-menu">';
	echo '<div class="sw-grid sw-col-700">';
	echo '<img class="sw-header-logo" src="' . SWP_PLUGIN_URL . '/images/admin-options-page/social-warfare-light.png" />';
	echo '<ul class="sw-header-menu">';
	$i = 0;
	foreach ( $swp_options_page['tabs']['links'] as $key => $value ) : ++$i;
		echo '<li' . ($i == 1 ? ' class="sw-active-tab"' : '') . '><a class="sw-tab-selector" href="#" data-link="' . $key . '"><span>' . $value . '</span></a></li>';
	endforeach;
	echo '</ul>';
	echo '</div>';
	echo '<div class="sw-grid sw-col-220 sw-fit">';
	echo '<a href="#" class="button sw-navy-button sw-save-settings">Save Changes</a>';
	echo '</div>';
	echo '<div class="sw-clearfix"></div>';
	echo '</div>';
	echo '</div>';

	/**
	 * Build the Tab Container
	 */

	if( function_exists('is_swp_registered') ):
		$swp_registration = is_swp_registered();
	else:
		$swp_registration = false;
	endif;

	echo '<div class="sw-admin-wrapper" sw-registered="' . absint( $swp_registration ) . '">';

	// Runs a full system check, printing errors found if any
	if( class_exists( 'swp_system_checker' ) ) {
		swp_system_checker::full_system_check();
	}

	echo '<form class="sw-admin-settings-form">';

	// Wrapper for the left 3/4 non-sidebar content
	echo '<div class="sw-tabs-container sw-grid sw-col-700">';

	// Loop through the options tabs and build the options page
	foreach ( $swp_options_page['options'] as $tab_name => $tab_options ) :

		// Individual Tab Container - Full Width
		echo '<div id="' . $tab_name . '" class="sw-admin-tab sw-grid sw-col-940">';

		// Loop through and output each option module for this tab
		foreach ( $tab_options as $key => $option ) :

			/**
			 * Title Module
			 */

			if ( $option['type'] == 'title' ) :
				echo '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';
				if(!empty($option['support'])):
					echo '<h2><a target="_blank" class="swp_support_link" href="'.$option['support'].'" title="Click here to learn more about these options.">i</a>' . $option['content'] . '</h2>';
				else:
					echo '<h2>' . $option['content'] . '</h2>';
				endif;
				echo '<div class="sw-premium-blocker" title="test"></div>';
				echo '</div>';
			endif;

			/**
			 * Description Module
			 */

			if ( $option['type'] == 'paragraph' ) :
				echo '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';
				echo '<p class="sw-subtitle">' . $option['content'] . '</p>';
				echo '<div class="sw-premium-blocker no-icon"></div>';
				echo '</div>';
			endif;

			/**
			 * Image Module
			 */

			if ( $option['type'] == 'image' ) :
				echo '<img src="' . $option['content'] . '">';
			endif;

			/**
			 * Image Upload Module
			 */

			if ( $option['type'] == 'image_upload' ) :

				// Fetch the value
				if ( isset( $swp_user_options[ $key ] ) ) :
					$value = $swp_user_options[ $key ];
				else :
					$value = $option['default'];
				endif;

				// Create a whole parent container
				echo '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';

				// Title goes on the left
				echo '<div class="sw-grid sw-col-300">';
				echo '<p class="sw-checkbox-label">Custom Button Image</p>';
				echo '</div>';

				// Button goes in the middle
				echo '<div class="sw-grid sw-col-300">';
				echo '<label for="upload_image">
						<input class="swp_upload_image_field" type="text" size="36" name="' . $key . '" value="' . $value . '" />
						<a class="swp_upload_image_button button sw-navy-button" for="' . $key . '" type="button" value="Upload Image" />Upload Image</a>
					</label>';
				echo '</div>';

				// Preview goes on the right
				echo '<div class="sw-grid sw-col-300 sw-fit">';
					echo '<div class="sw-preview-container">';
				if ( $value ) :
					echo '<img class="sw-admin-image-preview" src="' . $value . '" />';
					echo '<div class="sw-delete-image"></div>';
					endif;
					echo '</div>';
				echo '</div>';

				echo '</div>';

			endif;

			/**
			 * Buttons Module
			 */

			if ( $option['type'] == 'buttons' ) :

				// The Active Buttons
				echo '<div class="sw-grid sw-col-300">';
				echo '<h3 class="sw-buttons-toggle">' . __( 'Active' , 'social-warfare' ) . '</h3>';
				echo '</div>';

				echo '<div class="sw-grid sw-col-620 sw-fit">';
				echo '<div class="sw-active sw-buttons-sort">';

				// Check if we have saved settings to use
				if ( isset( $swp_user_options['newOrderOfIcons'] ) ) :

					// Loop through each active button
					foreach ( $swp_user_options['newOrderOfIcons'] as $key => $value ) :
						echo '<i class="sw-s sw-' . $key . '-icon" data-network="' . $key . '" premium-button="' . $option['content'][ $key ]['premium'] . '"></i>';
					endforeach;

					// Use defaults if nothing is saved
				else :

					// Loop through the available buttons
					foreach ( $option['content'] as $key => $value ) :
						if ( $value['default'] == true ) :
							echo '<i class="sw-s sw-' . $key . '-icon" data-network="' . $key . '" premium-button="' . $option['content'][ $key ]['premium'] . '"></i>';
						endif;
					endforeach;

				endif;

				echo '</div>';
				echo '</div>';

				echo '<div class="sw-clearfix"></div>';

				// The Inactive Buttons
				echo '<div class="sw-grid sw-col-300">';
				echo '<h3 class="sw-buttons-toggle">' . __( 'Inactive' , 'social-warfare' ) . '</h3>';
				echo '</div>';

				echo '<div class="sw-grid sw-col-620 sw-fit">';
				echo '<div class="sw-inactive sw-buttons-sort">';

				// Check if we have saved settings to use
				if ( isset( $swp_user_options['newOrderOfIcons'] ) ) :

					// Loop through each active button
					foreach ( $option['content'] as $key => $value ) :
						if ( ! isset( $swp_user_options['newOrderOfIcons'][ $key ] ) ) :
							echo '<i class="sw-s sw-' . $key . '-icon" data-network="' . $key . '" premium-button="' . $option['content'][ $key ]['premium'] . '"></i>';
						endif;
					endforeach;

					// Use defaults if nothing is saved
				else :

					// Loop through the available buttons
					foreach ( $option['content'] as $key => $value ) :
						if ( $value['default'] == false ) :
							echo '<i class="sw-s sw-' . $key . '-icon" data-network="' . $key . '" premium-button="' . $option['content'][ $key ]['premium'] . '"></i>';
						endif;
					endforeach;

				endif;

				echo '</div>';
				echo '</div>';

			endif;

			/**
			 * Checkbox Module
			 */

			if ( $option['type'] == 'checkbox' ) :

				// Check for a default value
				if ( isset( $swp_user_options[ $key ] ) && $swp_user_options[ $key ] == true ) :
					$status = 'on';
					$selected = 'checked';
				elseif ( isset( $swp_user_options[ $key ] ) && $swp_user_options[ $key ] == false ) :
					$status = 'off';
					$selected = '';
				elseif ( $option['default'] == true ) :
					$status = 'on';
					$selected = 'checked';
				else :
					$status = 'off';
					$selected = '';
				endif;

				// Check for four-fourths size
				if ( $option['size'] == 'four-fourths' ) :

					echo '<div class="sw-grid sw-col-620"><h2 class="sw-h-label">' . $option['title'] . '</h2><p class="sw-subtext-label">' . $option['description'] . '</p></div>';
					echo '<div class="sw-grid sw-col-300 sw-fit">';
					if(!empty($option['support'])):
						echo '<a target="_blank" class="swp_support_link swp_four_fourths" href="'.$option['support'].'" title="Click here to learn more about these options.">i</a>';
					endif;
					echo '<div class="sw-checkbox-toggle" status="' . $status . '" field="#' . $key . '"><div class="sw-checkbox-on">' . __( 'ON' , 'social-warfare' ) . '</div><div class="sw-checkbox-off">' . __( 'OFF' , 'social-warfare' ) . '</div></div>';
					echo '<input type="checkbox" class="sw-hidden" name="' . $key . '" id="' . $key . '" ' . $selected . '>';
					echo '</div>';

					// Check for three-fourths-advanced size
				elseif ( $option['size'] == 'two-thirds-advanced' ) :

					echo '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';

					echo '<div class="two-thirds-advanced">';
					echo '<div class="sw-grid sw-col-300"><h2 class="sw-h-label">' . $option['title'] . '</h2><p class="sw-subtext-label">' . $option['description'] . '</p></div>';
					echo '<div class="sw-grid sw-col-300">';
					echo '<div class="sw-checkbox-toggle" status="' . $status . '" field="#' . $key . '"><div class="sw-checkbox-on">' . __( 'ON' , 'social-warfare' ) . '</div><div class="sw-checkbox-off">' . __( 'OFF' , 'social-warfare' ) . '</div></div>';
					echo '<input type="checkbox" class="sw-hidden" name="' . $key . '" id="' . $key . '" ' . $selected . '>';
					echo '</div>';

					if(!empty($option['support'])):
						echo '<div class="sw-grid sw-col-300 sw-fit"><a target="_blank" class="swp_support_link" href="'.$option['support'].'" title="Click here to learn more about these options.">i</a></div>';
					else:
						echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
					endif;
					echo '</div>';
					echo '<div class="sw-clearfix"></div>';
					echo '<div class="sw-premium-blocker"></div>';
					echo '</div>';

					// Check for two-fourths size
				elseif ( $option['size'] == 'two-fourths' ) :

					if ( $last_size == 'two-fourths' ) :
						$last_size = '';
						$fit = 'sw-fit';
					else :
						$last_size = 'two-fourths';
						$fit = '';
					endif;

					echo '<div class="sw-grid sw-col-460 sw-option-container sw-fit ' . $key . '_wrapper" ' . ($option['dep'] ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' premium=' . $option['premium'] . '>';
					echo '<div class="sw-grid sw-col-460"><p class="sw-checkbox-label">' . $option['content'] . '</p></div>';
					echo '<div class="sw-grid sw-col-460 sw-fit">';
					echo '<div class="sw-checkbox-toggle" status="' . $status . '" field="#' . $key . '"><div class="sw-checkbox-on">' . __( 'ON' , 'social-warfare' ) . '</div><div class="sw-checkbox-off">' . __( 'OFF' , 'social-warfare' ) . '</div></div>';
					echo '<input type="checkbox" class="sw-hidden" name="' . $key . '" id="' . $key . '" ' . $selected . '>';
					echo '</div>';
					echo '<div class="sw-premium-blocker"></div>';
					echo '</div>';

					// All others
				else :

					echo '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' premium=' . $option['premium'] . '>';

					if ( isset( $option['header'] ) && $option['header'] == true ) :
						echo '<div class="sw-grid sw-col-300"><h2 class="sw-h-label">' . $option['content'] . '</h2></div>';
					else :
						echo '<div class="sw-grid sw-col-300"><p class="sw-checkbox-label">' . $option['content'] . '</p></div>';
					endif;

					echo '<div class="sw-grid sw-col-300">';
					echo '<div class="sw-checkbox-toggle" status="' . $status . '" field="#' . $key . '"><div class="sw-checkbox-on">' . __( 'ON' , 'social-warfare' ) . '</div><div class="sw-checkbox-off">' . __( 'OFF' , 'social-warfare' ) . '</div></div>';
					echo '<input type="checkbox" class="sw-hidden" name="' . $key . '" id="' . $key . '" ' . $selected . '>';
					echo '</div>';
					if(!empty($option['support'])):
						echo '<div class="sw-grid sw-col-300 sw-fit"><a target="_blank" class="swp_support_link" href="'.$option['support'].'" title="Click here to learn more about these options.">i</a></div>';
					else:
						echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
					endif;

					echo '<div class="sw-premium-blocker"></div>';
					echo '</div>';

				endif;
			endif;

			/**
			 * Input Module
			 */
			if ( $option['type'] == 'input' && isset( $option['size'] ) && $option['size'] == 'two-thirds' ) :

				if ( isset( $swp_user_options[ $key ] ) ) :
					$value = $swp_user_options[ $key ];
				elseif ( isset( $option['default'] ) ):
					$value = $option['default'];
				else :
					$value = '';
				endif;

				echo '<div class="sw-grid sw-col-940 sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';
				echo '<div class="sw-grid sw-col-300"><p class="sw-input-label">' . $option['name'] . '</p></div>';
				echo '<div class="sw-grid sw-col-300"><input name="' . $key . '" type="text" class="sw-admin-input" ' . (isset( $option['default'] ) ? 'placeholder="' . $option['default'] . '"' : '') . ' value="' . $value . '" /></div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
				echo '<div class="sw-premium-blocker"></div>';
				echo '<div class="sw-clearfix"></div>';
				echo '</div>';

			elseif ( $option['type'] == 'input' && isset( $option['size'] ) && $option['size'] == 'two-fourths' ) :

				if ( isset( $swp_user_options[ $key ] ) ) :
					$value = $swp_user_options[ $key ];
				else :
					$value = $option['default'];
				endif;

				if ( $last_size == 'two-fourths' ) :
					$last_size = '';
					$fit = 'sw-fit';
				else :
					$last_size = 'two-fourths';
					$fit = '';
				endif;

				echo '<div class="sw-grid sw-col-460 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';
				echo '<div class="sw-grid sw-col-460"><p class="sw-input-label">' . $option['name'] . '</p></div>';
				echo '<div class="sw-grid sw-col-460 sw-fit"><input name="' . $key . '" type="text" class="sw-admin-input" placeholder="0" value="' . $value . '" /></div>';
				echo '<div class="sw-premium-blocker"></div>';
				echo '</div>';

			elseif ( $option['type'] == 'input' && isset( $option['secondary'] ) ) :

				if ( isset( $swp_user_options[ $option['primary'] ] ) ) :
					$value = $swp_user_options[ $option['primary'] ];
				elseif ( isset( $option['default'] ) ) :
					$value = $option['default'];
				endif;

				if ( isset( $swp_user_options[ $option['secondary'] ] ) ) :
					$value2 = $swp_user_options[ $option['secondary'] ];
				elseif ( isset( $option['default_2'] ) ) :
					$value2 = $option['default_2'];
				endif;

				echo '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';

				echo '<div class="sw-grid sw-col-300"><p class="sw-input-label">' . $option['name'] . '</p></div>';
				echo '<div class="sw-grid sw-col-300"><input name="' . $option['primary'] . '" type="text" class="sw-admin-input" ' . (isset( $option['default'] ) ? 'placeholder="' . $option['default'] . '"' : '') . ' value="' . $value . '" /></div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"><input name="' . $option['secondary'] . '" type="text" class="sw-admin-input" ' . (isset( $option['default_2'] ) ? 'placeholder="' . $option['default_2'] . '"' : '') . ' value="' . $value2 . '" /></div>';

				echo '<div class="sw-premium-blocker"></div>';
				echo '</div>';

			endif;

			/**
			 * Select Module
			 */

			if ( $option['type'] == 'select' && isset( $option['secondary'] ) ) :

				if ( isset( $swp_user_options[ $option['primary'] ] ) ) :
					$value = $swp_user_options[ $option['primary'] ];
				else :
					$value = $option['default'];
				endif;

				if ( isset( $swp_user_options[ $option['secondary'] ] ) ) :
					$value2 = $swp_user_options[ $option['secondary'] ];
				else :
					$value2 = $option['default_2'];
				endif;

				echo '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';

				echo '<div class="sw-grid sw-col-300"><p class="sw-input-label">' . $option['name'] . '</p></div>';
				echo '<div class="sw-grid sw-col-300">';
				echo '<select name="' . $option['primary'] . '">';
				if ( ! isset( $option['default'] ) ) :
					echo '<option value="">Select...</option>';
				endif;
				foreach ( $option['content'] as $select_key => $select_value ) :
					echo '<option value="' . $select_key . '" ' . ($value == $select_key ? 'selected' :'') . '>' . $select_value . '</option>';
				endforeach;
				echo '</select>';
				echo '</div>';
				echo '<div class="sw-grid sw-col-300 sw-fit">';
				echo '<select name="' . $option['secondary'] . '">';
				if ( ! isset( $option['default_2'] ) ) :
					echo '<option value="">Select...</option>';
				endif;
				foreach ( $option['content_2'] as $select_key => $select_value ) :
					echo '<option value="' . $select_key . '" ' . ($value2 == $select_key ? 'selected' :'') . '>' . $select_value . '</option>';
				endforeach;
				echo '</select>';
				echo '</div>';

				echo '<div class="sw-premium-blocker"></div>';
				echo '</div>';

			elseif ( $option['type'] == 'select' && $option['size'] == 'two-fourths' ) :

				if ( isset( $swp_user_options[ $key ] ) ) :
					$value = $swp_user_options[ $key ];
				else :
					$value = $option['default'];
				endif;

				if ( $last_size == 'two-fourths' ) :
					$last_size = '';
					$fit = 'sw-fit';
				else :
					$last_size = 'two-fourths';
					$fit = '';
				endif;

				echo '<div class="sw-grid sw-col-460 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';
				echo '<div class="sw-grid sw-col-460"><p class="sw-checkbox-label">' . $option['name'] . '</p></div>';
				echo '<div class="sw-grid sw-col-460 sw-fit"><select name="' . $key . '">';
				if ( ! isset( $option['default'] ) ) :
					echo '<option value="">Select...</option>';
				endif;
				foreach ( $option['content'] as $select_key => $select_value ) :
					echo '<option value="' . $select_key . '" ' . ($value == $select_key ? 'selected' :'') . '>' . $select_value . '</option>';
				endforeach;
				echo '</select></div>';
				echo '<div class="sw-premium-blocker"></div>';
				echo '</div>';

			elseif ( $option['type'] == 'select' && $option['size'] == 'two-thirds' ) :

				if ( isset( $swp_user_options[ $key ] ) ) :
					$value = $swp_user_options[ $key ];
				else :
					$value = $option['default'];
				endif;

				echo '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';

				echo '<div class="sw-grid sw-col-300"><p class="sw-checkbox-label">' . $option['name'] . '</p></div>';
				echo '<div class="sw-grid sw-col-300"><select name="' . $key . '">';
				if ( ! isset( $option['default'] ) ) :
					echo '<option value="">Select...</option>';
				endif;
				foreach ( $option['content'] as $select_key => $select_value ) :
					echo '<option value="' . $select_key . '" ' . ($value == $select_key ? 'selected' :'') . '>' . $select_value . '</option>';
				endforeach;
				echo '</select></div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';

				echo '<div class="sw-premium-blocker"></div>';
				echo '</div>';

			endif;

			/**
			 * Three-Wide Column Labels Module
			 */

			if ( $option['type'] == 'column_labels' ) :
				if ( $option['columns'] == 3 ) :echo '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';
					echo '<div class="sw-grid sw-col-300"><p class="sw-select-label sw-short sw-no-padding">' . $option['column_1'] . '</p></div>';
					echo '<div class="sw-grid sw-col-300"><p class="sw-select-label sw-short sw-no-padding">' . $option['column_2'] . '</p></div>';
					echo '<div class="sw-grid sw-col-300 sw-fit"><p class="sw-select-label sw-short sw-no-padding">' . $option['column_3'] . '</p></div>';
					echo '<div class="sw-premium-blocker"></div>';
					echo '</div>';
				endif;
			endif;

			/**
			 * Divider Module
			 */

			if ( $option['type'] == 'divider' ) :
				if( !empty($option['premium']) && true === $option['premium'] ):
					echo '<div class="sw-clearfix" premium="1"></div><div class="sw-admin-divider sw-clearfix" premium="1"></div>';
				else:
					echo '<div class="sw-clearfix"></div><div class="sw-admin-divider sw-clearfix"></div>';
				endif;
			endif;

			/**
			 * HTML Module
			 */

			if ( $option['type'] == 'html' ) :

				echo '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';
				echo $option['content'];
				echo '<div class="sw-premium-blocker"></div>';
				echo '<div class="sw-clearfix"></div></div>';
			endif;

			/**
			 * Authentication / Button Module
			 */

			if ( $option['type'] == 'authentication' ) :
				echo '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';
				echo '<div class="sw-grid sw-col-300"><p class="sw-authenticate-label">' . $option['name'] . '</p></div>';
				echo '<div class="sw-grid sw-col-300">';
				if ( isset( $swp_user_options[ $option['dependant'] ] ) && $swp_user_options[ $option['dependant'] ] != '' ) :
					echo '<a class="button sw-green-button" href="' . $option['link'] . '">' . __( 'Connected' , 'social-warfare' ) . '</a>';
				else :
					echo '<a class="button sw-navy-button" href="' . $option['link'] . '">' . __( 'Authenticated' , 'social-warfare' ) . '</a>';
				endif;
				echo '</div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
				echo '<div class="sw-premium-blocker"></div>';
				echo '</div>';
			endif;

			/**
			 * Plugin Registration Module
			 */
			if ( defined('SWPP_PLUGIN_DIR') && 'plugin_registration' === $option['type'] ) :
				require_once SWPP_PLUGIN_DIR . '/functions/admin/options-registration.php';
			endif;

			/**
			 * Twitter Registration Module
			 */

			if ( $option['type'] == 'tweet_counts' ) :

				echo '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';

				// Check for a default value
				if ( isset( $swp_user_options['twitter_shares'] ) && $swp_user_options['twitter_shares'] == true ) :
					$status = 'on';
					$selected = 'checked';
				elseif ( isset( $swp_user_options['twitter_shares'] ) && $swp_user_options['twitter_shares'] == false ) :
					$status = 'off';
					$selected = '';
				else :
					$status = 'off';
					$selected = '';
				endif;

				// Begin Registration Wrapper
				echo '<div class="tweet-count-wrapper" registration="false">';

				// Registration Title
				echo '<h2>' . __( 'Tweet Count Registration' , 'social-warfare' ) . '</h2>';

				// Open the IS NOT Activated container
				echo '<div class="sw-grid sw-col-940 swp_tweets_not_activated" dep="twitter_shares" dep_val="[false]">';

				// The Warning Notice & Instructions
				echo '<p class="sw-subtitle sw-registration-text">' . __( 'In order to allow Social Warfare to track tweet counts, we\'ve partnered with NewShareCounts.com. Follow the steps below to register with NewShareCounts and allow us to track your Twitter shares.' , 'social-warfare' ) . '</p>';
				echo '<p class="sw-subtitle sw-registration-text sw-italic">Step 1: <a style="float:none;" class="button sw-navy-button" href="http://newsharecounts.com" target="_blank">' . __( 'Click here to visit NewShareCounts.com' , 'social-warfare' ) . '</a><br />' . __( 'Step 2: At NewShareCounts.com, Enter your domain and click the "Sign In With Twitter" button.' , 'social-warfare' ) . '<img class="sw-tweet-count-demo" src="' . SWP_PLUGIN_URL . '/images/admin-options-page/new_share_counts.png" /><br />' . __( 'Step 3: Flip the switch below to "ON" and then save changes.' , 'social-warfare' ) . '</p>';

				// Close the IS NOT ACTIVATED container
				echo '</div>';

				// Checkbox Module
				echo '<div class="sw-grid sw-col-300"><p class="sw-checkbox-label">Tweet Counts</p></div>';
				echo '<div class="sw-grid sw-col-300">';
				echo '<div class="sw-checkbox-toggle" status="' . $status . '" field="#twitter_shares"><div class="sw-checkbox-on">' . __( 'ON' , 'social-warfare' ) . '</div><div class="sw-checkbox-off">' . __( 'OFF' , 'social-warfare' ) . '</div></div>';
				echo '<input type="checkbox" class="sw-hidden" name="twitter_shares" id="twitter_shares" ' . $selected . ' />';
				echo '</div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';

				// Close the Registration Wrapper
				echo '</div>';

				echo '<div class="sw-premium-blocker"></div>';
				echo '</div>';

			endif;

			/**
			 * Close the Tab Container
			 */

			// Add a divider element if necessary
			if ( isset( $option['divider'] ) && $option['divider'] == true ) :
				echo '<div class="sw-clearfix"></div><div class="sw-admin-divider sw-clearfix"></div>';
			endif;

			if ( isset( $option['size'] ) && $option['size'] != 'two-fourths' ) :
				$last_size = '';
			endif;

			// Close the loop
		endforeach;

		// Close the tab container
		echo '</div>';

	endforeach;

	echo '</form>';
	echo '</div>';

	/**
	 * System Status Generator
	 */

	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$plugins = get_plugins();
	$pluginList = '';
	foreach ( $plugins as $plugin ) :
		$pluginList .= '<tr><td><b>' . $plugin['Name'] . '</b></td><td>' . $plugin['Version'] . '</td></tr>';
	endforeach;

	if ( function_exists( 'fsockopen' ) ) :
		$fsockopen = '<span style="color:green;">Enabled</span>';
	else :
		$fsockopen = '<span style="color:red;">Disabled</span>';
	endif;

	if ( function_exists( 'curl_version' ) ) :
		$curl_version = curl_version();
		$curl_status = '<span style="color:green;">Enabled: v' . $curl_version['version'] . '</span>';
	else :
		$curl_status = '<span style="color:red;">Disabled</span>';
	endif;

	$theme = wp_get_theme();

	$system_status = '
		<table style="width:100%;">
			<tr><td><h2>Environment Statuses</h2></td><td></td></tr>
			<tr><td><b>Home URL</b></td><td>' . get_home_url() . '</td></tr>
			<tr><td><b>Site URL</b></td><td>' . get_site_url() . '</td></tr>
			<tr><td><b>WordPress Version</b></td><td>' . get_bloginfo( 'version' ) . '</td></tr>
			<tr><td><b>PHP Version</b></td><td>' . phpversion() . '</td></tr>
			<tr><td><b>WP Memory Limit</b></td><td>' . WP_MEMORY_LIMIT . '</td></tr>
			<tr><td><b>Social Warfare Version</b></td><td>' . SWP_VERSION . '</td></tr>
			<tr><td><h2>Connection Statuses</h2></td><td></td></tr>
			<tr><td><b>fsockopen</b></td><td>' . $fsockopen . '</td></tr>
			<tr><td><b>cURL</b></td><td>' . $curl_status . '</td></tr>
			<tr><td><h2>Plugin Statuses</h2></td><td></td></tr>
			<tr><td><b>Theme Name</b></td><td>' . $theme['Name'] . '</td></tr>
			<tr><td><b>Theme Version</b></td><td>' . $theme['Version'] . '</td></tr>
			<tr><td><b>Active Plugins</b></td><td></td></tr>
			<tr><td><b>Number of Active Plugins</b></td><td>' . count( $plugins ) . '</td></tr>
			' . $pluginList . '
		</table>
		';

	/**
	 * The Right Sidebar
	 */

	echo '<div class="sw-admin-sidebar sw-grid sw-col-220 sw-fit">';
	echo '<a href="https://warfareplugins.com/affiliates/" target="_blank"><img src="' . SWP_PLUGIN_URL . '/images/admin-options-page/affiliate-300x150.jpg"></a>';
	echo '<a href="https://warfareplugins.com/support-categories/getting-started/" target="_blank"><img src="' . SWP_PLUGIN_URL . '/images/admin-options-page/starter-guide-300x150.jpg"></a>';
	echo '<a href="https://warfareplugins.com/how-to-measure-social-media-roi-using-google-analytics/" target="_blank"><img src="' . SWP_PLUGIN_URL . '/images/admin-options-page/measure-roi-300x150.jpg"></a>';
	echo '<p class="sw-support-notice sw-italic">' . __( 'Need help? Check out our <a href="https://warfareplugins.com/support/" target="_blank">Knowledgebase.' , 'social-warfare' ) . '</a></p>';
	echo '<p class="sw-support-notice sw-italic">' . __( 'Opening a support ticket? Copy your System Status by clicking the button below.' , 'social-warfare' ) . '</p>';
	echo '<a href="#" class="button sw-blue-button sw-system-status">' . __( 'Get System Status' , 'social-warfare' ) . '</a>';

	// Sytem Status Container
	echo '<div class="sw-clearfix"></div>';
	echo '<div class="system-status-wrapper">';
	echo '<h4>' . __( 'Press Ctrl+C to Copy this information.' , 'social-warfare' ) .'</h4>';
	echo '<div class="system-status-container">' . $system_status . '</div>';

	echo '</div>';

	echo '</div>';

	echo '</div>';

}

add_action( 'wp_ajax_swp_store_settings', 'swp_store_the_settings' );
/**
 * Handle the options save request inside of admin-ajax.php
 *
 * @since  unknown
 * @return void
 */
function swp_store_the_settings() {
	global $swp_user_options;

	if ( ! check_ajax_referer( 'swp_plugin_options_save', 'security', false ) ) {
		wp_send_json_error( esc_html__( 'Security failed.', 'social-warfare' ) );
		die;
	}

	$data = wp_unslash( $_POST );

	if ( empty( $data['settings'] ) ) {
		wp_send_json_error( esc_html__( 'No settings to save.', 'social-warfare' ) );
		die;
	}

	$settings = $data['settings'];

	$options = $swp_user_options;

	unset( $options['newOrderOfIcons']['active'] );
	unset( $options['newOrderOfIcons']['inactive'] );

	// Loop and check for checkbox values, convert them to boolean.
	foreach ( $settings as $key => $value ) {
		if ( 'true' == $value ) {
			$options[ $key ] = true;
		} elseif ( 'false' == $value ) {
			$options[ $key ] = false;
		} else {
			$options[ $key ] = $value;
		}
	}

	swp_update_options( $options );
	echo json_encode($options);

	die;
}
