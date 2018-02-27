<?php

class SWP_Options_Page {


	/**
	 * The Options Page Tabs
	 *
	 * Docblock each class property like this. Include a title, and then
	 * a one or two sentence minimum description.
	 *
	 */
	var $tabs;

	public function __construct() {

		// Docblock each tab and each section.

		/**
		 * Create the Display Tab.
		 *
		 * The key in this example will be "display", but the pretty name will be "Display".
		 *
		 */
		$tabs->display = new SWP_Options_Page_Tab();
		$tabs->display->set_name('Display');

		/**
		 * Create the Share Counts section of the display tab.
		 *
		 * This is what we use to create and modify a new options "secton".
		 *
		 */
		$tabs->display->sections->share_counts = new SWP_Options_Page_Section();
		$tabs->display->sections->share_counts->set_title( 'Share Counts' , 'http://warfareplugins.com/the-knowledge-base-article-for-this-section-of-options' );
		$tabs->display->sections->share_counts->set_description( 'This is the description' );

		/**
		 * This is what we will use to create and modify an individual option.
		 *
		 */
		$tabs->display->sections->share_counts->options->totals_for_each_button = new SWP_Options_Page_Option(
			array(
				'type'		=> 'checkbox',
				'size'		=> 'two-thirds',
				'content'	=> __( 'Button Counts' ,'social-warfare' ),
				'default'	=> true,
				'premium'	=> false,
				'priority'  => 1
			)
		);
	}

}
