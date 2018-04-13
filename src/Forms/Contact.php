<?php

namespace Svbk\WP\Shortcakes\Forms;

class Contact extends Subscribe {

	public $shortcode_id = 'svbk-contact-form';
	public $formClass = '\Svbk\WP\Forms\Contact';
	public $classes = array( 'form-contact' );

	public function title() {
		return __( 'Contact Form', 'svbk-shortcakes' );
	}

}
