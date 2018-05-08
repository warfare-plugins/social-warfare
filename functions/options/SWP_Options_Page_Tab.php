<?php

/**
 * SWP_Options_Page_Tab: The class used to create tabs on the options page.
 *
 * This class is used to create each individual tab on the options page. Each tab is an
 * object that contains a name, a priority, and a sections property. The sections property
 * is a collection of "section" objects each of which will contain a collection of related
 * options to display on the options page in a group.
 *
 * @since  3.0.0   | Created | 02 MAR 2017
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
	* @since  3.0.0 | 3 MAR 2018 | Created
	*/
	public function __construct( $name, $key ) {
		$this->sections = new stdClass();

        $this->set_name( $name );
        $this->set_link( $key );
        if ( isset( $key ) ) :
            $this->key = $key;
        else:
            $this->key = $this->name_to_key( $name );
        endif;
	}


    /**
    * Pushes one SWP_Options_Page_Section object into $this array of sections.
    *
    * @param SWP_Options_Page_Section $section The section to add to the array.
    * @return SWP_Options_Page_Tab $this The calling option, for method chaining.
    *
    */
    public function add_section( $section ) {
        if ( !( 'SWP_Options_Page_Section' === get_class( $section ) ||  is_subclass_of( $section, 'SWP_Options_Page_Section' ) ) ) :
            $this->_throw( 'Please provide an instance of SWP_Options_Page_Section as the parameter.' );
        endif;

        $key = $section->key;

        $this->sections->$key = $section;

        return $this;
    }

    /**
    * Adds multiple SWP_Options_Page_Section objects into $this array of sections.
    *
    * @param array $sections An array of SWP_Options_Page_Section.
    * @return SWP_Options_Page_Tab $this The calling option, for method chaining.
    *
    */
    public function add_sections( $sections ) {
        if ( !is_array( $sections ) ) :
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

    /**
    * Sets the Javascript for switching tabs on the Admin page.
    *
    * Notice: This is not an href or external link. This is just a key used by jQuery
    * to select the proper tab.
    *
    * @param string $link The key correlatign to the tab. Must match the javascript target.
    * @return SWP_Options_Page_Tab $this The calling option, for method chaining.
    *
    */
    public function set_link( $link ) {
        if ( !is_string( $link ) ) {
            $this->_throw( 'Please provide a valid string prefixed with "swp_" for the tab link.' );
        }

        $this->link = $link;

        return $this;
    }


	/**
    * A method to render the html for each tab.
	*
	* @since  3.0.0 | 03 MAR 2018 | Created
    * @param  null
	* @return string Fully qualified HTML for this tab.
	*
	*/
	public function render_HTML() {
        $map = $this->sort_by_priority($this->sections);

        $sections = $this->sort_by_priority( $map );

        $tab = '<div id="swp_' . strtolower( $this->key ) . '" class="sw-admin-tab sw-grid sw-col-940">';

        foreach( $map as $prioritized_section) {
            foreach( $this->sections as $section) {
                if ( $section->key === $prioritized_section['key'] ) :
                    $tab .= $section->render_HTML();
                endif;
            }
        }

        $tab .= '</div>';

        return $tab;
	}

}
