<?php 

/*****************************************************************
                                                                
          CHECK FOR PREMIUM ACTIVATION
                                                                
******************************************************************/
	function is_sw_registered() {
		
		// Fetch the User's Options Array
		$sw_user_options = get_option('socialWarfareOptions');
		
		// Create a Registration Code from the Domain Name
		$regCode = md5(get_home_url());
		
		// If the Premium Code is currently set....
		if(isset($sw_user_options['premiumCode']) && md5($regCode) == $sw_user_options['premiumCode']):
			
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
	function sw_admin_notice() {
		if(!is_sw_registered()):
			echo '<div class="notice is-dismissable sw_register_admin_notice"><p>Your copy of Social Warfare is not registered. Navigate to the <a href="/wp-admin/admin.php?page=social-warfare"><b>Social Warfare Settings Page</b></a> and select the "Register" tab to register now! You can view and manage your purchased licences on the <a target="_blank" href="https://warfareplugins.com/my-account/">My Account</a> page of the Warfare Plugins website. If you have any issues, please contact us and we\'ll be happy to help.</p></div>';
		endif;
	}
	// add_action( 'admin_notices', 'sw_admin_notice' );
	
/*****************************************************************
                                                                
    CHECK WARFARE PLUGINS SERVER
                                                                
******************************************************************/

	// A function to check if the site is registered at our server
	function sw_check_registration_status() {
	
		
		// Fetch the User's Options Array
		$sw_user_options = sw_get_user_options();
		
		// Fetch URL of the home page
		$homeURL = get_home_url();
			
		// Create a Registration Code from the Domain Name
		$regCode = md5($homeURL);	

		// IF the plugin thinks that it is already registered....
		if(is_sw_registered()):
			
			// Construct the request URL
			$url = 'https://warfareplugins.com/registration-api/?activity=check_registration&emailAddress='.$sw_user_options['emailAddress'].'&domain='.$homeURL.'&registrationCode='.md5($homeURL);
			
			// Send the link and load the response
			$response = sw_file_get_contents_curl($url);
			
			// If the response is negative, unregister the plugin....
			if($response == 'false'):
			
				// Set the premium code to null
				$sw_user_options['premiumCode'] = '';
				
				// Update the options array with the premium code nulled
				update_option('socialWarfareOptions',$sw_user_options);
			
			endif;
		
		// If the codes didn't match, but a premium code does exist
		elseif(isset($sw_user_options['premiumCode'])):
				
			// Attemp to unregister this from the Warfare Plugins Server
			$url = 'https://warfareplugins.com/registration-api/?activity=unregister&emailAddress='.$sw_user_options['emailAddress'].'&premiumCode='.$sw_user_options['premiumCode'];
			
			// Parse the response
			$response = sw_file_get_contents_curl($url);
			$response = json_decode($response,true);
			
			// If it unregistered, let's try to auto-reregister it....
			if($response['status'] == 'Success'):
			
				// Attempt to reregister it
				$url = 'https://warfareplugins.com/registration-api/?activity=register&emailAddress='.$sw_user_options['emailAddress'].'&domain='.get_home_url().'&registrationCode='.$regCode;
				
				// Parse the response
				$response = sw_file_get_contents_curl($url);
				$response = json_decode($response,true);

				// IF the registration attempt was successful....
				if($response['status'] == 'Success'):
				
					// Save our updated options
					$sw_user_options['premiumCode'] == $response['premiumCode'];
					
					// Update the options storing in our new updated Premium Code
					update_option('socialWarfareOptions',$sw_user_options);
					
					return true;
				
				// IF the registration attempt was NOT successful
				else:
				
					// Set the premium code to null
					$sw_user_options['premiumCode'] = '';
					
					// Update the options array with the premium code nulled
					update_option('socialWarfareOptions',$sw_user_options);
				
					return false;
					
				endif;				
		
			// IF it wasn't able to unregister
			else:
			
				// Set the premium code to null
				$sw_user_options['premiumCode'] = '';
				
				// Update the options array with the premium code nulled
				update_option('socialWarfareOptions',$sw_user_options);
			
				return false; 
				
			endif;
			
		endif;
	}
	
	
	
	
	
	
	