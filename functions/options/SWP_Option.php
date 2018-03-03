<?php

/**
 * SWP_Ooption: The parent class used to creat individual options on the options page.
 *
 * This class is used to create each individual option throughout the options page.
 * It provides the framework for each of the various types of options that are
 * available like input, select, checkbox, etc. Each type of option will be accessed
 * through a child class extended from this class.
 *
 * This parent class will contain the common methods used for setting the name, the
 * premium status, the defaults, etc.
 *
 * @since  2.4.0   | Created | 02 MAR 2017
 * @access public
 *
 */
class SWP_Option {


	/**
	 * SWP_Abstact Trait: Usuful methods able to be used by classes throughout the plugin.
	 *
	 */
	use SWP_Abstract;


	/**
	 * Name
	 *
	 * The name of this option. This is a "pretty" name that the plugin user will see.
	 *
	 * @var string
	 *
	 */
	public $name;


	/**
	 * Type
	 *
	 * The type property refers to the type of option this represents (e.g. input,
	 * textarea, checkbox, etc.)
	 *
	 * @var string
	 *
	 */
	public $type;


	/**
	 * Default
	 *
	 * The default property refers to the default value for this option. This is
	 * what the option will be set to until the user changes it.
	 *
	 * @var mixed This var is dependant on what type of option is being generated.
	 *
	 */
	public $default;


	/**
	 * Premium
	 *
	 * This property determines whether or not this option is a premium option. By
	 * default this property is set to false. The set_premium() method can be called
	 * to change this property. When called, the set_premium() method will accept a
	 * string corresponding to the registration key of the premium plugin on which
	 * this option relies. It will set the $premium_addon property to that string and
	 * switch this property to true.
	 *
	 * @var bool
	 *
	 */
	public $premium = false;


	/**
	 * Priority
	 *
	 * The priority property is used to determine the order in which the options are
	 * presented to the user. These options will be sorted prior to the rendering of
	 * the HTML in ascending order. That is to say, an option with a priority of 10
	 * will appear before an option with a priority of 20.
	 *
	 * @var integer
	 *
	 */
	public $priority;


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
	 * Set's the name of this option. The name is what the plugin user will see as the label.
	 *
	 * @since  2.4.0 | 02 MAR 2018 | Created
	 * @param string The name of this option
	 * @return object $this Allows for method chaining
	 *
	 */
	public function set_name( $name ) {
        if ( !is_string( $name ) ) {
            $this->throw(__CLASS__ . " method set_name() requires a string. {$name} is not acceptable." );
        }

        $this->name = $name;

		return $this;
	}


	/**
	 * Set the priority to determine where this option appears in relation to other
	 * options in the same section.
	 *
	 * @since  2.4.0 | 02 MAR 2018 | Created
	 * @param integer $priority The priority of this option.
	 * @return object $this Allows for method chaining.
	 * @TODO: Sanitize for integers only.
	 *
	 */
	public function set_priority( $priority ) {
		$this->priority = $priority;
		return $this;
	}

	/**
	 * Useful for adding new available choices to a dropdown item that already exists. For example, if Pro
	 * adds additional choices to an option that's already in core.
	 *
	 * @since 2.4.0 | 02 MAR 2018 | Created
	 * @param array A key->value set of options to add to a select box.
	 * @return object $this Allows for method chaining.
	 * @TODO: Make the choices variable merge in with existing choices parameter.
	 *
	 */
	public function add_choice( $choices ) {

		return $this;
	}

	/**
	 * Set the default value of this option. This value will be used until the plugin user changes the value
	 * to something else and saves the options.
	 *
	 * @since 2.4.0 | 02 MAR 2018 | Created
	 * @param mixed The default value will vary based on the kind of option being generated.
	 * @return object $this Allos for method chaining.
	 *
	 */
	public function set_default( $default ) {
		$this->default = $default;
		return $this;
	}


	/**
	 * Set the premium status of this option.
	 *
	 * Since there are going to be multiple addons, it's not sufficient to set premium to simply true or
	 * false. Instead, it will be false by default. Unless this method is called and a string corresponding
	 * the registration key of the corresponding premium addon is passed. Example: $SWP_Option->set_premium('pro');
	 *
	 * This will then set the premium property to true and place the registration key into the premium_addon property.
	 *
	 * This method does not need to be called unless it is a premium option.
	 *
	 * @since 2.4.0 | 02 MAR 2018 | Created
	 * @param string String corresponding to the registration key of premium plugin if true.
	 * @return $this Return the object to allow method chaining.
	 *
	 */
	public function set_premium( $premium_addon ) {
		if ( !is_string( $premium_addon ) ) {
			$this->throw(__CLASS__ . " method set_premium() requires a string. {$premium_addon} is not acceptable." );
		}

		$this->premium = true;
		$this->premium_addon = $premium_addon;

		return $this;
	}


	/**
	 * Some option types have multiple sizes that will determine their visual layout on the option
	 * page. This setter allows you to declare which one you want to use.
	 *
	 * @since 2.4.0 | 02 MAR 2018 | Created
	 * @param string The size of the option on the page (e.g. 'two-thirds').
	 * @return object $this Allows for method chaining.
	 *
	 */
	public function set_size( $size ) {
		$this->size = $size;
		return $this;
	}

}
