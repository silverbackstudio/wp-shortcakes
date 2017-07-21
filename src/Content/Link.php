<?php
namespace Svbk\WP\Shortcakes\Content;

use Svbk\WP\Shortcakes\Shortcake;

class Link extends Shortcake {

	public $shortcode_id = 'link';
	public $icon = 'dashicons-admin-links';
	public $classes = array( 'link' );
	public $post_query = array(
		'post_type' => array( 'page', 'post' ),
	);
	public $taxonomy = 'category';

	public static $defaults = array(
		'post_id' => 0,
		'term' => 0,
		'class' => '',
	);

	public function title() {
		return __( 'Link', 'svbk-shortcakes' );
	}

	function fields() {
		return array(
				array(
					'label'  => esc_html__( 'Post/Page to Link', 'svbk-shortcakes' ),
					'attr'   => 'post_id',
					'type'   => 'post_select',
					'query'    => $this->post_query,
					'multiple' => false,
					'description' => esc_html__( 'Select the post to link', 'svbk-shortcakes' ),
				),
				array(
					'label'    => __( 'Term to link', 'svbk-shortcakes' ),
					'attr'     => 'term',
					'type'     => 'term_select',
					'taxonomy' => $this->taxonomy,
					'description' => esc_html__( 'if you want to link a taxonomy term, please leave the post field blank', 'svbk-shortcakes' ),
					'multiple' => false,
				),
				array(
					'label'  => esc_html__( 'Classes', 'svbk-shortcakes' ),
					'attr'   => 'class',
					'type'   => 'text',
				),
			);
	}

	function ui_args() {

		$args = parent::ui_args();

		$args['inner_content']['label'] = __( 'Link Label', 'svbk-shortcakes' );

		return $args;

	}

	function getLink( $attr ) {

		if ( $attr['post_id'] ) {
			return get_permalink( $attr['post_id'] );
		}

		$term_link = get_term_link( intval( $attr['term'] ), $this->taxonomy );

		if ( $term_link && ! is_wp_error( $term_link ) ) {
			return $term_link;
		}

		return '';
	}

	function output( $attr, $content, $shortcode_tag ) {

		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );
		$label = $content ?: get_the_title( $attr['post_id'] );
		$classes = array_merge( $this->classes, explode( ' ', $attr['class'] ) );
		$link = $this->getLink( $attr );

		if ( $link ) {
			return '<a class="' . esc_attr( join( ' ', $classes ) ) . '" href="' . $link . '">' . $content . '</a>';
		}

	}

}
