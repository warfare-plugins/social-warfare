<?php
class SWP_Options_Page_Tab extends SWP_Abstract {

	// The name of the tab.
	public $name;
	public $priority;
	public $sections;

	public function __construct( $name ) {
		$this->sections = array();

        $this->set_name( $name );
	}

    public function add_section( $section ) {
        $this->sections_push( $section );

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

	}

}
