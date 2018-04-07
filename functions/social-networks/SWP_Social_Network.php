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
 *
 */
class SWP_Social_Network {


	/**
	 * SWP_Utility_Trait provides useful tool like error handling.
	 *
	 */
	use SWP_Utility_Trait;


	/**
	 * The display name of the social network
	 *
	 * This is the 'pretty name' that users will see. It should generally
	 * reflect the official name of the network according to the way that
	 * network is publicly branded.
	 *
	 * @var string
	 *
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
	 *
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
	 *
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
	 *
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
	 *
	 */
	public $premium = '';


	/**
	 * The active status of this network
	 *
	 * If the user has this network activated on the options page, then this
	 * property will be set to true. If not, it will be set to false.
	 *
	 * @var bool
	 *
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
	 *
	 */
	public $html = array();


	/**
	 * A method to add this network object to the globally accessible array.
	 *
	 * @since  3.0.0 | 06 APR 2018 | Created
	 * @param  none
	 * @return none
	 * @access public
	 *
	 */
	public function add_to_global() {

		global $swp_social_networks;
		$swp_social_networks[$this->key] = $this;

	}


	/**
	 * A function to run when the object is instantiated.
	 *
	 * @since  3.0.0 | 06 APR 2018 | Created
	 * @param  none
	 * @return none
	 * @access public
	 *
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
	 *
	 */
	public function set_name( $value ) {

        if ( !is_string( $value )  ||  empty( $value ) ) {
            $this->_throw("Please provide a string for your object's name." );
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
	 *
	 */
	public function set_default( $value ) {
		if ( !is_bool( $value ) || empty( $value ) ) {
			$this->_throw("Please provide a boolean value for your object's default state." );
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
	 *
	 */
	public function set_key( $value ) {

		if ( !is_string( $value ) ||  empty( $value ) ) {
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
	 *
	 */
	public function set_premium( $value ) {

		if ( !is_string( $value ) ||  empty( $value ) ) {
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
	 *
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
	 *
	 */
	public function set_active_state() {
		global $swp_user_options;
		if ( isset( $swp_user_options['order_of_icons'][$this->key] ) ) {
			$this->active = true;
		}
	}


	/**
	 * A method to save the generated HTML. This allows us to not have to
	 * run all of the computations every time. Instead, just reuse the HTML
	 * that was rendered by the method the first time it was created.
	 *
	 * @since  3.0.0 | 06 APR 2018 | Created
	 * @param  string  $html     The string of HTML to save in this property.
	 * @param  int     $post_id  The ID of the post that this belongs to.
	 * @return none
	 * @access public
	 *
	 */
	public function save_html( $html , $post_id ) {
		$this->html[$post_id] = $html;
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
	 *
	 */
	public function show_share_count( $array ) {

		// If the shares value isn't set, don't show the share count.
		if( !isset( $array['shares'][$this->key] )):
			return false;

		// If the global button level shares are turned off, don't show the share count.
		elseif( !$array['options']['network_shares'] ):
			return false;

		// If the total shares haven't yet exceeded the minimum set in the options, don't show the share count.
		elseif( $array['shares']['total_shares'] < $array['options']['minimum_shares']):
			return false;

		// If the share count is literally 0, don't show the share count.
		elseif( $array['shares'][$this->key] <= 0 ):
			return false;

		// Show the share count.
		else:
			return true;
		endif;
	}

	/**
	 * Create the HTML to display the share button
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $array The array of information used to create and display each social panel of buttons
	 * @return array $array The modified array which will now contain the html for this button
	 *
	 */
	public function render_html( $array ) {

		// If we've already generated this button, just use our existing html
		if ( isset( $this->html[$array['postID']] ) ) :
			return $this->html[$array['postID']];

		// If not, let's check if this network is activated and create the button HTML
		else:

			$share_link = $this->generate_share_link( $array );

			// Build the button wrapper
			$html= '<div class="nc_tweetContainer '.$this->key.'" data-id="' . $array['count'] . '" data-network="'.$this->key.'">';
			$html.= '<a rel="nofollow" target="_blank" href="' . $share_link . '" data-link="' . $share_link . '" class="nc_tweet">';

			// If we are showing share counts...
			if ( true === $this->show_share_count( $array ) ) :
				$html.= '<span class="iconFiller">';
				$html.= '<span class="spaceManWilly">';
				$html.= '<i class="sw sw-'.$this->key.'"></i>';
				$html.= '<span class="swp_share">' . $this->cta . '</span>';
				$html.= '</span></span>';
				$html.= '<span class="swp_count">' . swp_kilomega( $array['shares'][$this->key] ) . '</span>';

			// If we are not showing share counts...
			else :
				$html.= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-'.$this->key.'"></i><span class="swp_share"> ' . $this->cta . '</span></span></span></span>';
			endif;

			// Close up the button
			$html.= '</a>';
			$html.= '</div>';

			// Store these buttons so that we don't have to generate them for each set
			$this->save_html( $html , $array['postID'] );

		endif;

		return $html;

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
	public function get_shareable_permalink( $array ) {
		return urlencode( urldecode( SWP_URL_Management::process_url( $array['url'] , $this->key , $array['postID'] ) ) );
	}


}
