<?php

/**
 * The Buttons_Panel object class.
 *
 * Creates the Panel of share buttons based on options and settings. The class
 * is comprised of four main sections:
 *
 *
 * TABLE OF CONTENTS:
 *
 * SECTION #1: Insantiate a post object & compile all necessary data.
 *     __construct();
 *     establish_post_data();
 *     localize_options();
 *     establish_share_data();
 *     establish_location();
 *     establish_permalink();
 *     establish_active_buttons();
 *
 * SECTION #2: Allow developers to manipulate the object using setters.
 *     set_option();
 *     set_options();
 *
 * SECTION #3: Use the data to render out the HTML to display a panel of buttons.
 *     render_html();
 *
 * SECTION #4: Utility methods used throughout the class.
 *     display_name_to_key();
 *     should_print();
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
 *     render_buttons_html();
 *     render_total_shares_html();
 *     should_total_shares_render();
 *     do_print();
 *     generate_panel_html();
 *     debug();
 *
 *
 *
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     3.0.0 | 01 MAR 2018 | Created
 * @since     3.4.0 | 20 SEP 2018 | Moved the floating buttons panel out of this
 *                                  class and into a child class of this class.
 *
 */
class SWP_Buttons_Panel {


	/**
	 * Options
	 *
	 * We're using a local property to clone in the global $swp_user_options
	 * array. As a local options we and other developers accessing this object
	 * can use getters and setters to change any options possibly imaginable
	 * without affecting the global options.
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




	/***************************************************************************
	 *
	 * SECTION #1: INSTANTIATE THE OBJECT
	 *
	 * This is the first of three sections. In this section, we will set up all
	 * the necessary information in order to render out the buttons panel later.
	 *
	 *
	 */


    /**
     * The Construct Method
     *
     * This method creates the Buttons_Panel object. It gathers all of the
     * necessary data, the user options, the share counts, and stores it all in
     * local properties. Later we will call the public method render_html()
     * (e.g. $Buttons_Panel->render_html(); ) to actually render out the panel to
     * the screen.
 	 *
     * @since  3.0.0 | 01 MAR 2018 | Created
	 * @since  3.1.0 | 05 JUL 2018 | Created debug() & establish_post_data() methods.
	 * @since  3.4.0 | 20 SEP 2018 | Moved establish_post_id() into a conditional.
	 * @param  array optional $args The arguments passed in via shortcode.
	 * @param  boolean optional $shortcode If a shortcode is calling this class.
	 * @return void
     *
     */
    public function __construct( $args = array(), $shortcode = false ) {
        global $swp_social_networks;


		/**
		 * Pull in necessary data so that the methods below can use it to setup
		 * the Buttons_panel object properly.
		 *
		 */
        $this->networks     = $swp_social_networks;
		$this->args         = $args;
        $this->content      = isset( $args['content'] ) ? $args['content'] : '';
        $this->is_shortcode = $shortcode;


		/**
		 * The establish_post_id() runs several checks including fallback
		 * methods to ensure that there is an available post_id for us to use.
		 * However, if it fails to find a valid post_id, it will return false,
		 * and if that is the case, we bail. We can't build a set of buttons
		 * without one present.
		 *
		 */
        if ( false === $this->establish_post_id() ) {
            return;
        }


		/**
		 * Step by step, these methods walk through the process of compiling
		 * everything we'll need in order to render out a panel of buttons
		 * according to the user options, the per post options, share counts, etc.
		 *
		 */
        $this->establish_post_data();
        $this->localize_options();
		$this->establish_share_data();
  	    $this->establish_location();
		$this->establish_permalink();
        $this->establish_active_buttons();
        $this->debug();
    }


	/**
	 * Set the post ID for this buttons panel.
	 *
	 * We want to use the global post ID for whichever post is being looped
	 * through unless the post ID has been passed in as an argument.
	 *
	 * @since  3.0.0 | 09 APR 2018 | Created
	 * @since  3.4.0 | 20 SEP 2018 | Refactored to refine logic.
	 * @param  array $args The array of args passed in.
	 * @return boolean     True on success; False on failure.
	 *
	 */
     public function establish_post_id() {


		/**
         * Cycle through the available post_id labels, find which one was passed
         * in and use it as the post_id for this panel of buttons.
         *
         */
 		$id_labels = array( 'id', 'post_id', 'postid' );
 		foreach( $id_labels as $label ) {
 	        if ( isset( $this->args[$label] ) && is_numeric( $this->args[$label] ) ) {
 	            $this->post_id = $this->args[$label];
 				return true;
 	        }
 		}


		/**
		 * If the user provided a URL instead of an ID, let's see if we can
		 * convert it into a valid WordPress ID for a post.
		 *
		 */
        if ( isset( $this->args['url'] ) && $post_id_from_url = url_to_postid( $this->args['url'] ) ) {
			$this->post_id = $post_id_from_url;
			return true;
		}


		/**
		 * If the user didn't pass in any arguments related to an ID or URL then
		 * we will just use the post id of the current post.
		 *
		 */
		global $post;
        if ( is_object( $post ) ) :
            $this->post_id = $post->ID;
			return true;
        endif;


		/**
		 * If we were completely unable to establish a post_id, then return
		 * false.
		 *
		 */
		return false;

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

		// Fetch the post object.
		$post = get_post( $this->post_id );

		// Bail if the post object failed.
		if ( !is_object( $post ) ) {
			return;
		}

		// Set up the post data.
		$this->post_data = array(
			'ID'           => $post->ID,
			'post_type'    => $post->post_type,
			'permalink'    => get_the_permalink( $post->ID ),
			'post_title'   => $post->post_title,
			'post_status'  => $post->post_status,
			'post_content' => $post->post_content
		);
	}


	/**
	 * Localize the global options
	 *
	 * The goal here is to move the global $swp_options array into a local
	 * property so that the options for this specific instantiation of the
	 * buttons panel can have the options manipulated prior to rendering the
	 * HTML for the panel. We can do this by using getters and setters or by
	 * passing in arguments.
	 *
	 * @since  3.0.0 | 09 APR 2018 | Created
	 * @param  array  $args Arguments that can be used to change the options of
	 *                      the buttons panel.
	 * @return void
	 * @access private
	 *
	 */
	private function localize_options() {
        global $swp_user_options;
		$this->options = array_merge( $swp_user_options, $this->args);
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
		if( is_attachment() ) {
			return;
		}

		// If there is no content, this must be called directly via function or shortcode.
		if ( empty( $this->content ) && is_singular() ) {
			$this->location = 'above';
			return;
		}


		/**
		 * Location from the Post Options
		 *
		 * If the location was specified on the post options, we'll make sure
		 * to use this instead of the global options.
		 *
		 */
		$post_setting = get_post_meta( $this->post_id, 'swp_post_location', true );

        if( is_array($post_setting) ) {
             $post_setting = $post_setting[0];
        }

		// If the location is set in the post options, use that.
		if ( !empty( $post_setting ) && 'default' != $post_setting ) {
			$this->location = $post_setting;

            //* Exit early because this is a priority.
            return;
		}


		/**
		 * Global Location Settings
		 *
		 * Decide which post type we're on and pull the location setting
		 * for that type from the global options.
		 *
		 */
		// If we are on the home page
		if( is_front_page() ) {
            $home = $this->options['location_home'];
			$this->location = isset( $home ) ? $home : 'none';

            return;
        }


		// If we are on a singular page
		if ( is_singular() ) {
            $location = $this->options[ 'location_' . $this->post_data['post_type'] ];

            if ( isset( $location ) ) {
                $this->location = $location;
            }

        }


        if ( is_archive() || is_home() ) {
            $this->location = $this->options['location_archive_categories'];
        }
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
	 * @param  void
	 * @return object $this Allows for method chaining.
	 *
	 */
	public function establish_active_buttons() {
		$network_objects = array();


		/**
		 * If the user passed in an array of buttons either via the social_warfare()
		 * function of the [social_warfare buttons="buttons"] shortcode, these
		 * will take precedence over the buttons that are selected on the
		 * Social Warfare options page.
		 *
		 */
		if ( isset( $this->args['buttons'] ) ) {
			$this->args['buttons'] = explode( ',', $this->args['buttons'] );


			/**
			 * Trim out white space. We need to trim any whitespace in case
			 * folks put a space before or after any of the commas separating
			 * the button names that were passed in.
			 *
			 * e.g. [social_warfare buttons="twitter, google_plus"]
			 *
			 */
			foreach( $this->args['buttons'] as $index => $button ) {
				$this->args['buttons'][$index] = trim( $button );
			}


			/**
			 * Loop through the passed-in array of buttons, find the global
			 * Social_Network object associated to each network, and store them
			 * in the $network_objects array.
			 *
			 */
			foreach ( $this->args['buttons'] as $counts_key ) {
				$network_key = $this->display_name_to_key( $counts_key );
				foreach( $this->networks as $key => $network ):
					if( $network_key === $key ):
						$network_objects[] = $network;
					endif;
				endforeach;
			}

			// Store it in the $networks property and terminate the method.
			$this->networks = $network_objects;
			return $this;

		}

		$order           = $this->get_order_of_icons();
		$network_objects = $this->get_ordered_network_objects( $order );
		$this->networks  = $network_objects;
		return $this;
	}




	/***************************************************************************
	 *
	 * SECTION #2: ALLOW DEVELOPERS ACCESS TO SETTERS
	 *
	 * This section allows developers to manipulate the Buttons_Panel object
	 * prior to the final rendering of the html at the end.
	 *
	 *
	 */


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
	 * @param  array  $this->options An array of options to be merged into the
	 *                               existing options.
	 * @return object $this          Allows for method chaining.
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




	/***************************************************************************
	 *
	 * SECTION #3: RENDER THE FULLY QUALIFIED HTML FOR THE PANEL
	 *
	 * This section will use all of the data created by the object and process
	 * it all into properly formatted html for display on the screen.
	 *
	 */


	 /**
      * Runs checks before ordering a set of buttons.
      *
      * @since  3.0.6 | 14 MAY 2018 | Removed the swp-content-locator div.
      * @since  3.3.3 | 18 SEP 2018 | Added return value for should_print() condition.
      * @param  string $content The WordPress content, if passed in.
      * @return function @see $this->do_print
      *
      */
     public function render_html( $content = null ) {

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




	 /***************************************************************************
	  *
	  * SECTION #4: UTILITY FUNCTION USED THROUGHOUT THE CLASS
	  *
	  * This section contains methods that are used by other methods throughout
	  * the class to help organize and simplify logic throughout the class.
	  *
	  */


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
	 * @param  none
	 * @return string A string containing the float bar location.
	 *
	 */
	public function get_mobile_float_location() {
		if( is_single() && true == $this->get_option('floating_panel') && 'on' == $this->get_option('float_location_' . $this->post_data['post_type'] ) ) {
			return $this->get_option('float_mobile');
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
    protected function render_buttons_html( $max_count = null) {
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
     * @since  3.4.0 | 20 SEP 2018 | Moved display logic to should_total_shares_render()
     * @param  void
     * @return string $html The fully qualified HTML to display share counts.
     *
     */
    public function render_total_shares_html() {

		// Check if total shares should be rendered or not.
		if( false === $this->should_total_shares_render() ) {
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
	protected function should_total_shares_render() {


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
     * Handles whether to echo the HTML or return it as a string.
     *
     * @since  3.0.6 | 14 MAY 2018 | Removed the swp-content-locator div.
     * @param  void
     * @return mixed null if set to echo, else a string of HTML.
     *
     */
    public function do_print() {
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

        if ( !$this->should_print() && ( 'top' == $float_mobile || 'bottom' == $float_mobile ) ) :
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

            $total_shares_html = $this->render_total_shares_html();
            $buttons = $this->render_buttons_html();

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
