<?php

class SWP_Option_Toggle extends SWP_Option {

	public function __construct( $name, $key ) {
		parent::__construct( $name, $key );
		$this->type = 'checkbox';
		$this->default = true;
		$this->size = 'two-thirds';

	}

    /**
    * Override parent method to make this boolean-specific.
    *
    * @param boolean $value The boolean value to set as default.
    * @return SWP_Option_Toggle $this The calling object, for method chaining.
    */
    public function set_default( $value ) {
        if ( !is_bool( $value ) ||  !isset( $value ) ) {
            $this->_throw( 'Please provide a default value as a boolean.' );
        }

        $this->default = $value;

        return $this;
    }

    /**
    * Creates the HTML for the checkbox module.
    *
    * @return SWP_Option_Toggle $this The calling object, for method chaining.
    */
	public function render_HTML() {
	    $html = $this->open_HTML();
        $html .= $this->create_label();
        $html .= $this->create_toggle();

        $this->html = $html;

        return $html;
	}

    /**
    * Creates the boilerplate opening tags and classes.
    *
    * @return string $html HTML ready to be filled with a checkbox input.
    */
    private function open_HTML() {
        $size = $this->get_css_size();

        $html = '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $this->key . '_wrapper" ';
        $html .= $this->render_dependency();
        $html .= $this->render_premium();
        $html .= '>';

        $html .= '</div>';

        return $html;
    }

    private function create_label() {
        $label = '<div class="sw-grid sw-col-300">';
        $label .= '<p class="sw-checkbox-label">' . $this->name . '</p>';
        $label .= '</div>';

        return $label;
    }

    /**
    * Sets the HTML for the toggle element.
    *
    * This method produces fully qualifed HTML on its own, which is inserted into previously opened markup.
    *
    * @return string $html The checkbox and related HTML.
    */
    private function create_toggle() {
        $status = $this->default;

        if ( isset( $this->user_options[$this->name] ) ) :
            $status = $this->user_options[$this->name];
        endif;

        //* Map the boolean to on/off.
        $status = $status ? 'on' : 'off';

        $checked = $status === 'on' ? 'checked' : '';

        $html = '<div class="sw-grid sw-col-300">';
        $html .= '<div class="sw-checkbox-toggle" status="' . $status . '" field="#' . $this->key . '">';
            $html .= '<div class="sw-checkbox-on">' . __( 'ON', 'social-warfare' ) . '</div>';
            $html .= '<div class="sw-checkbox-off">' . __( 'OFF', 'social-warfare' ) . '</div>';
        $html .= '</div>';
        $html .= '<input type="checkbox" id="' . $this->key . '" class="sw-hidden" name="' . $this->key . '" data-swp-name="' . $this->key . '" ' . $checked . '/>';
        $html .= '</div>';

        return $html;
    }

    /**
    * Resolves open tags from open_HTML().
    *
    * @param string $html The HTML to close.
    * @see $this->open_HTML().
    * @return string $html Completed and valid HTML.
    */
    private function close_HTML() {

    }

}
