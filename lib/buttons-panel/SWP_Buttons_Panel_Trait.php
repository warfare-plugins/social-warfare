<?php

/**
 * SWP_Buttons_Panel_Trait
 *
 * The purpose of this trait is to allow access to commonly used methods
 * throughout the various Buttons_Panel classes of the plugin without having to
 * force the extension of an abstract class onto them.
 *
 *     create_panel();
 *
 *     generate_panel_html();
 *     generate_individual_buttons_html();
 *     generate_total_shares_html();
 *
 *     should_panel_display();
 *     should_total_shares_display();
 *
 *     get_alignment();
 *     get_colors();
 *     get_shape();
 *     get_scale();
 *     get_min_width();
 *     get_float_background();
 *     get_option();
 *     get_float_location();
 *     get_mobile_float_location();
 *     get_order_of_icons();
 *     get_ordered_network_objects();
 *     get_key_from_name();
 *
 *
 *
 * @since 3.4.0 | 21 SEP 2018 | Created
 *
 */
trait SWP_Buttons_Panel_Trait {


	/**
 	* Handles whether to echo the HTML or return it as a string.
 	*
 	* @since  3.0.6 | 14 MAY 2018 | Removed the swp-content-locator div.
 	* @param  void
 	* @return mixed null if set to echo, else a string of HTML.
 	*
 	*/
    public function create_panel() {
 	   $this->generate_panel_html();

 	   //* Add the Panel markup based on the location.
 	   switch ($this->location) {
 		   case 'both' :
 			   $content = $this->html . $this->content . $this->html;
 		   break;
 		   case 'above' :
 			   $content = $this->html . $this->content;
 		   break;
 		   case 'below' :
 			   $content = $this->content . $this->html;
 		   break;

 		   case 'none' :
 			   $content = $this->content;
 		   default :
 			   $content = $this->content;
 		   break;
 	   }

 	   $this->content = $content;

 	   if ( isset( $this->args['echo']) && true === $this->args['echo'] ) {
 		   echo $this->content;
 	   }

 	   return $this->content;
    }


   /**
	* Takes a display name and returns the snake_cased key of that name.
	*
	* This is used to convert a network's name, such as Google Plus,
	* to the database-friendly key of google_plus.
	*
	* @since  3.0.0 | 18 APR 2018 | Created
	* @param  string $name The string to convert.
	* @return string The converted string.
	*
	*/
   public function get_key_from_name( $string ) {
	   return preg_replace( '/[\s]+/', '_', strtolower( trim ( $string ) ) );
   }


   /**
	* Tells you true/false if the buttons should print on this page.
	*
	* Each variable is a boolean value. For the buttons to eligible for printing,
	* each of the variables must evaluate to true.
	*
	* $user_settings: Options editable by the Admin user.
	* $desired_conditions: WordPress conditions we require for the buttons.
	* $undesired_conditions: WordPress pages where we do not display the buttons.
	*
	*
	* @return Boolean True if the buttons are okay to print, else false.
	* @since  3.0.8 | 21 MAY 2018 | Added extra condition to check for content
	*                               (for calls to social_warfare()).
	* @since  3.3.3 | 18 SEP 2018 | Added check for in_the_loop().
	* @param  void
	* @return void
	*
	*/
   public function should_panel_display() {


	   /**
		* WordPress requires title and content. This indicates the buttons are
		* called via social_warfare() or via the shortcode.
		*
		*/
	   if ( empty( $this->content ) && !isset( $this->args['content'] )  ) {
		   return true;
	   }

	   $user_settings        = 'none' !== $this->location;
	   $desired_conditions   = is_main_query() && in_the_loop() && get_post_status( $this->post_id ) === 'publish';
	   $undesired_conditions = is_admin() || is_feed() || is_search() || is_attachment();

	   return $user_settings && $desired_conditions && !$undesired_conditions;
   }


   /**
	* A method to get the alignment when scale is set below 100%.
	*
	* @since  3.0.0 | 01 MAR 2018 | Created
	* @param  void
	* @return string A string of the appropriate CSS class to add to the panel.
	*
	*/
   protected function get_alignment() {
	   return ' scale-' . $this->get_option('button_alignment');
   }


   /**
	* A function to get the color states for this buttons panel.
	*
	* All of the buttons contain 3 states: default, hover, and single. The
	* default state is what the buttons look like when not being interacted
	* with. The hover is what all the buttons in the panel look like when
	* the panel is being hovered. The single is what the individual button
	* being hovered will look like.
	*
	* This method handles generating the classes that the CSS can target to
	* ensure that all three of those states work.
	*
	* @since  3.0.0 | 01 MAR 2018 | Created
	* @since  3.3.2 | 13 SEP 2018 | Modified to control float selectors better
	* @param  boolean $float Whether this is a floating panel or not.
	* @return string  The string of CSS classes to be used on the panel.
	*
	*/
   protected function get_colors( $float = false ) {


	   /**
		* If pro was installed, but no longer is installed or activated,
		* then this option won't exist and will return false. If so, then
		* we output the default core color/style classes.
		*
		*/
	   if ( false === $this->get_option( 'default_colors' ) ) {
		   return " swp_default_full_color swp_individual_full_color swp_other_full_color ";
	   }


	   /**
		* If the buttons are the static horizontal buttons (not the float),
		* or if it is the float but we are inheriting the styles from the
		* horizontal buttons, then just output the CSS classes that are used
		* for the horizontal buttons.
		*
		* "float_style_source" on the options page is actually answering
		* the question "Do the floating buttons inherit their colors from
		* the horizontal buttons?" It will be true if they do, and false if
		* they don't.
		*
		*/
	   $prefix = '';


	   /**
		* If this is a set of floating buttons and we are not inheriting
		* the color styles from the static floating buttons, then we need
		* to return the style classes that are specific to the floating
		* buttons being rendered.
		*
		*/
	   if( true === $float && false === $this->options['float_style_source'] ) {
		   $prefix = 'float_';
	   }


	   /**
		*
		* If it's a static, horizontal panel, there is no prefix. If it's
		* a floating panel, there is a prefix. However, the prefix needs
		* to be removed for the CSS class name that is actualy output.
		*
		* So here we fetch the appropriate color options, strip out the
		* "float_" prefix since we don't use that on the actual CSS
		* selector, and then return the CSS classes that will be added to
		* this buttons panel that is being rendered.
		*
		*/
	   $default = str_replace( $prefix, '', $this->get_option( $prefix . 'default_colors' ) );
	   $hover   = str_replace( $prefix, '', $this->get_option( $prefix . 'hover_colors' ) );
	   $single  = str_replace( $prefix, '', $this->get_option( $prefix . 'single_colors' ) );
	   return " swp_default_{$default} swp_other_{$hover} swp_individual_{$single} ";

   }


   /**
	* A method to fetch/determine the shape of the current buttons.
	*
	* @since  3.0.0 | 01 MAR 2018 | Created
	* @param  void
	* @return string The string of the CSS class to be used.
	*
	*/
   protected function get_shape() {
	   $button_shape = $this->get_option( 'button_shape' );

	   //* They have gone from an Addon to Core.
	   if ( false === $button_shape ) {
		   return " swp_flat_fresh ";
	   }

	   return " swp_{$button_shape} ";
   }


   /**
	* A method to fetch/determine the size/scale of the panel.
	*
	* @since  3.0.0 | 01 MAR 2018 | Created
	* @param  void
	* @return string The CSS class to be added to the panel.
	*
	*/
   protected function get_scale() {
	   $button_size = $this->get_option( 'button_size' );

	   //* They have gone from an Addon to Core.
	   if ( false === $button_size ) {
		   return " scale-100 ";
	   }

	   return ' scale-' . $button_size * 100;
   }


   /**
	* A method for getting the minimum width of the buttons panel.
	*
	* @since  3.0.0 | 01 MAR 2018 | Created
	* @param  void
	* @return string The HTML attribute to be added to the buttons panel.
	*
	*/
   protected function get_min_width() {
	   $min_width = $this->get_option( 'float_screen_width' );

	   //* They have gone from an Addon to Core.
	   if ( false === $min_width ) {
		   return 'data-min-width="1100" ';
	   }

	   return " data-min-width='{$min_width}' ";
   }


   /**
	* A method to determin the background color of the floating buttons.
	*
	* @since  3.0.0 | 01 MAR 2018 | Created
	* @param  void
	* @return string The HTML attribute to be added to the buttons panel.
	*
	*/
   protected function get_float_background() {
	   $float_background_color = $this->get_option( 'float_background_color' );

	   //* They have gone from an Addon to Core.
	   if ( false === $float_background_color ) {
		   return " ";
	   }

	   return ' data-float-color="' . $float_background_color;
   }


   /**
	* A function to avoid getting undefined index notices.
	*
	* @since  3.0.5 | 10 MAY 2018 | Created
	* @param  string $key The name of the option.
	* @return mixed       The value of that option.
	*
	*/
   protected function get_option( $key ) {

	   if( isset( $this->options[$key] ) ) {
		   return $this->options[$key];
	   }

	   return SWP_Utility::get_option( $key );
   }


   /**
	* A Method to determine the location of the floating buttons
	*
	* This method was created because we can't just use the option as it is set
	* in the options page. Instead, we must first check that we are on a single.php
	* page and second we must check that the floating buttons toggle is turned on.
	* Then and only then will we check the actual floating location and return it.
	*
	* @since  3.0.0 | 09 MAY 2018 | Created
	* @since  3.0.4 | 09 MAY 2018 | Added check for the global post type on/off toggle.
	* @param  void
	* @return string A string containing the float bar location.
	*
	*/
   public function get_float_location() {
	   $post_on = false;

	   if( is_home() && !is_front_page() || !isset( $this->post_id ) ) {
		   return 'none';
	   }

	   $post_setting = get_post_meta( $this->post_id, 'swp_float_location', true );

	   if( is_array( $post_setting ) ) {
			$post_setting = $post_setting[0];
	   }

	   // If the location is set in the post options, use that.
	   if ( !empty( $post_setting ) && 'default' != $post_setting ) {
		   if( 'off' === $post_setting) {
			   return 'none';
		   }

		   $post_on = true;
	   };

	   if ( $post_on || is_singular() && true === $this->get_option('floating_panel') && 'on' === $this->get_option('float_location_' . $this->post_data['post_type'] ) ) {
		   return $this->get_option('float_location');
	   }

	   return 'none';
   }


   /**
	* A Method to determine the location of the floating buttons on mobile devices
	*
	* This method was created because we can't just use the option as it is set
	* in the options page. Instead, we must first check that we are on a single.php
	* page and second we must check that the floating buttons toggle is turned on.
	* Then and only then will we check the actual floating location and return it.
	*
	* @since  3.0.0 | 09 MAY 2018 | Created
	* @since  3.0.4 | 09 MAY 2018 | Added check for the global post type on/off toggle.
	* @since  3.4.0 | 17 OCT 2018 | Added conditions for front_page, archive, category.
	* @param  void
	* @return string A string containing the float bar location.
	*
	*/
   public function get_mobile_float_location() {
	   $mobile_location = $this->get_option('float_mobile');

       //* Front page, archive, and categories do not have a global float option.
       //* Instead they use options in the post editor (saved in post_meta).
	   if ( is_front_page() || is_archive() || is_category() ) {
		   $float_enabled = get_post_meta( $this->post_data['ID'], 'swp_float_location', true );

		   if ( 'off' != $float_enabled ) {
			   return $mobile_location;
		   }

		   return 'none';
	   }

	   if( is_single() && true == $this->get_option('floating_panel') && 'on' == $this->get_option('float_location_' . $this->post_data['post_type'] ) ) {
		   return $mobile_location;
	   }

	   return 'none';
   }


   /**
	* A method to control the order in which the buttons are output.
	*
	* @since  3.4.0 | 20 SEP 2018 | Created
	* @param  void
	* @return array The array of network names in their proper order.
	*
	*/
   protected function get_order_of_icons() {
	   global $swp_social_networks;
	   $default_buttons = SWP_Utility::get_option( 'order_of_icons' );
	   $order           = array();


	   /**
		* If the icons are set to be manually sorted, then we simply use the
		* order from the options page that the user has set.
		*
		*/
	   if ( SWP_Utility::get_option( 'order_of_icons_method' ) === 'manual' ) {
		   return $default_buttons;
	   }


	   /**
		* Even if it's not set to manual sorting, we will still use the manual
		* order of the buttons if we don't have any share counts by which to
		* process the order dynamically.
		*
		*/
	   if( empty( $this->shares ) || !is_array( $this->shares ) || empty( array_filter( $this->shares ) ) ) {
		   return $default_buttons;
	   }


	   /**
		* If the icons are set to be ordered dynamically, and we passed the
		* check above then we will sort them based on how many shares each
		* network has.
		*
		*/
	   arsort( $this->shares );
	   foreach( $this->shares as $network => $share_count ) {
		   if( $network !== 'total_shares' && in_array( $network, $default_buttons ) ) {
			   $order[$network] = $network;
		   }
	   }
	   $this->options['order_of_icons'] = $order;
	   return $order;

   }


   /**
	* A method to arrange the array of network objects in proper order.
	*
	* @since  3.0.0 | 04 MAY 2018 | Created
	* @since  3.3.0 | 30 AUG 2018 | Renamed from 'order_network_objects' to 'get_ordered_network_objects'
	* @param  array $order An ordered array of network keys.
	* @return array        An ordered array of network objects.
	*
	*/
   protected function get_ordered_network_objects( $order ) {
	   $network_objects = array();

	   if ( empty( $order ) ) :
		   $order = SWP_Utility::get_option( 'order_of_icons' );
	   endif;

	   foreach( $order as $network_key ) {
		   foreach( $this->networks as $key => $network ) :
			   if ( $key === $network_key ) :
				   $network_objects[$key] = $network;
			   endif;
		   endforeach;
	   }

	   return $network_objects;
   }


   /**
	* Render the html for the indivial buttons.
	*
	* @since  3.0.0 | 01 MAR 2018 | Created
	* @param  integer $max_count The maximum number of buttons to display.
	* @return string             The compiled html for the buttons.
	*
	*/
   protected function generate_individual_buttons_html( $max_count = null) {
	   $html = '';
	   $count = 0;

	   foreach( $this->networks as $key => $network ) {
		   if ( isset( $max_count) && $count === $max_count) :
			   return $html;
		   endif;

		   // Pass in some context for this specific panel of buttons
		   $context['shares'] = $this->shares;
		   $context['options'] = $this->options;
		   $context['post_data'] = $this->post_data;
		   $html .= $network->render_HTML( $context );
		   $count++;
	   }
	   return $html;
   }


   /**
	* The Total Shares Count
	*
	* If share counts are active, renders the Total Share Counts HTML.
	*
	* @since  3.0.0 | 18 APR 2018 | Created
	* @since  3.3.2 | 12 SEP 2018 | Moved strtolower to $totals_argument
	* @since  3.4.0 | 20 SEP 2018 | Moved display logic to should_total_shares_display()
	* @param  void
	* @return string $html The fully qualified HTML to display share counts.
	*
	*/
   public function generate_total_shares_html() {

	   // Check if total shares should be rendered or not.
	   if( false === $this->should_total_shares_display() ) {
		   return;
	   }

	   // Render the html for the total shares.
	   $html = '<div class="nc_tweetContainer total_shares total_sharesalt" >';
		   $html .= '<span class="swp_count ">' . SWP_Utility::kilomega( $this->shares['total_shares'] ) . ' <span class="swp_label">' . __( 'Shares','social-warfare' ) . '</span></span>';
	   $html .= '</div>';

	   return $html;
   }


   /**
	* Should the total shares be rendered.
	*
	* @since  3.4.0 | 20 SEP 2018 | Created
	* @param  void
	* @return bool True: show shares; False: don't render them.
	*
	*/
   protected function should_total_shares_display() {


	   /**
		* If total shares are turned off and this isn't a shortcode then we're
		* not going to render any total shares.
		*
		*/
	   if ( false == $this->get_option('total_shares') && false == $this->is_shortcode) {
		   return false;
	   }


	   /**
		* If minimum share counts are enabled and this post hasn't achieved
		* that threshold of shares yet, then we don't show them.
		*
		*/
	   if ( $this->shares['total_shares'] < $this->get_option('minimum_shares') ) {
		   return false;
	   }


	   /**
		* If this is a shortcode, the buttons argument has been specifically
		* passed into the function, and no total/totals were passed in then
		* we do not render the total shares.
		*
		*/
	   $buttons          = isset( $this->args['buttons'] ) ? $this->args['buttons'] : array();
	   $total            = in_array('total', array_map('strtolower', $buttons) );
	   $totals           = in_array('totals', array_map('strtolower', $buttons) );
	   $shortcode_totals = ( empty( $buttons ) || $total || $totals );

	   if ( $this->is_shortcode && !$shortcode_totals ) {
		   return false;
	   }

	   // If none of the flags above get caught, return true.
	   return true;
   }


   /**
	* The method that renderes the button panel HTML.
	*
	* @since  3.0.0 | 25 APR 2018 | Created
	* @since  3.0.3 | 09 MAY 2018 | Switched the button locations to use the
	*                               location methods instead of the raw options value.
	* @since  3.0.6 | 15 MAY 2018 | Uses $this->get_option() method to prevent undefined index error.
	* @since  3.3.1 | 13 SEP 2018 | Added get_alignment()
	* @param  boolean $echo Echo's the content or returns it if false.
	* @return string        The string of HTML.
	*
	*/
   public function generate_panel_html( $echo = false ) {
	   if ( !isset( $this->post_id ) || is_preview() ) :
		   return;
	   endif;

	   $style = "";
	   $float_mobile = SWP_Utility::get_option( 'float_mobile');

	   if ( !$this->should_panel_display() && ( 'top' == $float_mobile || 'bottom' == $float_mobile ) ) :
			if ( true !== $this->get_option( 'floating_panel' ) ) :
				return $this->content;
			endif;

			$style = ' opacity: 0; ';
		endif;

	   // Create the HTML Buttons panel wrapper
	   $container = '<div class="swp_social_panel swp_horizontal_panel ' .
		   $this->get_shape() .
		   $this->get_colors() .
		   $this->get_scale() .
		   $this->get_alignment() .
		   '" ' . // end CSS classes
		   $this->get_min_width() .
		   $this->get_float_background() .
		   //* These below two data-attribute methods are inconsistent. But they
		   //* already existed and are used elsewhere, so I'm not touching them.
		   '" data-float="' . $this->get_float_location() . '"' .
		   ' data-float-mobile="' . $this->get_mobile_float_location() . '"' .
		   ' style="' . $style . '" >';

		   $total_shares_html = $this->generate_total_shares_html();
		   $buttons = $this->generate_individual_buttons_html();

		   if ($this->get_option('totals_alignment') === 'totals_left') :
			   $buttons = $total_shares_html . $buttons;
		   else:
			   $buttons .= $total_shares_html;
		   endif;

	   $html = $container . $buttons . '</div>';
	   $this->html = $html;
	   if ( $echo ) :
		   if( true == SWP_Utility::debug('buttons_output')):
			   echo 'Echoing, not returning. In SWP_Buttons_Panel on line '.__LINE__;
		   endif;
		   echo $html;
	   endif;

	   return $html;
   }
}
