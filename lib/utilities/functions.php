<?php

/**
 * This file serves to hold global functions made available to developers
 * by Social Warfare.
 *
 * Any actual plugin functionality needs to exist as a class method somewhere else.
 *
 * @since  3.3.0 | 14 AUG 2018 | Created file.
 *
 */

function socialWarfare( $content = false, $where = 'default', $echo = true ) {
    social_warfare( array( 'content' => $content, 'where' => $where, 'echo' => $echo ) );
}

function social_warfare( $args = array() ) {
    $buttons_panel = new SWP_Buttons_Panel( $args );
    return $buttons_panel->render_HTML();
}

function swp_kilomega( $number ) {
    return SWP_Utiltiy::kilomega( $number );
}
