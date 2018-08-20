<div class="registration-wrapper <?php echo $registration['key'] ?>" registration="<?php echo $registered; ?>">

    <h2><?php esc_html_e( $registration['plugin_name'].' Registration' , 'social-warfare' ); ?></h2>

    <div class="sw-grid sw-col-940 swp_is_not_registered">

        <div class="sw-red-notice">
            <?php _e( 'This copy of '.$registration['plugin_name'].' is NOT registered. <a target="_blank" href="https://warfareplugins.com">Click here</a> to purchase a license or add your account info below.' , 'social-warfare' ); ?>
        </div>

        <p class="sw-subtitle sw-registration-text">
            <?php esc_html_e( 'Enter your registration key for '.$registration['plugin_name'] .' and then click Register Plugin.' , 'social-warfare' ); ?>
        </p>

        <div class="sw-grid sw-col-300">
            <p class="sw-input-label">
                <?php esc_html_e( $registration['plugin_name'].' License Key' , 'social-warfare' ); ?>
            </p>
        </div>

        <div class="sw-grid sw-col-300">
            <input name="<?php echo $registration['key'] ?>_license_key" type="text" class="sw-admin-input" placeholder="License Key" value="<?php echo $license_key; ?>" />
        </div>

        <div class="sw-grid sw-col-300 sw-fit register_button_grid">
            <a href="#" class="register-plugin button sw-navy-button" swp-addon="<?php echo $registration['key']; ?>"  swp-item-id="<?php echo $registration["product_id"]; ?>">
                <?php esc_html_e( 'Register Plugin' , 'social-warfare' ); ?>
            </a>
        </div>

        <div class="sw-clearfix"></div>
    </div>

    <div class="sw-grid sw-col-940 swp_is_registered">

        <div class="sw-green-notice">
            <?php esc_html_e( 'This copy of '.$registration['plugin_name'].' is registered. Wah-hoo!', 'social-warfare' ); ?>
        </div>

        <p class="sw-subtitle sw-registration-text">
            <?php esc_html_e( 'To unregister your license click the button below to free it up for use on another domain.' , 'social-warfare' ); ?>
        </p>

        <div class="sw-grid sw-col-300">
            <p class="sw-authenticate-label">
                <?php esc_html_e( 'Deactivate Registration' , 'social-warfare' ); ?>
            </p>
        </div>

        <div class="sw-grid sw-col-300">
            <a href="#" class="unregister-plugin button sw-navy-button" swp-addon="<?php echo $registration['key']; ?>"  swp-item-id="<?php echo $registration["product_id"]; ?>">
                <?php esc_html_e( 'Unregister Plugin' , 'social-warfare' ); ?>
            </a>
        </div>
        <div class="sw-grid sw-col-300 sw-fit"></div>

    </div>

</div>
