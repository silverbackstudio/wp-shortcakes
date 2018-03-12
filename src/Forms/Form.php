<?php

namespace Svbk\WP\Shortcakes\Forms;

use Svbk\WP\Shortcakes\Shortcake;

class Form extends Shortcake {

	public $defaults = array(
		'title' => '',
		'hidden' => false,
		'privacy_link' => '',
		'file' => '',
		'open_button_label' => 'Open',
		'submit_button_label' => 'Submit',
		'redirect_to' => '',
	);

	public $shortcode_id = 'svbk-form';
	public $icon = 'dashicons-forms';
	public $field_prefix = 'frm';
	public $action = 'svbk_form';
	public $formClass = '\Svbk\WP\Forms\Submission';

	public $classes = array();

	public $confirmMessage = '';

	public $formParams = array();

	public $hardRedirect = false;
	public $redirectTo;
	public $redirectData = array();	

	public static $form_errors = array();

	public $renderOrder = array(
		'wrapperBegin',
		'openButton',
		'hiddenBegin',
		'closeButton',
		'hiddenContentBegin',
		'formBegin',
		'title',
		'content',
		'input',
		'requiredNotice',
		'beginPolicySubmit',
		'policy',
		'submitButton',
		'endPolicySubmit',
		'messages',
		'formEnd',
		'hiddenContentEnd',
		'hiddenEnd',
		'wrapperEnd',
	);

	public function title() {
		return __( 'Form', 'svbk-shortcakes' );
	}

	public static function register( $options = array(), $form_properties = array() ) {

		$instance = parent::register( $options );

		$instance->formParams = $form_properties;

		add_action( 'init', array( $instance, 'processSubmission' ) );

		return $instance;
	}

	public function register_scripts(){
		
		parent::register_scripts();
		
		\Svbk\WP\Forms\Form::enqueue_scripts();
	}

	protected function submitUrl() {

		return home_url(
			add_query_arg(
				array(
					'svbkSubmit' => $this->action,
				)
			)
		);

	}

	public function processSubmission() {

		if ( filter_input( INPUT_GET, 'svbkSubmit', FILTER_SANITIZE_SPECIAL_CHARS ) !== $this->action ) {
			return;
		}

		if( filter_input( INPUT_POST, 'ajax', FILTER_VALIDATE_BOOLEAN ) && ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$form = $this->getForm( true );
		$form->processSubmission();
		$errors = $form->getErrors();

		$redirect_to = filter_input( INPUT_POST, $form->fieldName('redirect_to'), FILTER_VALIDATE_INT );
		$redirect_url = null;
		
		if ( $redirect_to ) {
			$redirect_url = get_permalink( $redirect_to );

			if( !empty($this->redirectData) ) {
				$redirect_data = array_intersect_key( $form->getInput(), array_flip($this->redirectData) );
				$redirect_data = base64_encode( serialize( $redirect_data ) );
				$redirect_url = add_query_arg( 'fdata', $redirect_data, $redirect_url );
			}
		}

		if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
			@header( 'Content-Type: application/json' );
			send_nosniff_header();
			echo $this->formatResponse( $errors, $form, $redirect_url );
			exit;
		}

		self::$form_errors = $errors;

		if( empty( $errors ) && $redirect_url ) {
			wp_redirect( $redirect_url );
			exit;
		}

	}

	public function formatResponse( $errors, $form, $redirect_url = null ) {

		if ( ! empty( $errors ) ) {

			return json_encode(
				array(
					'prefix' => $this->field_prefix,
					'status' => 'error',
					'errors' => $errors,
				)
			);

		}

		$response = array(
			'prefix' => $form->field_prefix,
			'status' => 'success',
			'message' => $this->confirmMessage(),
		);
		
		if ( $redirect_url ) {
			$response['redirect'] = $redirect_url;
		}

		return json_encode( $response );
	}

	public function confirmMessage() {
		return $this->confirmMessage ?: __( 'Thanks for your request, we will reply as soon as possible.', 'svbk-shortcakes' );
	}

	public function fields() {
		return array(
			'title' => array(
					'label'  => esc_html__( 'Title', 'svbk-shortcakes' ),
					'attr'   => 'title',
					'type'   => 'text',
					'encode' => false,
					'description' => esc_html__( 'This title will replace the Page title', 'svbk-shortcakes' ),
					'meta'   => array(
					'placeholder' => esc_html__( 'Insert title', 'svbk-shortcakes' ),
				),
			),
			'hidden' => array(
				'label' => esc_html__( 'Hidden', 'svbk-shortcakes' ),
				'attr' => 'hidden',
				'type' => 'select',
				'options'     => array(
					array(
						'value' => '',
						'label' => esc_html__( 'Always Visible', 'svbk-shortcakes' ),
					),
					array(
						'value' => 'lightbox',
						'label' => esc_html__( 'Lightbox', 'svbk-shortcakes' ),
					),
					array(
						'value' => 'collapse',
						'label' => esc_html__( 'Collapse', 'svbk-shortcakes' ),
					),
				),
			),
			'open_button_label' => array(
				'label'  => esc_html__( 'Open button label', 'svbk-shortcakes' ),
				'attr'   => 'open_button_label',
				'type'   => 'text',
				'encode' => true,
				'description' => esc_html__( 'The label for the lightbox open button', 'svbk-shortcakes' ),
				'meta'   => array(
					'placeholder' => $this->defaults['open_button_label'],
				),
			),
			'submit_button_label' => array(
				'label'  => esc_html__( 'Submit button label', 'svbk-shortcakes' ),
				'attr'   => 'submit_button_label',
				'type'   => 'text',
				'encode' => true,
				'description' => esc_html__( 'The label for submit button', 'svbk-shortcakes' ),
				'meta'   => array(
					'placeholder' => $this->defaults['submit_button_label'],
			   ),
			),
			'redirect_to' => array(
				'label'  => esc_html__( 'Redirect To', 'svbk-shortcakes' ),
				'attr'   => 'redirect_to',
				'type'   => 'post_select',
				'query'    => array( 'post_type' => 'any' ),
				'multiple' => false,				
				'description' => esc_html__( 'Redirect to this page after successful form submission', 'svbk-shortcakes' ),
			),			
			'classes' => array(
				'label'    => esc_html__( 'Custom Classes', 'svbk-shortcakes' ),
				'attr'     => 'classes',
				'type'     => 'text',
			),
		);
	}

	protected function getForm( $set_send_params = false ) {

		$formClass = $this->formClass;

		$form = new $formClass;

		$form->field_prefix = $this->field_prefix;
		$form->action = $this->action;
		$form->submitUrl = $this->submitUrl();

		if( !empty( self::$form_errors  ) ) {
			$form->errors = self::$form_errors;
		}

		self::configure( $form, $this->formParams );

		return $form;
	}

	public function containerClasses( $attr ) {

		$classes = $this->getClasses( $attr );

		if ( ! empty( $attr['hidden'] ) ) {
			$classes[] = 'svbk-hidden';
			$classes[] = 'svbk-' . $attr['hidden'];
		}

		$classes[] = 'svbk-form-container';

		return $classes;
	}

	public function containerId( $attr, $index ) {
		return $this->field_prefix . '-container-' . $index;
	}

	public function renderOutput( $attr, $content, $shortcode_tag ) {

		static $index = 0;

		$index++;
		
		$attr = $this->shortcode_atts( $this->defaults, $attr, $shortcode_tag );
		$form = $this->getForm();

		$output = array_merge( parent::renderOutput( $attr, $content, $shortcode_tag ), $form->renderParts( $this->action, $attr ) );

		$output['wrapperBegin'] = '<div class="' . join( ' ', $this->containerClasses( $attr ) ) . '" id="' . $this->containerId( $attr, $index ) . '">';

		if ( $attr['title'] ) {
			$output['title'] = '<h2 class="form-title">' . $attr['title'] . '</h2>';
		}

		if( !empty($attr['redirect_to']) ) {
			$output['input']['redirect_to'] =  $form->renderField( 'redirect_to', 
				array(
					'label' => __( 'Redirect To', 'svbk-helpers' ),
					'type' => 'hidden',
					'default' => $attr['redirect_to'],
					'filter' => FILTER_VALIDATE_INT,
				) 
			);
		}

		switch ( $attr['hidden'] ) {
			case 'collapse':
				$output['openButton'] = '<a class="button svbk-show-content svbk-collapse-open" href="#' . $this->field_prefix . '-container-' . $index . '" >' . urldecode( $attr['open_button_label'] ) . '</a>';
				$output['hiddenBegin'] = '<div class="svbk-collapse-container">';
				$output['closeButton'] = '<a class="button svbk-hide-content svbk-collapse-close" href="#' . $this->field_prefix . '-container-' . $index . '" ><span>' . __( 'Close', 'svbk-shortcakes' ) . '</span></a>';
				$output['hiddenContentBegin'] = '<div class="svbk-form-content svbk-collapse-content">';
				$output['hiddenContentEnd'] = '</div>';
				$output['hiddenEnd'] = '</div>';
				break;
			case 'lightbox':
				$output['openButton'] = '<a class="button svbk-show-content svbk-lightbox-open" href="#' . $this->field_prefix . '-container-' . $index . '" >' . urldecode( $attr['open_button_label'] ) . '</a>';
				$output['hiddenBegin'] = '<div class="svbk-lightbox-container">';
				$output['closeButton'] = '<a class="button svbk-hide-content svbk-lightbox-close" href="#' . $this->field_prefix . '-container-' . $index . '" ><span>' . __( 'Close', 'svbk-shortcakes' ) . '</span></a>';
				$output['hiddenContentBegin'] = '<div class="svbk-form-content svbk-lightbox-content">';
				$output['hiddenContentEnd'] = '</div>';
				$output['hiddenEnd'] = '</div>';
				break;
		}

		$output['beginPolicySubmit'] = '<div class="form-policy-submit-wrapper">';
		$output['endPolicySubmit'] = '</div>';

		$output['wrapperEnd'] = '</div>';

		return $output;

	}

}
