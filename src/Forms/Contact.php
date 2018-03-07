<?php

namespace Svbk\WP\Shortcakes\Forms;

class Contact extends Subscribe {

	public $shortcode_id = 'svbk-contact-form';
	public $field_prefix = 'scf';
	public $action = 'svbk_contact_form';
	public $formClass = '\Svbk\WP\Helpers\Form\Contact';
	public $classes = array( 'form-contact' );

	public function title() {
		return __( 'Contact Form', 'svbk-shortcakes' );
	}

}
