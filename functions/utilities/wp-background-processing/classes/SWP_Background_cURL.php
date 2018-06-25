<?php

class SWP_Background_cURL extends WP_Background_Process {

	/**
	 * Prefix
	 *
	 * (default value: 'wp')
	 *
	 * @var string
	 * @access protected
	 */
	protected $prefix = 'swp';

	/**
	 * Action
	 *
	 * (default value: 'async_request')
	 *
	 * @var string
	 * @access protected
	 */
	protected $action = 'test_request';


	/**
	 * Initiate new async request
	 */
	public function __construct() {
        error_log("new SWP_Background_cURL()");
        $this->share_data['total_shares'] = 0;
		parent::__construct();
	}


	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $link ) {
        error_log("Running the task method.");
        error_log("The passed in data is: " . $link ) ;
        update_post_meta($this->data['ID'], "set_by_background_process", 'alphacentauri');
        return;

        foreach ($this->permalinks as $link ) {
            $unprocessed_share_data = SWP_CURL::file_get_contents_curl( $link );

            foreach( $swp_social_networks as $network => $network_object ) {
                $share_count = $network_object->parse_api_response($unprocessed_share_data[$network]);
                $this->share_data[$network] += $share_count;
                $this->share_data['total_shares'] += $share_count;
            }
        }

        //* This needs to be a separate loop so all of the share data can be summed.
        foreach( $swp_social_networks as $network => $network_object ) {
            delete_post_meta( $this->data['ID'], '_' . $network . '_shares' );
            update_post_meta( $this->data['ID'], '_' . $network . '_shares', $this->share_data[$network] );
        }


        return false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
        delete_post_meta( $this->data['ID'], '_total_shares' );
        update_post_meta( $this->data['ID'], '_total_shares', $this->share_data['total_shares'] );
		parent::complete();

		// Show notice to user or perform some other arbitrary task...
	}
}
