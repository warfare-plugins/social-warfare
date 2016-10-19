<?php
/**
 * Enqueue the Menu Item
 */

// Add the link to the WordPress menu
add_action( 'admin_menu', 'swp_admin_options_page' );
function swp_admin_options_page() {

	// Declare the menu link
	$swp_menu = add_menu_page(
		'Social Warfare',
		'Social Warfare',
		'manage_options',
		'social-warfare',
		'swp_plugin_options',
		swp_PLUGIN_DIR . '/images/admin-options-page/socialwarfare-20x20.png'
	);

	// Hook into the CSS and Javascript Enqueue process for this specific page
	add_action( 'admin_print_styles-' . $swp_menu, 'swp_admin_options_css' );
	add_action( 'admin_print_scripts-' . $swp_menu, 'swp_admin_options_js' );
}

/**
 * Enqueue the Settings Page CSS & Javascript
 */

// Enqueue the Admin Options CSS
function swp_admin_options_css() {
	$suffix = swp_get_suffix();

	wp_enqueue_style(
		'swp_admin_options_css',
		swp_PLUGIN_DIR . "/css/admin-options-page{$suffix}.css",
		array(),
		swp_VERSION
	);
}

// Enqueue the Admin Options JS
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
		swp_PLUGIN_DIR . "/js/admin-options-page{$suffix}.js",
		array( 'jquery' ),
		swp_VERSION
	);
}

/**
 * Build the Settings Page Form
 */

// We'll build the form here
function swp_plugin_options() {

	// Make sure the person accessing this link has proper permissions to access it
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'social-warfare' ) );
	}

	swp_build_options_page();

}

/**
 * A Function to Parse the Array & Builg the Options Page
 */
function swp_build_options_page() {

	$swp_user_options = get_option( 'socialWarfareOptions' );

	// Create all of the options in one giant array
	$swp_options_page = array(

		// A List of Options Page Tabs and Their Titles
		'tabs' => array(
			'links' => array(),
		),

		// A list of options in each of the options tabs
		'options' => array(),
	);

	// Fetch the global options array
	// global $swp_options_page;
	$swp_options_page = apply_filters( 'swp_options' , $swp_options_page );

	/**
	 * Build the header menu
	 */

	// Wrapper for the entire content area
	echo '<div class="sw-header-wrapper">';

	echo '<div class="sw-grid sw-col-940 sw-top-menu">';
	echo '<div class="sw-grid sw-col-700">';
	echo '<img class="sw-header-logo" src="' . swp_PLUGIN_DIR . '/images/admin-options-page/social-warfare-light.png" />';
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

	echo '<div class="sw-admin-wrapper" sw-registered="' . is_swp_registered() . '">';

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
				echo '<h2>' . $option['content'] . '</h2>';
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
				echo '<h3 class="sw-buttons-toggle">Active</h3>';
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
				echo '<h3 class="sw-buttons-toggle">Inactive</h3>';
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

				// The Inactive Buttons
				echo '<div class="sw-grid sw-col-940 sw-premium-buttons sw-option-container" premium="1">';
				echo '<div class="sw-grid sw-col-300">';
				echo '<h3 class="sw-buttons-toggle">Premium</h3>';
				echo '</div>';

				echo '<div class="sw-grid sw-col-620 sw-fit">';
				echo '<div class="sw-inactive sw-buttons-sort">';

				// Loop through the available buttons
				foreach ( $option['content'] as $key => $value ) :
					if ( $option['content'][ $key ]['premium'] == true ) :
						echo '<i class="sw-s sw-' . $key . '-icon" data-network="' . $key . '"></i>';
					endif;
				endforeach;

				echo '</div>';
				echo '</div>';
				echo '<div class="sw-premium-blocker"></div>';
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
					echo '<div class="sw-checkbox-toggle" status="' . $status . '" field="#' . $key . '"><div class="sw-checkbox-on">ON</div><div class="sw-checkbox-off">OFF</div></div>';
					echo '<input type="checkbox" class="sw-hidden" name="' . $key . '" id="' . $key . '" ' . $selected . '>';
					echo '</div>';

					// Check for three-fourths-advanced size
				elseif ( $option['size'] == 'two-thirds-advanced' ) :

					echo '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'dep="' . $option['dep'] . '" dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';

					echo '<div class="two-thirds-advanced">';
					echo '<div class="sw-grid sw-col-300"><h2 class="sw-h-label">' . $option['title'] . '</h2><p class="sw-subtext-label">' . $option['description'] . '</p></div>';
					echo '<div class="sw-grid sw-col-300">';
					echo '<div class="sw-checkbox-toggle" status="' . $status . '" field="#' . $key . '"><div class="sw-checkbox-on">ON</div><div class="sw-checkbox-off">OFF</div></div>';
					echo '<input type="checkbox" class="sw-hidden" name="' . $key . '" id="' . $key . '" ' . $selected . '>';
					echo '</div>';
					echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
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
					echo '<div class="sw-checkbox-toggle" status="' . $status . '" field="#' . $key . '"><div class="sw-checkbox-on">ON</div><div class="sw-checkbox-off">OFF</div></div>';
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
					echo '<div class="sw-checkbox-toggle" status="' . $status . '" field="#' . $key . '"><div class="sw-checkbox-on">ON</div><div class="sw-checkbox-off">OFF</div></div>';
					echo '<input type="checkbox" class="sw-hidden" name="' . $key . '" id="' . $key . '" ' . $selected . '>';
					echo '</div>';
					echo '<div class="sw-grid sw-col-300 sw-fit"></div>';

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
				else :
					$value = $option['default'];
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
				echo '<div class="sw-clearfix"></div><div class="sw-admin-divider sw-clearfix"></div>';
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
					echo '<a class="button sw-green-button" href="' . $option['link'] . '">Connected</a>';
				else :
					echo '<a class="button sw-navy-button" href="' . $option['link'] . '">Authenticate</a>';
				endif;
				echo '</div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
				echo '<div class="sw-premium-blocker"></div>';
				echo '</div>';
			endif;

			/**
			 * Plugin Registration Module
			 */

			if ( $option['type'] == 'plugin_registration' ) :

				if ( _swp_is_debug( 'register' ) ) {
					var_dump( swp_check_registration_status() );
				}

				// Begin Registration Wrapper
				echo '<div class="registration-wrapper" registration="' . (is_swp_registered() ? '1' : '0') . '">';

				// Registration Title
				echo '<h2>Premium Registration</h2>';

				// Open the IS NOT REGISTERED container
				echo '<div class="sw-grid sw-col-940 swp_is_not_registered">';

				// The Warning Notice & Instructions
				echo '<div class="sw-red-notice">This copy of Social Warfare is NOT registered. <a target="_blank" href="https://warfareplugins.com">Click here</a> to purchase a license or add your account info below.</div>';
				echo '<p class="sw-subtitle sw-registration-text">Follow these simple steps to register your Premium License and access all features.</p>';
				echo '<p class="sw-subtitle sw-registration-text sw-italic">Step 1: Enter your email.<br />Step 2: Click the "Register Plugin" button.<br />Step 3: Watch the magic.</p>';

				if ( is_multisite() ) :
					$homeURL = network_site_url();
				else :
					$homeURL = site_url();
				endif;
				$regCode = md5( $homeURL );
				if ( isset( $swp_user_options['emailAddress'] ) ) :
					$email = $swp_user_options['emailAddress'];
				else :
					$email = '';
				endif;

				if ( isset( $swp_user_options['premiumCode'] ) ) :
					$premiumCode = $swp_user_options['premiumCode'];
				else :
					$premiumCode = '';
				endif;

				// Email Input Module
				echo '<div class="sw-grid sw-col-300"><p class="sw-input-label">Email Address</p></div>';
				echo '<div class="sw-grid sw-col-300"><input name="emailAddress" type="text" class="sw-admin-input" placeholder="email@domain.com" value="' . $email . '" /></div>';
				echo '<input name="premiumCode" type="text" class="sw-admin-input sw-hidden" value="' . $premiumCode . '" />';
				echo '<input name="regCode" type="text" class="sw-admin-input sw-hidden" value="' . $regCode . '" />';
				echo '<input type="hidden" class="at-text" name="domain" id="domain" value="' . $homeURL . '" size="30" readonly data-premcode="' . md5( md5( $homeURL ) ) . '">';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';
				echo '<div class="sw-clearfix"></div>';

				// Activate Plugin Module
				echo '<div class="sw-grid sw-col-300"><p class="sw-authenticate-label">Activate Registration</p></div>';
				echo '<div class="sw-grid sw-col-300">';
				echo '<a href="#" id="register-plugin" class="button sw-navy-button">Register Plugin</a>';
				echo '</div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';

				// Close the IS NOT REGISTERED container
				echo '</div>';

				// Open the IS NOT REGISTERED container
				echo '<div class="sw-grid sw-col-940 swp_is_registered">';

				// The Warning Notice & Instructions
				echo '<div class="sw-green-notice">This copy of Social Warfare is registered. Wah-hoo!</div>';
				echo '<p class="sw-subtitle sw-registration-text">To unregister your license click the button below to free it up for use on another domain.</p>';

				// Deactivate Plugin Module
				echo '<div class="sw-grid sw-col-300"><p class="sw-authenticate-label">Deactivate Registration</p></div>';
				echo '<div class="sw-grid sw-col-300">';
				echo '<a href="#" id="unregister-plugin" class="button sw-navy-button">Unregister Plugin</a>';
				echo '</div>';
				echo '<div class="sw-grid sw-col-300 sw-fit"></div>';

				// Close the IS NOT REGISTERED container
				echo '</div>';

				// Close the Registration Wrapper
				echo '</div>';

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
				echo '<h2>Tweet Count Registration</h2>';

				// Open the IS NOT Activated container
				echo '<div class="sw-grid sw-col-940 swp_tweets_not_activated" dep="twitter_shares" dep_val="[false]">';

				// The Warning Notice & Instructions
				echo '<p class="sw-subtitle sw-registration-text">In order to allow Social Warfare to track tweet counts, we\'ve partnered with NewShareCounts.com. Follow the steps below to register with NewShareCounts and allow us to track your Twitter shares.</p>';
				echo '<p class="sw-subtitle sw-registration-text sw-italic">Step 1: <a style="float:none;" class="button sw-navy-button" href="http://newsharecounts.com" target="_blank">Click here to visit NewShareCounts.com</a><br />Step 2: At NewShareCounts.com, Enter your domain and click the "Sign In With Twitter" button.<img class="sw-tweet-count-demo" src="' . swp_PLUGIN_DIR . '/images/admin-options-page/new_share_counts.png" /><br />Step 3: Flip the switch below to "ON" and then save changes.</p>';

				// Close the IS NOT ACTIVATED container
				echo '</div>';

				// Checkbox Module
				echo '<div class="sw-grid sw-col-300"><p class="sw-checkbox-label">Tweet Counts</p></div>';
				echo '<div class="sw-grid sw-col-300">';
				echo '<div class="sw-checkbox-toggle" status="' . $status . '" field="#twitter_shares"><div class="sw-checkbox-on">ON</div><div class="sw-checkbox-off">OFF</div></div>';
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
			<tr><td><b>Social Warfare Version</b></td><td>' . swp_VERSION . '</td></tr>
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
	echo '<a href="https://warfareplugins.com/affiliates/" target="_blank"><img src="' . swp_PLUGIN_DIR . '/images/admin-options-page/affiliate-300x150.jpg"></a>';
	echo '<a href="https://warfareplugins.com/walkthrough/" target="_blank"><img src="' . swp_PLUGIN_DIR . '/images/admin-options-page/starter-guide-300x150.jpg"></a>';
	echo '<a href="https://warfareplugins.com/how-to-measure-social-media-roi-using-google-analytics/" target="_blank"><img src="' . swp_PLUGIN_DIR . '/images/admin-options-page/measure-roi-300x150.jpg"></a>';
	echo '<p class="sw-support-notice sw-italic">Need help? Check out our <a href="https://warfareplugins.com/support/" target="_blank">Knowledgebase.</a></p>';
	echo '<p class="sw-support-notice sw-italic">Opening a support ticket? Copy your System Status by clicking the button below.</p>';
	echo '<a href="#" class="button sw-blue-button sw-system-status">Get System Status</a>';

	// Sytem Status Container
	echo '<div class="sw-clearfix"></div>';
	echo '<div class="system-status-wrapper">';
	echo '<h4>Press Ctrl+C to Copy this information.</h4>';
	echo '<div class="system-status-container">' . $system_status . '</div>';

	echo '</div>';

	echo '</div>';

	echo '</div>';

}

/**
 * A Function to handle the request inside of admin-ajax.php
 */

add_action( 'wp_ajax_swp_store_settings', 'swp_store_the_settings' );
function swp_store_the_settings() {

	// Access the database
	global $wpdb;

	// Fetch the settings from the POST submission
	$settings = $_POST['settings'];

	// Fetch the existing options set
	$options = get_option( 'socialWarfareOptions' );

	unset( $options['newOrderOfIcons']['active'] );
	unset( $options['newOrderOfIcons']['inactive'] );

	// Loop and check for checkbox values, convert them to boolean
	foreach ( $settings as $key => $value ) :
		if ( $value == 'true' ) :
			$options[ $key ] = true;
		elseif ( $value == 'false' ) :
			$options[ $key ] = false;
		else :
			$options[ $key ] = $value;
		endif;
	endforeach;

	// Store the values back in the database
	return update_option( 'socialWarfareOptions',$options );

	// Kill WordPress
	wp_die();
}


/**
 * A Function to store the registration code
 */

add_action( 'wp_ajax_swp_store_registration', 'swp_store_the_registration' );
function swp_store_the_registration() {

	// Access the database
	global $wpdb;

	// Fetch the settings from the POST submission
	$premiumCode = $_POST['premiumCode'];
	$emailAddress = $_POST['email'];

	// Fetch the existing options set
	$options = get_option( 'socialWarfareOptions' );

	// Loop and check for checkbox values, convert them to boolean
	$options['premiumCode'] = $premiumCode;
	$options['emailAddress'] = $emailAddress;

	// Store the values back in the database
	return update_option( 'socialWarfareOptions',$options );

	// Kill WordPress
	wp_die();
}

/**
 * A Function to delete the registration code
 */

add_action( 'wp_ajax_swp_delete_registration', 'swp_delete_the_registration' );
function swp_delete_the_registration() {

	// Access the database
	global $wpdb;

	// Fetch the existing options set
	$options = get_option( 'socialWarfareOptions' );

	$options['premiumCode'] = '';
	$options['emailAddress'] = '';

	// Store the values back in the database
	return update_option( 'socialWarfareOptions',$options );

	// Kill WordPress
	wp_die();

}

add_action( 'wp_ajax_swp_ajax_passthrough', 'swp_ajax_passthrough' );
function swp_ajax_passthrough() {

	// Pass the URL request via cURL
	$response = swp_file_get_contents_curl( urldecode( $_POST['url'] ) );
	// Echo the response to the screen
	echo $response;

	// Kill WordPress
	wp_die();

}
