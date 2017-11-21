<?php

namespace Svbk\WP\Shortcakes\Forms;

class Contact extends Subscribe {

	public $md_template = '';

	public $shortcode_id = 'svbk-contact-form';
	public $field_prefix = 'scf';
	public $action = 'svbk_contact_form';
	public $formClass = '\Svbk\WP\Helpers\Form\Contact';
	public $classes = array( 'form-contact' );

	public function title() {
		return __( 'Contact Form', 'svbk-shortcakes' );
	}

	protected function getForm( $set_send_params = false ) {

		$form = parent::getForm( $set_send_params );

		if ( $set_send_params ) {
			$form->templateName = $this->md_template;
		}

		return $form;
	}

}
