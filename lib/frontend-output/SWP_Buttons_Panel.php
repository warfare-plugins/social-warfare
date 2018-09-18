<?php
/**
 * Creates the Panel of share buttons based on options and settings.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */
class SWP_Buttons_Panel {
	/**
	 * Options
	 *
	 * We're using a local property to clone in the global $swp_user_options array. As a local options
	 * we and other developers accessing this object can use getters and setters to change any options
	 * possibly imaginable without affecting the global options.
	 *
	 * @var array
	 *
	 */
    public $options = array();


	/**
	 * The Post ID
	 *
	 * @var int
	 *
	 */
	public $post_id;


	/**
	 * The location of the buttons in relation to the content.
	 *
	 * @var string above | below | both | none
	 *
	 */
	public $location = 'above';


	/**
	 * Arguments
	 *
	 * A temporary property used to store and access any arguments passed into
	 * the constructor. These will then be processed into the other properties
	 * as possible.
	 *
	 * @var array $args;
	 *
	 */
	public $args = array();


	/**
	 * The Content
	 *
	 * The WordPress content to which we are going to append the HTML of these buttons.
	 *
	 * @var string $content;
	 *
	 */
	public $content = '';


	/**
     * The fully qualified HTML for the Buttons Panel.
     *
     * @var string $html;
     */
    public $html = '';


	/**
     * The array of active buttons for $this Social Panel.
     *
     * @var array $active_buttons;
     */
    public $active_buttons = array();


	/**
     * The sum of share counts across active networks.
     *
     * @var integer $total_shares;
     */
    public $total_shares = 0;

    /**
     * The Construct Method
 	 *
	 * @param optional array $args The arguments passed in via shortcode.
     * @since  3.0.0 | 01 MAR 2018 | Created
	 * @since  3.1.0 | 05 JUL 2018 | Created debug() & establish_post_data() methods.
	 * @param  optional array $args The arguments passed in via shortcode.
	 * @param  optional boolean $shortcode If a shortcode is calling this class.
	 * @return void
     *
     */
    public function __construct( $args = array(), $shortcode = false ) {
        global $swp_social_networks;

        $this->networks     = $swp_social_networks;
		$this->args         = $args;
        $this->content      = isset( $args['content'] ) ? $args['content'] : '';
        $this->is_shortcode = $shortcode;

        $this->establish_post_id();
        $this->establish_post_data();

        if ( !isset( $this->post_id ) ) :
            return;
        endif;

        $this->localize_options();
		$this->establish_share_data();
  	    $this->establish_location();
		$this->establish_permalink();
        $this->establish_active_buttons();
        $this->debug();
    }


	/**
	 * Localize the global options
	 *
	 * The goal here is to move the global $swp_options array into a local property so that the options
	 * for this specific instantiation of the buttons panel can have the options manipulated prior to
	 * rendering the HTML for the panel. We can do this by using getters and setters or by passing in
	 * arguments.
	 *
	 * @since  3.0.0 | 09 APR 2018 | Created
	 * @param  array  $args Arguments that can be used to change the options of the buttons panel.
	 * @return none
	 * @access private
	 *
	 */
	private function localize_options() {
        global $swp_user_options;
		$this->options = array_merge( $swp_user_options, $this->args);
	}


	/**
	 * Set an option
	 *
	 * This method allows you to change one of the options for the buttons panel.
	 *
	 * @since  3.0.0 | 09 APR 2018 | Created
	 * @param  string $option The key of the option to be set.
	 * @param  mixed  $value  The value to which we will set that option.
	 * @return object $this   Allows for method chaining.
	 * @access public
	 *
	 */
	public function set_option( $option = '', $value = null ) {
        if ( empty( $option ) ) :
            $message = "Hey developer, " . __CLASS__ . __METHOD__ . "  a first paramter $option (string) and \$value (mixed). You provided " . gettype($value) . ".";
            throw new Exception($message);
        elseif ( null == $value ) :
            $message = "Hey developer, " . __CLASS__ . __METHOD__ . " a second paramter: \$value (mixed type). You provided " . gettype($value) . ".";
            throw new Exception($message);
        endif;

		$this->options[$this->options] = $value;
		return $this;
	}


	/**
	 * Set multiple options
	 *
	 * This method allows you to change multiple options for the buttons panel.
	 *
	 * @since  3.0.0 | 09 APR 2018 | Created
	 * @param  array  $this->options An array of options to be merged into the existing options.
	 * @return object $this   Allows for method chaining.
	 * @access public
	 *
	 */
	public function set_options( $options = array() ) {
        if ( !is_array( $options) ) :
            $message = "Hey developer, " . __CLASS__ . __METHOD__ . " requires an arry of options. You provided " . gettype($options) . ".";
            throw new Exception($message);
        endif;

		array_merge( $this->options , $options );
		return $this;
	}


	/**
	 * Set the post ID for this buttons panel.
	 *
	 * We want to use the global post ID for whichever post is being looped
	 * through unless the post ID has been passed in as an argument.
	 *
	 * @since  3.0.0 | 09 APR 2018 | Created
	 * @param  array $args The array of args passed in.
	 * @return none
	 * @access public
	 *
	 */
     public function establish_post_id() {
         global $post;

         if ( isset( $this->args['url'] ) ) :
             if ( $id = url_to_postid( $this->args['url'] ) ) :
                 return $this->post_id = $id;
             endif;
         endif;

        // Legacy support.
        if ( isset( $this->args['postid'] ) ) :
            return $this->post_id = $this->args['postid'];
        endif;
        // Current argument.
        if ( isset( $this->args['post_id'] ) && is_numeric( $this->args['post_id'] ) ) :
            return $this->post_id = $this->args['post_id'];
        endif;
        if ( isset( $this->args['id'] ) && is_numeric( $this->args['id'] ) ) :
            return $this->post_id = $this->args['id'];
        endif;

        //* We weren't provided any context for an ID, so default to the post.
        if ( is_object( $post ) ) :
            $this->post_id = $post->ID;
        endif;
   	}


    /**
     * Set the post data for this buttons panel.
     *
     * @since  3.1.0 | 05 JUL 2018 | Created
     * @return none
     * @access public
     * @param  void
     * @return void
     *
     */
    public function establish_post_data() {
        if( !empty( $this->post_id ) ):
            $post = get_post( $this->post_id );

            if ( !is_object( $post ) ) :
                return;
            endif;

            $this->post_data = array(
                'ID'           => $post->ID,
                'post_type'    => $post->post_type,
                'permalink'    => get_the_permalink( $post->ID ),
                'post_title'   => $post->post_title,
                'post_status'  => $post->post_status,
                'post_content' => $post->post_content
            );
        endif;
    }


    /**
     * Instantiates the share data from a given post ID.
     *
     * @since 3.1.0 | 25 JUN 2018 | Created the method.
     * @return void
     * @access public
     *
     */
    public function establish_share_data() {
        global $SWP_Post_Caches;
        $this->shares = $SWP_Post_Caches->get_post_cache( $this->post_id )->get_shares();

        return $this;
    }


	/**
	 * Establish the post content
	 *
	 * Take the content passed in via the $args and move it into a
	 * local property.
	 *
	 * @since  3.0.0 | 18 APR 2018 | Created
	 * @param  none
	 * @return none Everything is stored in a local property.
	 *
	 */
    public function establish_post_content() {
        if( isset( $this->args['content'] ) ):
			$this->content = $args['content'];
		endif;
    }


	/**
	 * Establish Location
	 *
	 * A method to handle figuring out where in the content these buttons are
	 * supposed to appear. It has to check the global options, the options set
	 * on the post, and be able to tell if this is being called without any
	 * content to which to append.
	 *
	 * @since  3.0.0 | 10 APR 2018 | Created
	 * @since  3.0.7 | 15 MAY 2018 | Added conditionals to ensure $post_setting isn't an array.
	 * @param  none
	 * @return none All values are stored in local properties.
	 * @access public
	 *
	 */
	public function establish_location() {
        //* Establish a default.
        $this->location = 'none';

		// Return with the location set to none if we are on attachment pages.
		if( is_attachment() ):
			return;
		endif;

		// If there is no content, this must be called directly via function or shortcode.
		if ( empty( $this->content ) && is_singular() ):
			$this->location = 'above';
		endif;

		/**
		 * Location from the Post Options
		 *
		 * If the location was specified on the post options, we'll make sure
		 * to use this instead of the global options.
		 *
		 */
		$post_setting = get_post_meta( $this->post_id, 'swp_post_location', true );

        if( is_array($post_setting) ) :
             $post_setting = $post_setting[0];
         endif;

		// If the location is set in the post options, use that.
		if ( !empty( $post_setting ) && 'default' != $post_setting ) {
			$this->location = $post_setting;

            //* Exit early because this is a priority.
            return;
		};


		/**
		 * Global Location Settings
		 *
		 * Decide which post type we're on and pull the location setting
		 * for that type from the global options.
		 *
		 */
		// If we are on the home page
		if( is_front_page() ) :
            $home = $this->options['location_home'];
			$this->location = isset( $home ) ? $home : 'none';

            return;
        endif;


		// If we are on a singular page
		if ( is_singular() ) :
            $location = $this->options[ 'location_' . $this->post_data['post_type'] ];

            if ( isset( $location ) ) :
                $this->location = $location;
            endif;

        endif;


        if ( is_archive() || is_home() ) :
            $this->location = $this->options['location_archive_categories'];
        endif;
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
    public function display_name_to_key( $string ) {
        return preg_replace( '/[\s]+/', '_', strtolower( trim ( $string ) ) );
    }


	/**
	 * A method for fetching the permalink.
	 *
	 * @since  3.0.0 | 01 MAR 2018 | Created
	 * @param  void
	 * @return void Values are stored in $this->permalink.
	 *
	 */
    protected function establish_permalink() {
        $this->permalink = get_permalink( $this->post_id );
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
    public function should_print() {

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
	 * The method that renderes the button panel HTML.
	 *
	 * @since  3.0.0 | 25 APR 2018 | Created
	 * @since  3.0.3 | 09 MAY 2018 | Switched the button locations to use the
	 *                               location methods instead of the raw options value.
	 * @since  3.0.6 | 15 MAY 2018 | Uses $this->option() method to prevent undefined index error.
	 * @since  3.3.1 | 13 SEP 2018 | Added get_alignment()
	 * @param  boolean $echo Echo's the content or returns it if false.
	 * @return string        The string of HTML.
	 *
	 */
    public function render_HTML( $echo = false ) {
        if ( !isset( $this->post_id ) || is_preview() ) :
            return;
        endif;

        $style = "";
        $float_mobile = SWP_Utility::get_option( 'float_mobile');

        if ( !$this->should_print() && ( 'top' == $float_mobile || 'bottom' == $float_mobile ) ) :
             if ( true !== $this->option( 'floating_panel' ) ) :
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

            $total_shares_html = $this->render_total_shares_html();
            $buttons = $this->render_buttons_html();

            if ($this->option('totals_alignment') === 'totals_left') :
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


	protected function get_alignment() {
		return ' scale-' . $this->option('button_alignment');
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
		if ( false === $this->option( 'default_colors' ) ) {
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
		$default = str_replace( $prefix, '', $this->option( $prefix . 'default_colors' ) );
		$hover   = str_replace( $prefix, '', $this->option( $prefix . 'hover_colors' ) );
		$single  = str_replace( $prefix, '', $this->option( $prefix . 'single_colors' ) );
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
        $button_shape = $this->option( 'button_shape' );

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
        $button_size = $this->option( 'button_size' );

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
        $min_width = $this->option( 'float_screen_width' );

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
        $float_background_color = $this->option( 'float_background_color' );

        //* They have gone from an Addon to Core.
        if ( false === $float_background_color ) {
            return " ";
        }

        return '" data-float-color="' . $float_background_color;
    }


	/**
	 * A function to avoid getting undefined index notices.
	 *
	 * @since  3.0.5 | 10 MAY 2018 | Created
	 * @param  string $key The name of the option.
	 * @return mixed       The value of that option.
	 *
	 */
	private function option($key) {
		if( isset( $this->options[$key] ) ) {
			return $this->options[$key];
		} else {
			return SWP_Utility::get_option( $key );
		}
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
	 * @param  none
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

		if ( $post_on || is_singular() && true === $this->option('floating_panel') && 'on' === $this->option('float_location_' . $this->post_data['post_type'] ) ) {
			return $this->option('float_location');
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
	 * @param  none
	 * @return string A string containing the float bar location.
	 *
	 */
	public function get_mobile_float_location() {
		if( is_single() && true == $this->option('floating_panel') && 'on' == $this->option('float_location_' . $this->post_data['post_type'] ) ) {
			return $this->option('float_mobile');
		}

        return 'none';
	}


    /**
     * Creates the fully qualified markup for floating button panel.
     *
     * @since  3.0.0 | 01 MAR 2018 | Created
     * @since  3.0.8 | 22 MAY 2018 | Added the $blacklist and in_array conditional.
     * @param  boolean $echo Whether or not to immediately echo the HTML.
     * @return string  $html The qualified markup for the panel.
     *
     */
    public function render_floating_HTML( $echo = true ) {
        $blacklist = ['none', 'top', 'bottom'];

        if ( in_array( $this->option('float_location'), $blacklist ) || is_preview() ) {
            return '';
        }

		if( is_singular() && 'none' !== $this->get_float_location() ) {

            //* BEGIN Old boilerplate that needs to be refactored.
	        $class = "";
	        $size = $this->option('float_size') * 100;
	        $side = $this->option('float_location');
	        $max_buttons = $this->option( 'float_button_count' );

			if( false == $max_buttons || 0 == $max_buttons ) {
				$max_buttons = 5;
			}


	        if ( 'none' != $this->get_float_location() ) {
	            $float_location =  $this->option('float_location');
	            $class = "swp_float_" . $this->option('float_location');
	        }

	        // *Get the vertical position
	        if ($this->option('float_alignment')  ) {
	            $class .= " swp_side_" . $this->option('float_alignment');
	        }

	        // *Set button size
	        if ( isset($this->options['float_size']) ) {
	            $position = $this->option('float_alignment');
	            $class .= " scale-${size} float-position-${position}-${side}";
	        }

	        //* END old boilerplate.

	        $share_counts = $this->render_total_shares_HTML();
	        $buttons = $this->render_buttons_HTML( (int) $max_buttons );

	        $container = '<div class="swp_social_panelSide swp_floating_panel swp_social_panel swp_' . $this->option('float_button_shape') .
                $this->get_colors(true) .
	            $this->option('transition') . '
	            ' . $class . '
	            ' . '" data-panel-position="' . $this->option('location_post') .
	            ' scale-' . $this->option('float_size') * 100 .
	            '" data-float="' . $float_location .
	            '" data-count="' . count($this->networks) .
	            '" data-float-color="' . $this->option('float_background_color') .
	            '" data-min-width="' . $this->option('float_screen_width') .
	            '" data-transition="' . $this->option('transition') .
	            '" data-float-mobile="' . $this->get_mobile_float_location() .'">';

	        if ($this->option('totals_alignment') === 'totals_left') {
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


	/**
	 * A method to establish the active buttons for this panel.
	 *
	 * First it will check to see if user arguments have been passed in. If not, it will
	 * check to see if they are set to manual or dynamic sorting. If manual, we will use
	 * the buttons in the order they were stored in the options array (they were set in
	 * this order on the options page.) If dynamic, we will look at the share counts and
	 * order them with the largest share counts appearing first.
	 *
	 * The results will be stored as an ordered array of network objects in the
	 * $this->networks property.
	 *
	 * @since  3.0.0 | 04 MAY 2018 | Created
	 * @param  none
	 * @return object $this Allows for method chaining.
	 *
	 */

    public function establish_active_buttons() {
        $network_objects = array();

        //* Specified buttons take precedence to global options.
        if ( isset( $this->args['buttons'] ) ) :
            $this->args['buttons'] = explode( ',', $this->args['buttons'] );

            foreach ( $this->args['buttons'] as $counts_key ) {
                $network_key = $this->display_name_to_key( $counts_key );
                foreach( $this->networks as $key => $network ):
                    if( $network_key === $key ):
                        $network_objects[] = $network;
                    endif;
                endforeach;
            }

        //* Use global button settings.
        else :
			// Order manually using the user's specified order.
            if ( !isset( $this->options['order_of_icons_method'] ) || $this->options['order_of_icons_method'] === 'manual' ) :
                $order = SWP_Utility::get_option( 'order_of_icons' );

			// Order them dynamically according to share counts.
            else :
                $order = $this->get_dynamic_buttons_order();
            endif;

			$network_objects = $this->get_ordered_network_objects( $order );
        endif;

        $this->networks = $network_objects;
        return $this;
    }


	/**
	 * A method to order the networks dynamically.
	 *
	 * @since  3.0.0 | 04 MAY 2018 | Created
	 * @param  none
	 * @return object An ordered array containing the social network objects.
	 *
	 */
    protected function get_dynamic_buttons_order() {
        global $swp_social_networks;
        $buttons = $this->options['order_of_icons'];
		$order = array();

		if( !empty( $this->shares ) && is_array( $this->shares ) ):
			arsort( $this->shares );
			foreach( $this->shares as $key => $value ):
				if($key !== 'total_shares' && in_array($key, $buttons)):

					$order[$key] = $key;
				endif;
			endforeach;
			$this->options['order_of_icons'] = $order;
		else:
			$order = $this->options['order_of_icons'];
		endif;
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
	public function get_ordered_network_objects( $order ) {
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


    public function render_buttons_HTML( $max_count = null) {
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
     * @param  void
     * @return string $html The fully qualified HTML to display share counts.
     *
     */
    public function render_total_shares_html() {
        $buttons = isset( $this->args['buttons'] ) ? $this->args['buttons'] : array();

        if ( false == $this->option('total_shares') ) {
            return '';
        }

        if ( $this->shares['total_shares'] < $this->option('minimum_shares') ) {
            return '';
        }

        $totals_argument = in_array('total', array_map('strtolower', $buttons))
            || in_array('totals', array_map('strtolower', $buttons));

        if ( $this->is_shortcode && !$totals_argument ) {
            return '';
        }

        $html = '<div class="nc_tweetContainer total_shares total_sharesalt" >';
            $html .= '<span class="swp_count ">' . SWP_Utility::kilomega( $this->shares['total_shares'] ) . ' <span class="swp_label">' . __( 'Shares','social-warfare' ) . '</span></span>';
        $html .= '</div>';

        return $html;
    }


    /**
     * Handles whether to echo the HTML or return it as a string.
     *
     * @since  3.0.6 | 14 MAY 2018 | Removed the swp-content-locator div.
     * @param  void
     * @return mixed null if set to echo, else a string of HTML.
     *
     */
    public function do_print() {
        $this->render_HTML();

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
     * Runs checks before ordering a set of buttons.
     *
     * @since  3.0.6 | 14 MAY 2018 | Removed the swp-content-locator div.
     * @since  3.3.3 | 18 SEP 2018 | Added return value for should_print() condition.
     * @param  string $content The WordPress content, if passed in.
     * @return function @see $this->do_print
     *
     */
    public function the_buttons( $content = null ) {

		/**
		 * If the content is empty, it means that the user is calling a panel
		 * of buttons directly using the social_warfare() function of the
		 * [social_warfare] shortcode.
		 *
		 */
		if ( empty( $this->content ) ) {
            return $this->do_print();
        }


		/**
		 * We have a standalone method designed to let us know if all the proper
		 * desired conditions are met in order to allow us to print the buttons.
		 *
		 */
        if ( !$this->should_print() ) {
            return $this->content;
        }

        if ( null !== $content && gettype( $content ) === 'string' ) {
            $this->args['content'] = $content;
        }

        return $this->do_print();
    }


    /**
     * Holds the query paramters for debugging.
     *
     * @since  3.1.0 | 05 JUL 2018 | Created the method.
     * @param  void
     * @return void
     *
     */
    public function debug() {
        if ( true === SWP_Utility::debug( 'buttons_panel' ) ) :
            echo "<pre>";
            var_dump($this);
            echo "</pre>";
        endif;
    }
}
