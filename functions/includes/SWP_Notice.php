<?php

class SWP_Notice {
    public function __construct( $key, $message, $class = '' ) {
        // delete_option('social_warfare_dismissed_notices');
        $this->init();
        $this->set_key( $key );
        $this->set_message( $message );
        $this->set_class( $class );
        $this->actions = '';

        add_action( 'admin_notices', [$this, 'print_HTML'] );
        add_action( 'swp_admin_notices', [$this, 'get_HTML'] );
        add_action( 'wp_ajax_perma_dismiss', [ $this, 'perma_dismiss' ] );
        add_action( 'wp_ajax_nopriv_perma_dismiss', [ $this, 'perma_dismiss' ] );
    }

    public function init() {
        $notices = get_option( 'social_warfare_dismissed_notices', [] );

        if ( [] === $notices ) {
            update_option( 'social_warfare_dismissed_notices', [] );
        }

        $this->notices = $notices;
    }


    public function should_display_notice() {
        if ( empty( $this->notices[$this->key] ) ) {

            return true;
        }

        return false === $this->notices[$this->key];
    }


    public function perma_dismiss() {
        $this->notices[$_POST['key']] = true;

        echo json_encode( update_option( 'social_warfare_dismissed_notices', $this->notices ) );
        wp_die();
    }


    public function set_message( $message ) {
        if ( !is_string( $message ) ) :
            throw("Please provide a string for your database key.");
        endif;

        $this->message = $message;

        return $this;
    }


    protected function set_key( $key ) {
        if ( !is_string ( $key ) ) :
            throw("Please provide a string for your database key.");
        endif;

        $this->key = $key;

        return $this;
    }

    protected function set_class( $class ) {
        if ( !is_string( $class ) ) :
            throw("Please provide a string for your database key.");
        endif;

        $this->class = $class;

        return $this;
    }


    public function add_cta( $message = '', $link = '', $class = '')  {
        if ( '' === $message ) :
            $message = "Thanks, I understand.";
        endif;

        if ( !empty( $link ) ) :
            $link = ' href="' . $link . '" target="_blank"';
        endif;

        $html = '<a class="swp-notice-cta ' . $class . '" ' . $link . '>';
            $html .= $message;
        $html .= "</a>";

        $this->actions .= $html;

        return $this;
    }


    public function render_HTML() {

        $html = '<div class="swp-dismiss-notice notice ' . $this->class . '" data-key="' . $this->key . '">';
            $html .= '<p>' . $this->message . '</p>';
            $html .= '<div class="swp-actions">';
                $html .= $this->actions;
            $html .= '</div>';
        $html .= '</div>';

        $this->html = $html;

        return $this;
    }


    public function get_HTML() {
        if ( !$this->should_display_notice() ) :
            return;
        endif;

        if ( empty( $this->html ) ) :
            $this->render_HTML();
        endif;

        return $this->html;
    }


    public function print_HTML() {
        if ( !$this->should_display_notice() ) :
            return;
        endif;

        if ( empty( $this->html ) ) :
            $this->render_HTML();
        endif;

        echo $this->html;

        return $this;
    }
}
