<?php

class SWP_Options_Input extends SWP_Options_Abstract {
    public $type;
    public $size;
    public $default;
    public $premium;
    public $addon;

    public function __construct( $name ) {
        parent::__construct( $name );
    }

    public function render_HTML() {
        //* Intentionally left blank.
        //* Each child class should override this method.
    }

    public function set_size( $size ) {
        if ( !is_string($size) || !strpos("-", $size) ) {
            return false;
        }

        $this->size = $size;

        return $this;
    }

    public function set_default() {
        //* Intentionally left blank.
        //* Each child class should override this method.
    }

    public function set_premium( $bool ) {
        if (!is_boolean($bool) ) {
            $this->throw("Requires a boolean, typically True. ");
        }

        $this->premium = $bool;

        return $this;
    }

    /**
     * Force a child option to be dependend on a parent option.
     *
     * @param string $parent The parent option's key.
     * @param array $values Values which enable this option to exist.
     *
     */
    public function set_dependency( $parent, $values ) {
        if ( !is_string( $parent ) ) {
            $this->throw( "Parent needs to be a string matching the key of another option." );
        }

        if ( !isset( $values) ) {
            $this->throw( "Dependency values must be set." );
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

/**
 * For <input type="text" />
 *
 */
class SWP_Options_Text extends SWP_Options_Input {

    public function __construct( $name ) {
        parent::__construct( $name );
        $this->default = '';
    }
}

class SWP_Options_Select extends SWP_Options_Input {
    public $choices;

    public function __construct( $name ) {
        parent::__construct( $name );

        $this->choices = array();
    }

    /**
     * Create the options for a select dropdown.
     *
     * @param array $choices Array of strings to be translated and made into options.
     * @return SWP_Options_Select $this This objec with the updated choices.
     *
     */
    public function set_choices( $choices )  {

        if ( !is_array( $choices ) ) {
            return false;
        }

        foreach( $choices as $index => $choice ) {
            $choices[$index] = __( $choice, 'social-warfare' );
        }

        $this->choices = $choices;

        return $this;
    }

    public function set_default( $bool ) {
        if ( !is_boolean($default ) ) {
            $this->throw( "Requires a boolean value." );
        }
    }
}
