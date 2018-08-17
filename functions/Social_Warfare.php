<?php

/**
 * A class of functions used to load the plugin files and functions
 *
 * This is the class that brings the entire plugin to life. It is used to
 * instatiate all other classes throughout the plugin.
 *
 * This class also serves as a table of contents for all of the plugin's
 * functionality. By browsing below, you will see a brief description of each
 * class that is being instantiated.
 *
 * @package   SocialWarfare\Utilities
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     3.0.0  | 19 FEB 2018 | Created
 * @since     3.1.0 | 20 JUN 2018 | Added instantiate_frontend_classes()
 *
 */
class Social_Warfare {


	/**
	 * The magic method used to instantiate this class.
	 *
	 * This method will load all of the classes using the "require_once" command.
	 * It will then instantiate them all one by one.
	 *
	 * @since  3.0.0  | 19 FEB 2018 | Created
	 * @since  3.1.0 | 20 JUN 2018 | Added instantiate_frontend_classes()
	 * @param  void
	 * @return void
	 * @access public
	 *
	 */
	public function __construct() {

		// Block these classes from being loaded again if a child class attempts
		// to run the parent::__construct() method.
        if ( get_class($this) === 'Social_Warfare' ) {

			// Loads the files for each class.
            $this->load_classes();

			// Instantiate all the core classes
            $this->instantiate_classes();

			// Instantiate the admin-only classes.
            if( true === is_admin() ) {
                $this->instantiate_admin_classes();
            }

			// Instantiate the frontend-only classes.
			if( false === is_admin() ) {
				$this->instantiate_frontend_classes();
			}

        }

        $this->core_version = SWP_VERSION;
	}


	/**
	 * The method used to instantiate all classes used on both frontend and admin.
	 *
	 * This method will instantiate every class throughout the plugin except for
	 * those classes that are used in both the frontend and the admin area.
	 *
	 * @since  3.0.0
	 * @param  void
	 * @return void
	 * @access public
	 *
	 */
	private function instantiate_classes() {
        global $SWP_Options_Page;


		/**
		 * The Social Networks Loader
		 *
		 * Instantiates the class that will load the social networks.
		 *
		 */
		new SWP_Social_Networks_Loader();


		/**
		 * The Localization Class
		 *
		 * Instantiates the class that will load the plugin translations.
		 *
		 */
		$Localization = new SWP_Localization();
        $Localization->init();


		/**
		 * The URL_Management Class
		 *
		 * This is the class that controls short links and UTM parameters.
		 *
		 */
		new SWP_URL_Management();


		/**
		 * The Script Class
		 *
		 * Instantiates the class that will enqueue all of the styles and
		 * scripts used throughout the plugin both frontend, and admin.
		 *
		 */
		new SWP_Script();


		/**
		 * The Shortcode Class
		 *
		 * Instantiate the class that will process all instances of the
		 * [social_warfare] shortcode used in posts and pages, and consequently
		 * convert those shortcodes into sets of share buttons.
		 *
		 */
		new SWP_Shortcode();


		/**
		 * The Header Output Class
		 *
		 * Instantiate the class that processes the values and creates the HTML
		 * output required in the <head> section of a website. This includes our
		 * font css, open graph meta tags, and Twitter cards.
		 *
		 */
		new SWP_Header_Output();


		/**
		 * The Display Class
		 *
		 * Instantiates the class that is used to queue up or hook the buttons
		 * generator into WordPress' the_content() hook which allows us to
		 * append our buttons to it.
		 *
		 */
		new SWP_Display();


		/**
		 * The Compatibility Class
		 *
		 * Instantiate the class that provides solutions to very specific
		 * incompatibilities with certain other plugins.
		 *
		 */
		new SWP_Compatibility();


		/**
		 * The Widget Class
		 *
		 * Instantiate the class that registers and output the "Popular Posts"
		 * widget. If other widgets are added later, this class will fire those
		 * up as well.
		 *
		 */
		new SWP_Widget();


        /**
         * Database Migration
         *
         * Converts camelCased variable names to the new snake_case option names.
         *
         */
        new SWP_Database_Migration();


        /**
         * The Options Page Class
         *
         * Instantiates the class that will load the plugin options page.
         *
         */
        $SWP_Options_Page = new SWP_Options_Page();


		/**
		 * The Post Cache Loader Class
		 *
		 * Instantiates a global object that will manage and load cached data
		 * for each individual post on a site allowing access to cached data like
		 * share counts, for example.
		 *
		 */
		global $SWP_Post_Caches;
		$SWP_Post_Caches = new SWP_Post_Cache_Loader();

	}


	/**
	 * This method will load up all of the frontend-only classes.
	 *
	 * @since  3.1.0 | 20 JUNE 2018 | Created
	 * @param  void
	 * @return void
	 * @access private
	 */
	private function instantiate_frontend_classes() {


	}

	/**
	 * This method will load up all of the admin-only classes.
	 *
	 * @since  3.0.0
	 * @param  void
	 * @return void
	 * @access public
	 *
	 */
	private function instantiate_admin_classes() {


		/**
		 * The Shortcode Generator
		 *
		 * Instantiate the class that creates the shortcode generator on the
		 * post editor which allows users to generate the [social_warfare]
		 * shortcodes by simply pointing clicking, and filling in a few fill in
		 * the blanks.
		 *
		 */
		new SWP_Shortcode_Generator();


		/**
		 * The Click to Tweet Class
		 *
		 * Instantiate the class that that creates the Click to Tweet button in
		 * the WordPress post editor's dashboard (the kitchen sink) and also
		 * process the shortcode on the front end.
		 *
		 */
		new SWP_Click_To_Tweet();


		/**
		 * The "Social Shares" column in the posts view.
		 *
		 * Instantiate the class that creates the column in the posts view of
		 * the WordPress admin area. This column allows you to see how many
		 * times each post has been shared. It also allows you to sort the
		 * column in ascending or descending order.
		 *
		 */
		new SWP_Column();


		/**
		 * The The Settings Link
		 *
		 * Instantiates the class that addes links to the plugin listing on the
		 * plugins page of the WordPress admin area. This will link to the
		 * Social Warfare options page.
		 *
		 */
		new SWP_Settings_Link();


		/**
		 * The User Profile Fields
		 *
		 * Instantiates the class that adds our custom fields to the user
		 * profile area of the WordPress backend. This allows users to set a
		 * Twitter username and Facebook author URL on a per-user basis. If set,
		 * this will override these same settings from the options page on any
		 * posts authored by that user.
		 *
		 */
		new SWP_User_Profile();

        new SWP_JSON_Cache_Handler();

        add_action('plugins_loaded', function() {
            /**
             * Instantiates all of our notices.
             *
             */
            new SWP_Notice_Loader();
        }, 50);

        require_once SWP_PLUGIN_DIR . '/functions/utilities/SWP_Plugin_Updater.php';
	}


	/**
	 * The method is used to include all of the files needed.
	 *
	 * @since  3.0.0
	 * @param  none
	 * @return none
	 * @access public
	 *
	 */
	private function load_classes() {

		// Require WordPress' core plugin class.
		require_once ABSPATH . 'wp-admin/includes/plugin.php';


		/**
		 * Utility Classes
		 *
		 * This loads our Utility Trait and our various classes used to provide general
		 * functionality that will be used by many different classes throughout the plugin.
		 *
		 */
		$utilities = array(
			'Utility_Trait',
            'Abstract',
            'Compatibility',
            'CURL',
            'Localization',
            'Permalink',
            'Database_Migration',
			'URL_Management',
            'Notice',
            'Notice_Loader',
            'Post_Cache_Loader',
            'Post_Cache',
            'JSON_Cache_Handler'
        );
        $this->load_files( '/functions/utilities/', $utilities);


		/**
		 * The Social Network Classes
		 *
		 * This family of classes provides the framework and the model needed
		 * for creating a unique object for each social network. It also
		 * provides for maximum extensibility to allow addons even easier access
		 * than ever before to create and add more social networks to the plugin.
		 *
		 */
		$social_networks = array(
			'Social_Networks_Loader',
			'Social_Network',
			'Google_Plus',
			'Facebook',
			'Twitter',
            'Linkedin',
            'Pinterest',
            'Stumble_Upon'
		);
		$this->load_files( '/functions/social-networks/', $social_networks);


        /**
         * The Addon Classes
         *
         * This family of classes provide for the management of addons. These
         * control the framework for registering addons, checking for updates
         * for the addons, and other addon specific tasks.
         *
         */
        $includes = array(
            'Addon'
        );
        $this->load_files( '/functions/includes/', $includes);


		/**
		 * The Frontend Output Classes
		 *
		 * This family of classes control everything that is output on the
		 * WordPress frontend. This includes the HTML for the buttons panels,
		 * the meta data that is output in the head section of the site, scripts
		 * and styles being enqueued for output, and other things like that.
		 *
		 */
        $frontends = array(
            'Buttons_Panel',
            'Header_Output',
            'Display',
            'Script',
            'Shortcode',
        );
        $this->load_files( '/functions/frontend-output/', $frontends );


		/**
		 * The Widget Classes
		 *
		 * These are the classes that create the widgets available for output in
		 * WordPress. Built in is the Popular Posts widget, but these also
		 * provide the framework for extensibility so that more widgets can be
		 * created later via addons.
		 *
		 */
		$widgets = array(
            'Popular_Posts_Widget',
            'Widget',
        );
        $this->load_files( '/functions/widgets/', $widgets );


		/**
		 * The Admin Classes
		 *
		 * This family of classes power everything that you see in the WordPress
		 * admin area of the site. This includes the Click To Tweet generator
		 * and Social Warfare shortcode generator buttons that you see at the
		 * top of the post editor. These include adding the share count column
		 * to the posts view and a few other things related to the admin area.
		 * This does NOT include the classes used to generate the options page
		 * for Social Warfare.
		 *
		 */
        $admins = array(
            'Click_To_Tweet',
            'Column',
            'Settings_Link',
            'Shortcode_Generator',
            'User_Profile',
        );
        $this->load_files( '/functions/admin/', $admins );


		/**
		 * The Options Classes
		 *
		 * These classes provide the framework that creates the admin options
		 * page as well as the tools needed for addons to be able to interface
		 * with it to add their own options.
		 *
		 */
        $options = array(
            'Option',
            'Options_Page',
            'Options_Page_Tab',
            'Options_Page_Section',
            'Option_Toggle',
            'Option_Select',
            'Option_Text',
            'Option_Textarea',
            'Section_HTML',
            'Option_Icons',
			'Addon_Registration',
        );
        $this->load_files( '/functions/options/', $options );


		/**
		 * The Update Checker
		 *
		 * This loads the class which will in turn load all other class that are
		 * needed in order to properly check for updates for addons.
		 *
		 */
		require_once SWP_PLUGIN_DIR . '/functions/plugin-update-checker/plugin-update-checker.php';


	}


    /**
     * Loads an array of related files.
     *
     * @param  string   $path  The relative path to the files home.
     * @param  array    $files The name of the files (classes), no vendor prefix.
     * @return none     The files are loaded into memory.
     *
     */
    private function load_files( $path, $files ) {
        foreach( $files as $file ) {

            //* Add our vendor prefix to the file name.
            $file = "SWP_" . $file;
            require_once SWP_PLUGIN_DIR . $path . $file . '.php';
        }
    }
}


/*******************************************************************************
 *
 *
 * WARNING! WARNING! WARNING! WARNING! WARNING! WARNING! WARNING! WARNING!
 *
 * EVERY FILE BELOW THIS POINT NEEDS TO BE REFACTORED. IT'S "REQUIRE_ONCE" THEN
 * NEEDS TO BE MIGRATED INTO THE CLASS ABOVE.
 *
 *
 * *****************************************************************************/


// TODO: These files need refactored into classes and to the appropriate sections above.
require_once SWP_PLUGIN_DIR . '/functions/admin/registration.php';
require_once SWP_PLUGIN_DIR . '/functions/admin/options-fetch.php';
require_once SWP_PLUGIN_DIR . '/functions/utilities/utility.php';

/**
 * Include the plugin's admin files.
 *
 */
if ( is_admin() ) {
	require_once SWP_PLUGIN_DIR . '/functions/admin/swp_system_checker.php';
	// require_once SWP_PLUGIN_DIR . '/functions/admin/options-page.php';
}
