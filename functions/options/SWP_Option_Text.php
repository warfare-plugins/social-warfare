<?PHP

/**
 * Used to create input options.
 *
 * This class is used to create each input option needed on the options page.
 *
 * @since  2.4.0   | Created | 02 MAR 2017
 * @access public
 */
class SWP_Option_Text extends SWP_Option {

    public function __construct( $name ) {
        parent::__construct( $name );
        $this->default = '';
    }

    public function set_default( $default ) {
        if ( !is_string( $default )  ) {
            $this->throw( 'Please provide a default value as a string.' );
        }

        $this->default = $default;

        return $this;
    }
}
