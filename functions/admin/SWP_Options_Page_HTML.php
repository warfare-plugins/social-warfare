<?php
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
