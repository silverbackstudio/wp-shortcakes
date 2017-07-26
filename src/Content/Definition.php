<?php

namespace Svbk\WP\Shortcakes\Content;

use Svbk\WP\Shortcakes\Shortcake;

class Definition extends Shortcake {

	public $shortcode_id = 'dfn';
	public $icon = 'dashicons-feedback';
	public $footnotes = array();

	public function title() {
		return __( 'Definition', 'svbk-shortcakes' );
	}

	public function add() {

		parent::add();

		$this->registerCPT();

		add_filter( 'the_content', array( $this, 'inline_shortcode' ), 1 );
		add_filter( 'the_content', array( $this, 'add_footnotes' ), 99 );
	}

	public function registerCPT() {

		$labels = array(
			'name' => __( 'Definitions', 'svbk-shortcakes' ),
			'singular_name' => __( 'Definition', 'svbk-shortcakes' ),
		);

		$args = array(
			'label' => __( 'Definitions', 'svbk-shortcakes' ),
			'labels' => $labels,
			'description' => '',
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_rest' => false,
			'rest_base' => '',
			'has_archive' => false,
			'show_in_menu' => true,
			'exclude_from_search' => false,
			'capability_type' => 'post',
			'map_meta_cap' => true,
			'hierarchical' => false,
			'rewrite' => array(
				'slug' => 'definition',
				'with_front' => true,
				),
				'query_var' => true,
				'menu_icon' => 'dashicons-exerpt-view',
				'supports' => array( 'title', 'editor' ),
		);

		register_post_type( 'definition', $args );
	}

	public function fields() {

		return array(
			array(
				'label'    => esc_html__( 'Select Definition', 'svbk-shortcakes' ),
				'attr'     => 'definition_post',
				'type'     => 'post_select',
				'query'    => array(
					'post_type' => 'definition',
				),
				'multiple' => false,
			),
			array(
				'label'    => esc_html__( 'Abbreviation', 'svbk-shortcakes' ),
				'attr'     => 'abbr',
				'type'     => 'checkbox',
			),
		);

	}

	public function add_footnotes( $content ) {

		if ( empty( $this->footnotes ) ) {
			return $content;
		}

		$output = '<aside id="footnotes"><dl>';

		$dfns = new \WP_Query( array(
		'post_type' => 'definition',
		'post__in' => $this->footnotes,
		'orderby' => 'post__in',
		) );

		$index = 1;

		while ( $dfns->have_posts() ) : $dfns->the_post();
			$output .= '<dt id="dfn-' . get_the_ID() . '" ><sup><a href="#dfn-ref-' . get_the_ID() . '">[' . $index . ']</a></sup>' . get_the_title() . '</dt>';
			$output .= '<dd>' . get_the_content() . '</dd>';
		endwhile;

		$output .= '</dl></aside>';

		$this->footnotes = array();

		wp_reset_query();
		wp_reset_postdata();

		return $content . $output;
	}

	public function inline_shortcode( $content ) {
		return $content;
	}


	public function output( $attr, $content, $shortcode_tag ) {

		static $index = 1;

		$attr = shortcode_atts( array(
		'definition_post' => false,
		'abbr' => false,
		), $attr, $shortcode_tag );

		$output = '';

		if ( $attr['definition_post'] ) {

			$output .= '<a id="dfn-ref-' . esc_attr( $attr['definition_post'] ) . '" class="sidenote" href="#dfn-' . esc_attr( $attr['definition_post'] ) . '">' . $content . '<sup>[' . $index . ']</sup></a>';

			$this->footnotes[ $index ] = $attr['definition_post'];
		}

		if ( defined( 'SHORTCODE_UI_DOING_PREVIEW' ) && (SHORTCODE_UI_DOING_PREVIEW === true) ) {
			return false;
		}

		return $output;

	}

}
