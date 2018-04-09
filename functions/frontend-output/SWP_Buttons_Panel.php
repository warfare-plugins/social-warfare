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

$Buttons = new SWP_Buttons_Panel( false );
$Buttons->set_option( 'custom_color' , '#FF0000' );
$Buttons->set_option( 'networks' , array( 'Twitter' , 'Facebook' , 'Pinterest' ) );
$Buttons->output_HTML();


/**
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

    public function __construct() {
        localize_options();
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
	private function localize_options( $args = array() ) {

		// First, clone the global options into our local property.
		global $swp_user_options;
		$this->options = $swp_user_options;

		// Second, if the user has passed in any args, merge them in.
		array_merge( $this->options , $args );

	}


	public function create_buttons( $args = array() ) {

	}

    /**
    * THE SHARE BUTTONS FUNCTION:
    *
    * This function accepts an array of parameters resulting in the outputting
    * of the Social Warfare Buttons.
    *
    * @since 1.0.0
    * @access public
    * @param array $array {
    *     @type mixed  $content The post content to which we append the buttons.
    *                           Default FALSE. Accepts string.
    *
    *     @type string $where   Overwrites the default location in relation
    *                           to content.
    *                           Accepts 'above', 'below', 'both', 'none'
    *
    *     @type bool   $echo    True echos the buttons. False returns HTML.
    *                           Default true.
    * }
    * @return string $content   The modified content
    */
    public function the_buttons( $array = array() ) {
        global $swp_user_options;
        $this->options = $swp_user_options;

    	if ( !is_array($array) ) {
    		$array = array();
    	}

        $defaults = array(
            'where'     => 'default',
            'echo'      => true,
            'content'   => false,
        );

        // *Set default array values.
        $array = array_merge( $defaults, $array );

    	// Get the options, or create them if they don't exist
    	if ( isset( $array['postID'] ) ) :
    		$post_id = $array['postID'];
    	else :
    		$post_id = get_the_ID();
    	endif;

    	// Check to see if display location was specifically defined for this post
    	$spec_where = get_post_meta( $post_id, 'nc_postLocation', true );

        if ( !$spec_where ) {
            $spec_where = 'default';
    	};

    	if ( $array['where'] == 'default' ) :
    		// If we are on the home page
    		if( is_front_page() ):
    			$array['where'] = $this->options['location_home'];

    		// If we are on a singular page
    		elseif ( is_singular() && !is_home() && !is_archive() && !is_front_page() ) :
    			if ( $spec_where == 'default' || $spec_where == '' ) :
    				$post_type = get_post_type( $post_id );

    				if ( isset( $this->options[ 'location_' . $post_type ] ) ) :
    					$array['where'] = $this->options[ 'location_' . $post_type ];
    				else :
    					$array['where'] = 'none';
    				endif;

    			else :
    				$array['where'] = $spec_where;
    			endif;

    		// If we are anywhere else besides the home page or a singular
    		else :
    			$array['where'] = $this->options['location_archive_categories'];
    		endif;
    	endif;

    	// *Do not show on attachement pages.
    	if ( true === is_attachment() ) :
    		return $array['content'];

    	// Disable the buttons on Buddy Press pages
    	elseif ( function_exists( 'is_buddypress' ) && is_buddypress() ) :
    		return $array['content'];

    	// Disable the buttons if the location is set to "None / Manual"
    	elseif ( 'none' === $array['where'] && !isset( $array['devs'] ) ) :
    		return $array['content'];

    	// Disable the button if we're not in the loop, unless there is no content which means the function was called by a developer.
    	elseif ( ( !is_main_query() || !in_the_loop()) && !isset( $array['devs'] ) ) :
    		return $array['content'];

    	// Don't do anything if we're in the admin section
    	elseif ( is_admin() ) :
    		return $array['content'];

    	// If all the checks pass, let's make us some buttons!
    	else :

    		// Set the options for the horizontal floating bar
    		$post_type = get_post_type( $post_id );
    		$spec_float_where = get_post_meta( $post_id , 'nc_floatLocation' , true );

    		if ( isset( $array['floating_panel'] ) && $array['floating_panel'] == 'ignore' ) :
    			$floatOption = 'float_ignore';
    		elseif ( $spec_float_where == 'off' && $this->options['button_alignment'] != 'float_ignore' ) :
    				$floatOption = 'floatNone';
    		elseif ( $this->options['floating_panel'] && is_singular() && $this->options[ 'float_location_' . $post_type ] == 'on' ) :
    			$floatOption = 'floating_panel' . ucfirst( $this->options['float_position'] );
    		else :
    			$floatOption = 'floatNone';
    		endif;

    		// Disable the plugin on feeds, search results, and non-published content
    		if ( !is_feed() && !is_search() && get_post_status( $post_id ) == 'publish' ) :

    			// Acquire the social stats from the networks
    			if ( isset( $array['url'] ) ) :
    				$buttons_array['url'] = $array['url'];
    			else :
    				$buttons_array['url'] = get_permalink( $post_id );
    			endif;

    			if ( isset( $array['scale'] ) ) :
    				$scale = $array['scale'];
    			else :
    				$scale = $this->options['button_size'];
    			endif;

    			// Fetch the share counts
    			$buttons_array['shares'] = get_social_warfare_shares( $post_id );

    			// Pass the swp_options into the array so we can pass it into the filter
    			$buttons_array['options'] = $this->options;

    			// Customize which buttosn we're going to display
    			if ( isset( $array['buttons'] ) ) :
					var_dump($array['buttons']);
    				// Fetch the global names and keys
    				$swp_options = array();
    				$swp_available_options = apply_filters( 'swp_options', $swp_options );
    				$available_buttons = $swp_available_options['options']['swp_display']['buttons']['content'];

    				// Split the comma separated list into an array
    				$button_set_array = explode( ',', $array['buttons'] );

    				// Match the names in the list to their appropriate system-wide keys
    				$i = 0;
    				foreach ( $button_set_array as $button ) :

    					// Trim the network name in case of white space
    					$button = trim( $button );

    					// Convert the names to their systme-wide keys
    					if ( swp_recursive_array_search( $button , $available_buttons ) ) :
    						$key = swp_recursive_array_search( $button , $available_buttons );

    						// Store the result in the array that gets passed to the HTML generator
    						$buttons_array['buttons'][ $key ] = $button;

    						// Declare a default share count of zero. This will be overriden later
    						if ( !isset( $buttons_array['shares'][ $key ] ) ) :
    							$buttons_array['shares'][ $key ] = 0;
    						endif;

    					endif;

    					$button_set_array[ $i ] = $button;
    					++$i;
    				endforeach;

    				// Manually turn the total shares on or off
    				if ( array_search( 'Total', $button_set_array ) ) :
                        $buttons_array['buttons']['total_shares'] = 'Total';
                    endif;

    			endif;

    			// Setup the buttons array to pass into the 'swp_network_buttons' hook
    			$buttons_array['count'] = 0;
    			$buttons_array['total_shares'] = 0;

    			if ( ( $buttons_array['options']['total_shares'] && $buttons_array['shares']['total_shares'] >= $buttons_array['options']['minimum_shares'] && !isset( $array['buttons'] ) )
    				|| 	( isset( $buttons_array['buttons'] ) && isset( $buttons_array['buttons']['total_shares'] ) && $buttons_array['total_shares'] >= $this->options['minimum_shares'] ) ) :
    				++$buttons_array['count'];
    			endif;

    			$buttons_array['html'] = array();
    			$buttons_array['postID'] = $post_id;

    			// Disable the subtitles plugin to avoid letting them inject their subtitle into our share titles
    			if ( is_plugin_active( 'subtitles/subtitles.php' ) && class_exists( 'Subtitles' ) ) :
    				remove_filter( 'the_title', array( Subtitles::getinstance(), 'the_subtitle' ), 10, 2 );
    			endif;

    			// This array will contain the HTML for all of the individual buttons
    			// $buttons_array = apply_filters( 'swp_network_buttons' , $buttons_array );
				global $swp_social_networks;
				foreach( $swp_social_networks as $network ):
					if( true === $network->is_active() ):
						$buttons_array['html'][$network->key] = $network->render_html($buttons_array);
						if(isset($buttons_array['shares'][$network->key])):
							$buttons_array['total_shares'] += intval( $buttons_array['shares'][$network->key] );
						endif;
						++$buttons_array['count'];
					endif;
				endforeach;

    			// Create the social panel
    			$assets = '<div class="nc_socialPanel swp_' . $this->options['button_shape'] . ' swp_d_' . $this->options['default_colors'] . ' swp_i_' . $this->options['single_colors'] . ' swp_o_' . $this->options['hover_colors'] . ' scale-' . $scale*100 .' scale-' . $this->options['button_alignment'] . '" data-position="' . $this->options['location_post'] . '" data-float="' . $floatOption . '" data-count="' . $buttons_array['count'] . '" data-floatColor="' . $this->options['float_background_color'] . '" data-emphasize="'.$this->options['emphasize_icons'].'">';

    			// Setup the total shares count if it's on the left
    			if ( ( $this->options['total_shares'] && $this->options['totals_alignment'] == 'totals_left' && $buttons_array['total_shares'] >= $this->options['minimum_shares'] && !isset( $array['buttons'] ) || ( $this->options['totals_alignment'] == 'totals_left' && isset( $buttons_array['buttons'] ) && isset( $buttons_array['buttons']['total_shares'] ) && $buttons_array['total_shares'] >= $this->options['minimum_shares'] ))
    			|| 	($this->options['totals_alignment'] == 'totals_left' && isset( $array['buttons'] ) && isset( $array['buttons']['total_shares'] ) && $buttons_array['total_shares'] >= $this->options['minimum_shares'] ) ) :
    				++$buttons_array['count'];
    				$assets .= '<div class="nc_tweetContainer totes totesalt" data-id="' . $buttons_array['count'] . '" >';
    				$assets .= '<span class="swp_count">' . swp_kilomega( $buttons_array['total_shares'] ) . ' <span class="swp_label">' . __( 'Shares','social-warfare' ) . '</span></span>';
    				$assets .= '</div>';
    			endif;

    			// Sort the buttons according to the user's preferences
    			if ( isset( $buttons_array ) && isset( $buttons_array['buttons'] ) ) :
    				foreach ( $buttons_array['buttons'] as $key => $value ) :
    					if ( isset( $buttons_array['html'][ $key ] ) ) :
    						$assets .= $buttons_array['html'][ $key ];
    					endif;
    				endforeach;
    			elseif ( $this->options['order_of_icons_method'] == 'manual' ) :
    				foreach ( $this->options['order_of_icons'] as $key => $value ) :
    					if ( isset( $buttons_array['html'][ $key ] ) ) :
    						$assets .= $buttons_array['html'][ $key ];
    					endif;
    				endforeach;
    			elseif ( $this->options['order_of_icons'] == 'dynamic' ) :
    				arsort( $buttons_array['shares'] );
    				foreach ( $buttons_array['shares'] as $thisIcon => $status ) :
    					if ( isset( $buttons_array['html'][ $thisIcon ] ) ) :
    						$assets .= $buttons_array['html'][ $thisIcon ];
    					endif;
    				endforeach;
    			endif;

    			// Create the Total Shares Box if it's on the right
    			if ( ( $this->options['total_shares'] && $this->options['totals_alignment'] != 'totals_left' && $buttons_array['total_shares'] >= $this->options['minimum_shares'] && !isset( $buttons_array['buttons'] ) )
    			|| 	( $this->options['totals_alignment'] != 'totals_left' && isset( $buttons_array['buttons'] ) && isset( $buttons_array['buttons']['total_shares'] ) && $buttons_array['total_shares'] >= $this->options['minimum_shares'] ) ) :
    				++$buttons_array['count'];
    				if ( $this->options['totals_alignment'] == 'total_shares' ) :
    					$assets .= '<div class="nc_tweetContainer totes" data-id="' . $buttons_array['count'] . '" >';
    					$assets .= '<span class="swp_count">' . swp_kilomega( $buttons_array['total_shares'] ) . ' <span class="swp_label">' . __( 'Shares','social-warfare' ) . '</span></span>';
    					$assets .= '</div>';
    				else :
    					$assets .= '<div class="nc_tweetContainer totes totesalt" data-id="' . $buttons_array['count'] . '" >';
    					$assets .= '<span class="swp_count"><span class="swp_label">' . __( 'Shares','social-warfare' ) . '</span> ' . swp_kilomega( $buttons_array['total_shares'] ) . '</span>';
    					$assets .= '</div>';
    				endif;
    			endif;

    			// Close the Social Panel
    			$assets .= '</div>';

    			// Reset the cache timestamp if needed
    			if ( swp_is_cache_fresh( $post_id ) == false  && isset($this->options['cache_method']) && 'legacy' === $this->options['cache_method'] ) :
    				delete_post_meta( $post_id,'swp_cache_timestamp' );
    				update_post_meta( $post_id,'swp_cache_timestamp',floor( ((date( 'U' ) / 60) / 60) ) );
    			endif;

    			if ( isset( $array['genesis'] ) ) :
    				if ( $array['where'] == 'below' && $array['genesis'] == 'below' ) :
    					return $assets;
    				elseif ( $array['where'] == 'above' && $array['genesis'] == 'above' ) :
    					return $assets;
    				elseif ( $array['where'] == 'both' ) :
    					return $assets;
    				elseif ( $array['where'] == 'none' ) :
    					return false;
    				endif;
    			else :
    				if ( $array['echo'] == false && $array['where'] != 'none' ) :
    					return $assets;
    				elseif ( $array['content'] === false ) :
    					echo $assets;
    				elseif ( isset( $array['where'] ) && $array['where'] == 'below' ) :
    					$content = $array['content'] . '' . $assets;
    					return $content;
    				elseif ( isset( $array['where'] ) && $array['where'] == 'above' ) :
    					$content = $assets . '' . $array['content'];
    					return $content;
    				elseif ( isset( $array['where'] ) && $array['where'] == 'both' ) :
    					$content = $assets . '' . $array['content'] . '' . $assets;
    					return $content;
    				elseif ( isset( $array['where'] ) && $array['where'] == 'none' ) :
    					return $array['content'];
    				endif;
    			endif;
    		else :
    			return $array['content'];
    		endif;

    	endif;
    }
}
