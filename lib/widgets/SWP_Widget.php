<?php

/**
 * A wrapper for WP_Widget.
 *
 * This class allows us finer control over how we create widgets, how the data
 * is stored, and how the product is displayed on the front end.
 *
 * It handles widget registration and loading in WordPress so the child class can
 * focus only on what its intention is.
 */
abstract class SWP_Widget extends WP_Widget {

	/**
	 * The data for the widget.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * The unique classname of the Widget.
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * Applies metadata about this widget to WP_Widget.
	 *
	 * @since  1.0.0 | 01 JAN 2018 | Created
	 * @param string $key The unique classname of the Widget.
	 * @param string $name The display name for WP Admin -> Appearance -> Widgets.
	 * @param array  $widget Fields required by WordPress. At a minimum, ['classname' => '', 'description' => '']
	 * @access public
	 */
	public function __construct( string $key, string $name, array $widget ) {
		$this->data     = $widget;
		$this->key      = $key;
		$widget['key']  = $key;
		$widget['name'] = $name;

		parent::__construct( $key, $name, $widget );
	}


	/**
	 * This is the draggable, sortable container which holds the
	 * form data. This is how users can add or remove the Widget from sidebar.
	 *
	 * This method must be defined in child class.
	 *
	 * @param array $settings The current settings for the widget.
	 */
	abstract public function generate_form_HTML( $settings );

	/**
	 * Creates the frontend display title for the widget.
	 * This method must be defined in child class.
	 *
	 * @param mixed $title The title of the widget.
	 */
	abstract public function generate_widget_title( $title );

	/**
	 * Creates the frontend display of the main widget contents.
	 * This method must be defined in child class.
	 *
	 * @param mixed $settings The settings for the widget.
	 */
	abstract public function generate_widget_HTML( $settings );


	/**
	 * Hooks into the SWP_Widget_Loder autoregistration.
	 *
	 * @since  3.5.0 | 13 DEC 2018 | Created
	 * @param  array $widgets The list of SWP widgets being registered.
	 * @filter hook Hooks into `swp_widgets`
	 */
	public function register_self( $widgets ) {
		$widgets[] = strtolower( $this->id_base );
		return $widgets;
	}


	/**
	 * Builds the display of the widget. The part we care about most.
	 *
	 * Must override WP_Widget->form().
	 *
	 * @since  1.0.0 | 01 JAN 2018 | Created
	 * @since  3.0.0 | 01 MAY 2018 | Refactored using loops, $this->set_attributes(),
	 *                              and added custom thumb sizes
	 * @param  array $settings Current settings.
	 * @return void Output is echoed directly to the screen
	 */
	public function form( $settings ) {

		$form  = '<div class="swp-widget swp_widget">';
		$form .= $this->generate_form_HTML( $settings );
		$form .= '</div>';

		echo esc_html( $form );
	}


	/**
	 * Handler for saving new settings.
	 *
	 * By default will always save changed settings.
	 * Please override in child class to filter and sanitize data.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $new_settings Updated values as input by the user in WP_Widget::form()
	 * @param  array $old_settings Previously set values.
	 * @return array The new values to store in the database.
	 */
	public function update( $new_settings, $old_settings ) {
		return $new_settings;
	}


	/**
	 * Builds the widget, including data passed in from `register_sidebar`
	 *
	 * Must override WP_Widget->widget().
	 *
	 * The theme or another plugin may have added in the 'before_title',
	 * 'after_title', 'before_widget', and 'after_widget' fields in the
	 * $args array by the `register_sidebar()` function.
	 *
	 * Whether or not we use them, we still need to account for them here.
	 *
	 * @since  3.5.0
	 * @access public
	 * @param  array $args Exgra data passed in by register_sidebar().
	 * @param  array $settings The settings for the particular instance of the widget.
	 */
	public function widget( $args, $settings ) {
		if ( isset( $args['before_widget'] ) ) {
			echo esc_html( $args['before_widget'] );
		}
		$title = isset( $settings['title'] ) ? $settings['title'] : '';

		echo '<div class="widget-text swp_widget_box">';

		if ( isset( $args['before_title'] ) ) {
			echo esc_html( $args['before_title'] );
		}

			echo '<div class="swp-widget-title">'
				. esc_html( $this->generate_widget_title( $title ) )
				. '</div>';

		if ( isset( $args['after_title'] ) ) {
			echo esc_html( $args['after_title'] );
		}

			echo '<div class="swp-widget-content">'
				. esc_html( $this->generate_widget_HTML( $settings ) )
				. '</div>';

		echo '</div>';

		if ( isset( $args['after_widget'] ) ) {
			echo esc_html( $args['after_widget'] );
		}
		echo esc_html( $args['after_widget'] );
	}
}
