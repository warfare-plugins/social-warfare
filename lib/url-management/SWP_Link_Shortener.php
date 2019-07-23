<?php

/**
 * SWP_Link_Shortener
 *
 * This class will serve as the parent class for link shortening integrations.
 * These integrations can extend this class to get easy access to it's methods
 * and properties.
 *
 * @since 4.0.0 | 19 JUL 2019 | Created
 *
 */
class SWP_Link_Shortener {


	/**
	 * This trait gives us access to the following debugging methods:
	 *
	 * debug()  Outputs all class properties to the screen.
	 * record_exit_status()  Stores bail conditions a dumpable array.
	 *
	 */
	use SWP_Debug_Trait;


	/**
	 * The unique key for each child class that extends this link shortening
	 * class. Keys should be snake_cased.
	 *
	 * @var string
	 *
	 */
	public $key;


	/**
	 * The pretty name of the link shortener that will be used when printing the
	 * name to the screen for the users to see.
	 *
	 * @var string
	 *
	 */
	public $name;


	/**
	 * The properties that will be used to generate the authentication button
	 * on the options page. This should contain, at a minimum, the following
	 * indices in the array.
	 *
	 * text: The text that will appear on the button.
	 * classes: The css classes that will be added to the button.
	 * target: An empty string or _blank for the anchor target.
	 * link: The URL to which the button should link.
	 *
	 * @var array
	 *
	 */
	public $button_properties = array();


	/**
	 * A string representing the name of the hook and the name of the
	 * corresponding function that will handle removing the token, login or
	 * whatever else information may be necessary to remove the apps authorization
	 * credentials from the plugin.
	 *
	 * @var string
	 *
	 */
	public $deactivation_hook = '';


	/**
	 * The Magic Constructor
	 *
	 * This will queue up the register_self() method which will, in turn, add
	 * this link shortener to the available drop-down list.
	 *
	 * @since  4.0.0 | 19 JUL 2019 | Created
	 * @param  void
	 * @return void
	 *
	 */
	public function __construct() {
		add_filter( 'swp_available_link_shorteners', array( $this, 'register_self' ) );
		add_action( 'wp_footer', array( $this, 'debug' ) );
	}


	/**
	 * A function to register this link shortening integration with the
	 * 'swp_register_link_shortners' filter so that it will show up and become
	 * an option on the options page.
	 *
	 * @since  4.0.0 | 18 JUL 2019 | Created
	 * @param  array $array An array of link shortening integrations.
	 * @return array        The modified array with our integration added.
	 *
	 */
	public function register_self( $array ) {
		$array[$this->key] = $this;
		return $array;
	}


	/**
	 * Fetch the bitly link that is cached in the local database.
	 *
	 * When the cache is fresh, we just pull the existing bitly link from the
	 * database rather than making an API call on every single page load.
	 *
	 * @since  3.3.2 | 12 SEP 2018 | Created
	 * @since  3.4.0 | 16 OCT 2018 | Refactored, Simplified, Docblocked.
	 * @since  4.0.0 | 23 JUL 2019 | Moved into this parent class.
	 * @param  int $post_id The post ID
	 * @param  string $network The key for the current social network
	 * @return mixed           string: The short url; false on failure.
	 *
	 */
	public function fetch_cached_shortlink( $post_id, $network ) {


		/**
		 * Fetch the local bitly link. We'll use this one if Google Analytics is
		 * not enabled. Otherwise we'll switch it out below.
		 *
		 */
		$short_url = get_post_meta( $post_id, $this->key . '_link', true );


		/**
		 * If Google analytics are enabled, we'll need to fetch a different
		 * shortlink for each social network. If they are disabled, we just use
		 * the same shortlink for all of them.
		 *
		 */
		if ( true == SWP_Utility::get_option('google_analytics') ) {
			$short_url = get_post_meta( $post_id, $this->key . '_link_' . $network, true);
		}


		/**
		 * We need to make sure that the $short_url returned from get_post_meta()
		 * is not false or an empty string. If so, we'll return false.
		 *
		 */
		if ( !empty( $short_url ) ) {
			return $short_url;
		}

		return false;
	}
}
