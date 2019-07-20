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
	 * The unique key for each child class that extends this link shortening
	 * class. Keys should be snake_cased.
	 * @var string
	 *
	 */
	public $key;


	/**
	 * The pretty name of the link shortener that will be used when printing the
	 * name to the screen for the users to see.
	 * @var string
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
	 * This trait gives us access to the following debugging methods:
	 *
	 * $this->debug()  Outputs all class properties to the screen.
	 * $this->record_exit_status()  Stores bail conditions in a local property.
	 *
	 */
	use SWP_Debug_Trait;


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
}
