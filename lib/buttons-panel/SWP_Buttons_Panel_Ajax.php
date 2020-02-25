<?php

class SWP_Buttons_Panel_Ajax {


	public function __construct() {

		// Register our method to fire when the hook is called.
		add_action('wp_ajax_swp_buttons_panel', array($this, 'build_buttons_panels') );
		add_action('wp_ajax_nopriv_swp_buttons_panel', array($this, 'build_buttons_panels') );
	}


	public function build_buttons_panels() {
		$this->establish_available_buttons();
		$this->generate_buttons_panel_html();
		$this->generate_lightbox_container();

		wp_die();
	}

	private function establish_available_buttons() {
		global $swp_social_networks;
		$networks = $swp_social_networks;

		// Sort the networks by their names.
		usort($networks, function($a,$b) {
			return strcmp($a->name, $b->name);
		});

		$group = 0;
		$i = 0;
		foreach( $networks as $network ) {

			if( 'more' === $network->key ) {
				continue;
			}

			$sorted_networks[$group][] = $network;

			$i++;
			if( $i % 4 === 0 ) {
				$group++;
			}
		}

		// Store the networks in a local property for later use.
		$this->network_groups = $sorted_networks;
	}

	private function generate_buttons_panel_html() {
		$buttons = '';

		foreach( $this->network_groups as $network_group ) {
			$networks = '';
			foreach( $network_group as $network ) {
				$networks .= ',' . $network->key;
			}

			$args = array(
				'id' => $_POST['post_id'],
				'buttons' => $networks,
				'button_size' => 1.4,
				'network_shares' => false
			);

			$buttons_panel = new SWP_Buttons_Panel( $args, true );
			$buttons .= $buttons_panel->render_html();
		}


		$this->buttons_html = $buttons;
	}

	private function generate_lightbox_container() {
		$html = '<div class="swp-lightbox-wrapper"><div class="swp-lightbox-inner">';
		$html .= '<div class="swp-lightbox-close"></div>';
		$html .= '<h1>Additonal Sharing Options</h1>';
		$html .= $this->buttons_html;
		$html .= '</div></div>';
		echo $html;
	}
}
