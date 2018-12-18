<?php
/**
*
*/
abstract class SWP_Widget extends WP_Widget {

	/**
    * Class constructor.
    *
    * This function really doesn't do much except call the constructor from the
    * parent class that's built into WordPress core.
    *
    *  @since  1.0.0 | 01 JAN 2018 | Created
    *
	*  @param string $id_base         Optional Base ID for the widget, lowercase and unique. If left empty,
	*                                a portion of the widget's class name will be used Has to be unique.
	*  @param string $name            Name for the widget displayed on the configuration page.
	*  @param array  $widget_options  Optional. Widget options. See wp_register_sidebar_widget() for information
	*                                on accepted arguments. Default empty array.
	*  @param array  $control_options Optional. Widget control options. See wp_register_widget_control() for
	*                                information on accepted arguments. Default empty array.
	*  @access public
    */
	function __construct( $key, $name, $widget ) {
		$this->data = $widget;
		$this->key = $key;
		$widget['key'] = $key;
		$widget['name'] = $name;

		// call WP_Widget constructor
		parent::__construct( $key, $name, $widget );

  		add_filter( 'swp_widgets', array( $this, 'register_self' ) );
	}

	/**
     * Creates the markup for the <form> (settings) inside the widget.
     *
     * This is the draggable, sortable container which holds the
     * form data. This is how users can add or remove the Widget from sidebar.
     *
     * This method must be defined in child class.
     *
     */
	abstract function generate_form_HTML( $settings );

	/**
     * Creates the frontend display title for the widget.
     * This method must be defined in child class.
     *
     */
	abstract function generate_widget_title( $title );

	/**
     * Creates the frontend display of the main widget contents.
     * This method must be defined in child class.
     *
     */
	abstract function generate_widget_HTML( $settings );


	/**
	 * Hooks into the SWP_Widget_Loder autoregistration.
	 *
	 * @since  3.5.0 | 13 DEC 2018 | Created
	 * @param  array $widgets The list of SWP widgets being registered.
	 * @filter hook Hooks into `swp_widgets`
	 *
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
	 * @param  array $instance Current settings.
	 * @return void Output is echoed directly to the screen
	 *
	 */
	public function form( $settings ) {

		$form = '<div class="swp_widget">';
		$form .= $this->generate_form_HTML( $settings );
		$form .= '</div>';

		echo $form;
	}


    /**
    * Handler for saving new settings.
    *
	* By default will always save changed settings.
	* Please override in child class to filter and sanitize data.
    *
    * @since  1.0.0
    * @access public
    * @param  array $new_instance Updated values as input by the user in WP_Widget::form()
    * @param  array $old_instance Previously set values.
    * @return array The new values to store in the database.
    *
    */
	public function update( $new_settings = array(), $old_settings  = array()) {
		return $new_settings;
	}


    /**
    * Builds the widget, including data passed in from `register_sidebar`
    *
    * Must override WP_Widget->widget().
    *
    * @since  3.5.0
    * @access public
    * @param  array $args     Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
    *                         These arguments are passed in from the `register_sidebar()` function.
	* @param  array $instance The settings for the particular instance of the widget.
    *
    */
	public function widget( $args, $settings ) {
        if( isset( $args['before_widget'] ) ) {
            echo $args['before_widget'];
        }

		echo '<div class="widget-text swp_widget_box">';

		    if( isset( $args['before_title'] ) ) {
		        echo $args['before_title'];
		    }

            echo '<div class="swp-widget-title">'
			     . $this->generate_widget_title( $settings['title'] )
				 . '</div>';

		    if( isset( $args['after_title'] ) ) {
		        echo $args['after_title'];
		    }

			echo '<div class="swp-widget-content">'
			     . $this->generate_widget_HTML( $settings )
				 .'</div>';

		echo '</div>';

        if( isset( $args['after_widget'] ) ) {
            echo $args['after_widget'];
        }
	}
}
