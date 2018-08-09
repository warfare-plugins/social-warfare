<?PHP

/**
 * SWP_Option_Icons: The class used to display available netowrks on the options
 * page.
 *
 * This class is used to create each individual nnetwork that is available to be
 * dragged and dropped between the active and inactive states.
 *
 * @package   SocialWarfare\Functions\Options
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     3.0.0  | 02 MAR 2018 | Created
 * @access    public
 *
 */

class SWP_Option_Icons extends SWP_Option {


	/**
	* html
	*
	* This property will contain the string of rendered html for this panel of
	* icons.
	*
	* @var string
	*
	*/
	public $html;


	/**
	 * The magic construct method designed to instantiate this option object.
	 *
	 * @since  3.0.0 | 02 MAR 2018 | Created
	 * @param  string $name The name of this option object.
	 * @param  string $key  The unique key of this option object.
	 * @return void
	 *
	 */
    public function __construct( $name, $key ) {
        global $swp_user_options;

        parent::__construct( $name, $key );
        add_filter( 'swp_options_page_defaults', array( $this , 'register_default' ) );
        add_filter( 'swp_options_page_values', array( $this, 'register_available_values' ) );

        $this->user_options = $swp_user_options;
    }


    public function register_default( $defaults = array() ) {
        if ( !array_key_exists( 'order_of_icons', $defaults ) ) :
            $defaults['order_of_icons'] = array(
    			'google_plus' => 'google_plus',
    			'twitter'     => 'twitter',
    			'facebook'    => 'facebook',
    			'linkedin'    => 'linkedin',
    			'pinterest'   => 'pinterest'
    		);
        endif;

        return $defaults;
    }


    public function register_available_values( $values ) {
        global $swp_social_networks;
        $networks = array();

        /* order_of_icons is an array of $network_key => $network_key
         * So we need to create an array in that form.
         * Yes, it is redundant, but that's how it is.
         */
        foreach( $swp_social_networks as $key => $object ) {
            $networks[$key] = $key;
        }

        $values['order_of_icons'] = array(
            'type' => 'none',
            'values'   => $networks
        );

        return $values;
    }


	/**
	 * A method to output the currently active icons.
	 *
	 * @since  3.0.0 | 02 MAR 2018 | Created
	 * @param  void
	 * @return object $this Allows for method chaining.
	 *
	 */
    public function do_active_icons() {
        $this->is_active_icons = true;
        return $this;
    }


	/**
	 * A method to output the currently inactive icons.
	 *
	 * @since  3.0.0 | 02 MAR 2018 | Created
	 * @param  void
	 * @return object $this Allows for method chaining.
	 *
	 */
    public function do_inactive_icons() {
        $this->is_active_icons = false;
        return $this;
    }


    /**
    * The Active buttons UI in the Display tab.
    *
    * @since  3.0.0 | 02 MAR 2018 | Created
    * @param array $icons The array of currently selected icons.
    * @return object $this The calling instance, for method chaining.
    *
    */
    public function render_active_icons() {
        global $swp_user_options;

        $all_networks = $this->get_all_networks();
        $user_icons = $swp_user_options['order_of_icons'];

        $html = '<div class="sw-grid sw-col-300">';
            $html .= '<h3 class="sw-buttons-toggle">' . __( 'Active' , 'social-warfare' ) . '</h3>';
        $html .= '</div>';

        $html .= '<div class="sw-grid sw-col-620 sw-fit">';
            $html .= '<div class="sw-active sw-buttons-sort">';

            if ( count($user_icons) > 0 ):
    			foreach( $user_icons as $network_key) {

                    //* On updates, this is being passed as an object for some reason.
                    if ( is_object( $network_key ) ) :

                        $network_key = $network_key->key;

                    //* This should not ever be reached. But if it does, fail gracefully.
                    elseif ( !is_string( $network_key) ) :
                        return;
                    endif;

                    if ( array_key_exists( $network_key, $all_networks ) && isset( $all_networks[$network_key]) ) :
                        $network = $all_networks[$network_key];

                        $html .= $this->render_icon_HTML( $network );
                    endif;
                }
            endif;

            $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="sw-clearfix"></div>';

        $this->html = $html;

        return $this;
    }


    /**
    * The Inactive buttons UI in the Display tab.
    *
    * @since  3.0.0 | 02 MAR 2018 | Created
    * @param array $icons The array of currently selected icons.
    * @return object $this The calling instance, for method chaining.
    *
    */
    public function render_inactive_icons() {
        global $swp_user_options;

        $all_networks = $this->get_all_networks();
        $user_icons = $swp_user_options['order_of_icons'];

        $first_all = reset( $all_networks );

        if ( gettype( $first_all ) === 'object' ) :

            //* Get the keys first, then diff the array.
            $keys = array_keys( $all_networks );

            $first_user = reset( $user_icons );

            if ( gettype( $first_user ) === 'object' ) :
                $temp = array();

                foreach( $user_icons as $object ) {
                    $temp[] = $object->key;
                }

                $user_icons = $temp;

            endif;

            $inactive_icons = array_diff( $keys, $user_icons );

        elseif ( array_key_exists( 0, $all_networks) ) :

            //* If $all_networks is numerically indexed, just diff the array.
            $inactive_icons = array_diff( $all_networks, $user_icons );

        endif;

        $html = '<div class="sw-grid sw-col-300">';
            $html .=  '<h3 class="sw-buttons-toggle">' . __( 'Inactive' , 'social-warfare' ) . '</h3>';
        $html .=  '</div>';

        $html .=  '<div class="sw-grid sw-col-620 sw-fit">';
            $html .=  '<div class="sw-inactive sw-buttons-sort">';
            if ( count( $inactive_icons) > 0 ) :
                foreach( $inactive_icons as $network_key) {
                    $network = $all_networks[$network_key];

                    $html .= $this->render_icon_HTML( $network );
                }
            endif;

            $html .= '</div>';
        $html .= '</div>';

        $this->html = $html;

        return $this;
    }


	/**
	 * Render the html for an individual icon.
	 *
	 * @since  3.0.0 | 02 MAR 2018 | Created
	 * @param  object $network The social network object
	 * @return string          The string of html with the new icon added.
	 *
	 */
    protected function render_icon_HTML( $network ) {
        $html = '<i class="sw-s sw-' . $network->key . '-icon" ';
        $html .= ' data-network="' . $network->key . '"';

        if ( !empty($network->premium) ) :
            $html .= ' premium="'.$network->premium.'"';
        endif;

        $html .= '></i>';


        return $html;
    }


	/**
	 * Render the html for the icons panel.
	 *
	 * @since  3.0.0 | 02 MAR 2018 | Created
	 * @param  void
	 * @return void Rendered html will be stored in local html property.
	 *
	 */
    public function render_HTML() {
        if ($this->is_active_icons) {
            $this->render_active_icons();
        } else {
            $this->render_inactive_icons();
        }

        return $this->html;
    }

}
