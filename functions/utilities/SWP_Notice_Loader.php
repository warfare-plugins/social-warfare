<?php

/**
 * SWP_Notice_Loader
 *
 * This is where we define all the messages, CTAs, and scheudling for each notice.
 * It is fine to bloat the method with as many notices as necessary.
 *
 * @since  3.0.9 | 09 JUN 2018 | Created the class.
 *
 */
class SWP_Notice_Loader {
    public function __construct() {
        $this->do_notices();
    }

    /**
     * Creates and instantiates the notices.
     *
     *
     * @since  3.0.9 | 09 JUN 2018 | Created the method.
     * @param  void
     * @return void
     */
    public function do_notices() {
        $message = 'As of June 30th, 2018, StumbleUpon will no longer exist as a sharing platform. Instead, they are moving in with Mix. ';
        $message .= 'While this is exciting for Mix, <b>share counts will not be transferred, and Mix is not providing a share button or API. </b>';
        $message .= 'You can read more about it <a href="http://help.stumbleupon.com/customer/en/portal/articles/2908172-transitioning-from-stumbleupon-to-mix" target="_blank">here</a>.';

        $notice = new SWP_Notice( 'stumble_uponc', $message, 'notice-info' );
        $date = new DateTime();
        $start = $date->modify('-1 days')->format('Y-m-d h:i:s');
        $end = $date->modify('+3 day')->format('Y-m-d h:i:s');
        $notice->set_start_date( $start )->set_end_date($end);
    }
}
