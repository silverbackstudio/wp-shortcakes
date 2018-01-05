<?php

namespace Svbk\WP\Shortcakes\Cards;

use Svbk\WP\Shortcakes\Shortcake;

class Simple extends Shortcake {

	public $defaults = array(
			'title' => '',
			'subtitle' => '',
			'classes' => '',
			'head_image' => '',
	);

	public $shortcode_id = 'simple_box';
	public $icon = 'dashicons-align-left';
	public $header_tag = 'h2';
	public $classes = array( 'simple-box' );
	public $image_size = 'post-thumbnail';

	public $renderOrder = array(
		'wrapperBegin',
		'image',
		'contentBegin',
		'title',
		'subtitle',
		'content',
		'contentEnd',
		'wrapperEnd',
	);

	public function title() {
		return __( 'Simple Box', 'svbk-shortcakes' );
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
			'head_image' => array(
				'label'       => esc_html__( 'Image', 'svbk-shortcakes' ),
				'attr'        => 'head_image',
				'type'        => 'attachment',
				'libraryType' => array( 'image' ),
				'addButton'   => esc_html__( 'Select Image', 'svbk-shortcakes' ),
				'frameTitle'  => esc_html__( 'Select Image', 'svbk-shortcakes' ),
			),
			'subtitle' => array(
				'label'  => esc_html__( 'Subtitle', 'svbk-shortcakes' ),
				'attr'   => 'subtitle',
				'type'   => 'text',
				'encode' => false,
				'description' => esc_html__( 'This title will replace the Page title', 'svbk-shortcakes' ),
				'meta'   => array(
					'placeholder' => esc_html__( 'Insert title', 'svbk-shortcakes' ),
				),
			),
			'classes' => array(
				'label'    => esc_html__( 'Custom Classes', 'svbk-shortcakes' ),
				'attr'     => 'classes',
				'type'     => 'text',
			),
		);
	}

	protected function getTitle( $attr ) {
		return $attr['title'];
	}

	protected function getImage( $attr ) {
		return $attr['head_image'] ? wp_get_attachment_image( $attr['head_image'], $this->image_size ) : '';
	}

	public function renderOutput( $attr, $content, $shortcode_tag ) {

		$attr = $this->shortcode_atts( $this->defaults, $attr, $shortcode_tag );
		$title = $this->getTitle( $attr );
		$image = $this->getImage( $attr );

		if ( $image ) {
			$this->classes[] = 'has-image';
		}

		$classes = array_merge( $this->classes, $this->getClasses( $attr ) );

		$output = parent::renderOutput($attr, $content, $shortcode_tag);

		if ( $image ) {
			$output['image'] = $image;
		}

		$output['wrapperBegin']  = '<div ' . $this->renderClasses( $classes ) . '>';
		$output['contentBegin'] = '<div class="content">';

		if ( $title ) {
			$output['title'] = sprintf( '<%2$s class="entry-title"><span>%1$s</span></%2$s>', $title, $this->header_tag );
		}

		if ( $attr['subtitle'] ) {
			$output['subtitle'] = '<div class="entry-title">' . $attr['subtitle'] . '</div>';
		}

		$output['content'] = '<div class="entry-content">' . $output['content'] . '</div>';
		$output['contentEnd'] = '</div>';
		$output['wrapperEnd'] = '</div>';
		
		return $output;

	}

}
