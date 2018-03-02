<?php

class SWP_Options_Page {


	/**
	 * The Options Page Tabs
	 *
	 * Docblock each class property like this. Include a title, and then
	 * a one or two sentence minimum description.
	 *
	 */
	public $tabs;

	public function __construct() {

		// Docblock each tab and each section.

		/**
		 * Create the Display Tab.
		 *
		 * The key in this example will be "display", but the pretty name will be "Display".
		 *
		 */
        $this->tabs = new stdClass();
		$this->tabs->display = new SWP_Options_Page_Tab();
		$this->tabs->display->set_priority( 1 );
		$this->tabs->display->set_name( 'Display' );

		/**
		 * Create the Share Counts section of the display tab.
		 *
		 * This section of options allows users to control the share count settings.
		 *
		 */
		$this->tabs->display->sections->share_counts = new SWP_Options_Page_Section();
	    $this->tabs->display->sections->share_counts->set_priority( 1 );
		$this->tabs->display->sections->share_counts->set_title( 'Share Counts' , 'http://warfareplugins.com/the-knowledge-base-article-for-this-section-of-options' );
		$this->tabs->display->sections->share_counts->set_description( 'This is the description' );


		$_options = $this->tabs->display->sections->share_counts->options;
		$_options->totals_for_each_button = new SWP_Options_Page_Option_Checkbox();
		$_options->totals_for_each_button->set_name( __( 'Button Counts' ,'social-warfare' ) )->set_priority(10)->set_size('two-thirds')->set_default(true)->set_premium('pro');
		/*
		$this->tabs->display->sections->share_counts->options->totals_for_each_button = new SWP_Options_Page_Option(
			array(
				'type'		=> 'checkbox',
				'size'		=> 'two-thirds',
				'content'	=> __( 'Button Counts' ,'social-warfare' ),
				'default'	=> true,
				'premium'	=> false,
				'priority'  => 1
			)
		);
		*/
	}

}
