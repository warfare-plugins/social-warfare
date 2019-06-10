<?php
if ( !class_exists( 'Social_Warfare_Addon' ) ) {
	return;
}

/**
 * Loads plugin assets and applies specific plugin data to the generic
 * Social_Warfare_Addon class.
 *
 */
class Social_Warfare_Follow_Widget extends Social_Warfare_Addon {


	/**
	 * Initializes the plugin with required data.
	 *
	 * @since 1.0.0 | 3 DEC 2018 | Created.
	 * @param void
	 * @return void
	 *
	 */
	public function __construct() {
		$this->name          = 'Social Warfare - Follow Widet';
		$this->key           = 'social-follow-widget';
		$this->core_required = '3.5.0';
		$this->product_id    = 253345;
		$this->version       = SWFW_VERSION;
		$this->filepath      = SWFW_PLUGIN_FILE;

		parent::__construct();

		if ($this->is_registered) {
			$this->init();
			add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
		}
	}


	/**
	 * Loads plugin data and initializes classes.
	 *
	 * @since 1.0.0 | 3 DEC 2018 | Created.
	 * @param void
	 * @return void
	 *
	 */
	public function init() {
		global $swfw_networks;

		$swfw_networks = array();
		$files = array(
			'Follow_Network',
			'Follow_Widget',
			'Cache',
			'Utility',

		);

		$this->load_files( '/lib/utilities/', $files );

		$this->init_networks();
		new SWFW_Follow_Widget();
	}


	/**
	 * Loads plugin styles.
	 *
	 * @since 1.0.0 | 3 DEC 2018 | Created.
	 * @param void
	 * @return void
	 *
	 */
	public function load_assets() {
		wp_enqueue_style( 'swfw-style', SWFW_PLUGIN_URL . '/assets/style.css' );

		// Uncomment this if the admin UX js is fixed.
		// if (is_admin() ){
		// 	wp_enqueue_script( 'swfw-script', SWFW_PLUGIN_URL . '/assets/widget.js' );
		// }
	}


	/**
	 * Loads each of the network-specifc SWFW_Network classes.
	 *
	 * @since 1.0.0 | 3 DEC 2018 | Created.
	 * @param void
	 * @return void
	 *
	 */
	public function init_networks() {
		$api_networks = array(
			'Facebook',
			'Pinterest',
			'Twitter',
			'Tumblr',
			'Instagram',
			'Vimeo'
		);

		$basic_networks = array(
			'Reddit',
			'Linkedin',
			'Flickr',
			'Medium',
			'Ello',
			'Blogger',
			'Snapchat',
			'Periscope',
		);

		$networks = array_merge( $api_networks, $basic_networks );

		$this->load_files( '/lib/networks/', $networks, true );
	}


	/**
	 * Loads an array of related files.
	 *
	 * @since 1.0.0 | 3 DEC 2018 | Created.
	 * @param  string   $path  The relative path to the files home.
	 * @param  array    $files The name of the files (classes), no vendor prefix.
	 * @param  bool     $instantiate Whether or not to immediately instantiate the class. Default false.
	 * @return none     The files are loaded into memory.
	 *
	 */
	private function load_files( $path, $files, $instantiate = false ) {
		foreach( $files as $file ) {

			$class = "SWFW_" . $file;
			require_once SWFW_PLUGIN_DIR . $path . $class . '.php';

			if ( $instantiate ) {
				new $class();
			}
		}
	}
}
