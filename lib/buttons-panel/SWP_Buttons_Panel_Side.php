<?php

/**
 * Creates the Panel of share buttons that appear on the side of the screen and
 * floats as the user scrolls the page based on options and settings.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 *
 */
class SWP_Buttons_Panel_Side extends SWP_Buttons_Panel {


	/**
	 * Creates the fully qualified markup for floating button panel.
	 *
	 * @since  3.0.0 | 01 MAR 2018 | Created
	 * @since  3.0.8 | 22 MAY 2018 | Added the $blacklist and in_array conditional.
	 * @param  boolean $echo Whether or not to immediately echo the HTML.
	 * @return string  $html The qualified markup for the panel.
	 *
	 */
	public function render_html( $echo = true ) {
		$blacklist = ['none', 'top', 'bottom'];

		if ( in_array( $this->get_option('float_location'), $blacklist ) || is_preview() ) {
			return '';
		}

		if( is_singular() && 'none' !== $this->get_float_location() ) {

			//* BEGIN Old boilerplate that needs to be refactored.
			$class = "";
			$size = $this->get_option('float_size') * 100;
			$side = $this->get_option('float_location');
			$max_buttons = $this->get_option( 'float_button_count' );

			if( false == $max_buttons || 0 == $max_buttons ) {
				$max_buttons = 5;
			}


			if ( 'none' != $this->get_float_location() ) {
				$float_location =  $this->get_option('float_location');
				$class = "swp_float_" . $this->get_option('float_location');
			}

			// *Get the vertical position
			if ($this->get_option('float_alignment')  ) {
				$class .= " swp_side_" . $this->get_option('float_alignment');
			}

			// *Set button size
			if ( isset($this->options['float_size']) ) {
				$position = $this->get_option('float_alignment');
				$class .= " scale-${size} float-position-${position}-${side}";
			}

			//* END old boilerplate.

			$share_counts = $this->generate_total_shares_html();
			$buttons = $this->generate_individual_buttons_html( (int) $max_buttons );

			$container = '<div class="swp_social_panelSide swp_floating_panel swp_social_panel swp_' . $this->get_option('float_button_shape') .
				$this->get_colors(true) .
				$this->get_option('transition') . '
				' . $class . '
				' . '" data-panel-position="' . $this->get_option('location_post') .
				' scale-' . $this->get_option('float_size') * 100 .
				'" data-float="' . $float_location .
				'" data-count="' . count($this->networks) .
				'" data-float-color="' . $this->get_option('float_background_color') .
				'" data-min-width="' . $this->get_option('float_screen_width') .
				'" data-transition="' . $this->get_option('transition') .
				'" data-float-mobile="' . $this->get_mobile_float_location() .'">';

			if ($this->get_option('totals_alignment') === 'totals_left') {
				$buttons = $share_counts . $buttons;
			} else {
				$buttons .= $share_counts;
			}

			$html = $container . $buttons . '</div>';
			$this->html = $html;

			if ( $echo ) {
				echo $html;
			}

			return $html;
		}

	}
}
