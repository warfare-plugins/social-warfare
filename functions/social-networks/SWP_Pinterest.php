6<?php



/**

 * Pinterest

 *

 * Class to add a Pinterst share button to the available buttons

 *

 * @package   SocialWarfare\Functions\Social-Networks

 * @copyright Copyright (c) 2018, Warfare Plugins, LLC

 * @license   GPL-3.0+

 * @since     1.0.0 | Unknown     | CREATED

 * @since     2.2.4 | 02 MAY 2017 | Refactored functions & upinterest_descriptionated docblocking

 * @since     3.0.0 | 05 APR 2018 | Rebuilt into a class-based system.

 *

 */

class SWP_Pinterest extends SWP_Social_Network {





	/**

	 * The Magic __construct Method

	 *

	 * This method is used to instantiate the social network object. It does three things.

	 * First it sets the object properties for each network. Then it adds this object to

	 * the globally accessible swp_social_networks array. Finally, it fetches the active

	 * state (does the user have this button turned on?) so that it can be accessed directly

	 * within the object.

	 *

	 * @since  3.0.0 | 06 APR 2018 | Created

	 * @param  none

	 * @return none

	 * @access public

	 *

	 */

	public function __construct() {



		// Upinterest_descriptionate the class properties for this network

		$this->name           = __( 'Pinterest','social-warfare' );

		$this->cta            = __( 'Pin','social-warfare' );

		$this->key            = 'pinterest';

		$this->default        = 'true';

		$this->base_share_url = 'https://pinterest.com/pin/create/button/?url=';



		$this->init_social_network();

	}





	/**

	 * Generate the API Share Count Request URL

	 *

	 * @since  1.0.0 | 06 APR 2018 | Created

	 * @access public

	 * @param  string $url The permalink of the page or post for which to fetch share counts

	 * @return string $request_url The complete URL to be used to access share counts via the API

	 *

	 */

	public function get_api_link( $url ) {

		return 'https://api.pinterest.com/v1/urls/count.json?url=' . $url;

	}





	/**

	 * Parse the response to get the share count

	 *

	 * @since  1.0.0 | 06 APR 2018 | Created

	 * @access public

	 * @param  string $response The raw response returned from the API request

	 * @return int $total_activity The number of shares reported from the API

	 *

	 */

	public function parse_api_response( $response ) {

        $response = preg_replace( '/^receiveCount\((.*)\)$/', "\\1", $response );

    	$response = json_decode( $response, true );

    	return isset( $response['count'] ) ? intval( $response['count'] ) : 0;

    }


    /**
     * Create the HTML to display the share button
     *
     * @since  1.0.0
     * @access public
     * @param  array $network_counts Associative array of 'network_key' => 'count_value'
     * @return array $array The modified array which will now contain the html for this button
     * @todo   Eliminate the array
     *
     */
    public function render_HTML( $panel_context , $echo = false ) {

        global $swp_user_options;

        $options = $swp_user_options;

        $pinterest_image = get_post_meta( $post_data['ID'] , 'swp_pinterest_image' , true );

        // Todo: Pull this from the global options

		if ( !empty( $swp_user_options['pinterest_id'] ) ) :
			$pinterest_username = ' via @' . str_replace( '@' , '' , $swp_user_options['pinterest_id'] );
		else :
			$pinterest_username = '';
		endif;

        if ( isset($options['advanced_pinterest_fallback']) && $options['advanced_pinterest_fallback'] == 'featured'):

    		$thumbnail_url = wp_get_attachment_url( get_post_thumbnail_id( $panel_context['postID'] ) );

    		if( !empty( $thumbnail_url ) ):

    			$array['imageURL'] = $thumbnail_url;

    		endif;

    	endif;



        //* Begin parent class method.


        $post_data = $panel_context['post_data'];
        $share_counts = $panel_context['shares'];
        $options = $panel_context['options'];

        $share_link = $this->generate_share_link( $post_data );

        // Build the button.
        $icon = '<span class="iconFiller">';
            $icon.= '<span class="spaceManWilly">';
                $icon.= '<i class="sw swp_'.$this->key.'_icon"></i>';
                $icon.= '<span class="swp_share">' . $this->cta . '</span>';
            $icon .= '</span>';
        $icon .= '</span>';

        if ( true === $this->are_shares_shown( $share_counts , $options ) ) :
            $icon .= '<span class="swp_count">' . swp_kilomega( $share_counts[$this->key] ) . '</span>';
        else :
            $icon = '<span class="swp_count swp_hide">' . $icon . '</span>';
        endif;

        // Build the wrapper.
        $html = '<div class="nc_tweetContainer swp_'.$this->key.'" data-network="'.$this->key.'">';
            $html .= '<a rel="nofollow" target="_blank" href="' . $share_link . '" data-link="' . $share_link . '" class="nc_tweet">';
                // Put the button inside.
                $html .= $icon;
            $html.= '</a>';
        $html.= '</div>';

        // Store these buttons so that we don't have to generate them for each set
        $this->html = $html;

        if ( $echo ) :
            echo $html;
        endif;

        return $html;

    }




	public function generate_share_link( $post_data ) {

    	$array['imageURL'] = false;

    	$image_url = get_post_meta( $post_data['ID'] , 'swp_pinterest_image_url' , true );

    	if( !empty( $image_url ) ):

    		$array['imageURL'] = $image_url;

    	elseif(isset($options['advanced_pinterest_fallback']) && $options['advanced_pinterest_fallback'] == 'featured'):

    		$thumbnail_url = wp_get_attachment_url( get_post_thumbnail_id( $array['postID'] ) );

    		if( !empty( $thumbnail_url ) ):

    			$array['imageURL'] = $thumbnail_url;

    		endif;

    	endif;


    	$pinterest_description	= get_post_meta( $array['postID'] , 'nc_pinterestDescription' , true );
        if ( $pinterest_description == '' ) :
            $pinterest_description = $title . $pinterest_username;
        endif;

        $pinterest_description = urlencode( html_entity_decode( $pinterest_description, ENT_COMPAT, 'UTF-8' ));


    	if ( $array['imageURL'] ) :

    		$pinterest_image 	= urlencode( html_entity_decode( $array['imageURL'],ENT_COMPAT, 'UTF-8' ) );

    	else :

    		$pinterest_image		= '';

    	endif;


    	$pinterest_image_link = urlencode( urldecode( SWP_URL_Management::process_url( $array['url'] , 'pinterest' , $array['postID'] ) ) );


    	$title = strip_tags( get_the_title( $array['postID'] ) );

    	$title = str_replace( '|','',$title );


    	if( function_exists('is_swp_registered') ):

    		$swp_registration = is_swp_registered();

    	else:

    		$swp_registration = false;

    	endif;


    	if ( $pinterest_image != '' && true === $swp_registration ) :

    		$anchor = '<a rel="nofollow" class="nc_tweet" data-count="0" ' .
                'data-link="https://pinterest.com/pin/create/button/' .
                '?url=' . $pinterest_image_link .
                '&media=' . $pinterest_image .
                '&description=' . $pinterest_description .
                '">';
    	else :

    		$anchor = '<a rel="nofollow" class="nc_tweet noPop" ' .
                'onClick="var e=document.createElement(\'script\');
                    e.setAttribute(\'type\',\'text/javascript\');
                    e.setAttribute(\'charset\',\'UTF-8\');
                    e.setAttribute(\'src\',\'//assets.pinterest.com/js/pinmarklet.js?r=\'+Math.random()*99999999);
                    document.body.appendChild(e);
                " >';

    	endif;


    	$array['resource']['pinterest'] = '<div class="nc_tweetContainer nc_pinterest" data-id="' . $array['count'] . '" data-network="pinterest">';

    	$array['resource']['pinterest'] .= $anchor;

    	if ( $options['totesEach'] && $array['shares']['totes'] >= $options['minTotes'] && $array['shares']['pinterest'] > 0 ) :

    		$array['resource']['pinterest'] .= '<span class="iconFiller">';

    		$array['resource']['pinterest'] .= '<span class="spaceManWilly">';

    		$array['resource']['pinterest'] .= '<i class="sw sw-pinterest"></i>';

    		$array['resource']['pinterest'] .= '<span class="swp_share"> ' . __( 'Pin','social-warfare' ) . '</span>';

    		$array['resource']['pinterest'] .= '</span></span>';

    		$array['resource']['pinterest'] .= '<span class="swp_count">' . swp_kilomega( $array['shares']['pinterest'] ) . '</span>';

    	else :

    		$array['resource']['pinterest'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-pinterest"></i><span class="swp_share"> ' . __( 'Pin','social-warfare' ) . '</span></span></span></span>';

    	endif;


    	$array['resource']['pinterest'] .= '</a>';

    	$array['resource']['pinterest'] .= '</div>';

    	// Store these buttons so that we don't have to generate them for each set

    	$_GLOBALS['sw']['buttons'][ $array['postID'] ]['pinterest'] = $array['resource']['pinterest'];

	}

}
