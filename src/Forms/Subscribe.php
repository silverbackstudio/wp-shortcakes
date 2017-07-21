<?php

namespace Svbk\WP\Shortcakes\Forms;

class Subscribe extends Form {

	public $md_apikey = '';
	public $md_template = '';
	public $messageDefaults;

	public $shortcode_id = 'svbk_subscribe_form';
	public $field_prefix = 'scf';
	public $action = 'svbk_subscribe_form';
	public $formClass = '\Svbk\WP\Helpers\Form\Subscribe';
	public $classes = array( 'form-subscribe' );

	public function title() {
		return __( 'Subscribe Form', 'svbk-shortcakes' );
	}

	public function confirmMessage() {
		return $this->confirmMessage ?: __( 'Thanks for your request, we will reply as soon as possible.', 'svbk-shortcakes' );
	}

	protected function getForm( $set_send_params = false ) {

		$form = parent::getForm( $set_send_params );

		if ( $set_send_params ) {

				$form->md_apikey = $this->md_apikey;
			$form->templateName = $this->md_template;

			if ( ! empty( $this->messageDefaults ) ) {
				$form->messageDefaults = array_merge(
				$form->messageDefaults,
				$this->messageDefaults
				);
			}
		}

		return $form;
	}

}
