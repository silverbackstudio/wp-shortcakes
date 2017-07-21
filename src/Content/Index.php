<?php

namespace Svbk\WP\Shortcakes\Content;

use Svbk\WP\Shortcakes\Shortcake;


class Index extends Shortcake {

	public $shortcode_id = 'indexed_content';
	public $icon = 'dashicons-editor-ol';
	public $sections = array();
	public $current_index = array();
	public $template;

	public function __construct( $properties ) {

		$instance->template = '<section id="%1$s" class="index-section">'
			. '<header class="section-header">'
				. '<div class="section-title"><span class="index-counter">%4$s</span><h3  >&nbsp;%2$s</h3></div>'
				. '<a class="anchor to-top" href="#index" title="' . __( 'Go to index', 'svbk-shortcakes' ) . '">&uarr;</a>'
				. '<p class="section-subtitle">%3$s</p>'
			. '</header>'
		. '</section>';

		parent::__construct( $properties );
	}

	public function title() {
		return __( 'Indexed Content', 'svbk-shortcakes' );
	}

	function fields() {

		return array(
			array(
				'label'  => esc_html__( 'Title', 'svbk-shortcakes' ),
				'attr'   => 'title',
				'type'   => 'text',
			),
			array(
				'label'  => esc_html__( 'Slug', 'svbk-shortcakes' ),
				'attr'   => 'slug',
				'type'   => 'text',
				'encode' => false,
			),
			array(
				'label'  => esc_html__( 'Index group', 'svbk-shortcakes' ),
				'attr'   => 'group',
				'type'   => 'text',
			),
		);

	}

	function add() {

		parent::add();

		add_shortcode( 'index', array( $this, 'index_shortcode' ) );
		add_shortcode( 'index-real', array( $this, 'index_shortcode' ) );

		add_filter( 'the_content', 'do_shortcode', 12 );
	}

	function register_ui() {

		parent::register_ui();

		shortcode_ui_register_for_shortcode( 'index',
			array(
				'label' => __( 'Index', 'svbk-shortcakes' ),
				'listItemImage' => 'dashicons-admin-links',
				'post_type' => $this->post_types,
				'attrs' => array(
					array(
						'label'  => esc_html__( 'Index group', 'svbk-shortcakes' ),
						'attr'   => 'group',
						'type'   => 'text',
						),
				),
			)
		);
	}

	function render_index( $sections ) {

		$output = '<ol id="index" class="content-index">';

		foreach ( $sections as $section ) {
			$output .= sprintf( '<li><a class="anchor" href="#%s">%s</a></li>', $section['slug'], $section['title'] );
		}

		$output .= '</ol>';

		return $output;
	}

	function replace_index( $content ) {

		$this->current_index = 0;

		$prepend = $this->render_index();

		$this->sections = array();

		return preg_replace( '/\[index([^\]])?\]/', $prepend, $content, 1 );
	}

	function index_shortcode( $attr, $content, $shortcode_tag ) {

		$attr = shortcode_atts( array(
		'group' => 'default',
		), $attr );

		if ( 'index' == $shortcode_tag ) {

			if ( defined( 'SHORTCODE_UI_DOING_PREVIEW' ) && SHORTCODE_UI_DOING_PREVIEW ) {
				return '<div class="index-preview">' . __( '-- Page Index --', 'svbk-shortcakes' ) . '</div>';
			}

			$new_tag = '[index-real ';

			foreach ( $attr as $key => $value ) {
				$new_tag .= $key . '="' . $value . '" ';
			}

			$new_tag .= ']';

			return $new_tag;
		}

		if ( ! isset( $this->sections[ $attr['group'] ] ) ) {
			return '';
		}

		$sections = &$this->sections[ $attr['group'] ];

		$prepend = $this->render_index( $sections );

		return $prepend;
	}

	function output( $attr, $content, $shortcode_tag ) {

		$attr = $this->shortcode_atts(
			array(
				'title' => '',
				'slug' => sanitize_title( isset( $attr['title'] ) ? $attr['title'] : uniqid() ),
				'group' => 'default',
			),
		$attr, $shortcode_tag );

		$output = apply_filters( 'svbk-shortcake-image', '', $attr, $content, $shortcode_tag );

		if ( $output ) {
			return $output;
		}

		$sections = &$this->sections[ $attr['group'] ];
		$index = &$this->current_index[ $attr['group'] ];

		$index++;

		$sections[ $index ] = array(
		'title' => $attr['title'],
		'slug' => $attr['slug'],
		);

		$output .= sprintf( $this->template, $attr['slug'], esc_html( $attr['title'] ), $content, $index );

		return $output;

	}

}
