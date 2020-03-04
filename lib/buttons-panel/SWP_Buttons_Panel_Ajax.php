<?php

/**
 * SWP_Buttons_Panel_Ajax
 *
 * This class will register the action needed to allow calls to admin ajax that
 * will fetch the buttons panels containing all of the available buttons. This
 * will be used for creating the lightbox popup that will be populated with all
 * sharing buttons and will be activated when the "More" button is clicked on.
 *
 * @since  4.0.0 | 25 FEB 2020 | Created
 *
 */
class SWP_Buttons_Panel_Ajax {


	/**
	 * The Magic Constructor
	 *
	 * This will isntantiate the class and do nothing more than register our
	 * method as a callback handler so that it will intercept and handle all of
	 * the calls to admin-ajax.php while using the swp_buttons_panel action.
	 *
	 * @since  4.0.0 | 25 FEB 2020 | Created
	 * @param  void
	 * @return void
	 *
	 */
	public function __construct() {

		// Register our method to fire when the hook is called.
		add_action('wp_ajax_swp_buttons_panel', array($this, 'build_buttons_panels') );
		add_action('wp_ajax_nopriv_swp_buttons_panel', array($this, 'build_buttons_panels') );
	}


	/**
	 * The build_buttons_panels() method is a declarative method that simply
	 * walks through all of the helper methods that build out the html for the
	 * buttons panels that will be generated into html.
	 *
	 * Note: This must be public to be used as a hook callback.
	 *
	 * @since  4.0.0 | 25 FEB 2020 | Created
	 * @access public
	 * @param  void
	 * @return void
	 *
	 */
	public function build_buttons_panels() {

		// If the post_id is invalid, just bail out immediately.
		if( empty( $_POST['post_id']) || false === is_numeric( $_POST['post_id'] ) ) {
			wp_die();
			return;
		}

		$this->establish_available_buttons();
		$this->generate_buttons_panel_html();
		$this->generate_lightbox_container();

		// This must be included to properly close out a call to admin-ajax.php.
		wp_die();
	}


	/**
	 * The establish_available_buttons() method will use the global array
	 * containing every registered social network in the plugin. It will divide
	 * it into arrays containing 4 networks each. These will be used to generate
	 * the buttons panel for each row.
	 *
	 * @since  4.0.0 | 25 FEB 2020 | Created
	 * @param  void
	 * @return void
	 *
	 */
	private function establish_available_buttons() {

		// Pull the global array of networks into a local variable.
		global $swp_social_networks;
		$networks = $swp_social_networks;

		// Sort the networks by their names.
		usort($networks, function($a,$b) {
			return strcmp($a->name, $b->name);
		});

		// Loope through and sort them into stacks of 4 networks.
		$group = 0;
		$i = 0;
		foreach( $networks as $network ) {

			// Skip the "More" button since we're already inside it's options.
			if( 'more' === $network->key ) {
				continue;
			}

			$sorted_networks[$group][] = $network;

			// Iterate our counters.
			$i++;
			if( $i % 4 === 0 ) {
				$group++;
			}
		}

		// Store the networks in a local property for later use.
		$this->network_groups = $sorted_networks;
	}


	/**
	 * The generate_buttons_panel_html() will use the array of available networks,
	 * loop through the networks, and instantiate buttons_panel objects, and use
	 * their render_html() methods to create the html for each panel.
	 *
	 * @since  4.0.0 | 25 FEB 2020 | Created
	 * @param  void
	 * @return void
	 *
	 */
	private function generate_buttons_panel_html() {
		$buttons = '';

		// Loop through each network group containing 4 networks. Each group will
		// be a row of buttons.
		foreach( $this->network_groups as $network_group ) {

			// Loop through each network within this group and add it to our args list.
			$networks = '';
			foreach( $network_group as $network ) {
				$networks .= ',' . $network->key;
			}

			// Set up the $args to be passed to the SWP_Buttons_Panel class.
			$args = array(
				'id' => $_POST['post_id'],
				'buttons' => $networks,
				'button_size' => 1.4,
				'network_shares' => false
			);

			// Create the Buttons panel and render its HTML.
			$buttons_panel = new SWP_Buttons_Panel( $args, true );
			$buttons .= $buttons_panel->render_html();
		}

		// Store all of the buttons html into a local property.
		$this->buttons_html = $buttons;
	}


	/**
	 *
	 * The generate_lightbox_container() will take all of the generated html and
	 * compile it into its complete and final form ready to send to the screen.
	 *
	 * @since  4.0.0 | 25 FEB 2020 | Created
	 * @param  void
	 * @return void Generated html is echoed directly to the screen.
	 *
	 */
	private function generate_lightbox_container() {
		$html = '<div class="swp-lightbox-wrapper swp-more-wrapper"><div class="swp-lightbox-inner">';
		$html .= '<i class="sw swp_share_icon"></i>';
		$html .= '<div class="swp-lightbox-close"></div>';
		$html .= '<h5>Where would you like to share this?</h5>';
		$html .= $this->buttons_html;

		if( true === SWP_Utility::get_option('powered_by_toggle')) {

			$affiliate_link = SWP_Utility::get_option('affiliate_link');
			if( false === $affiliate_link || empty( $affiliate_link ) || '#' === $affiliate_link ) {
				$affiliate_link = 'https://warfareplugins.com';
			}

			$html .= '<div class="swp_powered_by"><a href="'. $affiliate_link .'" target="_blank"><span>Powered by</span> <img src="/wp-content/plugins/social-warfare/assets/images/admin-options-page/social-warfare-pro-light.png"></a></div>';
		}

		$html .= '</div></div>';
		echo $html;
	}
}
