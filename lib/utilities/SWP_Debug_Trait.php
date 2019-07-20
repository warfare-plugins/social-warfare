<?php

/**
 * SWP_Debug_Trait
 *
 * The purpose of this trait is to allow access to commonly used methods
 * throughout the various classes of the plugin without always having to
 * include the SWP_Abstract class as a parent class.
 *
 * The SWP_Abstract class was primary designed to support the classes
 * used to create the options page. As such, using it as a parent class for
 * something like, say, SWP_Social_Network seemed out of order as the social
 * network objects would now have all of the properties that were used on the
 * options objects.
 *
 * This means that the object would not be structured the way that I would
 * prefer. As such, I think we should rename it to SWP_Options_Abstract and
 * move it into the options folder and only use it for a parent class in
 * the options classes. We can then migrate all methods that we want to use
 * everywhere else into this trait.
 *
 * @since 3.0.0 | 07 APR 2018 | Created
 *
 */
trait SWP_Debug_Trait {


	/**
    * Give classes an error handling method.
    *
    * @since  3.0.0 | 07 APR 2018 | Created
    * @param  mixed $message The message to send as an error.
    * @return object Exception An exception with the passed in message.
    *
    */
    public function _throw( $message ) {
        ob_start();
        print_r( debug_backtrace()[1]['args'] );
        $dump = ob_get_clean();

        if ( is_string( $message ) ) {
            throw new Exception( get_class( $this ) . '->' . debug_backtrace()[1]['function'] . '() ' . $message . ' Here is what I received: ' . $dump );
        } else {
            throw new Exception( get_class( $this ) . '->' . debug_backtrace()[1]['function'] . '() ' . PHP_EOL . var_dump( $message ) );
        }
    }


	/**
	 * A method for debugging and outputting the class object.
	 *
	 * This method allows you to input a URL debug paramater into the browser's
	 * address bar in order to easily output the entire contents of any given
	 * object. It is called by using the lowercase name of the class without
	 * the swp_ prefix. This is used following the ?swp_debug= parameter.
	 *
	 * Example: To debug SWP_Post_Cache, use ?swp_debug=post_cache.
	 *
	 * @since  3.1.0 | 25 JUN 2018 | Created
	 * @since  3.4.0 | 18 OCT 2018 | Moved into this trait.
	 * @param  void
	 * @return void
	 *
	 */
	public function debug() {


		/**
		 * This will allow the dumping of an entire class by simply adding
		 * ?swp_debug=class_name (without the swp_) to the end of a page's URL.
		 *
		 * Example: ?swp_debug=pro_bitly
		 *
		 */
		$class_name = str_replace('swp_', '', strtolower( get_class( $this ) ) );
		if( true === SWP_Utility::debug( $class_name ) ) {
			echo '<pre class="swp_debug_data">', var_dump( $this ), '</pre>';
		}


		/**
		 * This will dump out all method exit statuses by simply adding
		 * ?swp_debug=exit_statuses. This allows us to view the reasons why any
		 * class methods bailed out at any given time.
		 *
		 */
		global $swp_exit_statuses;
		if( true === SWP_Utility::debug( 'exit_statuses' ) && empty( $swp_exit_statuses['printed'] ) ) {
			echo '<pre class="swp_debug_data"><h3>Class Method Exit Statuses</h3><ol>';
			foreach($swp_exit_statuses as $key => $value ){
				echo '<li>' . $value . '</li>';
			}
			echo '</ol></pre>';
			$swp_exit_statuses['printed'] = true;
		}
	}


	/**
	 * An easy to access method for recording exit statuses. We often bail out
	 * of methods when certain conditions are not met. This will record that bail
	 * in a local class property (exit_status).
	 *
	 * This will create messages in the following format:
	 *
	 * "SWP_Pro_Bitly->shorten_link() exited while checking for "access_token" in
	 * \wp-content\plugins\social-warfare-pro\lib\url-management\SWP_Pro_Bitly.php
	 * on line 179"
	 *
	 * IMPORTANT: This should be implemented every single time a method contains
	 * a bail conditional. This will make debugging much, much easier.
	 *
	 * We subtract 1 from the line number, so if we always place this on the very
	 * next line immediately following the bail conditional then it will return
	 * the line number of the conditional itself.
	 *
	 * @since  4.0.0 | 19 JUL 2019 | Created
	 * @param  string $reason The name of the item, variable, or condition that caused the bail.
	 * @return void
	 *
	 */
	public function record_exit_status( $reason ) {

		// The lowercase class name without the swp_ prefix.
		$class_name = str_replace('swp_', '', strtolower( get_class( $this ) ) );

		// We'll only run the debug_backtrace if debugging is being accessed.
		if( false === SWP_Utility::debug( 'exit_statuses' ) && false === SWP_Utility::debug( $class_name ) ) {
			return;
		}

		// A global allows us to collect statuses from all across the plugin.
		global $swp_exit_statuses;
		if( empty( $swp_exit_statuses ) ) {
			$swp_exit_statuses = array();
		}

		// Collect the line, file, class and method that exited.
		$backtrace = debug_backtrace();
		$file      = $backtrace[0]['file'];
		$line      = $backtrace[0]['line'];
		$class     = $backtrace[1]['class'];
		$method    = $backtrace[1]['function'];

		// Compile the status and store it in the local $exit_statuses property and in the global.
		$status = $class . '->' . $method .'() exited while checking for "' . $reason . '" in ' . $file .' on line ' . ($line - 1);
		$swp_exit_statuses[$class . '->' . $method .'()'] = $status;
		$this->exit_statuses[$method] = $status;
	}

}
