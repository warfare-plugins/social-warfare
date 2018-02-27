<?php

/**
 * Register and output header meta tags
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

class SWP_Buttons_Panel {
    public $options;

    public function __construct() {
        // *$this->options can not be set to $swp_user_options yet
        // *as the options are not defined at this point.
    }

    /**
    * Prints the buttons to the page.
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
    			$array['where'] = $this->options['locationHome'];

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
    			$array['where'] = $this->options['locationSite'];
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

    		if ( isset( $array['float'] ) && $array['float'] == 'ignore' ) :
    			$floatOption = 'float_ignore';
    		elseif ( $spec_float_where == 'off' && $this->options['buttonFloat'] != 'float_ignore' ) :
    				$floatOption = 'floatNone';
    		elseif ( $this->options['float'] && is_singular() && $this->options[ 'float_location_' . $post_type ] == 'on' ) :
    			$floatOption = 'float' . ucfirst( $this->options['floatOption'] );
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
    				$scale = $this->options['buttonSize'];
    			endif;

    			// Fetch the share counts
    			$buttons_array['shares'] = get_social_warfare_shares( $post_id );

    			// Pass the swp_options into the array so we can pass it into the filter
    			$buttons_array['options'] = $this->options;

    			// Customize which buttons are displayed.
    			if ( isset( $array['buttons'] ) ) :

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
                        $buttons_array['buttons']['totes'] = 'Total';
                    endif;

    			endif;

    			// Setup the buttons array to pass into the 'swp_network_buttons' hook
    			$buttons_array['count'] = 0;
    			$buttons_array['totes'] = 0;

    			if ( ( $buttons_array['options']['totes'] && $buttons_array['shares']['totes'] >= $buttons_array['options']['minTotes'] && !isset( $array['buttons'] ) )
    				|| 	( isset( $buttons_array['buttons'] ) && isset( $buttons_array['buttons']['totes'] ) && $buttons_array['totes'] >= $this->options['minTotes'] ) ) :
    				++$buttons_array['count'];
    			endif;

    			$buttons_array['resource'] = array();
    			$buttons_array['postID'] = $post_id;

    			// Disable the subtitles plugin to avoid letting them inject their subtitle into our share titles
    			if ( is_plugin_active( 'subtitles/subtitles.php' ) && class_exists( 'Subtitles' ) ) :
    				remove_filter( 'the_title', array( Subtitles::getinstance(), 'the_subtitle' ), 10, 2 );
    			endif;

    			// This array will contain the HTML for all of the individual buttons
    			$buttons_array = apply_filters( 'swp_network_buttons' , $buttons_array );

    			// Create the social panel
    			$assets = '<div class="nc_socialPanel swp_' . $this->options['visualTheme'] . ' swp_d_' . $this->options['dColorSet'] . ' swp_i_' . $this->options['iColorSet'] . ' swp_o_' . $this->options['oColorSet'] . ' scale-' . $scale*100 .' scale-' . $this->options['buttonFloat'] . '" data-position="' . $this->options['location_post'] . '" data-float="' . $floatOption . '" data-count="' . $buttons_array['count'] . '" data-floatColor="' . $this->options['floatBgColor'] . '" data-emphasize="'.$this->options['emphasize_icons'].'">';

    			// Setup the total shares count if it's on the left
    			if ( ( $this->options['totes'] && $this->options['swTotesFormat'] == 'totesAltLeft' && $buttons_array['totes'] >= $this->options['minTotes'] && !isset( $array['buttons'] ) || ( $this->options['swTotesFormat'] == 'totesAltLeft' && isset( $buttons_array['buttons'] ) && isset( $buttons_array['buttons']['totes'] ) && $buttons_array['totes'] >= $this->options['minTotes'] ))
    			|| 	($this->options['swTotesFormat'] == 'totesAltLeft' && isset( $array['buttons'] ) && isset( $array['buttons']['totes'] ) && $buttons_array['totes'] >= $this->options['minTotes'] ) ) :
    				++$buttons_array['count'];
    				$assets .= '<div class="nc_tweetContainer totes totesalt" data-id="' . $buttons_array['count'] . '" >';
    				$assets .= '<span class="swp_count">' . swp_kilomega( $buttons_array['totes'] ) . ' <span class="swp_label">' . __( 'Shares','social-warfare' ) . '</span></span>';
    				$assets .= '</div>';
    			endif;

    			// Sort the buttons according to the user's preferences
    			if ( isset( $buttons_array ) && isset( $buttons_array['buttons'] ) ) :
    				foreach ( $buttons_array['buttons'] as $key => $value ) :
    					if ( isset( $buttons_array['resource'][ $key ] ) ) :
    						$assets .= $buttons_array['resource'][ $key ];
    					endif;
    				endforeach;
    			elseif ( $this->options['orderOfIconsSelect'] == 'manual' ) :
    				foreach ( $this->options['newOrderOfIcons'] as $key => $value ) :
    					if ( isset( $buttons_array['resource'][ $key ] ) ) :
    						$assets .= $buttons_array['resource'][ $key ];
    					endif;
    				endforeach;
    			elseif ( $this->options['orderOfIconsSelect'] == 'dynamic' ) :
    				arsort( $buttons_array['shares'] );
    				foreach ( $buttons_array['shares'] as $thisIcon => $status ) :
    					if ( isset( $buttons_array['resource'][ $thisIcon ] ) ) :
    						$assets .= $buttons_array['resource'][ $thisIcon ];
    					endif;
    				endforeach;
    			endif;

    			// Create the Total Shares Box if it's on the right
    			if ( ( $this->options['totes'] && $this->options['swTotesFormat'] != 'totesAltLeft' && $buttons_array['totes'] >= $this->options['minTotes'] && !isset( $buttons_array['buttons'] ) )
    			|| 	( $this->options['swTotesFormat'] != 'totesAltLeft' && isset( $buttons_array['buttons'] ) && isset( $buttons_array['buttons']['totes'] ) && $buttons_array['totes'] >= $this->options['minTotes'] ) ) :
    				++$buttons_array['count'];
    				if ( $this->options['swTotesFormat'] == 'totes' ) :
    					$assets .= '<div class="nc_tweetContainer totes" data-id="' . $buttons_array['count'] . '" >';
    					$assets .= '<span class="swp_count">' . swp_kilomega( $buttons_array['totes'] ) . ' <span class="swp_label">' . __( 'Shares','social-warfare' ) . '</span></span>';
    					$assets .= '</div>';
    				else :
    					$assets .= '<div class="nc_tweetContainer totes totesalt" data-id="' . $buttons_array['count'] . '" >';
    					$assets .= '<span class="swp_count"><span class="swp_label">' . __( 'Shares','social-warfare' ) . '</span> ' . swp_kilomega( $buttons_array['totes'] ) . '</span>';
    					$assets .= '</div>';
    				endif;
    			endif;

    			// Close the Social Panel
    			$assets .= '</div>';

    			// Reset the cache timestamp if needed
    			if ( swp_is_cache_fresh( $post_id ) == false  && isset($this->options['cacheMethod']) && 'legacy' === $this->options['cacheMethod'] ) :
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
