<?php
/**
 * Functions to load the front end display for the
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 * @since     3.0.0  | 21 FEB 2018 | Refactored into a class-based system.
 * @since     3.1.0 | 18 JUN 2018 | Replaced brack array notation.
 *
 */
class SWP_Display {


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
     * @since 3.1.0 | Changed priority for wp_footer. Makes the buttons loads
     *                 This post data instead of data in the loop.
     *
     */
    public function __construct() {


		// The global array of posts that have already been processed.
        global $swp_already_print;

		// The global array of the user-selected options.
        global $swp_user_options;

		// Declare var as array if not already done so.
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
    	// Fetch the user's settings
    	global $swp_user_options;

    	// Only hook into the_content filter if is_singular() is true or
    	// they don't use excerpts on the archive pages.
        if( true === is_singular() || true === $swp_user_options['full_content'] ):
            add_filter( 'the_content', array( $this, 'social_warfare_wrapper' ) , 20 );
            add_filter( 'the_content', array( $this, 'add_content_locator' ), 20);
        endif;

		// If we're not on is_singlular, we'll hook into the excerpt.
        if (false === is_singular() && false === $swp_user_options['full_content']) {
    		// Add the buttons to the excerpts
    		add_filter( 'the_excerpt', array( $this, 'social_warfare_wrapper' ) );
        }
    }


    /**
     * Inserts the empty div for locating Pin images (with javascript).
     *
     * @since  3.0.6 | 14 MAY | Created the method.
     * @param  string $content The WordPress content passed via filter.
     * @return void
     *
     */
    public function add_content_locator( $content ) {
        $content .= '<div class="swp-content-locator"></div>';
        return $content;
    }


    /**
    * A wrapper function for adding the buttons, content, or excerpt.
    *
    * @since  1.0.0
    * @param  string $content The content.
    * @return string $content The modified content
    *
    */
    public function social_warfare_wrapper( $content ) {

		// The global WordPress post object.
        global $post;

      	// Ensure it's not an embedded post
      	if (true === is_singular() && $post->ID !== get_queried_object_id()) {
      		return $content;
      	}

        // Pass the content to the buttons constructor to place them inside.
    	$buttons_panel = new SWP_Buttons_Panel( array( 'content' => $content ) );
        return $buttons_panel->the_buttons( $content );
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

		// Instantiate a new Buttons Panel.
        $side_panel = new SWP_Buttons_Panel( array( 'content' => "" ) );

		// Determine where the buttons are supposed to appear.
        $location = $side_panel->get_float_location();
        if ( 'none' === $location || 'ignore' === $location ) {
            return;
        }

		// Render the html to output to the screen.
        $side_panel->render_floating_HTML( $echo = true );

        return;
    }


    /**
     * The main social_warfare function used to create the buttons.
     *
     * @since  1.4.0
     * @param  array $array An array of options and information to pass into the
     *                      buttons function.
     * @return string       The modified content
     *
     */
    public static function social_warfare( $args = array() ) {

        $Buttons_Panel = new SWP_Buttons_Panel( $args );
    	echo $Buttons_Panel->render_HTML();
    }
}
