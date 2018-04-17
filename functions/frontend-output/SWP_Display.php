<?php
/**
 * Functions to load the front end display for the
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 * @since     2.4.0 | 21 FEB 2018 | Refactored into a class-based system.
 *
 */
class SWP_Display {
    public $already_print;
    public $Button;
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
            $swp_already_print = array();
        }
        $this->already_printed = $swp_already_print;
        $this->options = $swp_user_options;

        // Hook into the template_redirect so that is_singular() conditionals will be ready
        add_action('template_redirect', array($this, 'activate_buttons') );
        // Add the side floating buttons to the footer if they are activated
        if ( in_array( $this->options['float_location'], array( 'left', 'right' ), true ) ) {
            add_action( 'wp_footer', 'socialWarfareSideFloat' );
        }
    }
    /**
     * A function to add the buttons
     *
     * @since 2.1.4
     * @param none
     * @return none
     *
     */
    public function activate_buttons() {
    	// Fetch the user's settings
    	global $swp_user_options;
    	// Only hook into the_content filter if we're is_singular() is true or they don't use excerpts
        if( true === is_singular() || true === $swp_user_options['full_content'] ):
            add_filter( 'the_content', array($this, 'social_warfare_wrapper'), 10 );
        endif;
        if (false === is_singular()) {
    		// Add the buttons to the excerpts
    		add_filter( 'the_excerpt', array($this, 'social_warfare_wrapper') );
        }
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
    	// Fetch our global variables to ensure we haven't already processed this post
    	global $post;
    	$post_id = $post->ID;
    	// Check if it's already been processed
    	if( in_array( $post_id, $this->already_printed) ){
    		return $content;
    	}
    	// Ensure it's not an embedded post
    	if (true === is_singular() && $post_id !== get_queried_object_id()) {
    		return $content;
    	}

    	// Pass the content to the buttons constructor to place them inside.
		$buttons_panel = new SWP_Buttons_Panel( ['content' => $content ]);

		return $buttons_panel->the_buttons();
    }
    /**
     * The main social_warfare function used to create the buttons.
     *
     * @since  1.4.0
     * @param  array $array An array of options and information to pass into the buttons function.
     * @return string $content The modified content
     *
     */
    public static function social_warfare( $args = array() ) {
        $Buttons_Panel = new SWP_Buttons_Panel( $args );

    	return$Buttons_Panel->the_buttons();
    }
}
