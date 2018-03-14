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
	 * Contains a key->value array designating the available
	 * options that the plugin user can select from the select dropdown box.
	 *
	 * @var array
	 *
	 */
	public $choices = array();


	/**
	 * The __construct magic method.
	 *
	 * This method is used to instantiate the class.
	 *
	 * @param $name The name printed with the select.
	 * @return none
	 *
	 */
     public function __construct( $name, $key ) {
        parent::__construct( $name, $key );

         $this->choices = array();
     }


	/**
	 * A method for setting the available choices for this option.
	 *
	 * Accepts a $key->value set of options which will later be used to
	 * generate the select dropdown boxes from which the plugin user can select.
	 *
	 * This method will overwrite any existing choices previously set. If you
	 * want to add a choice, use add_choice or add_choices instead.
	 *
	 * @since 2.4.0 | 02 MAR 2018 | Created
	 * @param array $choices
	 * @return object $this Allows for method chaining
	 * @TODO: Use the throw() method instead of simply returning false.
	 *
	 */
    public function set_choices( $choices )  {

        if ( !is_array( $choices ) ) {
            $this->_throw( "You must provide an array of choices to go into the select." );
        }

        $this->choices = $choices;

        return $this;
    }

    public function set_default( $value ) {
        if ( !is_string( $value ) ||  empty( $value ) ) {
            $this->_throw( 'Please provide a default value as a string.' );
        }

        $this->default = $value;

        return $this;
    }

    /**
     * Create the options for a select dropdown.
     *
     * @since 2.4.0 | 02 MAR 2018 | Created
     * @param array $choices Array of strings to be translated and made into options.
     * @return SWP_Option_Select $this This object with the updated choices.
     *
     */
    public function add_choices( $choices )  {

        if ( !is_array( $choices ) ) {
            $this->_throw( "Please provide an array of choices. If you want to add a single choice, use add_choice()." );
        }

        foreach( $choices as $choice ) {
            $this->add_choice( $choice );
        }

        return $this;
    }


    /**
    * Add an option to the select.
    *
    * Additional addons may want to expand the choices available for
    * a given option.
    *
    * @since 2.4.0 | 02 MAR 2018 | Created
    * @param string $choice The choice to add to the select.
    * @return object $this Allows for method chaining
    * @TODO: Sanitize the input with the throw() method.
    * @TODO: Make this function actually do something (i.e. make it merge these choices
    * 		  into the existing array of choices.)
    * @return SWP_Option_Select $this The calling object with an updated chocies array.
    */
     public function add_choice( $choice ) {
         if ( !is_string( $choice ) ) {
             $this->_throw( "Please provide a choice to add to the select. The choice must be passed as a string." );
         }

         array_push( $this->choices, __( $choice, 'social-warfare' ) );

         return $this;
     }

    /**
     * Render the HTML
     *
     * Renders the HTML to the options page based on what
     * the properties of this object have been set to.
     *
     * @since 2.4.0 | 02 MAR 2018 | Created
     * @param none
     * @return string The rendered HTML of this option.
     * @TODO: Make this method render soem HTML.
     *
     */
    public function render_HTML( $echo = false ) {
        $html = $this->open_HTML();

        $html .= $this->create_select( $html );

        $html .= $this->close_HTML();

        $this->html = $html;

        return $html;
    }


    /**
     * Creates the boilerplate opening tags and classes.
     *
     * @return string $html HTML ready to be filled with a checkbox input.
     */
    private function open_HTML() {
        $html = '';

        if ( empty( $this->size ) ) :
            $this->size = '';
        endif;

        $size = $this->get_css_size( $this->size );

        //* Open the wrapper tag, remains open to add attributes.
        $html .= '<div class="sw-grid ' . $size . ' sw-fit sw-option-container ' . $this->name . '_wrapper" ';

        $html .= $this->render_dependency( $html );
        $html .= $this->render_premium( $html );

        //* Close the opening bracket. Tag is still open.
        $html .= '>';

        $html .= '<div class="sw-grid sw-col-300">';
        $html .= '<p class="sw-input-label">' . $this->name . '</p>';
        $html .= '</div>';
        $html .= '<div class="sw-grid sw-col-300">';

        return $html;
    }

    /**
     * Sets the HTML for the select with options.
     *
     * @return string $html The select and related HTML.
     */
    private function create_select( $html ) {
        $html .= '<select name=' . $this->key . '>';

        if ( isset($this->user_options[$this->key]) ) :
            $checked = $this->user_options[$this->key];
        else :
            $checked = $this->default;
        endif;

        foreach ( $this->choices as $key => $display_name ) {
            $html .= '<option value=' . $key . ' ' . selected($key, $checked, false) . '>' . $display_name . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

    /**
    * Resolves tags left open in open_HTML.
    *
    * @param string $html The HTML to close.
    * @return string $html Completed and valid HTML.
    */
    private function close_HTML( $html ) {
        $html .= '</div><div class="sw-premium-blocker></div></div>';

        return $html;
    }

}
