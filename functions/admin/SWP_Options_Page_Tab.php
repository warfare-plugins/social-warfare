<?php
class SWP_Options_Page_Tab {

	// The name of the tab.
	public $name;
	public $sections;
	public $priority;

	public function __construct() {

	}

	public function set_name( $name ) {
		$this->name = $name;
	}

    public function set_priority( $priority ) {
        if ( ! intval( $priority ) ) {
            return false;
        }

        $this->priority = $priority;

        return $priority;
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
