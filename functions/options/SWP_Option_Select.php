<?php

/**
 * SWP_Ooption_Select: The class used to create select options.
 *
 * This class is used to create each select option needed on the options page.
 *
 * @since  2.4.0   | Created | 02 MAR 2017
 * @access public
 *
 */
class SWP_Option_Select extends SWP_Option {


	/**
	 * Choices
	 *
	 * This property will contain a key->value array designating the available
	 * options that the plugin user can select from the select dropdown box.
	 *
	 * @var array
	 *
	 */
	public $choices = array();


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
    public function __construct( ) {

    }


	/**
	 * A method for setting the available choices for this option.
	 *
	 * This method will accept a $key->value set of options which will later be used to
	 * generate the select dropdown boxes from which the plugin user can select.
	 *
	 * @since 2.4.0 | 02 MAR 2018 | Created
	 * @param array $choices
	 * @return object $this Allows for method chaining
	 * @TODO: Use the throw() method instead of simply returning false.
	 *
	 */
    public function set_choices( $choices )  {

        if ( !is_array( $choices ) ) {
            return false;
        }

        $this->choices = $choices;

        return $this;
    }


	/**
	 * A method for adding choices to the existing list.
	 *
	 * This is because additional addons may simply want to expand on the number of
	 * choices that are available for certain option.
	 *
	 * @since 2.4.0 | 02 MAR 2018 | Created
	 * @param array $choices
	 * @return object $this Allows for method chaining
	 * @TODO: Sanitize the input with the throw() method.
	 * @TODO: Make this function actually do something (i.e. make it merge these choices
	 * 		  into the existing array of choices.)
	 * 		  
	 */
	public function add_choices( $choices ) {

	}


}
