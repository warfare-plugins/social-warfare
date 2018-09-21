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
 *     establish_local_options();
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
 *
 * 	   NOTE: These are inherited from SWP_Buttons_Panel_Trait.
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
 *     debug();
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


	use SWP_Buttons_Panel_Trait;


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
     *
     */
    public $html = '';


	/**
     * The array of active buttons for $this Social Panel.
     *
     * @var array $active_buttons;
     *
     */
    public $active_buttons = array();


	/**
     * The sum of share counts across active networks.
     *
     * @var integer $total_shares;
     *
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
        $this->establish_local_options();
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
	 * @param  void
	 * @return void
	 *
	 */
	protected function establish_local_options() {
        global $swp_user_options;
		$this->options = array_merge( $swp_user_options, $this->args);
	}


	/**
     * Establishes the share data from a given post ID.
     *
     * @since 3.1.0 | 25 JUN 2018 | Created the method.
     * @param  void
     * @return $this Allows for method chaining.
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
	 * @param  void
	 * @return void All values are stored in local properties.
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
			foreach ( $this->args['buttons'] as $network_name ) {
				$network_key = $this->get_key_from_name( $network_name );
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
      * @since  3.3.3 | 18 SEP 2018 | Added return value for should_panel_display() condition.
      * @param  string $content The WordPress content, if passed in.
      * @return function @see $this->create_panel()
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
             return $this->create_panel();
         }


 		/**
 		 * We have a standalone method designed to let us know if all the proper
 		 * desired conditions are met in order to allow us to print the buttons.
 		 *
 		 */
         if ( !$this->should_panel_display() ) {
             return $this->content;
         }

         if ( null !== $content && gettype( $content ) === 'string' ) {
             $this->args['content'] = $content;
         }

         return $this->create_panel();
     }

}
