<?PHP

class SWP_Option_Icons extends SWP_Option {

    public function __construct( $name, $key ) {
        global $swp_user_options;

        parent::__construct( $name, $key );
        $this->user_options = $swp_user_options;
    }

    public function do_active_icons() {
        $this->is_active_icons = true;
        return $this;
    }

    public function do_inactive_icons() {
        $this->is_active_icons = false;
        return $this;
    }

    /**
    * The Active buttons UI in the Display tab.
    *
    * @param array $icons The array of currently selected icons.
    * @return SWP_Section_HTML $this The calling instance, for method chaining.
    */
    public function render_active_icons() {
		$all_icons = $this->get_all_icons();
        $user_icons = $this->get_user_icons();
        if ( empty($user_icons) ) :
            $user_icons = [];
        endif;

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

                    if ( array_key_exists( $network_key, $all_icons ) && isset( $all_icons[$network_key]) ) :
                        $network = $all_icons[$network_key];

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
    * @param array $icons The array of currently selected icons.
    * @return SWP_Section_HTML $this The calling instance, for method chaining.
    */
    public function render_inactive_icons() {
        $all_icons = $this->get_all_icons();
        $user_icons = $this->get_user_icons();

        if ( empty( $user_icons )  ) :
            return $this;
        endif;

        $first = reset( $all_icons );

        if ( gettype( $first ) === 'object' ) :

            //* Get the keys first, then diff the array.
            $keys = [];

            foreach( $all_icons as $object) {
                $keys[] = $object->key;
            }

			foreach ( $user_icons as $object ) {
				$user_icon_keys[] = $object->key;
			}

            $inactive_icons = array_diff( $keys, $user_icon_keys );

        elseif ( array_key_exists( 0, $all_icons) ) :

            //* If $all_icons is numerically indexed, just diff the array.
            $inactive_icons = array_diff( $all_icons, $user_icons );

        endif;

        $html = '<div class="sw-grid sw-col-300">';
            $html .=  '<h3 class="sw-buttons-toggle">' . __( 'Inactive' , 'social-warfare' ) . '</h3>';
        $html .=  '</div>';

        $html .=  '<div class="sw-grid sw-col-620 sw-fit">';
            $html .=  '<div class="sw-inactive sw-buttons-sort">';
            if ( count( $inactive_icons) > 0 ) :
                foreach( $inactive_icons as $network_key) {
                    $network = $all_icons[$network_key];

                    $html .= $this->render_icon_HTML( $network );
                }
            endif;

            $html .= '</div>';
        $html .= '</div>';

        $this->html = $html;

        return $this;
    }

    protected function render_icon_HTML( $network ) {
        $html = '<i class="sw-s sw-' . $network->key . '-icon" ';
        $html .= ' data-network="' . $network->key . '"';

        if ( !empty($network->premium) ) :
            $html .= ' premium="'.$network->premium.'"';
        endif;

        $html .= '></i>';


        return $html;
    }

    public function render_HTML() {
        if ($this->is_active_icons) {
            $this->render_active_icons();
        } else {
            $this->render_inactive_icons();
        }

        return $this->html;
    }

}
