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

		$_options->network_count = new SWP_Option_Checkbox();
		$_options->network_count->set_name( 'Button Counts' )->set_priority(10)->set_size('two-thirds')->set_default(true)->set_premium('pro');

        $_options->frame_buster = new SWP_Option_Checkbox();
        $_options->frame_buster->set_name( 'Frame Buster' )->set_priority(10)->set_size('two-thirds-advanced')->set_default(true)->set_divider(true);

        $_options->full_content = new SWP_Option_Checkbox();
        $_options->full_content->set_name( 'Full Content?' )->set_default( false )->set_premium( 'pro' );

        $_options->force_new_shares = new SWP_Option_Checkbox();
        $_options->force_new_shares->set_name( 'Force New Shares? ')->set_default( false )->set_premium( 'pro' )->set_size( 'two-thirds ');
        //
        // 'sniplyBuster' => array(
        //     'type'			=> 'checkbox',
        //     'title' 		=> __( 'Frame Buster' , 'social-warfare' ),
        //     'description' 	=> __( 'If you want to stop content pirates from framing your content, turn this on.' , 'social-warfare' ),
        //     'size'			=> 'two-thirds-advanced',
        //     'default'		=> true,
        //     'divider'		=> true
        // ),
        //
        // 'full_content'		=> array(
        //     'type'				=> 'checkbox',
        //     'size'				=> 'two-thirds',
        //     'content'				=> __( 'Full Content?' , 'social-warfare' ),
        //     'default'			=> false,
        //     'premium'			=> false,
        //     'divider'			=> true
        // ),
        //
        // 'force_new_shares'		=> array(
        //     'type'				=> 'checkbox',
        //     'size'				=> 'two-thirds',
        //     'content'				=> __( 'Force New Shares?' , 'social-warfare' ),
        //     'default'			=> false,
        //     'premium'			=> false
        // )
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
