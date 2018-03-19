<?php

class SWP_Options_Page_Section extends SWP_Abstract {
    /**
     * The description printed on the Settings page under the title.
     * @var string $description
     */
	public $description;

    /**
     * The KnowledgeBase link printed on the Settings page near the title.
     * @var string $link
     */
	public $link;

    /**
     * The input elements reflecting configurable options to be set by the uesr.
     * @var array Array of SWP_Option objects.
     */
	public $options;

	public function __construct( $name ) {
		$this->options = new stdClass();

        $this->set_name( $name );
        $this->set_key();
	}

    /**
     * The related link to our KnowledgeBase article.
     *
     * @param string $link The direct link to the article.
     * @return SWP_Options_Page_Section $this The updated object.
     */
    public function set_information_link( $link ) {
        if ( !is_string( $link ) || strpos( $link, 'http' ) === false ) {
            $this->_throw( $link . ' must be a valid URL.' );
        }

        return $this;
    }

    /**
     * The description text appearing under the section's name.
     *
     * @param string $description The full text to be displayed in the section.
     * @return SWP_Options_Page_Section $this The updated object.
     *
     */
    public function set_description( $description ) {
        if ( !is_string( $description ) ) {
            $this->_throw( 'Please pass the description as a string.' );
        }

        $this->description = $description;

        return $this;
    }

    /**
     * Adds a user setting option to the section.
     * @param mixed $option One of the SWP_Option child classes.
     * @return SWP_Options_Page_Section $this The updated object.
     *
     */
    public function add_option( $option ) {
        $types = ['SWP_Addon_Registration', 'SWP_Option_Toggle', 'SWP_Option_Select', 'SWP_Option_Text', 'SWP_Option_Textarea'];

        $type = get_class( $option );

        if ( !in_array( $type, $types ) ) {
            $this->_throw("Requres one of the SWP_Option child classes.");
        }

        $name = $option->key;
        $this->options->$name = $option;

        return $this;
    }


    /**
     * Adds multiple options at once.
     *
     * @param array $options An array of SWP_Option child objects.
     * @return SWP_Options_Page_Section $this The updated object.
     */
    public function add_options( $options ) {
        if ( !is_array( $options ) ) {
            $this->_throw( "Requires an array of SWP_Option objects." );
        }

        foreach ( $options as $option ) {
            $this->add_option( $option );
        }

        return $this;
    }

	public function render_HTML() {
        //* The opening tag, which may or may not have dependencies or be premium.
        $html = '<div class="sw-section sw-grid sw-col-940 sw-fit sw-option-container ' . $this->key . '_title_wrapper" ';
        $html .= $this->render_dependency();
        $html .= $this->render_premium();
        $html .= '>';

            $html .= $this->create_title();
            $html .= $this->create_description();

            $html .= '<div class="sw-options-wrap">';
                $html .= $this->render_options();
            $html .= '</div>';

        $html .= '</div>';


        return $html;
	}


    protected function set_key( $key = null ) {
        if ( !empty( $key ) ) :
            if ( !is_string( $key ) ) :
                $this->_throw( 'Please provide a string for this section\'s key.' );
            endif;

            $this->key = $key;
            return $this;
        endif;

        //* Remove all non-word character symbols.
        $key = preg_replace( '#[^\w\s]#i', '', $this->name );

        //* Replace spaces with underscores.
        $key = preg_replace( '/\s+/', '_', $this->name );


        $this->key = strtolower( $key );

        return $this;
    }

    private function create_title() {
        //* Set the support link and title.
        $title = '<h2>';
        $title .= '<a target="_blank" class="swp_support_link" href="'. $this->link .'" title="Click here to learn more about these options.">i</a>';
        $title .= $this->name . '</h2>';

        return $title;
    }

    private function create_description() {
        $description = '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $this->key . '_subtitle_wrapper">';

        //* QUESTION: The dependency is already being set in render_HTML(). Do we also need to set it here?
        // $descrption .= $this->render_dependency( $description );
        // $descrption .= $this->render_premium( $description );

        $description .= '<p class="sw-subtitle">' . $this->description . '</p>';
        $description .= '</div>';

        return $description;
    }

    private function render_options() {
        $options = '';

        foreach ( $this->options as $index => $option ) {
            $options .= $option->render_HTML();
        }

        return $options;
    }

}
