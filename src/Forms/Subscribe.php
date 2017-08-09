<?php

namespace Svbk\WP\Shortcakes\Forms;

class Subscribe extends Form {

	public $mc_apikey = '';
	public $mc_list_id = '';
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

			$form->mc_apikey = $this->mc_apikey;
			$form->mc_list_id = $this->mc_list_id;

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
