<?php

namespace Svbk\WP\Shortcakes\Forms;

use Svbk\WP\Helpers\MailChimp;

class Download extends Subscribe {

	public $defaults = array(
		'title' => '',
		'hidden' => false,
		'privacy_link' => '',
		'file' => '',
		'open_button_label' => 'Open',
		'submit_button_label' => 'Submit',
		'redirect_to' => '',
	);
	
	public $shortcode_id = 'whitepaper_dl';
	public $formClass = '\Svbk\WP\Forms\Download';
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

}
