<?php


/**
 * The Options Page "Section" Class
 *
 * The options page is divided into tabs, sections and then actual options. This
 * class is the one used to create actual section objects. The section objects will
 * then be populated with the options that live within that particular section.
 *
 * @package   SocialWarfare\Functions\Social-Networks
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since 3.0.0 | 01 MAR 2018 | Created
 *
 */
class SWP_Options_Page_Section extends SWP_Abstract {


    /**
    * The description printed on the Settings page under the title.
    *
    * @var string $description
    *
    */
    public $description;


    /**
    * The KnowledgeBase link printed on the Settings page near the title.
    *
    * @var string $link
    *
    */
    public $link;

    /**
    * The input elements reflecting configurable options to be set by the uesr.
    *
    * This is the array where each of the avialable option objects are stored.
    * The HTML for each of these options will be rendered within this section
    * on the options page.
    *
    * @var array Array of SWP_Option objects.
    *
    */
    public $options;


	/**
	 * The magic construct method.
	 *
	 * In order to create a new section on the options page, it must contain at
	 * least an name and a unique key that differentiates it from all of the other
	 * sections on the options page.
	 *
	 * @since  3.0.0 | 01 MAR 2018 | Created
	 * @param  string $name The name of this section. Will be printed at the top.
	 * @param  string $key  The unique key for this section of the options page.
	 * @return void
	 *
	 * @todo If $key is a mandatory property, why are we checking if it's set?
	 *
	 */
    public function __construct( $name, $key ) {
        $this->options = new stdClass();
        $this->set_name( $name );

        if ( isset( $key ) ) :
            $this->key = $key;
        else:
            $this->key = $this->name_to_key( $name );
        endif;
    }


    /**
    * The related link to our KnowledgeBase article.
    *
    * @since  3.0.0 | 01 MAR 2018 | Created
    * @param  string $link The direct link to the article.
    * @return object $this Allows for method chaining.
    *
    */
    public function set_information_link( $link ) {
        if ( !is_string( $link ) || strpos( $link, 'http' ) === false ) {
            $this->_throw( $link . ' must be a valid URL.' );
        }

        $this->link = $link;

        return $this;
    }


    /**
    * The description text appearing under the section's name.
    *
    * @since  3.0.0 | 01 MAR 2018 | Created
    * @param  string $description The full text to be displayed in the section.
    * @return object $this The updated object. Allows for method chaining.
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
    *
    * This is the method that allows us to add an actual option to this section
    * of the settings page. An SWP_Option needs to be created, and then this method
    * allows that option to be added to this section.
    *
    * @since  3.0.0 | 01 MAR 2018 | Created
    * @param  mixed $option One of the SWP_Option child classes.
    * @return object $this The updated object. Allows for method chaining.
    *
    */
    public function add_option( $option ) {
        $types = ['SWP_Addon_Registration', 'SWP_Option_Toggle', 'SWP_Option_Select', 'SWP_Option_Text', 'SWP_Option_Textarea'];

        $type = get_class( $option );

        if ( !( in_array( $type, $types ) || is_subclass_of( $option, 'SWP_Option' ) ) ) {
            $this->_throw("Requres one of the SWP_Option child classes.");
        }

        $name = $option->key;
        $this->options->$name = $option;

        return $this;
    }


    /**
    * Adds multiple options at once.
    *
    * Option objects can be created inside of an array and then added to this
    * section via this method.
    *
    * @since  3.0.0 | 01 MAR 2018 | Created
    * @param array $options An array of SWP_Option child objects.
    * @return object $this The updated object. Allows for method chaining.
    *
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


    /**
    * A method to render the html for each tab.
    *
    * @since  3.0.0 | 03 MAR 2018 | Created
    * @param  void
    * @return string Fully qualified HTML for this tab.
    *
    */
    public function render_HTML() {
        //* The opening tag, which may or may not have dependencies or be premium.
        $html = '<div class="sw-section sw-grid sw-col-940 sw-fit sw-option-container ' . $this->key . '_title_wrapper" ';
        $html .= $this->render_dependency();
        $html .= $this->render_premium();
        $html .= '>';

            $html .= '<h2>';
                $html .= '<a target="_blank" class="swp_support_link" href="'. $this->link .'" title="Click here to learn more about these options.">i</a>';
            $html .= $this->name . '</h2>';

        $html .= '</div>';

        $html .= '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $this->key . '_description_wrapper">';
            $html .= '<p class="sw-subtitle">' . $this->description . '</p>';
        $html .= '</div>';

        // $html .= '<div class="sw-options-wrap">';
        $html .= $this->render_options();
        // $html .= '</div>';

        return $html;
    }


    /**
    * Sets the key used by dependent sections and options.
    *
    * @since 3.0.0 | 01 MAR 2018 | Created
    * @param string $key The unique key being assigned to this section.
    * @return SWP_Options_Page_Section $this The updated object.
    *
    */
    protected function set_key( $key = null ) {
        if ( !empty( $key ) ) :
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


    /**
    * Renders Title and Support Link HTML.
    *
    * @since  3.0.0 | 01 MAR 2018 | Created
    * @param  void
    * @return string $title The fullly qualified HTML for the section's title.
    * @todo What the heck is this method? This method doesn't do anything except return a variable
    *       that isn't even defined. Is this method actually called anywhere?
    *
    */
    private function create_title() {
        return $title;
    }


    /**
    * Renders the description HTML.
    *
    * @return string $description The fullly qualified HTML for the section's description.
    * @todo What the heck is this method? This method doesn't do anything except return a variable
    *       that isn't even defined. Is this method actually called anywhere?
    *
    */
    private function create_description() {
        return $description;
    }


    /**
    * Renders the section's options HTML.
    *
    * @since  3.0.0 | 01 MAR 2018 | Created
    * @param  void
    * @return string $options The fully qualified HTML for the sections options.
    *
    */
    private function render_options() {
        $map = $this->sort_by_priority($this->options);
        $options = '';

        foreach( $map as $prioritized ) {
            foreach( $this->options as $option) {
                if ( $option->key === $prioritized['key'] ) :
                    $options .= $option->render_HTML();
                endif;
            }
        }

        return $options;
    }
}
