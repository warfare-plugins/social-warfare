<?php
/**
 * Functions for loading the admin options page.
 *
 * @package   SocialWarfare\Admin\Functions
 * @copyright Copyright (c) 2016, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

$premium_code = '';
$email = '';

if ( ! empty( $swp_user_options['emailAddress'] ) ) {
	$email = $swp_user_options['emailAddress'];
}
if ( ! empty( $swp_user_options['premiumCode'] ) ) {
	$premium_code = $swp_user_options['premiumCode'];
}
?>

<div class="registration-wrapper" registration="<?php echo absint( is_swp_registered() ); ?>">

	<h2><?php esc_html_e( 'Premium Registration' , 'social-warfare' ); ?></h2>

	<div class="sw-grid sw-col-940 swp_is_not_registered">

		<div class="sw-red-notice">
			<?php _e( 'This copy of Social Warfare is NOT registered. <a target="_blank" href="https://warfareplugins.com">Click here</a> to purchase a license or add your account info below.' , 'social-warfare' ); ?>
		</div>

		<p class="sw-subtitle sw-registration-text">
			<?php esc_html_e( 'Follow these simple steps to register your Premium License and access all features.' , 'social-warfare' ); ?>
		</p>

		<p class="sw-subtitle sw-registration-text sw-italic">
			<?php esc_html_e( 'Step 1: Enter your email.' , 'social-warfare' ); ?><br />
			<?php esc_html_e( 'Step 2: Click the "Register Plugin" button.' , 'social-warfare' ); ?><br />
			<?php esc_html_e( 'Step 3: Watch the magic.' , 'social-warfare' ); ?>
		</p>

		<div class="sw-grid sw-col-300">
			<p class="sw-input-label">
				<?php esc_html_e( 'Email Address' , 'social-warfare' ); ?>
			</p>
		</div>

		<div class="sw-grid sw-col-300">
			<input name="emailAddress" type="text" class="sw-admin-input" placeholder="email@domain.com" value="<?php echo $email; ?>" />
		</div>

		<input name="premiumCode" type="text" class="sw-admin-input sw-hidden" value="<?php echo $premium_code; ?>" />
		<input name="regCode" type="text" class="sw-admin-input sw-hidden" value="<?php echo swp_get_registration_key( swp_get_site_url() ); ?>" />

		<div class="sw-grid sw-col-300 sw-fit"></div>
		<div class="sw-clearfix"></div>

		<div class="sw-grid sw-col-300">
			<p class="sw-authenticate-label">
				<?php esc_html_e( 'Activate Registration' , 'social-warfare' ); ?>
			</p>
		</div>

		<div class="sw-grid sw-col-300">
			<a href="#" id="register-plugin" class="button sw-navy-button">
				<?php esc_html_e( 'Register Plugin' , 'social-warfare' ); ?>
			</a>
		</div>

		<div class="sw-grid sw-col-300 sw-fit"></div>

	</div>

	<div class="sw-grid sw-col-940 swp_is_registered">

		<div class="sw-green-notice">
			<?php esc_html_e( 'This copy of Social Warfare is registered. Wah-hoo!', 'social-warfare' ); ?>
		</div>

		<p class="sw-subtitle sw-registration-text">
			<?php esc_html_e( 'To unregister your license click the button below to free it up for use on another domain.' , 'social-warfare' ); ?>
		</p>

		<div class="sw-grid sw-col-300">
			<p class="sw-authenticate-label">
				<?php esc_html_e( 'Deactivate Registration' , 'social-warfare' ); ?>
			</p>
		</div>

		<div class="sw-grid sw-col-300">
			<a href="#" id="unregister-plugin" class="button sw-navy-button">
				<?php esc_html_e( 'Unregister Plugin' , 'social-warfare' ); ?>
			</a>
		</div>
		<div class="sw-grid sw-col-300 sw-fit"></div>

	</div>

</div>
