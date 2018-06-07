<?php

/**
 * SWP_Notice
 *
 * A class to control the creation and display of admin notices throughout the
 * WordPress dashboard and on the Social Warfare settings page.
 *
 * @since  3.0.9 | 07 JUN 2018 | Created
 * @access public
 *
 */
class SWP_Notice {


	/**
	 * The Magic __construct method
	 *
	 * This method will initialize our notice object and then add the necessary hooks to
	 * allow it to be displayed and to be dismissed via admin-ajax.php.
	 *
	 * @since 3.0.9 | 07 JUN 2018 | Created
	 * @param str $key     A unique key for this notice.
	 * @param str $message The message for this notice
	 *
	 */
    public function __construct( $key, $message ) {
        $this->init();
        $this->set_key( $key );
        $this->set_message( $message );
        $this->actions = array();

		// Add hooks to display our admin notices in the dashbaord and on our settings page.
        add_action( 'admin_notices', array( $this, 'print_HTML' ) );
        add_action( 'swp_admin_notices', array( $this, 'get_HTML' ) );

		// Add a hook for permanently dismissing a notice via admin-ajax.php
        add_action( 'wp_ajax_perma_dismiss', array( $this, 'perma_dismiss' ) );
        add_action( 'wp_ajax_nopriv_perma_dismiss', array( $this, 'perma_dismiss' ) );

		// Add a hook for temporarily dismissing a notice via admin-ajax.php
        add_action( 'wp_ajax_temp_dismiss', array( $this, 'temp_dismiss' ) );
        add_action( 'wp_ajax_nopriv_temp_dismiss', array( $this, 'temp_dismiss' ) );

    }

    public function init() {
        $notices = get_option( 'social_warfare_dismissed_notices', array() );

        if ( [] === $notices ) {
            update_option( 'social_warfare_dismissed_notices', array() );
        }

        $this->notices = $notices;
    }


	/**
	 * A method to determine if this notice should be displayed.
	 *
	 * @TODO   Check for timestamps and see if 30 days have elapsed.
	 * @since  3.0.9 | 07 JUN 2018 | Created
	 * @return bool true/false
	 *
	 */
    public function should_display_notice() {
        if ( empty( $this->notices[$this->key] ) ) {

            return true;
        }

        return false === $this->notices[$this->key];
    }


	/**
	 * A method to permanently dismiss notices via admin-ajax.php
	 *
	 * @since  3.0.9 | 07 JUN 2018 | Created
	 * @param  none
	 * @return none The respond from update_option is echoed.
	 *
	 */
    public function perma_dismiss() {
        $this->notices[$_POST['key']] = true;

        echo json_encode( update_option( 'social_warfare_dismissed_notices', $this->notices ) );
        wp_die();
    }


	/**
	 * A method to temporarily dismiss notices via admin-ajax.php
	 *
	 * @since  3.0.9 | 07 JUN 2018 | Created
	 * @param  none
	 * @return none The respond from update_option is echoed.
	 *
	 */
	public function temp_dismiss() {
		// TODO: Create a timestamp
		$timestamp = '';
		$this->notices[$_POST['key']] = $timestamp;

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


    public function add_cta( $action = '', $link = '', $class = '' , $timeframe = 'permanent' )  {
        if ( '' === $action ) :
            $action = "Thanks, I understand.";
        endif;

        if ( !empty( $link ) ) :
            $link = ' href="' . $link . '" target="_blank"';
        endif;

        $cta              = array();
        $cta['action']    = $action;
        $cta['link']      = $link;
        $cta['class']     = $class;
		$cta['timeframe'] = $timeframe;

        $this->actions[] = $cta;

        return $this;
    }


    public function render_HTML() {
        if ( empty( $this->actions) ) :
            $this->add_cta();
        endif;

        $html = '<div class="swp-dismiss-notice notice" data-key="' . $this->key . '">';
            $html .= '<p>' . $this->message . '</p>';
            $html .= '<p> - Warfare Plugins Team</p>';
            $html .= '<div class="swp-actions">';

                foreach( $this->actions as $cta) {
                    $html .= '<a class="swp-notice-cta ' . $cta['class'] . '" ' . $cta['link'] . ' data-timeframe="'.$cta['timeframe'].'">';
                        $html .= $cta['action'];
                    $html .= "</a>";
                }

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
