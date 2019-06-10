<?php
/**
 * SWP_Follow_Network
 *
 * This is the class that is used for adding new social networks to the
 * buttons which can be selected on the options page and rendered in the
 * panel of buttons.
 *
 * @package   	SocialWarfareFollowWidget
 * @copyright 	Copyright (c) 2019, Warfare Plugins, LLC
 * @license  	GPL-3.0+
 * @since 		1.0.0 | 15 DEC 2018 | Created.
 *
 */
abstract class SWFW_Follow_Network {


	/**
	 * SWP_Debug_Trait provides useful tool like error handling.
	 *
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
	 * The snake_case name of the social network for unique identification.
	 * @var string
	 *
	 */
	public $key = '';


	/**
	 * The active status of this network.
	 *
	 * True iff the user has this network activated in the widget, else false.
	 *
	 * @var bool
	 *
	 */
	public $active = false;


	/**
	 * The URL for following a user on this network.
	 *
	 * The URL should take you to the user's profile where a 'follow' button
	 * exists nativley from the host network. No paramters are involved, but
	 * there is often a varible ``$username` in the URL.
	 *
	 * @var string
	 *
	 */
	public $url = '';


	/**
	 * An instance of SWP_Auth_Controller for this network.
	 * @var object SWP_Auth_Controller
	 *
	 */
	public $auth_helper = null;


	/**
	 * Whether or not this network should request an oAuth access_token.
	 * @var bool $needs_authorization
	 *
	 */
	public $needs_authorization = false;


	/**
	 * The ready to print <svg/> for the network icon.
	 * @var string $icon
	 *
	 */
	public $icon = '<i/>';


	/**
	 * The total number of followers for this network.
	 * @var int $follow_count
	 *
	 */
	public $follow_count = 0;


	/**
	 * Apply network arguments to create $this.
	 *
	 * @since 1.0.0 | 26 NOV 2018 | Created.
	 * @hook filter `swfw_follow_networks` | Array of SWFW_Follow_Network objects | @see SWFW_Follow_Widget.php
	 * @return void
	 *
	 */
	public function __construct( $args ) {
		global $swfw_networks;

		/**
		 * To verify that all of the $required keys are provided,
		 * we we remove it from the array once it is found.
		 *
		 * If any items remain in the array, the object does not meet the
		 * requirements to be built.
		 *
		 */
		$required = array( 'key', 'name', 'cta', 'url' );
		foreach( $args as $key => $value ) {
			$index = array_search( $key, $required );
			if ( is_numeric( $index ) ) {
				unset($required[$index]);
			}

			$this->$key = $value;
		}

		if ( count( $required ) > 0 ) {
			// If all the required fields were not provided, we'll send a message and bail.
			error_log("SWFW_Follow_Network requires these keys when constructing, which you are missing: ");
			foreach ( $required as $required_key ) {
				error_log( $required_key );
			}
			return;
		}

		$this->network = $this->key;

		$this->establish_icon();
		$this->establish_username();
		$this->establish_auth_helper();

		add_filter( 'swfw_follow_networks', array( $this, 'register_self' ) );
	}


	/**
	 * Handles the network-specific request and performs the request.
	 *
	 * The response needs to be stored as a local member for
	 * `$this->parse_api_response()`.
	 *
	 * Some networks, like Twitter, do all of the processing in this method
	 * and only use parse_api_response because it is required. It is highly
	 * dependent on how each network operates.
	 *
	 * @since  1.0.0 | 15 JAN 2019 | Created.
	 * @param void
	 * @return mixed String if we are able to make a follow count request,
	 *               else bool `false`.
	 *
	 */
	abstract function do_api_request();


	/**
	 * Decode the network-specific response to a useable format.
	 *
	 * After this has been called, the class member $follow_count is
	 * ready for use.
	 *
	 * @since  1.0.0 | 16 JAN 2019 | Created.
	 * @param void
	 * @return mixed Often an object.
	 *
	 */
	abstract function parse_api_response();


	/**
	 * If the follow count has recently been updated, fetch the stored value.
	 *
	 * Else, run a new request and save those values.
	 *
	 * @since  1.0.0 | 28 JAN 2019 | Created.
	 * @param void
	 * @return mixed Often an object.
	 *
	 */
	public function establish_follow_count() {
		if ( false == SWFW_Cache::is_cache_fresh() ) {
			$this->do_api_request();
			$this->parse_api_response();
			$this->save_follow_count();

		}

		$key = "{$this->key}_follow_count";
		$follow_count = SWFW_Utility::get_option( $key );
		$this->follow_count = SWP_Utility::kilomega( $follow_count );
	}


	/**
	 * Save the freshly fetched count for future use.
	 *
	 * @since  1.0.0 | 25 JAN 2019 | Created.
	 * @param void
	 * @return void
	 *
	 */
	public function save_follow_count() {
		// Networks that do not have count data still need an integer.
		if ( empty( $this->follow_count) ) {
			$this->follow_count = 0;
		}

		SWFW_Utility::update_network_count( $this->key, $this->follow_count );
	}


	/**
	 * Fetches the stored username from the database, if it exists.
	 *
	 * Since there can be multiple copies of the same widget,
	 * we'll have to check each instance for the networks it uses.
	 *
	 * @since 3.5.0 | 03 JAN 2018 | Created.
	 * @param void
	 * @return bool True iff the username exists, else false.
	 *
	 */
	protected function establish_username() {
		$widgets = SWFW_Follow_Widget::get_widgets();

		foreach( $widgets as $key => $settings ) {
			if ( !empty( $settings[$this->key . '_username'] ) ) {
				return $this->username = $settings[$this->key . '_username' ];
			}
		}

		return false;
	}


	/**
	 * Fetches the ready-to-render SVG for this network's icon.
	 *
	 * @since 3.5.0 | 15 JAN 2018 | Created.
	 * @param void
	 * @return void
	 *
	 */
	protected function establish_icon() {
		$this->icon = "<i class='sw swp_{$this->key}_icon'></i>";
	}


	/**
	 * Insantiates the SWP_Auth_Helper for this network.
	 *
	 * The auth helper provides methods for getting a user's tokens and secrets.
	 *
	 * @since 3.5.0 | 15 JAN 2018 | Created.
	 * @param void
	 * @return void
	 *
	 */
	protected function establish_auth_helper() {
		// This should not be reached, but is a safety mechanism.
		if ( !class_exists( 'SWP_Auth_Helper' ) ) {
			return;
		}

		// There are no features for this network that require authorization.
		if ( false == $this->needs_authorization ) {
			return;
		}

		$instance = new SWP_Auth_Helper( $this->network );
		add_filter( 'swp_authorizations', array( $instance, 'add_to_authorizations' ) );

		return $this->auth_helper = $instance;
	}


	/**
	 * Adds $this to the array of other used Network objects.
	 *
	 * @since  1.0.0 | 06 APR 2018 | Created
	 * @hook   filter| swp_follow_networks | Applied in SWFW_Follow_Widget
	 * @param  array $networks All of the created Social Follow Network classes.
	 * @return array $networks With `$this` network in the array.
	 * @access public
	 *
	 */
	public function register_self( $networks ) {
		return array_merge( $networks, array( $this ) );
	}


	/**
	 * Replaces the placeholder text 'swfw_username' with the actual username.
	 *
	 * @since  1.0.0 | 03 DEC 2018 | Created.
	 * @param void
	 * @return string A URL which goes to the 'Follow' page for this network.
	 *
	 */
	function generate_follow_link() {
		return str_replace( 'swfw_username', $this->username, $this->url);
	}


	/**
	 * Gets the follow link with `swfw_username` instead of a real username.
	 *
	 * @since  1.0.0 | 31 Jan 2019 | Created.
	 * @param void
	 * @return string A URL which goes to the 'Follow' page for this network.
	 *
	 */
	function get_generic_link() {
		return $this->url;
	}


	/**
	 * Indicates that this Network is used if a username is provided.
	 *
	 * @since  1.0.0 | 03 DEC 2018 | Created.
	 * @param void
	 * @return bool True if this network has a username in the DB, else false.
	 *
	 */
	function is_active() {
		return isset( $this->username ) && !empty( $this->username );
	}


	/**
	 * A controller for generating button HTML.
	 *
	 * This will read the user's options, and the apply the appropriate
	 * callback method to generate a button of a particular shape.
	 *
	 * @since  1.0.0 | 03 DEC 2018 | Created.
	 * @access public
	 * @param  string $shape The button style to generate. Used to create a callback.
	 * @return function The callback for the requested style.
	 *
	 */
	function generate_frontend_HTML( $shape ) {
		if ( !$this->is_active() ) {
			return '';
		}

		/**
		 * Each network is responsible for its own fetching and processing.
		 * If the value is fresh we already have it.
		 * Else, the network makes a new request.
		 *
		 * In the case of new requests, do them all THEN save the results.
		 *
		 */
		$this->establish_follow_count();
		$this->establish_display_settings();

		$style_variations_block = array('block', 'pill', 'shift', 'leaf' );

		if ( in_array( $shape, $style_variations_block ) ) {
			// Add the vendor prefix to prevent selector collision.
			return $this->generate_block_HTML( 'swfw_'.$shape.'_button' );
		}

		// Create the callback function as a string, then call it.
		$generate_x_HTML = "generate_" . $shape . "_HTML";

		return $this->$generate_x_HTML();
	}


	/**
	 * Sets the minimum follower count, used to decide if it is displayed.
	 *
	 * @since  1.0.0 | 12 FEB 2019 | Created.
	 * @access public
	 * @param  int $int The minimum number of followers desired.
	 * @return int $int, or 15 if $int was not a number.
	 *
	 */
	function set_minimum_count( $int ) {
		if ( !is_numeric( (int) $int) ) {
			// Use a default value.
			$int = 15;
		}

		return $this->minimum_count = (int) $int;
	}

	/**
	 * Prepares attributes and styles for frontend display.
	 *
	 * @since  1.0.0 | 29 JAN 2019 | Created.
	 * @access private The HTML methods are only called by this class.
	 * @param  void
	 * @return void
	 *
	 */
	private function establish_display_settings() {
		$this->background = "background-color: $this->color_primary";
		$this->border = "border: 1px solid $this->color_accent";
		$this->href = $this->generate_follow_link();

	}


	/**
	 * Renders Square button HTML.
	 *
	 * @since  1.0.0 | 03 DEC 2018 | Created
	 * @access private | This is the only class that should ever render follow button HTML.
	 * @param void
	 * @return string Fully qualified HTML for a Square follow button.
	 *
	 */
	private function generate_square_HTML() {
		$follow_count_HTML = $this->get_count_html( 'square' );
			// Not enough space for a name.
			$this->name = '';
		return
<<<BUTTON
<a target="_blank" href="{$this->href}">
	<div class="swfw-follow-button swfw_square_button swp-$this->key">
		<div class='swfw-network-icon'>
			{$this->icon}
		</div>

		<div class="swfw-text">
			{$follow_count_HTML}
			<span class='swfw-cta'>$this->cta</span>
		</div>
	</div>
</a>
BUTTON;
	}


	/**
	 * Renders Block button HTML.
	 *
	 * @since  1.0.0 | 03 DEC 2018 | Created
	 * @access private | This is the only class that should ever render follow button HTML.
	 * @param void
	 * @return string Fully qualified HTML for a Square follow button.
	 *
	 */
	private function generate_block_HTML( $shape ) {
		$follow_count_HTML = $this->get_count_html( $shape );
		return
<<<BUTTON
<div class="swfw-follow-button $shape swp-$this->key">
	<div class='swfw-network-icon'>
		{$this->icon}
	</div>

	<div class="swfw-text">
		{$follow_count_HTML}
	</div>

	<div class='swfw-cta-button'>
		<a target="_blank" href="{$this->href}"><div>$this->cta</div></a>
	</div>
</div>
BUTTON;
	}


	private function get_count_html( $shape ) {
		if ( 'square' == $shape ) {
			$this->name = '';
		}

		// They have no followers or follow data for this network.
		if ( (int) $this->follow_count < 1 || $this->follow_count < $this->minimum_count ) {
			$this->follow_count = $this->name;
			$this->follow_description = '';
		}

		if ( 'square' == $shape || 'block' == $shape ) {
			return "<span class='swfw-count'>$this->follow_count</span>";
		}

		return "<p class='swfw-count' style='margin: 0'>$this->follow_count $this->follow_description</p>";
	}


	/**
	 * Renders Buttons button HTML.
	 *
	 * @since  1.0.0 | 03 DEC 2018 | Created
	 * @param void
	 * @return string Fully qualified HTML for an Buttons follow button.
	 *
	 */
	public function generate_buttons_HTML( ) {
		$follow_count_HTML = $this->get_count_html( $shape );
		return
<<<BUTTON
<a target="_blank" href="{$this->href}">
	<div class="swfw-follow-button swfw_buttons_button swp-$this->key">
		<div class='swfw-network-icon'>
			{$this->icon}
		</div>

		<div class="swfw-text">
			<span class='swfw-cta'>$this->cta</span>
			{$follow_count_HTML}
		</div>
	</div>
</a>
BUTTON;
	}
}
