<?php

/**
*
*/
class SWP_Maybe_Widget extends WP_Widget {

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
	function __construct() {
		parent::__construct( false, $name = 'Social Warfare: Popular Posts' );
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
	 * The magic method used to instantiate this class.
	 *
	 * @since  3.0.0
	 * @param  none
	 * @return none
	 * @access public
	 *
	 */
	public function __construct() {
		add_action( 'widgets_init', array( $this , 'register_widgets' ) );
	}


	/**
	 * The function that runs on the widgets_init hook and registers
	 * our widget with WordPress.
	 *
	 * @since  3.0.0
	 * @param  none
	 * @return none
	 */
	function register_widgets() {
		register_widget( 'swp_popular_posts_widget' );
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
		/**
		 * BUILD OUT THE WIDGET
		 */

        // Output the "Before Widget" content
        if( isset( $args['before_widget'] ) ) :
            echo $args['before_widget'];
        endif;

		// Begin output of the widget html
		echo '<div class="widget-text swp_widget_box" style="' . $styles[ $style ]['wrapper'] . '">';

		// Check if title is set
		if ( $title ) :

            // Output the "Before Title" content
            if( isset( $args['before_title'] ) ) :
                echo $args['before_title'];
            endif;

			echo '<span class="widgettitle widget-title swp_popular_posts_title" style="' . $styles[ $style ]['links'] . '">' . $title . '</span>';

            // Output the "After Title" content
            if( isset( $args['after_title'] ) ) :
                echo $args['after_title'];
            endif;
		endif;

		// If a custom timeframe is not being used....
		if ( $timeframe == 0 ) :

			// Create the arguments for a query without a timeframe
			$swp_args = array(
				'posts_per_page' 	=> $count,
				'post_type' 		=> $post_type,
				'meta_key' 			=> '_' . $network,
				'orderby' 			=> 'meta_value_num',
				'order' 			=> 'DESC',
				'update_post_meta_cache' => false,
				'cache_results'     => false,
				'ignore_sticky_posts' => 1
			);

			// If a custom timeframe is being used....
		else :

			// Create the arguments for a query with a timeframe
			$swp_args = array(
				'posts_per_page' 	=> $count,
				'post_type' 		=> $post_type,
				'meta_key' 			=> '_' . $network,
				'orderby' 			=> 'meta_value_num',
				'order' 			=> 'DESC',
				'update_post_meta_cache' => false,
				'cache_results'     => false,
				'ignore_sticky_posts' => 1,
				'date_query'    	=> array(
					'column'  		=> 'post_date',
					'after'   		=> '- ' . $timeframe . ' days',
				),
			);
		endif;

		// Reset the main query
		wp_reset_postdata();

		// Query and fetch the posts
		$swq = new WP_Query( $swp_args );

		// Begin the loop
		if ( $swq->have_posts() ) :
			$i = 1;
			while ( $swq->have_posts() ) :

				if ( $i <= $count ) :
					$swq->the_post();

					// If we are supposed to show count numbers....
					if ( $showCount == 'true' ) :
						$postID = get_the_ID();
						$shares = get_post_meta( $postID,'_' . $network,true );
						$share_html = '<span class="swp_pop_count">' . SWP_Utility::kilomega( $shares ) . ' ' . $countLabel . '</span>';

					// If we are not supposed to show count numbers
					else :
						$share_html = '';
					endif;

					// If we are supposed to show thumbnails
					if ( $thumbnails == 'true' && has_post_thumbnail() ) :
						$thumbnail_url = wp_get_attachment_image_src( get_post_thumbnail_id() , 'thumbnail' );
						$thumbnail_html = '<a href="' . get_the_permalink() . '">';

						if ($thumb_size === 'custom') :
                            $thumb_width = preg_replace("/[^0-9]/", "", $thumb_width);
                            $thumb_height = preg_replace("/[^0-9]/", "", $thumb_height);

							$thumbnail_html .= '<img style="width:' . $thumb_width . 'px;height:' . $thumb_height . 'px;" class="swp_pop_thumb" src="' . $thumbnail_url[0] . '" title="' . str_replace('"','\'',get_the_title()) . '" alt="' . str_replace('"','\'',get_the_title()) . '" nopin="nopin" />';
						else:
							$thumbnail_html .= '<img style="width:' . $thumb_size . 'px;height:' . $thumb_size . 'px;" class="swp_pop_thumb" src="' . $thumbnail_url[0] . '" title="' . str_replace('"','\'',get_the_title()) . '" alt="' . str_replace('"','\'',get_the_title()) . '" nopin="nopin" />';
						endif;

						$thumbnail_html .= '</a>';

						// If we are not supposed to show thumbnails
					else :
						$thumbnail_html = '';
					endif;

					// Generate the HTML for a link
					$link_html = '<a style="font-size:' . $font_size . '%;' . $styles[ $style ]['links'] . '" class="swp_popularity" href="' . get_the_permalink() . '"><b>' . get_the_title() . '</b>' . $share_html . '</a>';

					// Output the post to the site
					echo '<div class="swp_popular_post">' . $thumbnail_html . '' . $link_html . '</div>';
					echo '<div class="swp_clearfix"></div>';

				endif;

			// End the loop
			endwhile;
		endif;

		// Reset the main query so as not to interfere with other queries on the same page
		wp_reset_postdata();
		echo '</div>';

        // Output the "After Widget" content
        if( isset( $args['after_widget'] ) ) :
            echo $args['after_widget'];
        endif;
	}

    /**
     * Creates the markup for the form (settings) inside the widget.
     *
     * This is how users customize the widget to meet their own needs.
     *
     * @var [type]
     */
	abstract function render_form_HTML() {
		//* Must be defined in child class.
	}

    /**
     * Creates the markup for a WordPress widget
     *
     * This is the draggable, sortable container which holds the
     * form data. This is how users can add or remove the Widget from sidebar.
     *
     */
	abstract function render_widget_HTML() {
		//* Must be defined in child class.

	}
}
