<?PHP

class SWP_Option_Icons extends SWP_Option {

    public function __construct() {
    }

    /**
    * The Active buttons UI in the Display tab.
    *
    * @param array $icons The array of currently selected icons.
    * @return SWP_Section_HTML $this The calling instance, for method chaining.
    */
    public function do_active_icons() {
        $user_icons = $this->get_user_icons();

        $html = '<div class="sw-grid sw-col-300">';
            $html .= '<h3 class="sw-buttons-toggle">' . __( 'Active' , 'social-warfare' ) . '</h3>';
        $html .= '</div>';

        $html .= '<div class="sw-grid sw-col-620 sw-fit">';
            $html .= '<div class="sw-active sw-buttons-sort">';

            foreach ( $user_icons['icons'] as $network => $data ) {
                $html .= '<i class="sw-s sw-' . $network . '-icon" ';
                $html .= ' data-network="' . $network . '"';

                if ( isset($data['premium']) && $data['premium'] === 'premium' ) :
                    $html .= ' premium="1"';
                endif;

                $html .= '></i>';
            }

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
    * //* TODO: finish this method.
    */
    public function do_inactive_icons() {
        $all_icons = $this->get_all_icons();
        $user_icons = $this->get_user_icons();

        $html = '<div class="sw-grid sw-col-300">';
            $html .=  '<h3 class="sw-buttons-toggle">' . __( 'Inactive' , 'social-warfare' ) . '</h3>';
        $html .=  '</div>';

        $html .=  '<div class="sw-grid sw-col-620 sw-fit">';
            $html .=  '<div class="sw-inactive sw-buttons-sort">';

            foreach( $all_icons['icons'] as $network => $data ) {
                if ( !isset( $user_icons['icons'][$network]) ) :
                    $html .= '<i class="sw-s sw-' . $network . '-icon" ';
                    $html .= ' data-network="' . $network . '"';

                    if ( isset($data['premium']) && $data['premium'] === 'premium' ) :
                        $html .= ' premium="1"';
                    endif;

                    $html .= '></i>';
                endif;
            }

            $html .= '</div>';
        $html .= '</div>';

        $this->html = $html;

        return $this;
    }

    public function render_HTML() {
        echo $this->html;
    }

}
