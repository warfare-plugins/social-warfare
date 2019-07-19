<?php

class SWP_Option_Authentication {
	public function __construct( $properties ) {
		$this->properties = $properties;
	}

	public function render_html() {
		<div class="sw-grid sw-col-940 sw-fit sw-option-container <?= $this->key ?> '_wrapper" data-dep="bitly_authentication" data-dep_val="[true]">
			<div class="sw-grid sw-col-300">
				<p class="sw-authenticate-label"><?php __( 'Bitly Link Shortening', 'social-warfare' ) ?></p>
			</div>
			<div class="sw-grid sw-col-300">
				<a  target="<?= $target ?>" class="button <?= $color ?>" href="<?= $link ?>"><?= $text ?></a>
			</div>
			<div class="sw-grid sw-col-300 sw-fit"></div>
		</div>
	}
}
