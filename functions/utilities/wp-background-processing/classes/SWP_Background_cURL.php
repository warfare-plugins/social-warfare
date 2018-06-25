<?php
/**
 * WP Async Request
 *
 * @package WP-Background-Processing
 */

if ( class_exists( 'WP_Async_Request' ) ) {

	/**
	 * Abstract WP_Async_Request class.
	 *
	 * @abstract
	 */
	class SWP_Background_cURL extends WP_Async_Request {

		/**
		 * Prefix
		 *
		 * (default value: 'wp')
		 *
		 * @var string
		 * @access protected
		 */
		protected $prefix = 'wp';

		/**
		 * Action
		 *
		 * (default value: 'async_request')
		 *
		 * @var string
		 * @access protected
		 */
		protected $action = 'async_request';

		/**
		 * Identifier
		 *
		 * @var mixed
		 * @access protected
		 */
		protected $identifier;

		/**
		 * Data
		 *
		 * (default value: array())
		 *
		 * @var array
		 * @access protected
		 */
		protected $data = array();

		/**
		 * Initiate new async request
		 */
		public function __construct() {
			$this->identifier = $this->prefix . '_' . $this->action;

			add_action( 'wp_ajax_' . $this->identifier, array( $this, 'maybe_handle' ) );
			add_action( 'wp_ajax_nopriv_' . $this->identifier, array( $this, 'maybe_handle' ) );
		}


        /**
         * Set data used during the request
         *
         * @param array $data Data.
         *
         * @return $this
         */
        public function data( $data ) {
            $this->data = $data;

            return $this;
        }


		/**
		 * Dispatch the async request
		 *
		 * @return array|WP_Error
		 */
		public function dispatch() {
			$url  = add_query_arg( $this->get_query_args(), $this->get_query_url() );
			$args = $this->get_post_args();

			return wp_remote_post( esc_url_raw( $url ), $args );
		}

		/**
		 * Get query args
		 *
		 * @return array
		 */
		protected function get_query_args() {
			if ( property_exists( $this, 'query_args' ) ) {
				return $this->query_args;
			}

			return array(
				'action' => $this->identifier,
				'nonce'  => wp_create_nonce( $this->identifier ),
			);
		}

		/**
		 * Get query URL
		 *
		 * @return string
		 */
		protected function get_query_url() {
			if ( property_exists( $this, 'query_url' ) ) {
				return $this->query_url;
			}

			return admin_url( 'admin-ajax.php' );
		}

		/**
		 * Get post args
		 *
		 * @return array
		 */
		protected function get_post_args() {
			if ( property_exists( $this, 'post_args' ) ) {
				return $this->post_args;
			}

			return array(
				'timeout'   => 0.01,
				'blocking'  => false,
				'body'      => $this->data,
				'cookies'   => $_COOKIE,
				'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
			);
		}

		/**
		 * Maybe handle
		 *
		 * Check for correct nonce and pass to handler.
		 */
		public function maybe_handle() {
			// Don't lock up other requests while processing
			session_write_close();

			check_ajax_referer( $this->identifier, 'nonce' );

			$this->handle();

			wp_die();
		}

		/**
		 * Handle
		 *
		 */
		protected function handle() {
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
                delete_post_meta( $this->id, '_' . $network . '_shares' );
                update_post_meta( $this->id, '_' . $network . '_shares', $this->share_data[$network] );
            }

            delete_post_meta( $this->id, '_total_shares' );
            update_post_meta( $this->id, '_total_shares', $this->share_data['total_shares'] );
        }

	}
}
