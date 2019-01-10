<?php
/**
 * Of data we handle, log in credentials and access tokens are the most sensitive.
 * We want to keep them as safe and secure as possible.
 *
 * We encode the entire swp_authorizations option, as well as the keys and
 * values for those options. This way no sensitive data is kept as plaintext
 * in the database.
 *
 * Since we are using two way encoding/decoding, this means other users who
 * know the encoding functions are also able to access this data.
 *
 * To prevent this would be to store the data in our own server,
 * rather than the user's server, but that is not an option.
 *
 */
class SWP_Credential_Helper {


	public function __construct() {


		if ( $_GET['page'] == 'social-warfare' ) {
			$this->options_page_scan_url();
		}
	}


	public static function get_instance() {
		return new SWP_Credential_Helper();
	}


	/**
	 * Retrieve a token granted by a third party service.
	 *
	 * We use base64_encode so that no network name or token is stored as
	 * plaintext in the database.
	 *
	 * This is not the same as hashing it like a password, as a malicious user
	 * could base64_decode any data in these fields to get the original values.
	 *
	 * @since 3.5.0 | 10 JAN 2018 | Created.
	 * @param  string $network The host service that provided the token.
	 * @param  string $field    The type of token to fetch. Usually 'access_token'.
	 * @return mixed  A string token, if it exists, else `false`.
	 *
	 */
	public static function get_token( $network, $field = 'access_token' ) {
		$tokens = self::get_authorizations();
		$tokens = SWP_Credential_Helper::get_instance()->get_authorizations();
		$network_key = base64_encode( $network );

		if ( empty ( $tokens[$network_key] ) ) {
			return false;
		}

		$encoded_token = $tokens[$network_key][$field];
		if ( empty( $encoded_token ) ) {
			return false;
		}

		return base64_decode( $encoded_token );
	}


	/**
	 * Deletes network data, if it exists.
	 *
	 * @since 3.5.0 | 10 JAN 2018 | Created.
	 * @param  string $network The network with data to delete.
	 * @param  string $field   The type of data to remove. Most often 'access_token'.
	 * @return bool            True iff deleted, else false.
	 *
	 */
	public static function delete_token( $network, $field = 'access_token' ) {
		$tokens = $this->get_authorizations();
		$network_key = base64_encode( $network );

		if ( isset( $tokens[$network_key] ) ) {
			unset( $tokens[$network_key][$field] );
			return true;
		}

		return false;
	}


	/**
	 * When processing network authentications, the user is ultimately
	 * redirected back to the Social Warfare options.
	 *
	 * If the authentication was a success, we can store the token for later use.
	 *
	 * The paramters these functions look for are generated in
	 * https://warfareplugins.com/authorizations/${network}/return_token.php.
	 *
	 * @since 3.5.0 | 10 JAN 2018 | Created.
	 * @param void
	 * @return void
	 *
	 */
	protected function options_page_scan_url() {
		// We have a new access_token.
		if ( isset( $_GET['network'] )  && isset( $_GET['access_token'] ) ) {
			$updated = $this->store_data( $_GET['network'], 'access_token', $_GET['access_token'] );
		}

		// We have a new access_secret.
		if ( isset( $_GET['network'] ) && isset( $_GET['access_secret'] ) ) {
			$this->store_data( $_GET['network'], 'access_secret', $_GET['access_secret'] );
		}
	}


	/**
	 * Retrieve a token granted by a third party service.
	 *
	 * We use base64_encode so that no network name or token is stored as
	 * plaintext in the database.
	 *
	 * This is not the same as hashing it like a password, as a malicious user
	 * could base64_decode any data in these fields to get the original values.
	 *
	 * @since 3.5.0 | 10 JAN 2018 | Created.
	 * @param  string $network 	The host service that provided the token.
	 * @param  string $field	The type of token to fetch. Usually 'access_token'.
	 * @return bool  			True iff updated, else false.
	 *
	 */
	protected function store_data( $network, $field, $data ) {
		$network_key = base64_encode( $network );

		$tokens = $this->get_authorizations();

		if ( empty( $tokens[$network_key] ) ) {
			$tokens[$network_key] = array();
		}

		$tokens[$network_key][$field] = base64_encode( $data );

		return $this->update_authorizations( $tokens );
	}


	/**
	 * Fetches and prepares options for use by $this.
	 *
	 * The encoding is not secure, but it obfuscates the data. This makes it
	 * more difficult for malicious to access sensitive information.
	 *
	 * @since 3.5.0 | 10 JAN 2018 | Created.
	 * @access private	Any other function that wants this data needs to use other helper methods.
	 * @param  array $authorizations	The data to store.
	 * @return array  					The authorizations, or an empty array.
	 *
	 */
	private function get_authorizations() {
		$encoded_tokens = get_option( 'swp_authorizations', array() );
		if ( empty( $encoded_tokens ) ) {
			return array();
		}

		return json_decode( base64_decode( $encoded_tokens ), true );
	}


	/**
	 * Encodes and stores the options in the database.
	 *
	 * The encoding is not secure, but it obfuscates the data. This makes it
	 * more difficult for malicious to access sensitive information.
	 *
	 * @since 3.5.0 | 10 JAN 2018 | Created.
	 * @access private	Any other function that wants this data needs to use other helper methods.
	 * @param  array $authorizations	The data to store.
	 * @return bool  					True iff the options were successfully updated.
	 *
	 */
	private function update_authorizations( $raw_tokens ) {
		if ( !is_array( $raw_tokens ) ) {
			error_log( 'SWP_Credential_Helper->update_options() requires parameter 1 to be an array.' );
			return false;
		}

		$encoded_tokens = base64_encode( json_encode( $raw_tokens ) );
		return update_option( 'swp_authorizations', $encoded_tokens );
	}
}
