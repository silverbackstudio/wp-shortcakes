<?php

namespace Svbk\WP\Shortcakes\Cards;

class PreviewPost extends Preview {

	public $shortcode_id = 'preview_card_post';
	public $post_query = array(
		'post_type' => 'page',
	);
	public $classes = array( 'preview-card', 'post-thumb' );

	public function title() {
		return $this->title ?: __( 'Post Preview Card', 'svbk-shortcakes' );
	}

	public function fields() {

		$fields = parent::fields();

		foreach ( $fields as &$field ) {
			if ( 'url' === $field['attr'] ) {
				$field = array(
					'label'    => esc_html__( 'Select Page', 'svbk-shortcakes' ),
					'attr'     => 'linked_post',
					'type'     => 'post_select',
					'query'    => $this->post_query,
					'multiple' => false,
				);
			}
		}

		return $fields;
	}

	protected function shortcode_atts( $defaults, $attr = array(), $shortcode_tag = '' ) {

		unset( $defaults['url'] );
		$defaults['linked_post']  = '';

		return parent::shortcode_atts( $defaults, $attr, $shortcode_tag );
	}

	protected function getLink( $attr ) {
		return get_permalink( $attr['linked_post'] );
	}

	protected function getTitle( $attr ) {
		return $attr['title'] ?: get_the_title( $attr['linked_post'] );
	}

	protected function getImage( $attr ) {

		$image = wp_get_attachment_image( $attr['head_image'], $this->image_size );
		if ( $image ) {
			return $image;
		}

		$image = get_the_post_thumbnail( $attr['linked_post'], $this->image_size );
		if ( $image ) {
			return $image;
		}

		return '<div class="image-placeholder"></div>';
	}

}
