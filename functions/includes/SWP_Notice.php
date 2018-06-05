<?php

class SWP_Notice {
    public function __construct( $key, $message, $type, $args ) {

        $this->set_key( $key );
        $this->set_message( $message );
        $this->notices = get_option( 'social_warfare_dismissed_notices', [] );

        if ( !$this->should_display_notice() ) :
            return;
        endif;


        if ( $type === 'dashboard' ) :
                $this->notice = $this->create_dashboard_notice( $message );
                add_action( 'admin_notices', [$this, 'print_HTML'] );

        elseif ( $type = 'options_page' ) :
                //* This class does not exist, but this code outlines the process
                //* we would use to insert it into the options page somewhere.
                $notice = new SWP_Option_Notice( $key, $message );
                $notice->set_priority( $args['priority'] );
                $tab = $args['tab'];
                $section = $args['section'];
                $SWP_Options_Page->tabs->$tab->sections->$section->add_option( $notice );

        else :
                // code...
        endif;

        add_action( 'wp_ajax_perma_dismiss', [ $this, 'perma_dismiss' ] );
        add_action( 'wp_ajax_nopriv_perma_dismiss', [ $this, 'perma_dismiss' ] );
    }

    public function should_display_notice() {

        if ( true === $this->notices[$this->key] ) {
            return false;
        }

        return true;
    }


    public function perma_dismiss() {
        $this->notices[$_POST['key']] = true;

        echo json_encode( update_option( 'social_warfare_dismissed_notices', $notices ) );
        wp_die();
    }


    public function set_message( $message ) {
        if ( 'string' !== gettype( $key ) ) :
            throw("Please provide a string for your database key.");
        endif;

        return $this;
    }


    public function set_key( $key ) {
        if ( 'string' !== gettype( $key ) ) :
            throw("Please provide a string for your database key.");
        endif;

        return $this;
    }

    public function render_HTML() {
        $class = isset( $args['class_name'] ) ? $args['class_name'] : '';

        $html = '<div class="swp-notice swp-dismiss-notice notice ' . $class . '" data-key=" <?php echo $this->key ?>">';
            $html .= '<p>' . $this->message . '</p>';
        $html .= '</div>';

        $this->html = $html;
        return $this;
    }


    public function print_HTML() {
        if ( empty( $this->html) ) :
            $this->render_HTML();
        endif;

        echo $this->html;

        return $this;
    }
}
