<?php
//Requires the parent class provided by Social Warfare.
if (!class_exists( 'SWP_Widget' ) ) {
	return;
}

/**
 * Creates the HTML for frontend and backend display,
 * and handles widget settings updates.
 *
 * @package   	SocialWarfareFollowWidget
 * @copyright 	Copyright (c) 2019, Warfare Plugins, LLC
 * @license   	GPL-3.0+
 * @since 		1.0.0 | 15 DEC 2018 | Created.
 */
class SWFW_Follow_Widget extends SWP_Widget {


	/**
	 * Instantiates a WordPress Widget by providing $this data to SWP_Widget.
	 *
	 * @since 1.0.0 | 03 DEC 2018 | Created.
	 * @see social-warfare\lib\widgets\SWP_Widget::__construct()
	 * @param none
	 * @return void
	 *
	 */
	function __construct() {
		$key = strtolower( __CLASS__ );
		$name = 'Social Warfare: Follow Widget';
		$widget = array(
			'classname' => $key,
			'description' => 'Increase follower growth for your favorite social networks.',
		);

		parent::__construct( $key, $name, $widget );
		$this->debug();
	}


	/**
	 * Fetches all instances of this widget.
	 *
	 * Since there can be any number of copies of the same widget,
	 * we'll have to check each instance for the networks it uses.
	 *
	 * @since 3.5.0 | 10 JAN 2018 | Created.
	 * @param void
	 * @return array List of all the user-created widgets for the site.
	 *
	 */
	public static function get_widgets() {
		$widgets = get_option( 'widget_swfw_follow_widget', array());

		if (empty($widgets)) {
		   return array();
		}

		foreach( $widgets as $key => $settings ) {
			// This is a wordress field, not a widget.
			if ( '_multiwidget' == $key ) {
				continue;
			}

			if ( is_numeric( $key ) ) {
				// This is an instance of a SWFW_Widget. Keep it in $widgets.
			}
			else {
				// Remove the non-widget from $widgets.
				unset($widgets[$key]);
			}
		}

		return $widgets;
	}


	/**
	 * Creates an input[type=text] which corresponds to the widget's display title.
	 *
	 * @since 1.0.0 | 03 DEC 2018 | Created.
	 * @param string $title The display title for the widget.
	 * @return string Fully qualified HTML to render the input.
	 *
	 */
	function generate_title_input( $title ) {
		$wp_id = $this->get_field_id( 'title' );
		$wp_name = $this->get_field_name( 'title');

		return
<<<TITLE
<div class="swfw-input-field">
	<label for={$wp_id}>Widget Title</label>
	<input type="text" id="$wp_id" name="$wp_name" value="$title" placeholder="$title" />
</div>
TITLE;
	}


	/**
	 * Creates an input[type=select] which corresponds to the button shape.
	 *
	 * @since 1.0.0 | 03 DEC 2018 | Created.
	 * @param string $selection The currently selected button shape.
	 *                          One of ['square', 'block', 'buttons']
	 * @return string Fully qualified HTML to render the select.
	 *
	 */
	function generate_shape_select($selection) {
		$wp_id   = $this->get_field_id( 'shape' );
		$wp_name = $this->get_field_name( 'shape' );

		$opts = array(
			'square'  => 'Square',
			'block'   => 'Block',
			'buttons' => 'Buttons',
			'leaf'    => 'Leaf in the wind',
			'shift'   => 'Shift',
			'pill'   => 'Pills'
		);

		$options_html = '';
		foreach($opts as $key => $name) {
			$selected      = selected($selection, $key, false);
			$options_html .= "<option value='$key' $selected>$name</option>";
		}

		return
<<<SELECT
<div class="swfw-input-field">
	<label for={$wp_id}>Button Shape</label>
	<select id="$wp_id" name="$wp_name" value="$selection">
		$options_html
	</select>
</div>
SELECT;
	}


	/**
	 * Creates an input[type=text] which corresponds to count display settings .
	 *
	 * @since 1.0.0 | 03 DEC 2018 | Created.
	 * @param string $title The display title for the widget.
	 * @return string Fully qualified HTML to render the input.
	 *
	 */
	function generate_minimum_count_input( $minimum_count ) {
		$wp_id   = $this->get_field_id( 'minimum_count' );
		$wp_name = $this->get_field_name( 'minimum_count' );

		return
<<<INPUT
<div class="swfw-input-field">
	<label for={$wp_id}>Minimum Count</label>
	<input type="text" id="$wp_id" name="$wp_name" value="$minimum_count" />
</div>
INPUT;
	}

	/**
	 * Generates the backend display <form>.
	 *
	 * @since 1.0.0 | 03 DEC 2018 | Created.
	 * @param array $settings The settings as previously saved.
	 * @return string Fully qualified HTML to render the form.
	 *
	 */
	function generate_form_HTML( $settings ) {
		$networks = apply_filters( 'swfw_follow_networks', array() );
		$defaults = array(
			'title'	=> 'Follow me on social media',
			'shape'	=> 'square',
			'minimum_count' => 15
		);


		foreach($defaults as $key => $default) {
			if ( !isset( $settings[$key] ) ) {
				$settings[$key] = $default;
			}
		}

		$html = $this->generate_title_input( $settings['title'] );
		$html .= $this->generate_shape_select( $settings['shape'] );
		$html .= $this->generate_minimum_count_input( $settings['minimum_count'] );

		foreach( $networks as $network ) {
			$key         = $network->key . '_username';
			$wp_id       = $this->get_field_id( $key );
			$wp_name     = $this->get_field_name( $key );
			$username    = isset( $settings[$key]) ? $settings[$key] : '';
			$class       = !empty($username) ? 'swfw-active ' : 'swfw-inactive';
			$placeholder = isset( $network->placeholder ) ? $network->placeholder : 'Username';
			$field       =
<<<FIELD
<div class="swfw-follow-field $class">
	<div class="swfw-follow-field-icon swp-$network->key" target="_blank"><i class="sw swp_{$network->key}_icon"></i></div>
	<label for="$wp_id">$network->name</label>
	<input id="$wp_id" name="$wp_name" type="text" placeholder="$placeholder" value="$username"/>
</div>
FIELD;
			 $html .= $field;
		}

		return $html;
	}

	/**
	 * Creates the frontend display title. Required by parent::widget().
	 *
	 * @since 1.0.0 | 03 DEC 2018 | Created.
	 * @param string $title The display title for the widget.
	 * @return string Fully qualified HTML for the display title.
	 *
	 */
	function generate_widget_title( $title ) {
		return "<h4>$title</h4>";
	}


	/**
	* Builds the front end display, including data passed in from `register_sidebar`
	*
	* `register_sidebar` could be called by the theme, which passes in more data.
	*  This extra data is applied in parent::widget().
	*
	* @since  1.0.0 | 03 DEC 2018 | Created.
	* @access public
	* @hook   filter | swfw_follow_networks | Array of SWFW_Follow_Network objects.
	* @param  array $settings The settings as input & saved in the backend.
	* @return string $html Fully qualified HTML to display a Social Follow Widget.
	*
	*/
	function generate_widget_HTML( $settings ) {
		$container_shape = $settings['shape'];
		$style_variations_block = array('block', 'pill', 'shift', 'leaf' );

		if ( in_array( $container_shape, $style_variations_block ) ) {
			$container_shape = 'swfw_block_container';
		}
		else {
			$container_shape = 'swfw_' . $container_shape . '_container';
		}

		$html = "<div class='swfw-follow-container $container_shape'>";

		$networks = apply_filters( 'swfw_follow_networks', array() );
		$buttons = '';

		// Build the network follow button.
		foreach($networks as $network) {
			if ( false == $network->is_active() ) {
				continue;
			}

			$network->set_minimum_count( $settings['minimum_count'] );


			$key = $network->key.'_username';
			$buttons .= $network->generate_frontend_HTML( $settings['shape'] );
		}

		if ( false == SWFW_Cache::is_cache_fresh() ) {
			SWFW_Utility::save_follow_counts();
		}

		$html .= $buttons;
		return $html .= "</div>";
	}


	/**
	* Inhereted from WP_Widget. By default it will always save changed settings.
	*
	* @since  1.0.0
	* @access public
	* @param  array $new_instance Updated values as input by the user in WP_Widget::form()
	* @param  array $old_instance Previously set values.
	* @return array The new values to store in the database.
	*
	*/
	public function update( $new_settings, $old_settings ) {
		foreach ($new_settings as $key => $value) {
			$new_settings[$key] = esc_html( $value );
		}

		return $new_settings;
	}

	public function debug() {
		if ( !isset( $_GET['swfw_debug'] ) ) {
			return;
		}

		$key = $_GET['swfw_debug'];

		switch( $key ) {
			case 'get_count_data' :
				$options = SWFW_Utility::get_options();
				echo "<pre>".var_export($options, 1)."</pre>";
				wp_die();

			case 'get_all_data' :
				$options = SWFW_Utility::get_options();
				$options['usernames'] = $this->get_all_usernames();
				echo "<pre>".var_export($options, 1)."</pre>";
				wp_die();


			case 'reset_count_data' :
				if ( !is_admin() ) {
					break;
				}

				$options = array( 'last_updated' => 0  );
				$updated = update_option( 'swfw_options', $options );
				$message = $updated
						   ? 'Success! Follow Widget options have been reset.'
						   : 'No changes were made to your SWFW options.';
				wp_die($message);
		}
	}

	protected function get_all_usernames() {
		$widgets = SWFW_Follow_Widget::get_widgets();
		$networks = apply_filters( 'swfw_follow_networks', array() );
		$usernames = array();

		foreach ( $networks as $network ) {
			if ( false == $network->is_active() ) {
				continue;
			}

			foreach( $widgets as $key => $settings ) {
				if ( !empty( $settings[$network->key . '_username'] ) ) {
					$usernames[$network->key] = $settings[$network->key . '_username' ];
				}
			}
		}

		return $usernames;
	}
}
