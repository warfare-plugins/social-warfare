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
			$sorted_networks[$group][] = $network;
			if( $i % 4 === 0 ) {
				$group++;
			}
			$i++;
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
				'buttons' => $networks
			);

			$buttons_panel = new SWP_Buttons_Panel( $args, true );
			$buttons .= $buttons_panel->render_html();
		}
		
		echo $buttons;
	}
}
