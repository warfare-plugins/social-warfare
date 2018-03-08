<?php

class SWP_Addon extends SWP_Abstract {
    /**
     * The license key which activated the addon. Defaults to ''.
     * @var string
     */
    public $license;

    /*
     * The registration status of the addon. Defaults to false.
     * @var boolean
     */
    public $registered;

    public function __construct( $name ) {
        parent::__construct( $name );

        $this->license = '';

    }

    public function validate( $key ) {

    }

    public function activate() {

    }

    public function deactivate() {

    }

    public function update_registration() {

    }
}

class SWP_Pro extends SWP_Addon {

    public function __construct( $key ) {
        parent::__construct( 'pro' );
        $this->validate( $key );
    }
}

$pro = new SWP_Option_Page_Section( 'Social Warfare - Pro Registration' );
$pro->set_description( 'To unregister your license click the button below to free it up for use on another domain.' );
$pro->set_priority( 10 );

$button = new SWP_Option_Button();
