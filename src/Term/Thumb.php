<?php

namespace Svbk\WP\Shortcakes\Term;

use Svbk\WP\Shortcakes\Shortcake;
use WP_Query;


class Thumb extends Shortcake {

	public $shortcode_id = 'term_thumb';
	public $template = 'template-parts/term';

	public $taxonomy = 'category';
	public $classes = array( 'term-thumb' );

	public $image_field = 'image';
	public $image_size = 'thumbnail';

	public $icon = 'dashicons-exerpt-view';

	public $renderOrder = array(
		'wrapperBegin',
		'headerBegin',
		'title',
		'headerEnd',
		'contentBegin',
		'description',
		'button',
		'contentEnd',
		'image',
		'wrapperEnd',
	);

	public $defaults = array(
		'term_id' => '',
		'link_label' => 'Open',
		'classes' => '',
	);

	public function title() {
		return __( 'Term Thumbnail', 'svbk-shortcakes' );
	}

	public function fields() {
		return array(
			'term_id' => array(
				'label'    => esc_html__( 'Select Term', 'svbk-shortcakes' ),
				'attr'     => 'term_id',
				'type'     => 'term_select',
				'taxonomy' => $this->taxonomy,
				'multiple' => false,
			),
			'link_label' => array(
				'label'    => esc_html__( 'Link Label', 'svbk-shortcakes' ),
				'attr'     => 'link_label',
				'type'     => 'text',
			),
		);
	}

	public function getClasses( $attr, $term = null ) {

		$classes = parent::getClasses( $attr );

		if ( $term ) {
			$classes[] = 'term-id-' . $term->term_id;
			$classes[] = 'term-' . $term->slug;
		}

		return $classes;

	}

	public function renderOutput( $attr, $content, $shortcode_tag ) {

		$attr = $this->shortcode_atts( $this->defaults, $attr, $shortcode_tag );
		$output = array();
		$term = get_term( $attr['term_id'], $this->taxonomy );

		if ( ! $term ) {
			$output['title'] = __( 'Term not found', 'svbk-shortcakes' );
		} else if ( is_wp_error( $term ) ) {
			$output['title'] = __( 'Taxonomy not found', 'svbk-shortcakes' );
		} else {
			$output['wrapperBegin'] = '<div ' . self::renderClasses( $this->getClasses( $attr, $term ) ) . '>';

			$output['headerBegin'] = '<div class="entry-header">';
			$output['title'] = '<h2 class="entry-title">' . apply_filters( 'single_term_title', $term->name ) . '</h2>';
			$output['headerEnd'] = '</div>';

			$output['contentBegin'] = '<div class="entry-content">';

			if ( $this->image_field && function_exists( 'get_field' ) ) {
				$image_id = get_field( $this->image_field, $this->taxonomy . '_' . $attr['term_id'] );
			}

			if ( ! empty( $image_id ) ) {
				$output['image'] = wp_get_attachment_image( $image_id, $this->image_size );
			}

			$description = get_term_field( 'description', $term );

			if ( $description ) {
				$output['description'] = '<div class="description"> ' . $description . '</div>';
			}

			$output['button'] = '<a class="button" href="' . get_term_link( $term ) . '">' . esc_html( $attr['link_label'] ) . '</a>';
			$output['contentEnd'] = '</div>';
			$output['wrapperEnd'] = '</div>';
		}

		return $output;

	}

}
