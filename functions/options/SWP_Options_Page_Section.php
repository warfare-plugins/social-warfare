<?php

class SWP_Options_Page_Section extends SWP_Abstract {

	public $name;
	public $description;
	public $information_link;
	public $priority;
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
        if ( !is_string( $link ) || strpos( 'http', $link) ) {
            $this->throw("\$link must be a valid URL.");
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
            $this->throw( "Requires a string." );
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
        $types = ['Checkbox', 'Select', 'Input', 'Textarea'];
        $type = get_class( $option );

        if ( !in_array( $type, $types ) ) {
            $this->throw("Requres one of the SWP_Option child classes.");
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
            $this->throw( "Requires an array of SWP_Option objects." );
        }

        foreach ( $options as $option ) {
            $this->add_option( $option );
        }

        return $this;
    }

    public function add_divider() {
        //* Every section will add a divider below, except for the last section.
    }


	function sort_by_priority() {

		/**
		 * Take the $this->options array and sort it according to the priority attribute of each option.
		 * This will allow us to add options via the addons and place them where we want them by simply
		 * assigning a priority to it that will place it in between the two other options that are
		 * immediately higher and lower in their priorities. Or place it at the end if it has the highest
		 * priority.
		 *
		 */

	}

}
