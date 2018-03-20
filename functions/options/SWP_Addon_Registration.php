<?php

class SWP_Addon_Registration extends SWP_Option_Text {
    public $key;
    public $license_key;
    public $product_id;

    public function __construct( $name, $key ) {
        parent::__construct( $name, $key ) ;
        $this->display_name = 'Social Warfare - ' . $name;
        $this->set_key( $key );
        $this->license_key = $this->get_license_key();
        $this->product_id = 63157;
        $this->version = '2.3.5';
    }

    public function render_HTML() {
        $registered = 0;

        if ( !empty( $this->license_key) ) :
            $registered = 1;
        endif;

        $html = '<div class="registration-wrapper '. $this->key . '" registration="' . $registered . '">';
            $html .= '<h2>' . __($this->name . ' Registration', 'social-warfare') . '</h2>';

            $html .= $this->not_registered();
            $html .= $this->is_registered();

        $html .= '</div>';

        $this->html = $html;

        return $html;
    }

    public function get_license_key() {
        return '3034dd4c7d9dda6fe924fd59aa83fca3';
        return $this->user_options( $this->key . "_license_key" );
    }

    protected function not_registered() {
        $html = '<div class="sw-grid sw-col-940 swp_is_not_registered">';

            $html .= '<div class="sw-red-notice">';
                $html .=  __( 'This copy of '. $this->name .' is NOT registered. <a target="_blank" href="https://warfareplugins.com">Click here</a> to purchase a license or add your account info below.' , 'social-warfare' );
            $html .= '</div>';

            $html .= '<p class="sw-subtitle sw-registration-text">';
                $html .= __( 'Enter your registration key for '. $this->name .' and then click Register Plugin.' , 'social-warfare' );
            $html .= '</p>';

            $html .= '<div class="sw-grid sw-col-300">';
                $html .= '<p class="sw-input-label">';
                    $html .= __( $this->name . ' License Key' , 'social-warfare' );
                $html .= '</p>';
            $html .= '</div>';

            $html .= '<div class="sw-grid sw-col-300">';
                $html .= '<input name="' . $this->key . '_license_key" type="text" class="sw-admin-input" placeholder="License Key" value="' . $this->license_key . '" />';
            $html .= '</div>';

            $html .= '<div class="sw-grid sw-col-300 sw-fit register_button_grid">';
                $html .= '<a href="#" class="register-plugin button sw-navy-button" swp-addon="' . $this->key . '"  swp-item-id="' . $this->product_id . '">';
                    $html .= __( 'Register Plugin' , 'social-warfare' );
                $html .= '</a>';
            $html .= '</div>';

            $html .= '<div class="sw-clearfix"></div>';
        $html .= '</div>';

        return $html;
    }

    protected function is_registered() {
        ob_start();

        ?>

        <div class="sw-grid sw-col-940 swp_is_registered">

            <div class="sw-green-notice">
                <?php _e( 'This copy of '. $this->name .' is registered. Wah-hoo!', 'social-warfare' ); ?>
            </div>

            <p class="sw-subtitle sw-registration-text">
                <?php _e( 'To unregister your license click the button below to free it up for use on another domain.' , 'social-warfare' ); ?>
            </p>

            <div class="sw-grid sw-col-300">
                <p class="sw-authenticate-label">
                    <?php _e( 'Deactivate Registration' , 'social-warfare' ); ?>
                </p>
            </div>

            <div class="sw-grid sw-col-300">
                <a href="#" class="unregister-plugin button sw-navy-button" swp-addon="<?php echo $this->key ?>"  swp-item-id="<?php echo $this->product_id; ?>">
                    <?php _e( 'Unregister Plugin' , 'social-warfare' ); ?>
                </a>
            </div>
            <div class="sw-grid sw-col-300 sw-fit"></div>

        </div>

        <?php

        $html = ob_get_clean();

        return $html;
    }
}