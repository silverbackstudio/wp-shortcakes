<?php

namespace Svbk\WP\Shortcakes\Forms;

use Svbk\WP\Helpers\MailChimp;

class Download extends Form {

	public $defaults = array(
		'title' => '',
		'hidden' => false,
		'privacy_link' => '',
		'file' => '',
		'open_button_label' => 'Open',
		'submit_button_label' => 'Submit',
		'redirect_to' => '',
	);

	public $mc_apikey = '';
	public $mc_list_id = '';
	public $md_apikey = '';
	public $md_template = '';
	public $md_sender_template = '';
	public $messageDefaults = array();
	public $subscribeAttributes = array();

	public $shortcode_id = 'whitepaper_dl';
	public $field_prefix = 'wdl';
	public $action = 'sendwhitepaper';
	public $formClass = '\Svbk\WP\Helpers\Form\Download';
	public $classes = array( 'whitepaper-dl', 'form-download' );

	public function title() {
		return __( 'Whitepaper Download', 'svbk-shortcakes' );
	}

	public function fields() {

		$fields = parent::fields();

		$fields['file'] = array(
			'label'       => __( 'File to Download', 'svbk-shortcakes' ),
			'attr'        => 'file',
			'type'        => 'attachment',
			// 'libraryType' => array( 'pdf' ),
			'multiple'    => false,
			'addButton'   => __( 'Select File', 'svbk-shortcakes' ),
			'frameTitle'  => __( 'Select File', 'svbk-shortcakes' ),
		);

		return $fields;
	}

	public function confirmMessage() {
		return $this->confirmMessage ?: __( 'Thanks for your request, the file you requested will be sent to your inbox.', 'svbk-shortcakes' );
	}

	protected function getForm( $set_send_params = false ) {

		$form = parent::getForm( $set_send_params );

		if ( $set_send_params ) {

			$form->mc_apikey = $this->mc_apikey;
			$form->mc_list_id = $this->mc_list_id;
			$form->md_apikey = $this->md_apikey;

			$form->templateName = $this->md_template;
			$form->senderTemplateName = $this->md_sender_template;
			
			if ( ! empty( $this->messageDefaults ) ) {
				$form->messageDefaults = array_merge(
					$form->messageDefaults,
					$this->messageDefaults
				);
			}

			if ( ! empty( $this->subscribeAttributes ) ) {
				$form->subscribeAttributes = $this->subscribeAttributes;
			}
		}

		return $form;
	}

}
