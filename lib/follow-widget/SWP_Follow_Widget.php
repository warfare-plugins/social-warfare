<?php

class SWP_Follow_Widget {


	public function __construct() {
		
	}
}


add_action('in_admin_footer','add_facebook_script');



/**
 * Initialize the plugin.
 *
 * This function will run on the plugins_loaded hook after Social Warfare Core
 * has already been loading making it's classes available to us here.
 *
 * We will also check for what version core is on, as well as doing checks for
 * existence for essential classes that we need to extend.
 *
 * If an asset is missing, we gracefully fail by not loading these files and
 * instead and providing a notice to the user that they need to install
 * core, or update the outdated parent plugin.
 *
 * @since  3.0.0 | 01 MAR 2018 | Created
 * @hook  action plugins_loaded Origin in WordPress Core.
 * @param  void
 * @return void
 *
 */
function initialize_social_follow_widget() {


	/**
	 * Social Warfare (Core) is missing.
	 *
	 * If core is not loaded, we leave the plugin active, but we do not proceed
	 * to load up, activate, or instantiate any of pro's features. Instead we
	 * simply activate a dashboard notification to let the user know that they
	 * need to activate core.
	 *
	 */
	if ( !defined( 'SWP_VERSION' ) ) :
		add_action( 'admin_notices', 'swp_needs_core' );
		return;
	endif;


	/**
	 * The Social_Warfare_Addon class does not exist.
	 *
	 * If for some reason core failed to load the Addon class that this plugin
	 * will be extending, we will attempt to load that class here. This is the
	 * class in core that Social_Warfare_Pro will be extending.
	 *
	 */
	$addon_path = SWP_PLUGIN_DIR . '/lib/Social_Warfare_Addon.php';
	if ( !class_exists( 'Social_Warfare_Addon' ) && file_exists( $addon_path ) ) :
		require_once( $addon_path );
	endif;

	/**
	 * Version Compatibility
	 *
	 * If core is available, and it's on 3.3.0 or higher, we'll go ahead and
	 * load and instantiate the Social Warfare Pro class to fire up the plugin.
	 *
	 */

	if ( class_exists( 'Social_Warfare_Addon' ) && version_compare( SWP_VERSION , '3.5.0' ) <= 0 ) {

		$addon_path = SWFW_PLUGIN_DIR . '/lib/Social_Warfare_Follow_Widget.php';
		if ( file_exists( $addon_path ) ) {
			require_once $addon_path;
			new Social_Warfare_Follow_Widget();
		}
	}

	/**
	 * If core is simply too far out of date, we will create a dashboard notice
	 * to inform the user that they need to update core to the appropriate
	 * version in order to get access to pro.
	 *
	 */
	else {
		add_filter( 'swp_admin_notices', 'swp_addon_update_notification' );
	}


	/**
	 * The plugin update checker
	 *
	 * This is the class for the plugin update checker. It is not dependent on
	 * a certain version of core existing. Instead, it simply checks if the class
	 * exists, and if so, it uses it to check for updates from GitHub.
	 *
	 */
	if ( class_exists( 'Puc_v4_Factory') ) :
		$update_checker = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/warfare-plugins/social-follow-widget',
			__FILE__,
			'social-follow-widget'
		);
		$update_checker->getVcsApi()->enableReleaseAssets();
	endif;
}


/**
 * Notificiation that Social Warfare (core) is needed.
 *
 * This is the dashboard notification that will alert users that in order to
 * use the features of this plugin, they will need to have the core plugin
 * installed and activated.
 *
 * @since  2.2.0 | Unknown | Created
 * @param  void
 * @return void
 *
 */
if ( !function_exists( 'swp_needs_core' ) ) :
	function swp_needs_core() {
		echo '<div class="update-nag notice is-dismissable"><p><b>Important:</b> You currently have Social Warfare - Pro installed without our Core plugin installed.<br/>Please download the free core version of our plugin from the <a href="https://wordpress.org/plugins/social-warfare/" target="_blank">WordPress plugins repository</a>.</p></div>';
	}
endif;


/**
 * Notify users that the versions of Social Warfare and Social Warfare Pro are
 * are currently on incompatible versions with each other.
 *
 * @since  2.2.0 | Unknown | Created
 * @param  array $notices An array of notices to which we add our notice.
 * @return void
 *
 */
 function swp_addon_update_notification( $notices = array() ) {
	 if ( is_string( $notices ) ) {
		 $notices = array();
	 }

	 $notices[] = array(
		 'key'   => 'update_notice_follow_widget_' . SWFW_VERSION, // database key unique to this version.
		 'message'   => 'Looks like your copy of Social Warfare - Follow Widget isn\'t up to date with Core. While you can still use both of these plugins, we highly recommend you keep Warfare Plugins products up-to-date for the best of what we have to offer.',
		 'ctas'  => array(
			 array(
				 'action'    => 'Remind me in a week.',
				 'timeframe' => 7 // dismiss for one week.
			 ),
			 array(
				 'action'    => 'Thanks for letting me know.',
				 'timeframe' => 0 // permadismiss for this version.
			 )
		 )
	 );

	 return $notices;
}


function add_facebook_script() {
	?>
	<script>
	  window.fbAsyncInit = function() {
		FB.init({
		  appId      : '368445043720990', // "Cortland's First App"
		  cookie     : true,
		  xfbml      : true,
		  version    : 'v3.2'
		});

		FB.AppEvents.logPageView();

	  };

	  (function(d, s, id){
		 var js, fjs = d.getElementsByTagName(s)[0];
		 if (d.getElementById(id)) {return;}
		 js = d.createElement(s); js.id = id;
		 js.src = "https://connect.facebook.net/en_US/sdk.js";
		 fjs.parentNode.insertBefore(js, fjs);
	   }(document, 'script', 'facebook-jssdk'));
	</script>
	<?php
}
