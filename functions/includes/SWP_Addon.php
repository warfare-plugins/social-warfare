<?php

class SWP_Addon extends SWP_Abstract {
    public function __construct( $name, $key, $product_id, $version ) {
        $this->plugin_name = $name;
        $this->product_id = $product_id;
        $this->key = $key;
        $this->version = $version;

        add_filter( 'swp_registrations', [$this, 'init_addon']);

    }

    public function init_addon( $registrations ) {

        if ( defined('SWP_VERSION') && version_compare(SWP_VERSION , SWAWP_CORE_VERSION_REQUIRED) >= 0 ) :
            array_push( $registrations, [
                'plugin_name'   => $this->plugin_name,
                'key'           => $this->key,
                'product_id'    => $this->product_id,
                'version'       => $this->version
            ]);
        endif;

        return $registrations;
    }
}
