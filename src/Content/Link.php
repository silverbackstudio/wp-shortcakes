<?php
namespace Svbk\WP\Shortcakes\Content;

use Svbk\WP\Shortcakes\Shortcake;

class Link extends Shortcake {

	public $shortcode_id = 'link';
	public $icon = 'dashicons-admin-links';
	public $classes = array( 'link' );
	public $post_query = array(
		'post_type' => 'any',
	);
	public $taxonomy = 'category';

	public $renderOrder = array(
		'linkBegin',
		'content',
		'linkEnd',
	);

	public static $defaults = array(
		'post_id' => 0,
		'term' => 0,
		'url' => '',
		'target' => 0,
		'class' => '',
	);

	public function title() {
		return __( 'Link', 'svbk-shortcakes' );
	}

	public function fields() {
		return array(
			'post_id' => array(
				'label'  => esc_html__( 'Post/Page to Link', 'svbk-shortcakes' ),
				'attr'   => 'post_id',
				'type'   => 'post_select',
				'query'    => $this->post_query,
				'multiple' => false,
				'description' => esc_html__( 'Select the post to link', 'svbk-shortcakes' ),
			),
			'term' => array(
				'label'    => __( 'Term to link', 'svbk-shortcakes' ),
				'attr'     => 'term',
				'type'     => 'term_select',
				'taxonomy' => $this->taxonomy,
				'description' => esc_html__( 'if you want to link a taxonomy term, please leave the post field blank', 'svbk-shortcakes' ),
				'multiple' => false,
			),
			'url' => array(
				'label'  => esc_html__( 'Link URL', 'svbk-shortcakes' ),
				'attr'   => 'url',
				'type'   => 'url',
				'description' => esc_html__( 'Select the URL to link', 'svbk-shortcakes' ),
			),
			'target' => array(
				'label'  => esc_html__( 'Open link in a new window', 'svbk-shortcakes' ),
				'attr'   => 'target',
				'type'   => 'checkbox',
			),			
			'class' => array(
				'label'  => esc_html__( 'Classes', 'svbk-shortcakes' ),
				'attr'   => 'class',
				'type'   => 'text',
			),				
		);
	}

	public function ui_args() {

		$args = parent::ui_args();

		$args['inner_content']['label'] = __( 'Link Label', 'svbk-shortcakes' );

		return $args;

	}

	public function getLink( $attr ) {

		if ( $attr['post_id'] ) {
			return get_permalink( $attr['post_id'] );
		}

		if( $attr['term'] ) {
			$term_link = get_term_link( intval( $attr['term'] ), $this->taxonomy );
		
			if ( $term_link && ! is_wp_error( $term_link ) ) {
				return $term_link;
			}
		}
		
		if ( $attr['url'] ) {
			return $attr['url'];
		}

		return '';
	}

	public function renderOutput( $attr, $content, $shortcode_tag ) {
		
		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

		$label = $content ?: get_the_title( $attr['post_id'] );

		$output = parent::renderOutput($attr, $content, $shortcode_tag);

		$link = $this->getLink( $attr );
		$link_target = filter_var( $attr['target'], FILTER_VALIDATE_BOOLEAN );

		if ( $link ) {
			$output['linkBegin'] = '<a  ' . $this->renderClasses( $this->getClasses( $attr ) ) . '  href="' . esc_url( $link ) . '" ' . ( $link_target ? ' target="_blank"' : '' ) . '  >';
			$output['linkEnd'] = '</a>';
		}

		return $output;

	}	

}
