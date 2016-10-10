<?php

/*****************************************************************

		  CHECK FOR PREMIUM ACTIVATION

******************************************************************/
	function is_swp_registered() {

		// Fetch the User's Options Array
		$swp_user_options = get_option('socialWarfareOptions');

		if(is_multisite()):
			$domain = network_site_url();
		else:
			$domain = site_url();
		endif;

		$regCode = md5($domain);

		// If the Premium Code is currently set....
		if(isset($swp_user_options['premiumCode']) && md5($regCode) == $swp_user_options['premiumCode']):

			// It's registered
			return true;

		// IF the premium code doesn't match....
		else:

			// It's not registered
			return 0;

		endif;
	}

/*****************************************************************

	 ADMIN NOTICE

******************************************************************/
	function swp_admin_notice() {
		if(!is_swp_registered()):
			echo '<div class="notice is-dismissable swp_register_admin_notice"><p>Your copy of Social Warfare is not registered. Navigate to the <a href="/wp-admin/admin.php?page=social-warfare"><b>Social Warfare Settings Page</b></a> and select the "Register" tab to register now! You can view and manage your purchased licences on the <a target="_blank" href="https://warfareplugins.com/my-account/">My Account</a> page of the Warfare Plugins website. If you have any issues, please contact us and we\'ll be happy to help.</p></div>';
		endif;
	}
	// add_action( 'admin_notices', 'swp_admin_notice' );

/*****************************************************************

	CHECK WARFARE PLUGINS SERVER

******************************************************************/

	if(isset($_GET['reg_check']) && $_GET['reg_check'] == true):
		var_dump(swp_check_registration_status());
	endif;

	// A function to check if the site is registered at our server
	function swp_check_registration_status() {

		// Fetch the User's Options Array
		$swp_user_options = get_option('socialWarfareOptions');

		// Fetch URL of the home page
		if(is_multisite()):
			$homeURL = network_site_url();
		else:
			$homeURL = site_url();
		endif;

		// Create a Registration Code from the Domain Name
		$regCode = md5($homeURL);

		// IF the plugin thinks that it is already registered....
		if(is_swp_registered()):

			// Construct the request URL
			$url = 'https://warfareplugins.com/registration-api/?activity=check_registration&emailAddress='.$swp_user_options['emailAddress'].'&domain='.$homeURL.'&registrationCode='.md5($homeURL);

			// Send the link and load the response
			$response = swp_file_get_contents_curl($url);

			// If the response is negative, unregister the plugin....
			if($response === 'false'):

				// Set the premium code to null
				$swp_user_options['premiumCode'] = '';

				// Update the options array with the premium code nulled
				update_option('socialWarfareOptions',$swp_user_options);

				return false;

			else:

				return true;

			endif;

		// If the codes didn't match, but a premium code does exist
		elseif(isset($swp_user_options['premiumCode'])):

			// Attemp to unregister this from the Warfare Plugins Server
			$url = 'https://warfareplugins.com/registration-api/?activity=unregister&emailAddress='.$swp_user_options['emailAddress'].'&premiumCode='.$swp_user_options['premiumCode'];

			// Parse the response
			$response = swp_file_get_contents_curl($url);
			$response = json_decode($response,true);

			// If it unregistered, let's try to auto-reregister it....
			if($response['status'] == 'Success'):

				// Attempt to reregister it
				$url = 'https://warfareplugins.com/registration-api/?activity=register&emailAddress='.$swp_user_options['emailAddress'].'&domain='.get_home_url().'&registrationCode='.$regCode;

				// Parse the response
				$response = swp_file_get_contents_curl($url);
				$response = json_decode($response,true);

				// IF the registration attempt was successful....
				if($response['status'] == 'Success'):

					// Save our updated options
					$swp_user_options['premiumCode'] == $response['premiumCode'];

					// Update the options storing in our new updated Premium Code
					update_option('socialWarfareOptions',$swp_user_options);

					return true;

				// IF the registration attempt was NOT successful
				else:

					// Set the premium code to null
					$swp_user_options['premiumCode'] = '';

					// Update the options array with the premium code nulled
					update_option('socialWarfareOptions',$swp_user_options);

					return false;

				endif;

			// IF it wasn't able to unregister
			else:

				// Set the premium code to null
				$swp_user_options['premiumCode'] = '';

				// Update the options array with the premium code nulled
				update_option('socialWarfareOptions',$swp_user_options);

				return false;

			endif;

		endif;
	}
