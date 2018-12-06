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
		parent::__construct( false, $args['name'] );
		add_action( 'widgets_init', array( $this , 'register_self' ) );
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
	* Inherited from WP_Widget.
    *
    * @since  1.0.0
    * @access public
    * @param  array $new_instance Updated values as input by the user in WP_Widget::form()
    * @param  array $old_instance Previously set values.
    * @return array Sanitized array of final values.
    *
    */
	abstract function update( $new_settings = array(), $old_settings  = array());


    /**
    * Echoes the widget content.
    *
    * This sub-class over-rides this function from the parent class to generate the widget code.
    *
    * @since  3.5.0
    * @access public
    * @param  array $args     Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
    *                         These arguments are passed in from the `register_sidebar()` function.
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

			echo $this->generate_widget_HTML();

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
	abstract function generate_form_HTML( $settings );

    /**
     * Creates the markup for a WordPress widget
     *
     * This is the draggable, sortable container which holds the
     * form data. This is how users can add or remove the Widget from sidebar.
     *
     * This method must be defined in child class.
     */
	abstract function generate_widget_HTML( $args, $settings );
}
