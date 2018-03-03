<?php

class SWP_Option_Input extends SWP_Option {
    public $type;
    public $size;
    public $content;
    public $default;

    public function __construct() {
        $this->name = "string";
        $this->choices = array();
        // $this->toggle =
    }


    public function render_HTML() {
        //* Intentionally left blank.
        //* Each child class should override this method.
    }

}


class SWP_Option_Select extends SWP_Option {
    public function __construct( ) {

    }

    public function set_choices( $content )  {

        if ( !is_array( $content ) ) {
            return false;
        }

        $this->content = $content;

        return $this;
    }


}
/*
$select = new SWP_Select();

$content = array(
				'top' => __( 'Top of the Page' ,'social-warfare' ),
				'bottom' => __( 'Bottom of the Page' ,'social-warfare' ),
				'left' => __( 'On the left side of the page' ,'social-warfare' ),
				'right' => __('On the right side of the page', 'social-warfare'),
                );

$select->set_size('two-fourths')->set_content( $content )->default( 'bottom' );
*/
