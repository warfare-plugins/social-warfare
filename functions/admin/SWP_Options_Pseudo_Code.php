<?php

/**
 * The options object will need to result in something like this:
 *
 * New classes to create:
 *
 * SWP_Options_Page                replaces options-array.php         \
 * SWP_Options_Page_Tab            replaces options-array.php          \  This is the options object that will be
 * SWP_Options_Page_Section        replaces options-array.php          /  used to populate the options page.
 * SWP_Options_Page_Option         replaces options-array.php         /
 * SWP_Options_Page_Registration   replaces options-registration.php      This will process all user registrations for addons.
 * SWP_Options_Page_HTML           replaces options-page.php              This will populate the Options Page with HTML
 * SWP_User_Options                replaces options-fetch.php             This will fetch the options that the plugin user has set.
 * SWP_User_Options_Migration	   										  This will migrate the old swp_user_options into the new object.
 *
 * The idea here is that each portion of the $SWP_Options object will have methods that allow for
 * addons to interact with the object. In the tabs, you will be able to add new sections. In the
 * sections, you will be able to add new options. These will be sorted by the priority attribute
 * of each.
 *
 * Options_Page = class-based object with properties and methods
 * Tab = class-based object with properties and methods.
 * Section = class-based object with properties and methods.
 * Option = class-based object with properties and methods.
 *
 * Nomenclature: Let's use Options ($SWP_Options) to refer to what is available to the plugin user
 * to choose from, and let's use User Options ($SWP_User_Options) to refer to what the user of the
 * plugin has set.
 *
 * Also, I believe that this is the only class where using plural makes more than enough sense to
 * violate our singular only naming policy: SWP_Options class. (Scratch this, I added "page" to the
 * end of the name.)
 *
 * Note: We should probably use priorities in multiples of 10 so that it's very easy for
 * 		 addons to add things in between them when needed. Plus we can always use fractions
 * 		 when needed, I suppose. Or we could start with a base of multiples of 100.
 *
 * 		 We need to find a way so that whenever a new Tab, Section, or Option is added, it immediately
 * 		 sorts all items in the object of that type according to their priority. So if a new options is
 * 		 added, all options in that section need to get sorted so that it is being inserted where it is
 * 		 intended to be inserted.
 *
 * Here is some Pseudo code that sort of displays what I want the SWP_Options object to look like
 * once it is all put together (Even though I used equals signs, look at this as an object, not an array. It's pseudo-code.):
 *
 * 	$SWP_Options_Page = // This is the main options object for the plugin
 * 		$tabs = // This object contains all the tabs across the top of the options page.
 * 			$display = // A tab contains the tab name and the options sections for that tab and the tab priority to be sorted by.
 * 				$name = "Display"
 * 				$priority = 1
 * 				$sections = // This object contains all the options sections for the tab.
 * 					$share_counts = // This is an example of an options section. This is the "Share Counts" section of the "Display" tab.
 * 						$title = "Share Counts" // This is the title of this section.
 * 						$descriptions = "This is the descriptions" // This is the description of this section.
 * 						$information_link = 'http://warfareplugins.com/the-knowledge-base-article-for-this-section-of-options' // This is the KB article link.
 * 						$priority = 1 // This is the priority used to sort this section among other section in this tab.
 * 						$options = // This is the options object for this section. It contains all the options to be used in this section.
 * 							$total_for_each_button = // This is an example of an poption in this section.
 * 								'type'		=> 'checkbox',
 *								 'size'		=> 'two-thirds',
 *								 'content'	=> __( 'Button Counts' ,'social-warfare' ),
 *								 'default'	=> true,
 *								 'premium'	=> false,
 *								 'priority'  => 1 // Priority used to sort this option among the other options in this section.
 * 							$total_for_the_panel =
 * 								'type'		=> 'checkbox',
 *								 'size'		=> 'two-thirds',
 *								 'content'	=> __( 'Button Counts' ,'social-warfare' ),
 *								 'default'	=> true,
 *								 'premium'	=> false,
 *								 'priority'  => 1
 * 			$styles
 * 			$social_identity
 * 			$advanced
 * 			$registration
 *
 * 	$SWP_Options_Page
 * 		$tabs
 * 			$a_tab
 * 				$name
 * 				$priority
 * 				$sections
 * 					$section_1
 * 						$title
 * 						$description
 * 						$information_link
 * 						$priority
 * 						$options
 * 							$option_1
 * 							$option_2
 * 							$option_3
 * 					$section_2
 * 						$title
 * 						$description
 * 						$information_link
 * 						$priority
 * 						$options
 * 							$option_1
 * 							$option_2
 * 							$option_3
 * 			$b_tab
 * 				$name
 * 				$priority
 * 				$sections
 * 					$section_1
 * 						$title
 * 						$description
 * 						$information_link
 * 						$priority
 * 						$options
 * 							$option_1
 * 							$option_2
 * 							$option_3
 * 					$section_2
 * 						$title
 * 						$description
 * 						$information_link
 * 						$priority
 * 						$options
 * 							$option_1
 * 							$option_2
 * 							$option_3
 * 			$c_tab
 *
 */

/**
 * This is the main options object. All addons will need access to this so we'll make it
 * a global. Then all addons can simply call the methods outlined below to add their options
 * right into the core options without all that janky stuff that's going on now.
 *
 */
global $SWP_Options_Page = new SWP_Options_Page;

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



class SWP_Options_Page_Tab {

	// The name of the tab.
	var $name;
	var $sections;
	var $priority;

	public function __construct() {

	}

	public function set_name( $name ) {
		$this->name = $name;
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

class SWP_Options_Page_Section {

	var $options;
	var $title;
	var $description;
	var $information_link;

	public function __construct() {

	}

	public function set_title( $title , $kb_link ) { // KB stands for knowledge base articles. We have one for every section of options.
		$this->title = $title;
		$this->information_link = $kb_link;
	}

	public function set_description( $description ) {
		$this->description = $description;
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

class SWP_Options_Page_Option {

	var $type;
	var $size;
	var $content;
	var $default;
	var $premium;
	var $priority;


	public function __construct( $attributes ) {

		/**
		 * Cycle through each attribute and set it. Not all attributes apply to all option types so we need
		 * to check if each one is set before setting the property. I'd rather use a class-object so that if
		 * I want to add a method or something to it later, I will be able to do so easily just like with the
		 * tabs and sections.
		 *
		 */
		if( isset($attributes['type']) ) {
			$this->type = $type;
		}

	}


	/**
	 * Useful for adding new available choices to a dropdown item that already exists. For example, if Pro
	 * adds additional choices to an option that's already in core.
	 *
	 */
	public function add_choice() {

	}

	/**
	 * What if the cool new choice that we just added above is so cool that we want it to be the default?
	 *
	 */
	public function set_default() {

	}

}

/**
 * This is the class that will be used to parse the SWP_Options object into the HTML that appears on the
 * options page that the plugin user can use to set the user options to their preferences.
 *
 * This will replace options-page.php.
 *
 */
class SWP_Options_Page_HTML {

	public function __construct() {

		foreach( $SWP_Options->tabs as $option ) {
			// Output the tabs, then the sections inside each tab, then the options within each section.
		}

	}
}


class SWP_User_options {


	public function __construct() {
		get_option('Our_Options_In_The_Database');
		$this->remove_unavailable_options();
		$this->set_defaults();
	}

	/**
	 * This will compare the User Options in the database against the SWP_Options_Page object. If it does
	 * not exist in the SWP_Options_Page object, that means that the addon that offered this option is not
	 * active or not registered so delete it from SWP_User_Options.
	 *
	 * But DO NOT remove registration keys or registration timestamps.
	 *
	 */
	public function remove_unavailable_options() {

	}

	/**
	 * Instead of a giant array of defaults like we have now, have it sort the options against the SWP_Options_Page object.
	 * Any User Option that isn't set, simply set it to it's default value.
	 *
	 */
	public function set_defaults() {

	}


}
