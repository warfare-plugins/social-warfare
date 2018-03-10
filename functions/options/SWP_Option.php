<?php

/**
 * SWP_Option: The parent class used to creat individual options on the options page.
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
     * The type of HTML input element.
     *
     * Valid types are:
     *
     * text, select, checkbox, textarea
     * @var string $type
     */
    public $type;

    /**
     * The CSS class representing the size (width) of the input.
     *
     * @see set_size()
     * @var string $size
     */
    public $size;

    /**
     * The default value for the given input.
     *
     * @var mixed $default. See the corresponding class's set_default() method.
     */
    public $default;


	/**
	 * The __construct magic method.
	 *
	 * This method is used to instantiate the class.
	 *
	 * @param none
	 * @return none
	 *
	 */
     public function __construct( $name ) {
         parent::__construct( $name );
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
     public function set_default( $value ) {
         if ( !is_string( $value ) ||  empty( $value ) ) {
             $this->_throw( 'Please provide a default value as a string.' );
         }

         $this->default = $value;

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
         $options = [ 'one-fourth', 'two-fourths', 'three-fourths', 'four-fourths' ];
         $sizes = PHP_EOL;

         foreach( $options as $option ) {
             $sizes .= $option . PHP_EOL;
         }

         if ( !in_array( $size, $sizes) ) {
             $this->throw( "Please enter a valid size. Acceptable sizes are:" . $sizes );
         }

         $this->size = $size;

         return $this;
     }

    /**
     * Creates HTML based on the option's properties and user settings.
     *
     * @param  bool $echo If True, immediataly echoes the markup to the page.
     *                    Else returns the markup as a string.
     * @return string $html
     */
    public function render_HTML( $echo = false ) {
        //* Intentionally left blank.
        //* Each child class should override this method.
        $this->throw( "Should not be called from the parent class." );
    }

    /**
     * Force a child option to depend on a parent option.
     *
     * If the parent's value is one of the values passed in as $values,
     * the option will be visible ont the Settings page. Otherwise, the option
     * is hidden until the dependency is set to that value.
     *
     * @param string $parent The parent option's key.
     * @param array $values Values which enable this option to exist.
     *
     */
    public function set_dependency( $parent, $values ) {
        if ( !is_string( $parent ) ) {
            $this->throw( 'Argument $parent needs to be a string matching the key of another option.' );
        }

        if ( !isset( $values) ) {
            $this->throw( 'Dependency values must passed in as the second argument.' );
        }

        if ( !is_array( $values ) ) {
            $values = array( $values );
        }

        $this->dependency = new stdObject();
        $this->dependency->parent = $parent;
        $this->dependency->values = $values;

        return $this;
    }
}
