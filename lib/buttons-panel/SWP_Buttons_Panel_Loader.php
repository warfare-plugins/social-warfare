<?php
/**
 * Functions to load the front end display for the
 *
 * This used to be the SWP_Display class in /lib/frontend-output/
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 * @since     3.0.0 | 21 FEB 2018 | Refactored into a class-based system.
 * @since     3.1.0 | 18 JUN 2018 | Replaced brack array notation.
 * @since     3.4.0 | 21 SEP 2018 | Ported from SWP_Display to SWP_Buttons_Panel_Loader
 *
 */
class SWP_Buttons_panel_Loader {


	/**
	 * A global for storing post ID's to prevent duplicate processing on the
	 * same posts. Array of post ID's that have been processed during this
	 * pageload.
	 *
	 * @since 2.1.4
	 *
	 * @var array
	 *
	 */
    public $already_printed;


	/**
	 * Options
	 *
	 * This property takes the global $swp_user_options array and stores it
	 * into a local class property.
	 *
	 * @var array
	 */
	public $options;


    /**
     * The class constructor.
     *
     * @since  3.1.0 | Changed priority for wp_footer. Makes the buttons loads
     *                 This post data instead of data in the loop.
     * @param  void
     * @return void
     *
     */
    public function __construct() {


		/**
		 * The global array of posts that have already been processed. This
		 * allows us to ensure that we are not filtering the content from
		 * the_content filter on the same post more than once.
		 *
		 */
        global $swp_already_print;

		// The global array of the user-selected options.
        global $swp_user_options;

		// Declare variable as array if not already done so.
        if ( !is_array( $swp_already_print ) ) {
            $swp_already_print = array();
        }

		// Move these two globals into local properties.
        $this->already_printed = $swp_already_print;
        $this->options = $swp_user_options;

        // Hook into the template_redirect so that is_singular() conditionals will be ready
        add_action( 'template_redirect', array( $this, 'activate_buttons' ) );
        add_action( 'wp_footer', array( $this, 'floating_buttons' ) , 20 );
    }


    /**
     * A function to add the buttons
     *
     * @since  2.1.4 | 01 JAN 2017 | Created
     * @since  3.0.6 | 14 MAY 2018 | Added second filter for the_content.
     * @param  void
     * @return void
     *
     */
    public function activate_buttons() {

		// Bail if we're in the presence of a known conflict without a fix.
        if ( Social_Warfare::has_plugin_conflict() ) {
            return;
        }

    	// Only hook into the_content filter if is_singular() is true or
    	// they don't use excerpts on the archive pages.
        if( is_singular() || true === SWP_Utility::get_option( 'full_content' ) ) {
            add_filter( 'the_content', array( $this, 'social_warfare_wrapper' ) , 20 );
            add_filter( 'the_content', array( $this, 'add_content_locator' ), 20);
        }

		// If we're not on is_singlular, we'll hook into the excerpt.
        if ( !is_singular() && false === SWP_Utility::get_option( 'full_content' ) ) {
    		add_filter( 'the_excerpt', array( $this, 'social_warfare_wrapper' ) );
        }
    }


    /**
     * Add the content locator div.
     *
     * Inserts the empty div for locating Pin images (with javascript). We only
     * add this to the content if the pinit button is active.
     *
     * @since  3.0.6 | 14 MAY 2018 | Created the method.
     * @since  3.4.0 | 19 SEP 2018 | Added check for pinit_toggle option.
     * @param  string $content The WordPress content passed via filter.
     * @return string $content The modified string of content.
     *
     */
    public function add_content_locator( $content ) {

		if( true === SWP_Utility::get_option( 'pinit_toggle' ) ) {
        	$content .= '<div class="swp-content-locator"></div>';
		}

        return $content;
    }


    /**
    * A wrapper function for adding the buttons, content, or excerpt.
    *
    * @since  1.0.0
    * @param  string $content The content.
    * @return string $content The modified content
    * @todo   Why is the $content passed to both the instantator and the method?
    *
    */
    public function social_warfare_wrapper( $content ) {

		// The global WordPress post object.
        global $post;

      	// Ensure it's not an embedded post
      	if ( is_singular() && $post->ID !== get_queried_object_id() ) {
			return $content;
      	}

        // Pass the content to the buttons constructor to place them inside.
    	$buttons_panel = new SWP_Buttons_Panel( array( 'content' => $content ) );
        return $buttons_panel->render_html();
    }


	/**
	 * A function to add the side floating buttons to a post.
	 *
	 * @since  2.0.0
	 * @param  void
	 * @return void
	 *
	 */
    function floating_buttons() {

		// Bail if we're in the presence of a known conflict with no fix.
        if ( Social_Warfare::has_plugin_conflict() ) {
            return;
        }

		// Instantiate a new Buttons Panel.
        $side_panel = new SWP_Buttons_Panel_Side( array( 'content' => "" ) );

		// Determine if the floating buttons are not supposed to print.
        $location = $side_panel->get_float_location();
        if ( 'none' === $location || 'ignore' === $location ) {
            return;
        }

		// Render the html to output to the screen.
        $side_panel->render_html( $echo = true );

    }


    /**
     * The main social_warfare function used to create the buttons.
     *
     * @since  3.0.0 | 01 MAR 2018 | A class based method created which clones
     *                               the public facing function.
     * @param  array $array An array of options and information to pass into the
     *                      buttons function.
     * @return string       The html for a panel of buttons.
     *
     */
    public static function social_warfare( $args = array() ) {
        $Buttons_Panel = new SWP_Buttons_Panel( $args );
    	echo $Buttons_Panel->render_html();
    }
}
