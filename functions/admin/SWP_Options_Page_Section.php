<?php

class SWP_Options_Page_Section {

	public $title;
	public $description;
	public $information_link;
	public $priority;
	public $options;

	public function __construct() {
		$this->options = new stdClass();
	}

    // KB stands for knowledge base articles. We have one for every section of options.
	public function set_title( $title , $kb_link ) {
		$this->title = $title;
		$this->information_link = $kb_link;
	}

	public function set_description( $description ) {
		$this->description = $description;
	}

    public function set_priority( $priority ) {
        if ( ! intval( $priority ) ) {
            return false;
        }

        $this->priority = $priority;

        return $priority;
    }

	function sort_by_priority() {

		/**
		 * Take the $this->options array and sort it according to the priority attribute of each option.
		 * This will allow us to add options via the addons and place them where we want them by simply
		 * assigning a priority to it that will place it in between the two other options that are
		 * immediately higher and lower in their priorities. Or place it at the end if it has the highest
		 * priority.
		 *
		 */

	}

}
