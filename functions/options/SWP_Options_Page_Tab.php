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
    * Links
    * This is the link used by Javscript to switch tabs.
    *
    * @var string $link
    */
    public $link;

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
        if ( 'SWP_Options_Page_Section' !== get_class( $section ) ) :
            $this->_throw( 'Please provide an instance of SWP_Options_Page_Section as the parameter.' );
        endif;

        array_push( $this->sections, $section );

        return $this;
    }

    public function add_sections( $sections ) {
        if ( !is_array( $sections ) || get_class( $sections[0] ) !== 'SWP_Options_Page_Section' ) :
            $this->_throw( 'This method requires an array. Please use add_section to add a single instance of SWP_Options_Page_Section.' );
        endif;

        foreach ( $sections as $section ) {
            if ( 'SWP_Options_Page_Section' !== get_class( $section ) ) :
                $this->_throw( 'This need an array of SWP_Options_Page_Section objects.' );
            endif;

            $this->add_section( $section );
        }

        return $this;
    }

    public function set_link( $link ) {
        if ( !is_string( $link ) ) {
            $this->_throw( 'Please provide a valid string prefixed with "swp_" for the tab link.' );
        }

        $this->link = $link;

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
    }

	/**
    * A method to render the html for each tab.
	*
	* @since  2.4.0 | 03 MAR 2018 | Created
    * @param  null
	* @return string Fully qualified HTML for this tab.
	*
	*/
	public function render_HTML() {
        // $container= '<div id="' . $this->name . '">' . $this->name . '</div>';
        $container = '<div id="swp_' . strtolower( $this->name ) . '" class="sw-admin-tab sw-grid sw-col-940">';

        foreach( $this->sections as $index => $section ) {
            $container .= $section->render_HTML();
        }

        $container .= '</div>';

        return $container;
	}

}
