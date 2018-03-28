<?php

/**
 * The parent class for all Option_X types.
 *
 * This class is used to create each individual option throughout the options page.
 * It provides the framework for each of type of option that is
 * available: input, select, checkbox, and textarea. Each of these options is
 * instantiated through their respective class.
 *
 * @since  2.4.0   | Created | 02 MAR 2017
 * @access public
 *
 */
class SWP_Option extends SWP_Abstract {


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
    * The key for this option in the database.
    *
    * @var string $key
    */
    public $key;


    /**
    * The default value for the given input.
    *
    * @var mixed $default. See the corresponding class's set_default() method.
    */
    public $default;


    /**
    * The string of HTML which creates the element.
    *
    * @var string $html
    */
    public $html;


    /**
    * Boolean indicating whether the plugin is registered or not.
    *
    * @var bool $swp_registration
    */
    public $swp_registration;


    /**
    * The required constructor for PHP classes.
    *
    * @param string $name The display name for the toggle.
    * @param string $key The database key for the user setting.
    *
    */
    public function __construct( $name, $key ) {
        parent::__construct( $name );

        //* TODO: Write the real method to verify registration.
        $this->swp_registration = true;
        $this->set_key( $key );
    }


    /**
    * Fetches the css class to match a given size given as a string.
    *
    * @param Optional string $size The size of the element using SWP sizing.
    * @return object $this Allows for method chaining.
    */
    protected function get_css_size( $size = '' ) {
        $size = '' === $size ? $this->size : $size;

        $map = [
            'two-fourths'   => ' sw-col-460 ',
            'two-thirds'    => ' sw-col-300 ',
            'four-fourths'  => ' sw-col-620 ',
        ];

        if ( empty($size) ) :
            return $map['two-thirds'];
        endif;

        return $map[$size];
    }


    /**
    * Get the pre-defined value of the option.
    *
    * @since April 15 2018
    */
    protected function get_value() {
        if ( isset($this->value) ) {
            return $this->value;
        }

        if ( isset( $this->user_options[$this->key] ) ) {
            return $this->user_options[$this->key];
        }

        return $this->default;
    }


    /**
    * Creates HTML based on the option's properties and user settings.
    *
    * @return void
    */
    public function render_HTML() {
        //* Intentionally left blank.
        //* Each child class should override this method.
        $this->_throw( "Should not be called from the parent class." );
    }


	/**
    * Set the default value of this option. This value will be used until the plugin user changes the value
    * to something else and saves the options.
    *
    * @since 2.4.0 | 02 MAR 2018 | Created
    * @param mixed The default value will vary based on the kind of option being generated.
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
            $this->_throw( 'Argument $parent needs to be a string matching the key of another option.' );
        }

        if ( !isset( $values) ) {
            $this->_throw( 'Dependency values must passed in as the second argument.' );
        }

        if ( !is_array( $values ) ) {
            $values = array( $values );

        }

        $this->dependency = new stdClass();
        $this->dependency->parent = $parent;
        $this->dependency->values = $values;

        return $this;
    }


    /**
    * Assign the database key for this element.
    *
    * @param string $key The key which correlates to the input.
    * @return object $this The calling instance, for method chaining.
    */
    public function set_key( $key ) {
        if ( !is_string( $key ) ) {
            $this->_throw( 'Please provide a key to the database as a string.' );
        }

        $this->key = $key;

        return $this;
    }


	/**
    * Some option types have multiple sizes that will determine their visual layout on the option
    * page. This setter allows you to declare which one you want to use.
    *
    * @since 2.4.0 | 02 MAR 2018 | Created
    * @param string The size of the option on the page (e.g. 'two-thirds').
    * @return object $this The calling instance, for method chaining.
    *
    */
    public function set_size( $size ) {
        $options = [ 'two-fourths', 'two-thirds', 'four-fourths' ];

        if ( !in_array( $size, $options) ) {
            $sizes = PHP_EOL;

            foreach( $options as $option ) {
                $sizes .= $option . PHP_EOL;
            }

            $this->_throw( "Please enter a valid size. Acceptable sizes are:" . $sizes );
        }

        $this->size = $size;

        return $this;
    }
}
