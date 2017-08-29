<?php

namespace Svbk\WP\Shortcakes\Cards;

use Svbk\WP\Shortcakes\Shortcake;

class Preview extends Shortcake {

	public $defaults = array(
			'head_image' => 0,
			'title' => '',
			'enable_markdown' => false,
			'url' => '',
			'target' => '',
			'link_label' => '',
			'classes' => '',
	);

	public $shortcode_id = 'preview_card';
	public $image_size = 'thumbnail';
	public $buttonClasses = array( 'readmore' );
	public $linkImage = true;
	public $linkTitle = true;
	public $wrapperTag = 'div';
	public $classes = array( 'preview-card' );

	public static $defaultRenderOrder = array(
		'wrapperBegin',
		'headerBegin',
		'title',
		'headerEnd',
		'image',
		'contentBegin',
		'content',
		'button',
		'contentEnd',
		'wrapperEnd',
	);

	public $renderOrder;

	public function __construct( $properties ) {

		$this->renderOrder = self::$defaultRenderOrder;

		parent::__construct( $properties );
	}

	public function title() {
		return __( 'Preview Card', 'svbk-shortcakes' );
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
			'url' => array(
				'label'    => esc_html__( 'URL', 'svbk-shortcakes' ),
				'attr'     => 'url',
				'type'     => 'url',
				'description' => esc_html__( 'This URL will be used instead of Page permalink.', 'svbk-shortcakes' ),
			),
			'link_label' => array(
				'label'  => esc_html__( 'Button Label', 'svbk-shortcakes' ),
				'attr'   => 'link_label',
				'type'   => 'text',
				'encode' => false,
				'meta'   => array(
					'placeholder' => esc_html__( 'Insert button label', 'svbk-shortcakes' ),
				),
			),
			'target' => array(
				'label'    => esc_html__( 'Open in new window', 'svbk-shortcakes' ),
				'attr'     => 'target',
				'type'     => 'checkbox',
			),
			'enable_markdown' => array(
				'label'    => esc_html__( 'Enable Markdown', 'svbk-shortcakes' ),
				'attr'     => 'enable_markdown',
				'type'     => 'checkbox',
			),
			'classes' => array(
				'label'    => esc_html__( 'Custom Classes', 'svbk-shortcakes' ),
				'attr'     => 'classes',
				'type'     => 'text',
			),
		);
	}

	protected function getLink( $attr ) {
		return $attr['url'];
	}

	protected function getImage( $attr ) {
		return wp_get_attachment_image( $attr['head_image'], $this->image_size ) ?: '<div class="image-placeholder"></div>';
	}

	protected function getTitle( $attr ) {
		return $attr['title'];
	}

	protected function getClasses( $attr ) {
		return array_map( 'trim', explode( ' ', $attr['classes'] ) );
	}

	protected function parseMarkdown( $content ) {
		$content = str_replace( array( "\n", '<p>' ), '', $content );
		$content = str_replace( array( '<br />', '<br>', '<br/>' ), "\n", $content );
		$content = str_replace( '</p>', "\n\n", $content );

		$md = new \Michelf\Markdown;
		return $md->transform( $content );
	}

	public function renderOutput( $attr, $content, $shortcode_tag ) {

		$attr = $this->shortcode_atts( $this->defaults, $attr, $shortcode_tag );

		$link = $this->getLink( $attr );
		$image = $this->getImage( $attr );
		$title = $this->getTitle( $attr );

		$target = $attr['target'] ? ' target="_blank" ' : '';

		if ( $attr['enable_markdown'] ) {
			$content = $this->parseMarkdown( $content );
		}

		$classes = array_merge( $this->classes, $this->getClasses( $attr ) );

		$output['wrapperBegin']  = '<' . $this->wrapperTag . ' class="' . esc_attr( join( ' ', $classes ) ) . '">';

		if ( $title ) {
			$output['headerBegin'] = '<div class="entry-header">';
			$output['title'] = sprintf( ($this->linkTitle && $link) ? '<h2 class="entry-title"><a href="%2$s" %3$s >%1$s</a></h2>':'<h2 class="entry-title">%1$s</h2>', $title, esc_attr( $link ), $target );
			$output['headerEnd'] = '</div>';
		}

		if ( $image ) {
			$output['image'] = sprintf( ($this->linkImage && $link) ? '<a href="%2$s" %3$s >%1$s</a>':'%1$s', $image, esc_attr( $link ), $target );
		}

		$output['contentBegin'] = '<div class="card-text">';
		$output['content'] = '  <div class="entry-content">' . $content . '</div>';

		if ( $link && $attr['link_label'] ) {
			$output['button'] = '  <a class="' . esc_attr( join( ' ', $this->buttonClasses ) ) . '" href="' . esc_attr( $link ) . '" ' . $target . ' >' . $attr['link_label'] . '</a>';
		}

		$output['contentEnd'] = '</div>';
		$output['wrapperEnd'] = '</' . $this->wrapperTag . '>';

		return $output;

	}

}
