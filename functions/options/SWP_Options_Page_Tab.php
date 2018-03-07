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
class SWP_Options_Page_Tab extends SWP_Abstract {
	/**
	 * Sections
	 *
	 * This property will contain a bunch of "section" objects each pertaining to a
	 * different section of related options. Sections, like tabs, are also sorted
	 * by their priority property in ascending order.
	 *
	 * @var array A group of "option" objects.
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
	public function __construct( $name ) {
		$this->sections = array();

        $this->set_name( $name );
	}

    public function add_section( $section ) {
        if ( 'SWP_Options_Page_Section' !== get_class( $section ) ) {
            $this->throw( 'Please provide an instance of SWP_Options_Page_Section as the paramter.' );
        }

        array_push( $this->sections, $section );

        return $this;
    }

    public function add_sections( $sections ) {
        if ( !is_array( $sections ) || get_class( $sections[0] ) !== 'SWP_Options_Section' ) {
            $this->throw("Requres an array of SWP_Options_Section objects.");
        }

        foreach ( $sections as $section ) {
            $this->add_section( $section );
        }

        return $this;
    }

	public function sort_by_priority() {

		/**
		 * Take the $this->sections and sort them according to their priority. So a section
		 * with a priority of 1 will show up before a section wwith a priority of 2. Again,
		 * this will allow addons to add sections of options right in the middle of a tab.
		 * Set a section to 3 and it will show up in between sections that have priorities
		 * of 2 and 4.
		 */


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
