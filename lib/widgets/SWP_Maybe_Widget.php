<?php

/**
*
*/
abstract class SWP_Maybe_Widget extends WP_Widget {

	/**
    * Class constructor.
    *
    * This function really doesn't do much except call the constructor from the
    * parent class that's built into WordPress core.
    *
    *  @since  1.0.0 | 01 JAN 2018 | Created
    *  @param  void
    *  @return void
    *  @access public
    *
    */
	function __construct( $args ) {
		parent::__construct( false, $name = $args['name'] );
		add_action( 'widgets_init', array( $this , 'register_self' ) );
	}


    /**
    * Sets commonly applied attributes.
    *
    * @since  3.0.0 | 08 Feb 2018 | Created
    * @param  string $name The name to be called.
    * @param  string $class The CSS class to be applied.
    * @param  string $value The default value for the element.
    * @return string The string filled with attribute/value pairs.
    *
    */
    private function set_attributes( $name, $class, $value) {
        $attributes = " id=\"{$this->get_field_id($name)}\" class=\"{$class}\" name=\"{$this->get_field_name($name)}\" data-swp-name=\"{$name}\" ";

        if ( isset( $value) ) {
            $attributes .= " value=\"{$value}\" ";
        }

        return $attributes;
    }


	/**
	 * The function that runs on the widgets_init hook and registers
	 * our widget with WordPress.
	 *
	 * @since  3.0.0
	 * @param  none
	 * @return none
	 */
	function register_self() {
		register_widget( $this->key );
	}


	/**
	 * Outputs the Settings Update form
	 *
	 * @since  1.0.0 | 01 JAN 2018 | Created
	 * @since  3.0.0 | 01 MAY 2018 | Refactored using loops, $this->set_attributes(),
	 *                              and added custom thumb sizes
	 * @param  array $instance Current settings.
	 * @return void Output is echoed directly to the screen
	 *
	 */
	function form( $settings ) {
        $defaults = array(
            'title'         => "SW Widget"
            // ...
        );

		$settings = array_merge( $settings, $defaults );

		$form = '<div class="swp_widget">';
		$form .= $this->render_form_HTML( $settings );
		$form .= '</div>';

		echo $form;
	}


    /**
    * Update widget form values.
    *
    * @since  1.0.0
    * @access public
    * @param  array $new_instance Updated values as input by the user in WP_Widget::form()
    * @param  array $old_instance Previously set values.
    * @return array Sanitized array of final values.
    *
    */
	function update( $new_settings, $old_settings ) {
		//* Do checks on $new_settings to make sure data is valid.
		return $old_settings;
	}


    /**
    * Echoes the widget content.
    *
    * This sub-class over-rides this function from the parent class to generate the widget code.
    *
    * @since  1.0.0
    * @since  3.0.0 | 09 FEB 2018 | Refactored and added the $args array output
    * @access public
    * @param  array $args     Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
    * @param  array $instance The settings for the particular instance of the widget.
    *
    */
	function widget( $args, $settings ) {

        if( isset( $args['before_widget'] ) ) {
            echo $args['before_widget'];
        }

		echo '<div class="widget-text swp_widget_box">';


		    if( isset( $args['before_title'] ) ) {
		        echo $args['before_title'];
		    }

			echo '<span class="widgettitle widget-title swp_popular_posts_title">' . $title . '</span>';

		    if( isset( $args['after_title'] ) ) {
		        echo $args['after_title'];
		    }

		echo '</div>';

        if( isset( $args['after_widget'] ) ) {
            echo $args['after_widget'];
        }
	}

    /**
     * Creates the markup for the form (settings) inside the widget.
     *
     * This is how users customize the widget to meet their own needs.
     *
     * This method must be defined in child class.
     */
	abstract function render_form_HTML( $settings );

    /**
     * Creates the markup for a WordPress widget
     *
     * This is the draggable, sortable container which holds the
     * form data. This is how users can add or remove the Widget from sidebar.
     *
     * This method must be defined in child class.
     */
	abstract function render_widget_HTML( $args, $settings );
}
