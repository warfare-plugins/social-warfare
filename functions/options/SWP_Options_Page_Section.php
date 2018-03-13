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
		$this->options = array();

        $this->set_name( $name );
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
        $types = ['SWP_Option_Toggle', 'SWP_Option_Select', 'SWP_Option_Text', 'SWP_Option_Textarea'];

        $type = get_class( $option );

        if ( !in_array( $type, $types ) ) {
            $this->_throw("Requres one of the SWP_Option child classes.");
        }

        array_push($this->options, $option);

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

	public function render_html() {
		// Render the opening div for the section.
		// Loop through each option within this section and call their render_html() function.
		// Render the closing div for the section including the horizontal rule/divider.
	}

}
