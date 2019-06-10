<?php
/**
 * Helper methods for getting, updating, and storing data.
 *
 */
class SWFW_Utility {

	/**
	 * The plugins data stored in wp_options.
	 *
	 * Contains network counts and a last_updated timestamp.
	 * An example:
	 * array(
	 *     'last_updated' => 3481834,
	 *     'pinterest_follow_count' => 1231,
	 *     'facebook_follow_count' => 3851 );
	 *
	 * Settings are otherwise automatically managed by WP_Widget for each instance
	 * of Social_Warfare_Follow_Widget.
	 *
	 * @var array $options
	 *
	 */
	public static $options = array();


	/**
	 * Fetches an option if it exists.
	 *
	 * @since  1.0.0 | 03 JAN 2018 | Created.
	 * @param  string $key The target data.
	 * @return mixed The value if it exists, else bool `false`.
	 *
	 */
	public static function get_option( $key ) {
		if ( empty ( self::$options ) ) {
			self::get_options();
		}

		if ( isset( self::$options[$key] ) ) {
			return self::$options[$key];
		}

		return false;
	}


	/**
	 * Saves the new follow count.
	 *
	 * @since  1.0.0 | 03 JAN 2018 | Created.
	 * @param  void
	 * @return bool True iff the value in the database is changed; else false.
	 *
	 */
	public static function save_follow_counts() {
		$updated = update_option( 'swfw_options', self::$options, true );
		SWFW_Cache::update_cache_timestamp();

		return $updated;
	}


	/**
	 * Retrieves the associative array of plugin options.
	 *
	 * If the options do not exist they are created.
	 *
	 * @since 1.0.0 | 15 JAN 2019 | Created.
	 * @param void
	 * @return array The plugin options from the WP database.
	 *
	 */
	public static function get_options() {
		$options = get_option( 'swfw_options', array() );

		if ( empty( $options ) ) {
			 //Initialize the SWFW options with a timestamp.
			$options = array( 'last_updated' => 0  );
			update_option( 'swfw_options', $options );
		}

		return self::$options = $options;
	}

	/**
	 * Update the local stored value for a network count.
	 *
	 * First update all local values, then store the updated array in the database.
	 *
	 * @param  string $network	The network whose follow count to update.
	 * @param  int    $count  	The value of the count.
	 * @return void
	 *
	 */
	public static function update_network_count( $network, $count ) {
		if ( empty ( self::$options ) ) {
			self::get_options();
		}

		$key = "{$network}_follow_count";

		self::$options[$key] = $count;
	}


	/**
	 * Stores data to the swfw_options column in the database.
	 *
	 * @param  string $key The target data.
	 * @return mixed The value if it exists, else bool `false`.
	 *
	 */
	public static function update_option( $key, $value ) {
		if ( empty ( self::$options ) ) {
			self::get_options();
		}

		self::$options[$key] = $value;

		return update_option( 'swfw_options', self::$options, true );
	}

}
