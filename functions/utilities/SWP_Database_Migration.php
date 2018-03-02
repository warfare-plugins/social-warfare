<?php

/**
 * Migrates camel cased database keys to semantic, snake cased keys.
 *
 * The migrate() method should only ever be called once. This class is planned
 * to be obsolete in the future.
 */
class SWP_Database_Migration {

    public function __construct() {
        if ( !$this->is_migrated() ) {
            $this->migrate();
        }
    }

    /**
     * Checks to see if our new options have been stored in the database.
     *
     * @return bool True if migrated, else false.
     *
     */
    public function is_migrated() {
        $option = get_option( 'social_warfare_settings' , false);

        //* Cast the potential options array to boolean.
        return !!$option;
    }

    /**
     * Map prevous key/value pairs to new keys.
     *
     * @return [type] [description]
     */
    private function migrate() {
        $options = get_option( 'socialWarfareOptions', array() );

        $map = array(
            'sniplyBuster' => 'frame_buster',

        );

        $migrations = array();

        //* Camel refers to the previous key in the options table
        //* whether or not it was camelCase.
        foreach( $options as $camel => $value ) {

            //* We specified an update to the key.
            if ( array_key_exists( $camel, $map) ) {
                $snake = $map[$camel];
                $migrations[$snake] = $value;

            //* The previous key was fine, keep it.
            } else {
                $migrations[$camel] = $value;
            }
        }

        update_option( 'social_warfare_settings', $migrations);
    }
}
