<?php

/**
 * Popular Posts Widget
 *
 * Allows users to show most popular posts by share count.
 * Settings include widget title, network selections, thumbnail options, styles, and more.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | Created | Unknown
 * @since     3.0.0 | Updated | 07 Feb 2018 | Adding custom thumbnail sizes
 * @since     3.0.0 | Updated | 08 Feb 2018 | Refactored code from procedural style to loops. Added set_attributes().
 * @since     3.0.0 | Updated | 09 Feb 2018 | Added the post type selector
 */
class SWP_Popular_Posts_Widget extends WP_Widget {

	/**
	 * Class constructor.
	 *
	 * This function really doesn't do much except call the constructor from the
	 * parent class that's built into WordPress core.
	 *
	 *  @since  1.0.0 | 01 JAN 2018 | Created
	 *  @return void
	 *  @access public
	 */
	public function __construct() {
		parent::__construct( false, $name = 'Social Warfare: Popular Posts' );
	}


	/**
	 * Sets commonly applied attributes.
	 *
	 * @since  3.0.0 | 08 Feb 2018 | Created
	 * @param  string $name The name to be called.
	 * @param  string $css_class The CSS class to be applied.
	 * @param  string $value The default value for the element.
	 * @return string The string filled with attribute/value pairs.
	 */
	private function set_attributes( $name, $css_class, $value ) {
		$attributes = " id=\"{$this->get_field_id($name)}\" class=\"{$css_class}\" name=\"{$this->get_field_name($name)}\" data-swp-name=\"{$name}\" ";

		if ( isset( $value ) ) {
			$attributes .= " value=\"{$value}\" ";
		}

		return $attributes;
	}


	/**
	 * Outputs the Settings Update form
	 *
	 * @since  1.0.0 | 01 JAN 2018 | Created
	 * @since  3.0.0 | 01 MAY 2018 | Refactored using loops, $this->set_attributes(),
	 *                              and added custom thumb sizes
	 * @param  array $instance Current settings.
	 * @return void Output is echoed directly to the screen
	 */
	public function form( $instance ) {

		$defaults = array(
			'title'        => 'Popular Posts',
			'count'        => '10',
			'timeframe'    => '0',
			'post_type'    => 'post',
			'network'      => 'total_shares',
			'showCount'    => 'true',
			'countLabel'   => 'Total Shares',
			'style'        => 'style_01',
			'thumbnails'   => 'true',
			'thumb_size'   => '100',
			'thumb_width'  => 'thumb_size',
			'thumb_height' => 'thumb_size',
			'font_size'    => '100',
			'custom_bg'    => '#ffffff',
			'custom_link'  => '#000000',
		);

		/**
		 * If the user set their value for $var, set it to that. Otherwise set
		 * it to the default display value.
		 */
		foreach ( $defaults as $var => $display ) :
			if ( isset( $instance[ $var ] ) ) {
				$$var = esc_attr( $instance[ $var ] );
			} else {
				$$var = $display;
			}
		endforeach;

		// Fetch the Social Warfare Options
		global $swp_user_options;
		$options = $swp_user_options;

		// Fetch the networks that are active on this blog
		$available_networks = $options['order_of_icons'];

		// Build the Widget Form
		$form = '<div class="swp_popular_post_options">';

		// The Widget Title Field
		$form .= '<p class="title">';
		$form .= '<label for="' . $this->get_field_id( 'title' ) . '">Widget Title</label>';
		$form .= "<input type=\"text\" {$this->set_attributes("title", "widefat", $title)} />";
		$form .= '</p>';

		// Number of Posts to Display Field
		$form .= '<p class="count">';
		$form .= '<label for="' . $this->get_field_id( 'count' ) . '">How many posts would you like to display?</label>';
		$form .= "<input type=\"text\" {$this->set_attributes("count", "widefat", $count)} />";
		$form .= '</p>';

		// Age of the posts to display field
		$form .= '<p class="timeframe">';
		$form .= '<label for="' . $this->get_field_id( 'timeframe' ) . '">What is maximum age of a post (in days) that you would like to include (0 = Unlimited)?</label>';
		$form .= "<input type=\"number\" {$this->set_attributes("timeframe", "widefat", $timeframe)} min=\"0\" />";
		$form .= '</p>';

		// Get the public post Types
		$post_types = SWP_Utility::get_post_types();

		if ( ! empty( $post_types ) ) :

			// Display the share count toggle field
			$form .= '<p class="post_type">';
			$form .= '<label for="' . $this->get_field_id( 'post_type' ) . '">What post type would you like to display?</label>';
			$form .= "<select {$this->set_attributes( 'post_type', 'widefat', null )}>";

			// Loop through the Custom Post Type Options
			foreach ( $post_types as $this_post_type ) :
				$form .= '<option value="' . $this_post_type . '" ' . selected( $this_post_type, $post_type, false ) . '>' . ucfirst( $this_post_type ) . '</option>';
			endforeach;

			$form .= '</select>';
			$form .= '</p>';

		endif;

		// Which networks to use as the basis field
		$form .= '<p class="network">';
		$form .= '<label for="' . $this->get_field_id( 'network' ) . '">Which network would you like to base your posts\' popularity on?</label>';
		$form .= "<select {$this->set_attributes('network', 'widefat', null)}>";
		$form .= "<option value=\"total_shares\" {selected($network, 'total_shares', false)}>All Networks</option>";

		foreach ( $available_networks as $key => $value ) :

			$opt      = $key . '_shares';
			$selected = selected( $network, $opt, false );
			$net      = ucfirst( $value );

			$form .= "<option value=\"$opt\" $selected>$net</option>";

		endforeach;

		$form .= '</select>';
		$form .= '</p>';

		// Display the share count toggle field
		$form .= '<p class="showCount">';
		$form .= '<label for="' . $this->get_field_id( 'showCount' ) . '">Would you like to show the count?</label>';
		$form .= "<select {$this->set_attributes( 'showCount', 'widefat', null )}>";
		$form .= '<option value="true" ' . selected( $show_count, 'true', false ) . '>Yes</option>';
		$form .= '<option value="false" ' . selected( $show_count, 'false', false ) . '>No</option>';
		$form .= '</select>';
		$form .= '</p>';

		// Count Label Field
		$form .= '<p ' . ( true !== $show_count ? 'style="display:none;"' : '' ) . ' data-dep="showCount" data-dep_val=\'' . wp_json_encode( array( true ) ) . '\' class="countLabel">';
		$form .= '<label for="' . $this->get_field_id( 'countLabel' ) . '">Count Number Label</label>';
		$form .= "<input type=\"text\" {$this->set_attributes( 'countLabel', 'widefat', $count_label)} />";
		$form .= '</p>';

		// Post thumbnails toggle field
		$form .= '<p class="thumbnails">';
		$form .= '<label for="' . $this->get_field_id( 'thumbnails' ) . '">Would you like to display thumbnails?</label>';
		$form .= "<select {$this->set_attributes( 'thumbnails', 'widefat', null)} >";
		$form .= '<option value="true" ' . selected( $thumbnails, 'true', false ) . '>Yes</option>';
		$form .= '<option value="false" ' . selected( $thumbnails, 'false', false ) . '>No</option>';
		$form .= '</select>';
		$form .= '</p>';

		// Thumbnails size field
		$form .= '<p ' . ( true !== $thumbnails ? 'style="display:none;"' : '' ) . ' data-dep="thumbnails" data-dep_val=\'' . wp_json_encode( array( true ) ) . '\' class="thumb_size">';
		$form .= '<label for="' . $this->get_field_id( 'thumb_size' ) . '">What size would you like your thumbnails?</label>';
		$form .= "<select {$this->set_attributes( 'thumb_size', 'widefat', null )} >";

		for ( $i = 5; $i < 16; $i++ ) {
			$val      = $i * 10;
			$selected = selected( $thumb_size, $val, false );
			$form    .= "<option value=\"$val\" $selected>{$val}px</option>";
		}

		$form .= '<option value="custom" ' . selected( $thumb_size, 'custom', false ) . '>Custom</option>';
		$form .= '</select>';
		$form .= '</p>';

		// If $thumb_size, show the custom height/width fields.
		$form .= '<p ' . ( 'custom' !== $thumb_size ? 'style="display:none;"' : '' ) . ' data-dep="thumb_size" data-dep_val=\'' . wp_json_encode( array( 'custom' ) ) . '\' class="custom_thumb_size">';
		$form .= '<label for="' . $this->get_field_id( 'thumb_width' ) . '">Thumbnail width</label>';
		$form .= "<input type=\"number\" {$this->set_attributes( 'thumb_width', 'widefat', $thumb_width)} />";
		$form .= '</p>';

		$form .= '<p ' . ( 'custom' !== $thumb_size ? 'style="display:none;"' : '' ) . ' data-dep="thumb_size" data-dep_val=\'' . wp_json_encode( array( 'custom' ) ) . '\' class="custom_thumb_size">';
		$form .= '<label for="' . $this->get_field_id( 'thumb_height' ) . '">Thumbnail height</label>';
		$form .= "<input type=\"number\" {$this->set_attributes( 'thumb_height', 'widefat', $thumb_height)} />";
		$form .= '</p>';

		// Font size field
		$form .= '<p class="font_size">';
		$form .= '<label for="' . $this->get_field_id( 'font_size' ) . '">What size would you like the font?</label>';
		$form .= "<select {$this->set_attributes( 'font_size', 'widefat', null )}>";

		for ( $i = 5; $i < 16; $i++ ) {
			$val      = $i * 10;
			$selected = selected( $font_size, $val, false );
			$form    .= "<option value=\"$val\" $selected>{$val}%</option>";
		}

		$form .= '</select>';
		$form .= '</p>';

		// Color Scheme Field
		$ctt_styles = array(
			'Vanilla',
			'Inspired by Twitter',
			'Inspired by Facebook',
			'Inspired by Google Plus',
			"Don't Stop Believin'",
			'Thunderstruck',
			"Livin' On A Prayer",
		);

		$form .= '<p class="style">';
		$form .= '<label for="' . $this->get_field_id( 'style' ) . '">Which color scheme would you like to use?</label>';
		$form .= "<select {$this->set_attributes( 'style', 'widefat', null )}>";

		foreach ( $ctt_styles as $idx => $ctt_style ) :

			// *Accounting for 0 offset
			++$idx;

			if ( $idx < 10 ) :
				$val = "style_0{$idx}";
			else :
				$val = "style_{$idx}";
			endif;

			$selected = selected( $val, $style, false );
			$form    .= "<option value=\"$val\" $selected>{$ctt_style}</option>";

		endforeach;

		$form .= '<option value="custom" ' . selected( $style, 'custom', false ) . '>Custom</option>';
		$form .= '</select>';
		$form .= '</p>';

		// Custom Background Color Field
		$form .= '<p ' . ( 'custom' !== $style ? 'style="display:none;"' : '' ) . ' data-dep="style" data-dep_val=\'' . wp_json_encode( array( 'custom' ) ) . '\' class="custom_bg">';
		$form .= '<label for="' . $this->get_field_id( 'custom_bg' ) . '">Custom Background Color</label>';
		$form .= "<input type=\"text\" {$this->set_attributes( 'custom_bg', 'widefat', $custom_bg )} />";
		$form .= '</p>';

		// Custom Link Color Field
		$form .= '<p ' . ( 'custom' !== $style ? 'style="display:none;"' : '' ) . ' data-dep="style" data-dep_val=\'' . wp_json_encode( array( 'custom' ) ) . '\' class="custom_link">';
		$form .= '<label for="' . $this->get_field_id( 'custom_link' ) . '">Custom Link Color</label>';
		$form .= "<input type=\"text\" {$this->set_attributes( 'custom_link', 'widefat', $custom_link )} />";
		$form .= '</p>';

		// Close the Div
		$form .= '</div>';

		// Output the form fields
		echo wp_kses_post( $form );
	}


	/**
	 * Update widget form values.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $new_instance Updated values as input by the user in WP_Widget::form()
	 * @param  array $old_instance Previously set values.
	 * @return array Sanitized array of final values.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Fetch the values from the form
		$instance['title']        = wp_strip_all_tags( $new_instance['title'] );
		$instance['count']        = wp_strip_all_tags( $new_instance['count'] );
		$instance['timeframe']    = wp_strip_all_tags( $new_instance['timeframe'] );
		$instance['post_type']    = wp_strip_all_tags( $new_instance['post_type'] );
		$instance['network']      = wp_strip_all_tags( $new_instance['network'] );
		$instance['showCount']    = wp_strip_all_tags( $new_instance['showCount'] );
		$instance['countLabel']   = wp_strip_all_tags( $new_instance['countLabel'] );
		$instance['style']        = wp_strip_all_tags( $new_instance['style'] );
		$instance['thumbnails']   = wp_strip_all_tags( $new_instance['thumbnails'] );
		$instance['thumb_size']   = wp_strip_all_tags( $new_instance['thumb_size'] );
		$instance['thumb_width']  = wp_strip_all_tags( $new_instance['thumb_width'] );
		$instance['thumb_height'] = wp_strip_all_tags( $new_instance['thumb_height'] );
		$instance['font_size']    = wp_strip_all_tags( $new_instance['font_size'] );
		$instance['custom_bg']    = wp_strip_all_tags( $new_instance['custom_bg'] );
		$instance['custom_link']  = wp_strip_all_tags( $new_instance['custom_link'] );

		return $instance;
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
	 */
	public function widget( $args, $instance ) {
		// Assign values from $args
		$before_widget = isset( $args['before_widget'] ) ? $args['before_widget'] : '';
		$after_widget  = isset( $args['after_widget'] ) ? $args['after_widget'] : '';
		$before_title  = isset( $args['before_title'] ) ? $args['before_title'] : '';
		$after_title   = isset( $args['after_title'] ) ? $args['after_title'] : '';

		// Fetch the field values from the form
		$title        = isset( $instance['title'] ) ? $instance['title'] : 'Popular Posts';
		$count        = isset( $instance['count'] ) ? $instance['count'] : '10';
		$timeframe    = isset( $instance['timeframe'] ) ? $instance['timeframe'] : '0';
		$post_type    = isset( $instance['post_type'] ) ? $instance['post_type'] : 'post';
		$network      = isset( $instance['network'] ) ? $instance['network'] : 'total_shares';
		$show_count   = isset( $instance['showCount'] ) ? $instance['showCount'] : 'true';
		$count_label  = isset( $instance['countLabel'] ) ? $instance['countLabel'] : 'Total Shares';
		$style        = isset( $instance['style'] ) ? $instance['style'] : 'style_01';
		$thumbnails   = isset( $instance['thumbnails'] ) ? $instance['thumbnails'] : 'true';
		$thumb_size   = isset( $instance['thumb_size'] ) ? $instance['thumb_size'] : '100';
		$font_size    = isset( $instance['font_size'] ) ? $instance['font_size'] : '100';
		$custom_bg    = isset( $instance['custom_bg'] ) ? $instance['custom_bg'] : '#ffffff';
		$thumb_width  = isset( $instance['thumb_width'] ) ? $instance['thumb_width'] : $thumb_size;
		$thumb_height = isset( $instance['thumb_height'] ) ? $instance['thumb_height'] : $thumb_size;
		$custom_link  = isset( $instance['custom_link'] ) ? $instance['custom_link'] : '#000000';

		// Correct the previous style with the new version if it is present on the site
		if ( 'first_style' === $style || 'second_style' === $style ) :
			$style = 'style_01';
		endif;

		// Define the array of background links and clors.

		// Vanilla (No Styling)
		$styles['style_01']['wrapper'] = 'background:transparent;';
		$styles['style_01']['links']   = '';

		// Inspired by Twitter
		$styles['style_02']['wrapper'] = 'padding:15px;background:#429cd6;';
		$styles['style_02']['links']   = 'color:#ffffff;';

		// Inspired by Facebook
		$styles['style_03']['wrapper'] = 'padding:15px;background:#3a589e;';
		$styles['style_03']['links']   = 'color:#ffffff;';

		// Inspired by Google Plus
		$styles['style_04']['wrapper'] = 'padding:15px;background:#df4b37;';
		$styles['style_04']['links']   = 'color:#ffffff;';

		// Inspired by LinkedIn
		$styles['style_05']['wrapper'] = 'padding:15px;background:#0d77b7;';
		$styles['style_05']['links']   = 'color:#ffffff;';

		// Inspired by Pinterest
		$styles['style_06']['wrapper'] = 'padding:15px;background:#cd2029;';
		$styles['style_06']['links']   = 'color:#ffffff;';

		// Don't Stop Believin'
		$styles['style_07']['wrapper'] = 'padding:15px;background:#333333;';
		$styles['style_07']['links']   = 'color:#ffffff;';

		// Thunderstruck
		$styles['style_08']['wrapper'] = 'padding:15px;background:#30394F;';
		$styles['style_08']['links']   = 'color:#ffffff;';

		// Livin' On A Prayer
		$styles['style_09']['wrapper'] = 'padding:15px;background:#EEEEEE;';
		$styles['style_09']['links']   = 'color:#30394F;';

		// Custom
		$styles['custom']['wrapper'] = 'padding:15px;background:' . $custom_bg . ';';
		$styles['custom']['links']   = 'color:' . $custom_link . ';';

		/**
		 * BUILD OUT THE WIDGET
		 */

		// Output the "Before Widget" content
		if ( isset( $args['before_widget'] ) ) :
			echo esc_html( $args['before_widget'] );
		endif;

		// Begin output of the widget html
		echo '<div class="widget-text swp_widget_box" style="' . esc_attr( $styles[ $style ]['wrapper'] ) . '">';

		// Check if title is set
		if ( $title ) :

			// Output the "Before Title" content
			if ( isset( $args['before_title'] ) ) :
				echo esc_html( $args['before_title'] );
			endif;

			echo '<span class="widgettitle widget-title swp_popular_posts_title" style="' . esc_attr( $styles[ $style ]['links'] ) . '">' . esc_html( $title ) . '</span>';

			// Output the "After Title" content
			if ( isset( $args['after_title'] ) ) :
				echo esc_html( $args['after_title'] );
			endif;
		endif;

		$swp_args = array(
			'posts_per_page'         => $count,
			'post_type'              => $post_type,
			'meta_key'               => '_' . $network, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'                => 'meta_value_num',
			'order'                  => 'DESC',
			'cache_results'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'ignore_sticky_posts'    => 1,
		);

		if ( 0 !== $timeframe ) {
			$swp_args['date_query'] = array(
				'column' => 'post_date',
				'after'  => '- ' . $timeframe . ' days',
			);
		}

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
					if ( 'true' === $show_count ) :
						$post_id    = get_the_ID();
						$shares     = get_post_meta( $post_id, '_' . $network, true );
						$share_html = '<span class="swp_pop_count">' . esc_html( SWP_Utility::kilomega( $shares ) ) . ' ' . esc_html( $count_label ) . '</span>';
					else :
						$share_html = '';
					endif;

					// If we are supposed to show thumbnails
					if ( 'true' === $thumbnails && has_post_thumbnail() ) :
						$thumbnail_url  = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail' );
						$thumbnail_html = '<a href="' . get_the_permalink() . '">';

						if ( 'custom' === $thumb_size ) :
							$thumb_width  = preg_replace( '/[^0-9]/', '', $thumb_width );
							$thumb_height = preg_replace( '/[^0-9]/', '', $thumb_height );

							$thumbnail_html .= '<img style="width:' . esc_attr( $thumb_width ) . 'px;height:' . esc_attr( $thumb_height ) . 'px;" class="swp_pop_thumb" src="' . esc_url( $thumbnail_url[0] ) . '" title="' . esc_attr( str_replace( '"', '\'', get_the_title() ) ) . '" alt="' . esc_attr( str_replace( '"', '\'', get_the_title() ) ) . '" ' . SWP_AMP::hide_if_amp( 'nopin="nopin"' ) . ' />';
						else :
							$thumbnail_html .= '<img style="width:' . esc_attr( $thumb_size ) . 'px;height:' . esc_attr( $thumb_size ) . 'px;" class="swp_pop_thumb" src="' . esc_url( $thumbnail_url[0] ) . '" title="' . esc_attr( str_replace( '"', '\'', get_the_title() ) ) . '" alt="' . esc_attr( str_replace( '"', '\'', get_the_title() ) ) . '" ' . SWP_AMP::hide_if_amp( 'nopin="nopin"' ) . '  />';
						endif;

						$thumbnail_html .= '</a>';

						// If we are not supposed to show thumbnails
					else :
						$thumbnail_html = '';
					endif;

					// Generate the HTML for a link
					$link_html = '<a style="font-size:' . esc_attr( $font_size ) . '%;' . $styles[ $style ]['links'] . '" class="swp_popularity" href="' . esc_url( get_the_permalink() ) . '"><b>' . esc_html( get_the_title() ) . '</b>' . $share_html . '</a>';

					// Output the post to the site
					echo '<div class="swp_popular_post">' . wp_kses_post( $thumbnail_html ) . '' . wp_kses_post( $link_html ) . '</div>';
					echo '<div class="swp_clearfix"></div>';

				endif;

				// End the loop
			endwhile;
		endif;

		// Reset the main query so as not to interfere with other queries on the same page
		wp_reset_postdata();
		echo '</div>';

		// Output the "After Widget" content
		if ( isset( $args['after_widget'] ) ) :
			echo esc_html( $args['after_widget'] );
		endif;
	}
}
