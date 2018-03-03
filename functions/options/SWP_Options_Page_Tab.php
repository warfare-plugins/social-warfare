<?php

/**
 * SWP_Options_Page_Tab: The class used to create tabs on the options page.
 *
 * This class is used to create each individual tab on the options page. Each tab is an
 * object that contains a name, a priority, and a sections property. The sections property
 * is a collection of "section" objects each of which will contain a collection of related
 * options to display on the options page in a group.
 *
 * @since  2.4.0   | Created | 02 MAR 2017
 * @access public
 *
 */
class SWP_Options_Page_Tab {

	/**
	 * SWP_Abstact Trait: Usuful methods able to be used by classes throughout the plugin.
	 *
	 */
	use SWP_Options_Abstract;


	/**
	 * Name
	 *
	 * The name of this option. This is a "pretty" name that the plugin user will see.
	 *
	 * @var string
	 *
	 */
	public $name;


	/**
	 * Priority
	 *
	 * The priority property is used to determine the order in which the options are
	 * presented to the user. These options will be sorted prior to the rendering of
	 * the HTML in ascending order. That is to say, an option with a priority of 10
	 * will appear before an option with a priority of 20.
	 *
	 * @var integer
	 *
	 */
	public $priority;


	/**
	 * Sections
	 *
	 * This property will contain a bunch of "section" objects each pertaining to a
	 * different section of related options. Sections, like tabs, are also sorted
	 * by their priority property in ascending order.
	 *
	 * @var object A group of "option" objects.
	 *
	 */
	public $sections;


	/**
	 * The magic method used to instantiate this class.
	 *
	 * This method instantiates this class by settings the "sections" property to
	 * an object so the the "options" objects can easily be added to it later on.
	 *
	 * @since  2.4.0 | 3 MAR 2018 | Created
	 */
	public function __construct() {
		$this->sections = new stdClass();
	}


	/**
	 * A method to render the html for the menu  across the top of the options page.
	 *
	 * @since  2.4.0 | 03 MAR 2018 | Created
	 * @param  null
	 * @return string The html of the menu items for each tab.
	 *
	 */
	public function render_menu_html() {

	}

	/**
	 * A method to render the html for each tab.
	 *
	 * @since  2.4.0 | 03 MAR 2018 | Created
	 * @param  null
	 * @return string The html of the content of each tab.
	 *
	 */
	public function render_html() {
		// Open the tab's div container.
		// Loop through each of this tabs sections, calling on their ->render_html() function.
		// Close the tab's div container.
	}

}
