<?php

class SWP_Option_Page_Registration extends SWP_Abstract {
    public $addon;

    public function __construct() {
        apply_filters( 'swp_registrations', array() );
    }

    /**
     * Create the HTML for registration sections.
     *
     * $local_vars are the variables needed to properly set $file's HTML.
     *
     * @param  String $file       The template file to include.
     * @param  array $local_vars  Variables found inside the file. Stored as
     *                            [ 'var_name' => value ]
     * @return Void               Void
     */
    public function render( $file, $local_vars ) {
        $path = PLUGIN_DIR . '/includes/' . $file;

        if ( !file_exists( $path ) ) {
            $this->_throw( 'Missing template file to render HTML.' );
        }

        ob_start();
        extract( $local_vars );
        include( $file );
        ob_flush();
    }
}
