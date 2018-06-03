<?php
/**
 * Functions to load the front end display for the
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 * @since     3.0.0 | 21 FEB 2018 | Refactored into a class-based system.
 *
 */
class SWP_Display {
    public $already_print;

    public function __construct() {
        /**
         * A global for storing post ID's to prevent duplicate processing on the same posts
         * @since 2.1.4
         * @var array $swp_already_print Array of post ID's that have been processed during this pageload.
         *
         */
        global $swp_already_print;
        global $swp_user_options;

        if ( !is_array( $swp_already_print ) ) {
            $swp_already_print = [];
        }

        $this->already_printed = $swp_already_print;
        $this->options = $swp_user_options;

        // Hook into the template_redirect so that is_singular() conditionals will be ready
        add_action('template_redirect', [$this, 'activate_buttons'] );
        add_action( 'wp_footer', [$this, 'floating_buttons'] );
    }


    /**
     * A function to add the buttons
     *
     * @since 2.1.4
     * @since 3.0.6 | 14 MAY | Added second filter for the_content.
     * @param none
     * @return none
     *
     */
    public function activate_buttons() {
    	// Fetch the user's settings
    	global $swp_user_options;

    	// Only hook into the_content filter if we're is_singular() is true or they don't use excerpts
        if( true === is_singular() || true === $swp_user_options['full_content'] ):
            add_filter( 'the_content', [$this, 'social_warfare_wrapper'], 20 );
            add_filter( 'the_content', [$this, 'add_content_locator'], 20);
        endif;

        if (false == is_singular()) {
            global $wp_filter;
    		// Add the buttons to the excerpts

    		add_filter( 'the_excerpt', [$this, 'social_warfare_wrapper'] );
        }
    }

    /**
     * Inserts the empty div for locating Pin images (with javascript).
     *
     * @param string $content The WordPress content passed via filter.
     * @since 3.0.6 | 14 MAY | Created the method.
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
    * @return String $content The modified content
    *
    */
    public function social_warfare_wrapper( $content ) {
        global $post;
        
      	// Ensure it's not an embedded post
      	if (true === is_singular() && $post->ID !== get_queried_object_id()) {
      		return $content;
      	}

        // Pass the content to the buttons constructor to place them inside.
    		$buttons_panel = new SWP_Buttons_Panel( ['content' => $content ]);

        return $buttons_panel->the_buttons( $content );
    }


    function floating_buttons() {

        $side_panel = new SWP_Buttons_Panel( ['content' => "" ]);

        $location = $side_panel->get_float_location();

        if ( 'none' === $location || 'ignore' === $location ) {
            return;
        }

        $side_panel->render_floating_HTML( $echo = true );

        return;
    }


    /**
     * The main social_warfare function used to create the buttons.
     *
     * @since  1.4.0
     * @param  array $array An array of options and information to pass into the buttons function.
     * @return string $content The modified content
     *
     */
    public static function social_warfare( $args = [] ) {
        $Buttons_Panel = new SWP_Buttons_Panel( $args );

    	echo $Buttons_Panel->render_HTML();
    }
}
