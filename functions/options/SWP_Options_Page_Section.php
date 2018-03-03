<?php

class SWP_Options_Page_Section {


	/**
	 * SWP_Abstact Trait: Usuful methods able to be used by classes throughout the plugin.
	 *
	 */
	use SWP_Options_Abstract;

	public $name;
	public $description;
	public $link;
	public $priority;
	public $options;

	public function __construct() {
		$this->options = new stdClass();
	}


	public function set_link( $link ) {

		if ( !is_string( $link ) ) {
			$this->throw(__CLASS__ . " method set_name() requires a string. {$link} is not a string." );
		}

		$this->link = $link;

		return $this;
	}

	public function set_description( $description ) {

		if ( !is_string( $description ) ) {
			$this->throw(__CLASS__ . " method set_name() requires a string. {$description} is not a string." );
		}

		$this->description = $description;

		return $this;
	}


	public function render_html() {
		// Render the opening div for the section.
		// Loop through each option within this section and call their render_html() function.
		// Render the closing div for the section including the horizontal rule/divider.
	}

}
