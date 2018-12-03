<?php

namespace Svbk\WP\Shortcakes\Forms;

use Svbk\WP\Shortcakes\Shortcake;
use Svbk\WP\Forms;
use Exception;

class Form extends Shortcake {

	public $defaults = array();

	public $shortcode_id = 'svbk-form';
	public $icon = 'dashicons-forms';
	public $field_prefix = 'frm';
	public $action = 'svbk_form';
	public $formClass = '\Svbk\WP\Forms\Submission';
	public $form;

	public $classes = array();

	public $confirmMessage = '';

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

		$instance->defaults = array_merge( array(
			'title' => '',
			'hidden' => false,
			'privacy_link' => '',
			'open_button_label' => __('Open', 'svbk-shortcakes'),
			'submit_button_label' => __( 'Submit', 'svbk-shortcakes') ,
			'redirect_to' => '',
		), $instance->defaults );

		//Retrocompatibility with 4.1
		if ( ! empty( $form_properties ) ) {
		    _deprecated_argument( __FUNCTION__, '4.2.0', 'Please use the setForm method' );
    		$instance->setForm( $form_properties );
		}

		return $instance;
	}

	public function setForm( $form = array() ){
		
		if( is_array( $form ) ) {
			$formClass = $this->formClass;
			$this->form = new $formClass( $form );
		} elseif ( is_string( $form ) && Forms\Manager::has( $form ) ) {
			$this->form = Forms\Manager::get( $form );
		} elseif( is_a( $form,  Form::class ) ) {
			$this->form = $form;
		} else {
			throw new Exception( 'Unable to setup form' );
		}
		
		return $this->form;
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

	protected function getForm() {

		if( !empty( self::$form_errors  ) ) {
			$form->errors = self::$form_errors;
		}

		return $this->form;
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
		return $this->shortcode_id . '-container-' . $index;
	}

	public function renderOutput( $attr, $content, $shortcode_tag ) {

		static $index = 0;

		$index++;
		
		$attr = $this->shortcode_atts( $this->defaults, $attr, $shortcode_tag );
		$form = $this->getForm();

		$output = parent::renderOutput( $attr, $content, $shortcode_tag );
		
		if ( !empty($output['content']) ){
			$output['content'] = '<div class="form__description">'. $output['content'] . '</div>';
		}
		
		$output = array_merge( $output, $form->renderParts( $attr ) );

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
				$output['openButton'] = '<a class="button svbk-show-content svbk-collapse-open" href="#' . $this->containerId( $attr, $index ) . '" >' . $attr['open_button_label'] . '</a>';
				$output['hiddenBegin'] = '<div class="svbk-collapse-container">';
				$output['closeButton'] = '<a class="button svbk-hide-content svbk-collapse-close" href="#' . $this->containerId( $attr, $index ) . '" ><span>' . __( 'Close', 'svbk-shortcakes' ) . '</span></a>';
				$output['hiddenContentBegin'] = '<div class="svbk-form-content svbk-collapse-content">';
				$output['hiddenContentEnd'] = '</div>';
				$output['hiddenEnd'] = '</div>';
				break;
			case 'lightbox':
				$output['openButton'] = '<a class="button svbk-show-content svbk-lightbox-open" href="#' . $this->containerId( $attr, $index ) . '" >' . $attr['open_button_label'] . '</a>';
				$output['hiddenBegin'] = '<div class="svbk-lightbox-container">';
				$output['closeButton'] = '<a class="button svbk-hide-content svbk-lightbox-close" href="#' . $this->containerId( $attr, $index ) . '" ><span>' . __( 'Close', 'svbk-shortcakes' ) . '</span></a>';
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
