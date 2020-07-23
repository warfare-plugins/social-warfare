<?php
/**
 * Pinterest
 *
 * Class to add a Pinterest share button to the available buttons
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
		$this->name           = __( 'Pinterest', 'social-warfare' );
		$this->cta            = __( 'Pin', 'social-warfare' );
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
	 * @since  3.0.0 | 01 MAY 2018 | Re-wrote the function to a class method.
	 * @since  3.0.6 | 14 MAY 2018 | Appended $pinterest_username to $pinterest_description.
	 * @since  3.0.9 | 04 JUN 2018 | Updated the check for pinterest image.
	 * @access public
	 * @return array $panel_context Array of
	 *                   ['post_data']  => metadata about the post;
	 *                   ['shares']     => share count data
	 *                   ['options']    => swp_user_options
	 * @param  bool $echo If true, this will immediately echo its code rather than save it for later.
	 *
	 */
	public function render_HTML( $panel_context, $echo = false ) {

		/**
		 * The global array containing the admin's options as set on the Social
		 * Warfare admin options page.
		 *
		 */
		global $swp_user_options;
		$options = $swp_user_options;

		// The ID of the current WordPress post or page.
		$post_id = $panel_context['post_data']['ID'];

		/**
		 * The processed permalink for the current post. Since this passes
		 * through our SWP_Link_Manager::process_url() method it will have the
		 * Google Anaytlics UTM and link shortening applied (if those features
		 * are turned on in the options).
		 *
		 */
		$post_url = urlencode( urldecode( SWP_Link_Manager::process_url( $panel_context['post_data']['permalink'], 'pinterest', $post_id ) ) );


		/**
		 * This meta or custom field is where the ID is stored for the image
		 * that the user has uploaded into the "Pinterest Image" spot. This will
		 * return an array of images ID's.
		 *
		 */
		$metabox_pinterest_image = get_post_meta( $post_id , 'swp_pinterest_image', false );


		/**
		 * This conditonal will trigger if the user has uploaded an image into
		 * the "Pinterest Image" field.
		 *
		 */
		if ( false === empty( $metabox_pinterest_image ) && false !== $metabox_pinterest_image ):

			// If the user has uploaded multiple Pinterest images.
			if( count( $metabox_pinterest_image ) > 1 ) {
				$pinterest_image = 'multiple';

			// If the user has uploaded only one single Pinterest image.
			} else {
				$pinterest_image = wp_get_attachment_url( $metabox_pinterest_image[0] );
			}


		/**
		 * The user has not uploaded a designated Pinterest image, then we will
		 * check for fallback image conditions and use those if necessary.
		 *
		 * In this case, we'll attempt to use the post's designated featured
		 * image as the Pinterest image.
		 *
		 */
		elseif ( 'featured' === SWP_Utility::get_option( 'pinterest_fallback' ) ):
			$pinterest_image = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
		else :
			$pinterest_image = '';
		endif;


		/**
		 * This section will get the Pinterest username if one is set in the
		 * admin options page. We will include this in the description when shared.
		 *
		 */
		$pinterest_username = '';
		$pinterest_id = SWP_Utility::get_option( 'pinterest_id' );
		if ( false === empty( $pinterest_id ) ) {
			 $pinterest_username = ' via @' . str_replace( '@' , '' , $pinterest_id );
		}

		// The post's title.
		$title = str_replace( '|', '', strip_tags( $panel_context['post_data']['post_title'] ) );

		/**
		 * Check if the user has provided a description for the "Pinterest
		 * Description" field in the post meta boxes.
		 *
		 */
		$pinterest_description	= get_post_meta( $post_id, 'swp_pinterest_description', true );


		/**
		 * In some bizarre instances, the description was returned as an array
		 * even when the "true" parameter is provided as the third parameter in
		 * the get_post_meta() function above. If this is the case, we'll strip
		 * it out here and just grab the very first item returned in that array.
		 *
		 */
		if( is_array( $pinterest_description ) && !empty( $pinterest_description ) ) {
			$pinterest_description = $pinterest_description[0];
			// delete_post_meta( $post_id , 'swp_pinterest_description' );
			update_post_meta( $post_id , 'swp_pinterest_description' , $pinterest_description );
		}


		/**
		 * If no Pinterest description was provided, then we'll use the post
		 * title as the description.
		 *
		 */
		if ( empty( $pinterest_description ) ) {
			$pinterest_description = $title;
		}

		$pinterest_username = SWP_Pinterest::get_via();
		$pinterest_description = SWP_Pinterest::trim_pinterest_description( $pinterest_description, $pinterest_username );


		/**
		 * Now that we've processed all of our variables, we'll proceed to put
		 * together the HTML for the button.
		 *
		 * If we have a designated Pinterest image, we'll start here...
		 */
		if ( !empty( $pinterest_image ) ) {

			// If the user has uploaded multiple Pinterest images...
			if( 'multiple' === $pinterest_image ) {

				// Build all the data needed by the JS process into this array.
				$pin_data                = array();
				$pin_data['description'] = $pinterest_description;
				$pin_data['url']         = $post_url;

				// Store the permalink of each Pinterest image in the "images" indice.
				foreach( $metabox_pinterest_image as $image ) {
					$pin_data['images'][] = wp_get_attachment_url( $image );
				}

				$json_pin_data = json_encode( $pin_data, JSON_HEX_APOS );
				$anchor = '<a rel="nofollow noreferrer noopener" class="nc_tweet swp_share_link pinterest_multi_image_select" data-count="0" data-link="#" data-pins=\''.$json_pin_data.'\'>';

			// If the user has uploaded one single Pinterest image...
			// TODO: Document
			} else {
				$link = 'https://pinterest.com/pin/create/button/' .
				'?url=' . $panel_context['post_data']['permalink'] .
				'&media=' . urlencode( $pinterest_image ) .
				'&description=' . urlencode( $pinterest_description );
				$anchor = '<a rel="nofollow noreferrer noopener" class="nc_tweet swp_share_link" data-count="0" ' .
						'data-link="'.$link.'" '.SWP_AMP::display_if_amp('href="'.$link.'"').' >';
			}

		// If the user has not uploaded any Pinterest images.
		} else {
			if( SWP_AMP::is_amp() ) {
				$link = 'https://pinterest.com/pin/create/button/' .
				'?url=' . $panel_context['post_data']['permalink'];
				$anchor = '<a rel="nofollow noreferrer noopener" class="nc_tweet swp_share_link" data-count="0" ' .
						'data-link="'.$link.'" href="'.$link.'" >';
			} else {
				$anchor = '<a rel="nofollow noreferrer noopener" class="nc_tweet swp_share_link noPop" ' .
						'onClick="var e=document.createElement(\'script\');
							e.setAttribute(\'type\',\'text/javascript\');
							e.setAttribute(\'charset\',\'UTF-8\');
							e.setAttribute(\'src\',\'//assets.pinterest.com/js/pinmarklet.js?r=\'+Math.random()*99999999);
							document.body.appendChild(e);
						" >';
			}
		}

		 //* Begin parent class method.

		 $post_data = $panel_context['post_data'];
		 $share_counts = $panel_context['shares'];
		 $options = $panel_context['options'];

		 // Build the button.
		 $icon = '<span class="iconFiller">';
			 $icon.= '<span class="spaceManWilly">';
				 $icon.= '<i class="sw swp_'.$this->key.'_icon"></i>';
				 $icon.= '<span class="swp_share">' . $this->cta . '</span>';
			 $icon .= '</span>';
		 $icon .= '</span>';

		 if ( true === $this->are_shares_shown( $panel_context ) ) :
			 $icon .= '<span class="swp_count">' . SWP_Utility::kilomega( $share_counts[$this->key] ) . '</span>';
		 else :
			 $icon = '<span class="swp_count swp_hide">' . $icon . '</span>';
		 endif;

		 // Build the wrapper.
		 $html = '<div class="nc_tweetContainer swp_share_button swp_'.$this->key.'" data-network="'.$this->key.'">';
			 $html .= $anchor;
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
		return 0;
	 }

	 /**
	  * Trims the text of a pinterest description down to the 500 character max.
	  *
	  * @since  3.5.0 | 21 FEB 2019 | Created.
	  * @param  string $pinterest_description The target Pinterest description.
	  * @return string The same pinterest description, capped at 500 characters.
	  *
	  */
	 public static function trim_pinterest_description( $pinterest_description, $via = '') {
		 if ( strlen( $pinterest_description ) > 500 ) {
			 /**
			  * The provided description is too long before we have added
			  * anything. We need to trim it before appending the @via.
			  *
			  */
			 $read_more = '... ' . $via;
			 $cutoff = 500 - strlen( $read_more );

			 $pinterest_description = substr( $pinterest_description, 0, $cutoff );
			 $pinterest_description .= $read_more;
		 }
		 else {
			 /**
			  * The description length + via length would be too long, so
			  * trim a little bit of description so via will fit.
			  *
			  */
			 if ( strlen( $pinterest_description ) + strlen( $via ) > 500 ) {
				 $cutoff = 500 - strlen( $via );
				 $pinterest_description = substr( $pinterest_description, 0, $cutoff );
			 }

			 $pinterest_description .= $via;
		 }
		 return $pinterest_description;
	 }

	 /**
	  * Fetches the user's @via for Pinterest, if it exists.
	  *
	  * @since  3.5.1 | 26 FEB 2019 | Created.
	  * @param  void
	  * @return string The '@via $username', or an empty string.
	  *
	  */
	public static function get_via() {
		$pinterest_username = '';
		$via = SWP_Utility::get_option( 'pinterest_id' );

		if ( !empty( $via ) ) {
			$pinterest_username = ' via @' . str_replace( '@' , '' , $via );
		}
		return $pinterest_username;
	 }
}
