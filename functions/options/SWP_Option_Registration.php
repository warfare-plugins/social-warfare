<?php

class SWP_Option_Registration extends SWP_Abstract {
    public function __construct( $name ) {
        parent::__construct( $name) ;
        $this->display_name = 'Social Warfare - ' . $name;
    }

    public function render_HTML() {
        $registered = 0;

        if ( !empty( $this->license_key) ) :
            $registered = 1;
        endif;

        $html = '<div class="registration-wrapper '. $this->key . '" registration="' . $registered . '">';
            $html .= '<h2>' . __($this->plugin_name . ' Registration', 'social-warfare') . '</h2>';

            $html .= $this->not_registered();
            $html .= $this->is_registered();

        $html .= '</div>';
    }

    protected function not_registered() {
        $html = '<div class="sw-grid sw-col-940 swp_is_not_registered">';

            $html .= '<div class="sw-red-notice">';
                $html .=  __( 'This copy of '. $this->plugin_name .' is NOT registered. <a target="_blank" href="https://warfareplugins.com">Click here</a> to purchase a license or add your account info below.' , 'social-warfare' );
            $html .= '</div>';

            $html .= '<p class="sw-subtitle sw-registration-text">';
                $html .= __( 'Enter your registration key for '. $this->plugin_name .' and then click Register Plugin.' , 'social-warfare' );
            $html .= '</p>';

            $html .= '<div class="sw-grid sw-col-300">';
                $html .= '<p class="sw-input-label">';
                    $html .= __( $this->plugin_name . ' License Key' , 'social-warfare' );
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
                <?php __( 'This copy of '. $this->plugin_name .' is registered. Wah-hoo!', 'social-warfare' ); ?>
            </div>

            <p class="sw-subtitle sw-registration-text">
                <?php __( 'To unregister your license click the button below to free it up for use on another domain.' , 'social-warfare' ); ?>
            </p>

            <div class="sw-grid sw-col-300">
                <p class="sw-authenticate-label">
                    <?php __( 'Deactivate Registration' , 'social-warfare' ); ?>
                </p>
            </div>

            <div class="sw-grid sw-col-300">
                <a href="#" class="unregister-plugin button sw-navy-button" swp-addon="<?php echo $registration['key']; ?>"  swp-item-id="<?php echo $registration["product_id"]; ?>">
                    <?php __( 'Unregister Plugin' , 'social-warfare' ); ?>
                </a>
            </div>
            <div class="sw-grid sw-col-300 sw-fit"></div>

        </div>

        <?php

        $html = ob_end_clean();

        return $html;
    }
}