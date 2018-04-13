<?php

namespace Svbk\WP\Shortcakes\Forms;

class Subscribe extends Form {

	public $shortcode_id = 'svbk_subscribe_form';
	public $formClass = '\Svbk\WP\Forms\Subscribe';
	public $classes = array( 'form-subscribe' );

	public function title() {
		return __( 'Subscribe Form', 'svbk-shortcakes' );
	}

	public function confirmMessage() {
		return $this->confirmMessage ?: __( 'Thanks for your request, we will reply as soon as possible.', 'svbk-shortcakes' );
	}

}
