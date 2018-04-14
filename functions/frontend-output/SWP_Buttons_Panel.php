<?php

/**
 * Register and output header meta tags
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */


/**
 * So we want to make it so that developers can easily access this class and make changes to the
 * buttons panel on the fly for any particular instantiation of it. They don't need access to every
 * single method or property of the buttons. Instead, we want to focus specifically on options that are
 * available on the options page.
 *
 * So ideally, the buttons panel will pull it's parameters from the SWP_User_Options object, but it can
 * then be overwritten by a developer. For example, something like this might do the trick:
 *
 * $generate_html: By default, this class will fully create the set of buttons and produce the HTML either
 * via a return or an echo. It will go from top to bottom and put absolutely everything together.
*/

//
// class SWP_Buttons_Panel {
// 	public $options;
// 	public $args;
//
// 	// Create an SWP_Buttons_Panel object without generating any HTML.
// 	public function __construct( $generate_html = true ) {
// 		global $SWP_User_Options;
// 		$options = $SWP_User_Options;
// 		$this->do_something();
// 	}
//
// 	// Allow developers to set options.
// 	public function set_option( $option_name , $new_value ) {
// 		$this->$options[$option_name] = $new_value;
// 	}
//
// 	// Create the HTML for the buttons without actually instantiating anything.
// 	public static function new_buttons_panel( $args ) {
// 		$this->$args = $args;
// 		$this->do_something();
// 	}
// }


/**
 * So in this example, it will use the SWP_User_Options to fill in and create all of the default options, but
 * it will then overwrite the custom_color and networks options programatically via a setter method and then
 * after all of the manipulations have been done, the programmer can call to have the HTML output.
 *
 * $generate_html: By passing in 'false' through the generate_html variable, it will return an object that can
 * be manipulated.
 *
 * $Buttons = new SWP_Buttons_Panel( false );
 * $Buttons->set_option( 'custom_color' , '#FF0000' );
 * $Buttons->set_option( 'networks' , array( 'Twitter' , 'Facebook' , 'Pinterest' ) );
 * $Buttons->output_HTML();
 *
 * Alternatively, they should ALSO be able to use the $args method of instantiating an object. Perhaps something
 * like this:
 *
 * Again, this version should do everything. Pull all paramters from the User_Options object, overwrite the
 * 'custom_color' and 'networks' with the $args and then output the HTML of the buttons panel.
 *
 * Note #1: This static method should also be able to be called without any $args and should return HTML for the
 * buttons without actually creating an instantiation of the class itself. It's just a one and done. Process the
 * options and output the HTML.
 *
 * Note #2: This method should be what the procedural social_warfare() function calls. For example:
 *
 * function social_warfare( $args = array() ) {
 * 		return SWP_Buttons_Panel::new_buttons_panel( $args );
 * }
 *
 */

//$args = array(
//	'custom_color' => '#FF0000',
//	'networks' => array( 'Twitter' , 'Facebook' , 'Pinterest' )
//)
// SWP_Buttons_Panel::new_buttons_panel( $args );


/**
 * CONCLUSION: The goal is to have a variety of approaches for developers to use in outputting the buttons as
 * they desire.
 *
 * #1: They should be able to instantiate the class into an object and call getters and setters to manipulate
 * their output.
 *
 * #2: They should be able to call the static method to simply process everything all in one go without even
 * instantiating the class. Just call the method, and boom, there's some buttons on the page now. It should
 * function, practically speaking the same as calling: echo 'This gets printed on the screen'; Nothing is
 * instantiated, it just processes the command and echos or returns a string of HTML.
 *
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
	public $args = [];


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
     * @var array active_buttons;
     */
    public $active_buttons = [];


	/**
	 * The Construct Method
	 *
	 * A Few Important Notes:
	 * 1. We want EVERYTHING to get processed during instantiation.
	 * 2. The render_html method will be called afterwards.
	 * 3. The render_html method will NOT call any ESTABLISH methods.
	 * 4. The render_html method will use local properties to create the HTML.
	 * 5. Anything in the options array, should stay in that array.
	 * 5A. The "buttons" passed in via the args, needs to replace the
	 * 		"order_of_icons" in the options array.
	 * 5B. The "order_the_buttons" method needs to modify the "order_of_icons" in
	 * 		the options array if needed.
	 * 5C. Remove the set_scale method. Use the options array, setters, and args.
	 * 6. Add a method to the social network class to eliminate passing over
	 * 		the entire buttons panel object to that class. Instead we want
	 * 		to pass the localized options array, the counts, and anything else
	 * 		needed. That method will store it in local properties so that the
	 * 		render_html method can be called and it will have everything it needs.
	 * 	7. Instead of using the is_active() method of the networks, we'll just loop
	 * 		through the order_of_icons keys and call the render_html from the global
	 * 		network objects.
	 *
	 * @param array $args [description]
	 */
    public function __construct( $args = array() ) {
        global $swp_social_networks, $post;

        $this->networks = $swp_social_networks;
		$this->args = $args;

		if( isset( $args['content'] ) ):
			$this->content = $args['content'];
		endif;

        //* Access the $post once while we have it. Values may be overwritten.
        $this->post_type = $post->post_type;
        $this->post_status = $post->post_status;
        $this->post_id = $post->ID;

        $this->localize_options( $args );
	    $this->establish_post_id();
	    $this->establish_location();
		$this->establish_float_location();
		$this->establish_permalink();
        $this->establish_active_buttons();
		$this->shares = get_social_warfare_shares( $this->post_id );
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

		$this->options = array_merge( $swp_user_options, $this->args );
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
	public function set_option( $option , $value ) {
		$this->options[$options] = $value;
		return $this;
	}


	/**
	 * Set multiple options
	 *
	 * This method allows you to change multiple options for the buttons panel.
	 *
	 * @since  3.0.0 | 09 APR 2018 | Created
	 * @param  array  $options An array of options to be merged into the existing options.
	 * @return object $this   Allows for method chaining.
	 * @access public
	 *
	 */
	public function set_options( $options ) {
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
		// Legacy support.
		if ( isset( $this->args['postID'] ) ) :
			$this->post_id = $this->args['postID'];
        endif;

		// Current argument.
		if ( isset( $this->args['post_id'] ) ) :
			$this->post_id = $this->args['post_id'];
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
	 * @param  none
	 * @return none All values are stored in local properties.
	 * @access public
	 *
	 */
	public function establish_location() {
        //* Establish a default.
        $this->location = 'none';

		/**
		 * Location from the Post Options
		 *
		 * If the location was specified on the post options, we'll make sure
		 * to use this instead of the global options.
		 *
		 */
		$preset_location = get_post_meta( $this->post_id, 'nc_postLocation', true );

		// If the location is set in the post options, use that.
		if ( !empty( $preset_location ) && 'default' !== $preset_location ) {
			$this->location = $preset_location;
		};

		/**
		 * Global Location Settings
		 *
		 * Decide which post type we're on and pull the location setting
		 * for that type from the global options.
		 *
		 */

		// If we are on the home page
		if( is_front_page() ):
			$this->location = $this->options['location_home'];
        endif;

		// If we are on a singular page
		if ( is_singular() && !is_home() ) :
            $location = $this->options[ 'location_' . $this->post_type ];
            if ( isset( $location ) ) :
                $this->location = $location;
            endif;
        endif;

        if ( $this->is_archive() ) :
            $this->location = $this->options['location_archive_categories'];
        endif;
	}


    /**
     * Takes a display name and returns the snake_cased key of that name.
     *
     * This is used to convert a network's name, such as Google Plus,
     * to the database-friendly key of google_plus.
     *
     * @param  string $name The string to convert.
     * @return string The converted string.
     */
    public function display_name_to_key( $string ) {
        return preg_replace( '/[\s]+/', '_', strtolower( trim ( $string ) ) );
    }


    protected function establish_float_position() {
        // Set the options for the horizontal floating bar
        $post_type = get_post_type( $this->post_id );
        $spec_float_where = get_post_meta( $post_id , 'nc_floatLocation' , true );

        if ( isset( $this->args['floating_panel'] ) && $this->args['floating_panel'] == 'ignore' ) :
            $floatOption = 'float_ignore';
        elseif ( $spec_float_where == 'off' && $this->options['button_alignment'] != 'float_ignore' ) :
                $floatOption = 'floatNone';
        elseif ( $this->options['floating_panel'] && is_singular() && $this->options[ 'float_location_' . $post_type ] == 'on' ) :
            $floatOption = 'floating_panel' . ucfirst( $this->options['float_position'] );
        else :
            $floatOption = 'floatNone';
        endif;
    }


    protected function establish_permalink() {
        if ( isset( $this->args['post_id'] ) ) :
            $this->permalink = $this->args['post_id'];
        else :
            $this->permalink = get_permalink( $this->post_id );
        endif;
    }


    //* When we have known incompatability with other themes/plugins,
    //* we can put those checks in here.
    /**
     * Checks for known conflicts with other plugins and themes.
     *
     * If there is a fatal conflict, returns true and exits printing.
     * If there are other conflicts, they are silently handled and can still
     * print.
     *
     * @return bool $conflict True iff the conflict is fatal.
     */
    protected function has_plugin_conflict() {
        $conflict;

		// Disable subtitles plugin to prevent it from injecting subtitles
		// into our share titles.
		if ( is_plugin_active( 'subtitles/subtitles.php' ) && class_exists( 'Subtitles' ) ) :
			remove_filter( 'the_title', array( Subtitles::getinstance(), 'the_subtitle' ), 10, 2 );
		endif;

        //* Disable on BuddyPress pages.
        if ( function_exists( 'is_buddypress' ) && is_buddypress() ) :
            $conflict = true;
        endif;

        return $conflict;
    }


    /**
     * Tells you true/false if the buttons should print on this page.
     *
     * @return Boolean True if the buttons are okay to print, else false.
     */
    public function should_print() {
        return (
            //* User settings.
            $this->location !== 'none' &&

            //* Conditions we want.
            is_main_query() && in_the_loop() && get_post_status( $this->post_id ) == 'publish' &&

            //* Conditions we do not want.
            !is_admin() && !is_feed() && !is_search() && !is_attachment()
        );
    }


    public function render_html() {

		if ( ! $this->should_print() ) :
			return $this->args['content'];
		endif;

		// Create the HTML Buttons panel wrapper
        $html = '<div class="nc_socialPanel swp_' . $this->options['button_shape'] .
            ' swp_default_' . $this->options['default_colors'] .
            ' swp_individual_' . $this->options['single_colors'] .
            ' swp_other_' . $this->options['hover_colors'] .
            ' scale-' . $this->options['scale'] * 100 .
            ' scale-' . $this->options['button_alignment'] .
            '" data-position="' . $this->options['location_post'] .
            '" data-float="' . $this->float_option .
            '" data-count="' . $buttons_array['count'] .
            '" data-floatColor="' . $this->options['float_background_color'] .
            '" data-emphasize="'.$this->options['emphasize_icons'].'
            ">';

            $html .= $this->render_share_counts();

            $html .= $this->render_buttons();

        $html .= "</div>";

        return $html;
    }


    public function establish_active_buttons() {
        $buttons = [];

        //* Specified buttons take precedence to global options.
        if ( isset( $this->args['buttons'] ) ) :
            $this->args['buttons'] = explode( ',', $this->args['buttons'] );

            foreach ( $this->args['buttons'] as $button_key ) {

                $network_key = $this->display_name_to_key( $button_key );

                foreach( $this->networks as $key => $network ):
                    if( $network_key === $key ):
                        $buttons[] = $network;
                    endif;
                endforeach;
            }

        //* Use global button settings.
        else :

            if ( $this->options['order_of_icons_method'] === 'manual' ) :
                $buttons = $this->get_manual_buttons();
            else :
                $buttons = $this->get_dynamic_buttons();
            endif;

        endif;

        $this->buttons = $buttons;

        return $this;
    }

    protected function get_manual_buttons() {
        $buttons = [];
        $order = $this->options['order_of_icons'];

        foreach( $order as $network_key ) {
            foreach( $this->networks as $key => $network ):
                if( $key === $network_key && true === $network->is_active() ):
                    $buttons[] = $network;
                endif;
            endforeach;
        }

        return $buttons;
    }

    //* TODO: How can we sort the buttons dynamically?
    //* This is dependent on how we fetch share counts.
    protected function get_dynamic_buttons() {
        $buttons = [];

        return $buttons;
    }

    public function render_buttons() {
        foreach( $this->buttons as $button ) {
            $this->html .= $button->render_HTML();
        }
    }


    /**
     * If share counts are active, renders the Share Counts HTML.
     *
     * @return string $html The fully qualified HTML to display share counts.
     *
     */
    public function render_share_counts() {
        $html = '';
        $total_shares = $this->options['total_shares'];

        if ( empty( $total_shares) || $total_shares <= $this->options['minimum_shares'] ) {
            return $html;
        }

        $classname = $this->options['totals_alignment'] === 'totals_left' ? 'totals-left' : 'totals-right';

        $html .= '<div class="nc_tweetContainer totes totesalt" data-id="' . $buttons_array['count'] . '" >';
            $html .= '<span class="swp_count ' . $classname . '">' . swp_kilomega( $this->options['total_shares'] ) . ' <span class="swp_label">' . __( 'Shares','social-warfare' ) . '</span></span>';
        $html .= '</div>';

        return $html;
    }

    //* TODO: This has not been refactored.
    protected function handle_timestamp() {
        if ( swp_is_cache_fresh( $post_id ) == false  && isset($this->options['cache_method']) && 'legacy' === $this->options['cache_method'] ) :
			delete_post_meta( $post_id,'swp_cache_timestamp' );
			update_post_meta( $post_id,'swp_cache_timestamp',floor( ((date( 'U' ) / 60) / 60) ) );
		endif;
    }


    /**
     * Handles whether to echo the HTML or return it as a string.
     *
     * @return mixed null if set to echo, else a string of HTML.
     *
     */
    public function do_print() {
        if ( $this->args['echo'] || $this->content === false ) {
            echo $this->html;
            return;
        }

        //* Add the Panel markup based on the location.
        if ( $this->location === 'both' ) :
            $this->content = $this->html + $this->content + $this->html;
        elseif ( $this->location === 'above' ) {
            $this->content = $this->html + $this->content;
        } else {
            $this->content = $this->content + $this->html;
        }

        return $this->content;
    }


    public function the_buttons() {
		if( $this->location == 'none' ) :
            return;
        endif;

        if ( $this->has_plugin_conflict() ) {
            return;
        }


		// Disable the plugin on feeds, search results, and non-published content
		if ( ! $this->should_print() ) :
            return $this->args['content'];
        endif;

		$buttons_array['shares'] = get_social_warfare_shares( $post_id );

		$buttons_array['options'] = $this->options;

		$this->html .= $this->render_social_panel();

		$this->handle_timestamp();

        return $this->do_print();
    }
}
