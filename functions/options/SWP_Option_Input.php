<?php

/**
 * SWP_Ooption_Input: The class used to create input options.
 *
 * This class is used to create each input option needed on the options page.
 *
 * @since  2.4.0   | Created | 02 MAR 2017
 * @access public
 *
 */
class SWP_Option_Input extends SWP_Option {


	/**
	 * The __construct magic method.
	 *
	 * This method is used to instantiate the class. However, right now, it is left
	 * empty as the majority of this class simply relies on properties and property
	 * setters, and the render_html methods of each child class.
	 *
	 * @param none
	 * @return none
	 *
	 */
    public function __construct() {

    }


	/**
	 * Render the HTML
	 *
	 * This is the method that will render the HTML to the options page based on what
	 * the properties of this object have been set to.
	 *
	 * @since 2.4.0 | 02 MAR 2018 | Created
	 * @param none
	 * @return string The rendered HTML of this option.
	 * @TODO: Make this method render soem HTML.
	 * 
	 */
    public function render_HTML() {

    }

}
