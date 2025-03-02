<?php

/**
 * SWP_Social_Network
 *
 * This is the class that is used for adding new social networks to the
 * buttons which can be selected on the options page and rendered in the
 * panel of buttons.
 *
 * @package   SocialWarfare\Functions\Social-Networks
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since 3.0.0 | 05 APR 2018 | Created
 */
class SWP_Social_Network {


	/**
	 * SWP_Debug_Trait provides useful tool like error handling and a debug
	 * method which outputs the contents of the current object.
	 */
	use SWP_Debug_Trait;


	/**
	 * The display name of the social network
	 *
	 * This is the 'pretty name' that users will see. It should generally
	 * reflect the official name of the network according to the way that
	 * network is publicly branded.
	 *
	 * @var string
	 */
	public $name = '';


	/**
	 * The call to action text.
	 *
	 * This is the text that will appear on the button whenever it is
	 * hovered over. For example, Facebook says "share" and Google Plus
	 * says "+1".
	 *
	 * @var string
	 */
	public $cta = '';


	/**
	 * The snake_case name of the social network
	 *
	 * This is 'ugly name' of the network. This a snake_case key used for
	 * the purpose of eliminating spaces so that we can save things in the
	 * database and other such cool things.
	 *
	 * @var string
	 */
	public $key = '';


	/**
	 * The default state of this network
	 *
	 * This property will determine where the icon appears in the options page
	 * prior to the user setting and saving it. If true, it will appear in the
	 * active section. If false, it will appear in the inactive section. Once
	 * the user has updated/saved their preferences, this property will no
	 * longer do anything.
	 *
	 * @var bool If true, the button is turned on by default.
	 */
	public $default = true;


	/**
	 * The premium status of this network
	 *
	 * Whether this button is a premium network. An empty string refers to a
	 * non-premium network. A string containing the key of the premium addon
	 * to which this is a member is used for premium networks. For example,
	 * setting this to 'pro' means that it is a premium network dependant on
	 * the Social Warfare - Pro addon being installed and registered.
	 *
	 * @var string
	 */
	public $premium = '';


	/**
	 * The active status of this network
	 *
	 * If the user has this network activated on the options page, then this
	 * property will be set to true. If not, it will be set to false.
	 *
	 * @var bool
	 */
	public $active = false;


	/**
	 * The generated html for the button
	 *
	 * After the first time the HTML is generated, we will store it in this variable
	 * so that when it is needed for the second or third panel on the page, the render
	 * html method will not have to make all the computations again.
	 *
	 * The html will be stored in an array indexed by post ID's. For example $this->html[27]
	 * will contain the HTML for this button that was generated for post with 27 as ID.
	 *
	 * @var array
	 */
	public $html_store = array();


	/**
	 * The Base URL for the share link
	 *
	 * This will allow us to generate the share link for networks that only use just
	 * one URL parameter, the URL to the post. This way we can use a boilerplate method
	 * for generating the share links here in the parent class and will only have to
	 * overwrite that method in child classes that absolutely need it.
	 *
	 * @var string
	 */
	public $base_share_url = '';


	/**
	 * Whether or not to show the share count for this network.
	 *
	 * @var boolean $show_shares;
	 */
	public $show_shares = false;

	/**
	 * The generated HTML for the social network button.
	 * This property stores the button's HTML once it is generated,
	 * to avoid regenerating it multiple times.
	 *
	 * @var string
	 */
	public $html = '';

	// TODO: Docblock
	public $visible_on_amp = true;


	/**
	 * A method to add this network object to the globally accessible array.
	 *
	 * @since  3.0.0 | 06 APR 2018 | Created
	 * @param  none
	 * @return none
	 * @access public
	 */
	public function add_to_global() {

		global $swp_social_networks;
		$swp_social_networks[ $this->key ] = $this;
	}


	/**
	 * A function to run when the object is instantiated.
	 *
	 * @since  3.0.0 | 06 APR 2018 | Created
	 * @param  none
	 * @return none
	 * @access public
	 */
	public function init_social_network() {
		$this->add_to_global();
		$this->set_active_state();
	}


	/**
	 * A method for providing the object with a name.
	 *
	 * @since 3.0.0 | 05 APR 2018 | Created
	 * @param string $value The name of the object.
	 * @return object $this Allows chaining of methods.
	 * @access public
	 */
	public function set_name( $value ) {

		if ( ! is_string( $value ) || empty( $value ) ) {
			$this->_throw( "Please provide a string for your object's name." );
		}

		$this->name = $value;

		return $this;
	}


	/**
	 * A method for updating this network's default property.
	 *
	 * @since 3.0.0 | 05 APR 2018 | Created
	 * @param bool $value The default status of the network.
	 * @return object $this Allows chaining of methods.
	 * @access public
	 */
	public function set_default( $value ) {
		if ( ! is_bool( $value ) || empty( $value ) ) {
			$this->_throw( "Please provide a boolean value for your object's default state." );
		}

		$this->default = $value;

		return $this;
	}


	/**
	 * A method for updating this network's key property.
	 *
	 * @since 3.0.0 | 05 APR 2018 | Created
	 * @param string $value The key for the network.
	 * @return object $this Allows chaining of methods.
	 * @access public
	 */
	public function set_key( $value ) {

		if ( ! is_string( $value ) || empty( $value ) ) {
			$this->_throw( 'Please provide a snake_case string for the key value.' );
		}

		$this->key = $value;
		return $this;
	}


	/**
	 * A method for updating this network's premium property.
	 *
	 * @since 3.0.0 | 05 APR 2018 | Created
	 * @param string $value A string corresponding to the key of the dependant premium addon.
	 * @return object $this Allows chaining of methods.
	 * @access public
	 */
	public function set_premium( $value ) {

		if ( ! is_string( $value ) || empty( $value ) ) {
			$this->_throw( 'Please provide a string corresponding to the premium addon to which this network depends.' );
		}

		$this->premium = $value;
		return $this;
	}


	/**
	 * A method to return the 'active' status of this network.
	 *
	 * @since 3.0.0 | 06 APR 2018 | Created
	 * @param none
	 * @return bool
	 * @access public
	 */
	public function is_active() {
		return $this->active;
	}


	/**
	 * A method to set the 'active' status of this network.
	 *
	 * @since 3.0.0 | 06 APR 2018 | Created
	 * @param none
	 * @return none
	 * @access public
	 */
	public function set_active_state() {
		global $swp_user_options;
		if ( isset( $swp_user_options['order_of_icons'][ $this->key ] ) ) {
			$this->active = true;
		}
	}


	/**
	 * A method to save the generated HTML. This allows us to not have to
	 * run all of the computations every time. Instead, just reuse the HTML
	 * that was rendered by the method the first time it was created.
	 *
	 * @since  3.0.0 | 06 APR 2018 | Created
	 * @param  string $html     The string of HTML to save in this property.
	 * @param  int    $post_id  The ID of the post that this belongs to.
	 * @return none
	 * @access public
	 */
	public function save_html( $html, $post_id ) {
		$this->html_store[ $post_id ] = $html;
	}

	/**
	 * Show Share Counts?
	 *
	 * A method to determine whether or not share counts need to be shown
	 * while rendering the HTML for this network's button.
	 *
	 * @since  3.0.0 | 06 APR 2018 | Created
	 * @param  array $array The array of data from the buttons panel.
	 * @return bool
	 * @access public
	 * @TODO Make it accept two parameters, both arrays, $options and $share_counts.
	 */
	public function is_share_count_shown( $btn_array ) {

		// If the shares value isn't set, don't show the share count.
		if ( ! isset( $btn_array['shares'][ $this->key ] ) ) :
			return false;

			// If the global button level shares are turned off, don't show the share count.
		elseif ( ! $btn_array['options']['network_shares'] ) :
			return false;

			// If the total shares haven't yet exceeded the minimum set in the options, don't show the share count.
		elseif ( $btn_array['shares']['total_shares'] < $btn_array['options']['minimum_shares'] ) :
			return false;

			// If the share count is literally 0, don't show the share count.
		elseif ( $btn_array['shares'][ $this->key ] <= 0 ) :
			return false;

			// Show the share count.
		else :
			return true;
		endif;
	}


	/**
	 * Create the HTML to display the share button
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $network_counts Associative array of 'network_key' => 'count_value'
	 * @return array $array The modified array which will now contain the html for this button
	 * @todo   Eliminate the array
	 */
	public function render_HTML( $panel_context, $output = false ) {

		// TODO: DOCBLOCK
		if ( false === $this->visible_on_amp && SWP_AMP::is_amp() ) {
			return '';
		}

		$post_data            = $panel_context['post_data'];
		$share_counts         = $panel_context['shares'];
		$post_data['options'] = $panel_context['options'];

		$share_link = $this->generate_share_link( $post_data );

		// Build the button.
		$icon          = '<span class="iconFiller">';
			$icon     .= '<span class="spaceManWilly">';
				$icon .= '<i class="sw swp_' . $this->key . '_icon"></i>';
				$icon .= '<span class="swp_share">' . $this->cta . '</span>';
			$icon     .= '</span>';
		$icon         .= '</span>';

		if ( true === $this->are_shares_shown( $panel_context ) ) :
			$icon .= '<span class="swp_count">' . SWP_Utility::kilomega( $share_counts[ $this->key ] ) . '</span>';
		else :
			$icon = '<span class="swp_count swp_hide">' . $icon . '</span>';
		endif;

		// Build the wrapper.
		$html      = '<div class="nc_tweetContainer swp_share_button swp_' . $this->key . '" data-network="' . $this->key . '">';
			$html .= '<a class="nc_tweet swp_share_link" rel="nofollow noreferrer noopener" target="_blank" href="' . $share_link . '" data-link="' . $share_link . '">';
				// Put the button inside.
				$html .= $icon;
			$html     .= '</a>';
		$html         .= '</div>';

		// Store these buttons so that we don't have to generate them for each set
		$this->html = $html;

		if ( $output ) :
			echo esc_html( $html );
		endif;

		return $html;
	}


	/**
	 *
	 * Returns a boolean indicateding whether or not to display share counts.
	 *
	 * @since  3.0.0 | 18 APR 2018 | Created
	 * @since  3.3.0 | 24 AUG 2018 | Removed use of $options, calls
	 *                               SWP_Utility::get_option() instead.
	 * @since  4.0.0 | 12 JUL 2019 | Added check for delayed share count display.
	 * @param  array $share_counts The array of share counts
	 * @param  array $options  DEPRECATED The array of options from the button panel object.
	 * @return bool  True if share counts should be displayed, else false.
	 */
	public function are_shares_shown( $panel_context = array() ) {

		/**
		 * Bail out and return false if the $panel_context was not passed in
		 * properly as a paramter. We'll need this for share counts, post data,
		 * and other important information.
		 */
		$required_context = array( 'shares', 'options', 'post_data' );
		foreach ( $required_context as $requirement ) {
			if ( empty( $panel_context[ $requirement ] ) ) {
				return false;
			}
		}

		/**
		 * There are three main sections of the $panel_context. We'll take what
		 * we need here so that we can have cleaner, neater access to them later.
		 */
		$share_counts = $panel_context['shares'];
		$options      = $panel_context['options'];
		$post_id      = $panel_context['post_data']['ID'];

		/**
		 * If the share counts are delayed in the option, then we'll check the
		 * current age of the post and check to see if they are still delayed
		 * or if they can be shown now.
		 */
		if ( true === SWP_Buttons_Panel::are_share_counts_delayed( $post_id ) ) {
			return false;
		}

		/**
		 * Cast a string 'true'/'false' to a boolean true/false in case it was
		 * passed in via the shortcode.
		 */
		if ( is_string( $options['network_shares'] ) ) {
			$options['network_shares'] = ( strtolower( $options['network_shares'] ) === 'true' );
		}

		// False if the share count is empty
		if ( empty( $share_counts[ $this->key ] ) ) {
			return false;
		}

		// False if the total share count is below the minimum
		if ( $share_counts['total_shares'] < SWP_Utility::get_option( 'minimum_shares' ) ) {
			return false;
		}

		// False if the share count is zero.
		if ( 0 === $share_counts[ $this->key ] ) {
			return false;
		}

		// False if network shares are turned off in the options.
		if ( false === $options['network_shares'] ) {
			return false;
		}

		return true;
	}


	/**
	 * A method for processing URL's.
	 *
	 * This is designed to process the URL that is being shared onto the social
	 * platorms. It takes care of encoding, UTM parameters, link shortening, etc.
	 *
	 * @since  3.0.0 | 06 APR 2018 | Created
	 * @param  array $array  The array of data from the buttons panel.
	 * @return string        The processed URL.
	 */
	public function get_shareable_permalink( $post_data ) {
		return urlencode( urldecode( SWP_Link_Manager::process_url( $post_data['permalink'], $this->key, $post_data['ID'] ) ) );
	}


	/**
	 * Generate the share link
	 *
	 * This is the link that is being clicked on which will open up the share
	 * dialogue. Thie method is only used for networks that use this exact same pattern.
	 * For anything that accepts more than just the post permalink as a URL parameter,
	 * those networks will have to overwrite this method with their own custom method
	 * in their respective child classes.
	 *
	 * @since  3.0.0 | 08 APR 2018 | Created
	 * @param  array $array The array of information passed in from the buttons panel.
	 * @return string The generated link
	 * @access public
	 */
	public function generate_share_link( $post_data ) {
		$share_link = $this->base_share_url . $this->get_shareable_permalink( $post_data );
		return $share_link;
	}


	/**
	 * Generate the API Share Count Request URL
	 *
	 * For most social networks, the api link is unique and this method will need to be
	 * overwritten in their respective child classes. However, for any networks that do
	 * not support share counts, having the method here in the parent class will allow
	 * us to simply use this one without have to write a new one in each child class.
	 *
	 * @since  3.0.0 | 08 APR 2018 | Created
	 * @access public
	 * @param  string $url The permalink of the page or post for which to fetch share counts
	 * @return string $request_url The complete URL to be used to access share counts via the API
	 */
	public function get_api_link( $url ) {
		return 0;
	}


	/**
	 * Parse the response to get the share count
	 *
	 * For most social networks, parsing of the API response needs to be a unique method
	 * that is declared in each network's child class. However, we are including it here
	 * for all networks that do not support share counts at all. If a network does not
	 * support share count fetching, then it can just use the method defined here in the
	 * parent class.
	 *
	 * @since  3.0.0 | 08 APR 2018 | Created
	 * @access public
	 * @param  string $response The raw response returned from the API request
	 * @return int $total_activity The number of shares reported from the API
	 */
	public function parse_api_response( $response ) {
		return 0;
	}


	/**
	 * The get_share_count() method is a quick and easy method for getting the
	 * currently stored share count for this network for a given post. You must
	 * pass it the post_id for the desired post.
	 *
	 * @since  4.1.0 | 17 APR 2020 | Created
	 * @param  integer $post_id The ID of the desired post.
	 * @return integer The number of shares for this post for this network.
	 */
	public function get_share_count( $post_id ) {

		// Get the share counts from the stored meta field.
		$share_counts = get_post_meta( $post_id, '_' . $this->key . '_shares', true );

		// If false was returned, return the integer 0 instead.
		if ( false === $share_counts ) {
			return 0;
		}

		// Otherwise return the integer of the share counts.
		return (int) $share_counts;
	}


	/**
	 * The update_share_count() method will update the share counts for this
	 * social network for a given post. It will check first to ensure that the
	 * new numbers coming in are higher than the old, previous numbers, and then
	 * store the new numbers.
	 *
	 * @since  4.0.2 | 21 JUL 2020 | Created
	 * @since  4.1.0 | 06 AUG 2020 | Added a check for force_new_shares debugging.
	 * @param  integer $post_id     The Post ID
	 * @param  integer $share_count The new number of shares/activity
	 * @return boolean True if updated, False if not updated.
	 */
	public function update_share_count( $post_id, $share_count ) {

		/**
		 * Check if the new counts are higher than the old counts.
		 *
		 * However, right here we also check to see if the
		 * ?swp_debug=force_new_shares URL parameter has been set. This will
		 * force us to accept the new share counts even if they are lower than
		 * the previously stored share counts.
		 */
		$previous_counts = get_post_meta( $post_id, '_' . $this->key . '_shares', true );
		if ( $previous_counts > $share_count && false === SWP_Utility::debug( 'force_new_shares' ) ) {
			return false;
		}

		// Remove the old counts and replace them with the new counts.
		delete_post_meta( $post_id, '_' . $this->key . '_shares' );
		update_post_meta( $post_id, '_' . $this->key . '_shares', $share_count );
		return true;
	}


	/**
	 * The update_total_counts() method is used to update the "total shares"
	 * value which aggregates the share counts of all the active social networks
	 * combined.
	 *
	 * @since  4.1.0 | 21 JUL 2020 | Created
	 * @param  integer $post_id The Post ID
	 * @return void
	 */
	public function update_total_counts( $post_id ) {
		global $swp_social_networks;

		// Fetch the current share count for each active network.
		$total_shares = 0;
		foreach ( $swp_social_networks as $Network ) {
			if ( $Network->is_active() ) {

				// Add the share count to our running total.
				$total_shares += $Network->get_share_count( $post_id );
			}
		}

		// Remove the old total and replace it with the new one.
		delete_post_meta( $post_id, '_total_shares' );
		update_post_meta( $post_id, '_total_shares', $total_shares );
	}
}
