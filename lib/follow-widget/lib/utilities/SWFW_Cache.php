<?php
/**
 * Controls the frequency of API requests. Requests are made up to once/24 hours.
 *
 */
class SWFW_Cache {

	use SWP_Debug_Trait;


	/**
	 * True iff the follow counts are less than 24 hours old.
	 *
	 * Since all follow networks use the same cache timesamp, we can have one
	 * universal $is_fresh field to direct all follow networks.
	 * @var boolean $is_fresh
	 *
	 */
	static $is_fresh;

	/**
	 * For this addon we will consider the age limit to be 24 hours.
	 *
	 * @return boolean True iff `last_updated` is less than 24 hours old.
	 *
	 */
	public static function is_cache_fresh() {

		if ( isset( self::$is_fresh ) ) {
			return self::$is_fresh;
		}

		$last_updated = (int) SWFW_Utility::get_option( 'last_updated' );
		$current_time =  (int) time() / DAY_IN_SECONDS;
		self::debug();

		self::$is_fresh = $current_time - $last_updated < 24;
		return self::$is_fresh;
	}


	/**
	 * Updates the follow counts in the database and the last_updated timestamp.
	 *
	 * @param  array $counts Looks like: array('network_key' => (int) follow_count)
	 * @return bool  True iff the counts were updated, else false.
	 *
	 */
	public static function update_cache_timestamp() {
		$now = (int) time() / DAY_IN_SECONDS;

		return SWFW_Utility::update_option( 'last_updated', $now );
	}

	public function debug() {
		$parameter = $_GET['swfw_debug'];
		if ( empty( $parameter ) ) {
			return;
		}

		switch( $parameter ) {
			case 'force_api_requests' : {
				SWFW_Utility::update_option( 'last_updated', 0);
				self::$is_fresh = false;
			}
		}
	}
}
