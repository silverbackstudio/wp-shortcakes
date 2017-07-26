<?php

namespace Svbk\WP\Shortcakes\Term;

use Svbk\WP\Shortcakes\Shortcake;
use WP_Query;

class Archive extends Shortcake {

	public $shortcode_id = 'term_archive';
	public $template_base = 'template-parts/content';
	public $classes = array( 'term-archive', 'post-list' );

	public $taxonomy = 'category';
	public $icon = 'dashicons-editor-ul';

	public $post_query = array(
		'post_type' => 'post',
		'post_status' => 'publish',
	);

	public static $defaults = array(
			'term_id' => '',
			'posts_per_page' => 6,
			'post_status' => 'publish',
	);

	public function title() {
		return __( 'Term Archive', 'svbk-shortcakes' );
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
			'posts_per_page' => array(
				'label'    => __( 'Post Count', 'svbk-shortcakes' ),
				'attr'     => 'posts_per_page',
				'type'     => 'number',
			),
		);
	}

	public function ui_args() {
		$ui_args = parent::ui_args();

		unset( $ui_args['inner_content'] );

		return $ui_args;
	}

	protected function getQueryArgs( $attr ) {

		$args = array_merge(
			$this->post_query,
			array(
				'posts_per_page' => (int) $attr['posts_per_page'],
				'tax_query' => array(
					array(
						'taxonomy' => $this->taxonomy,
						'field' => 'term_id',
						'terms' => explode( ',', $attr['term_id'] ),
					),
				),
			)
		);

		if ( get_query_var( 'paged' ) > 1 ) {
			$args['paged'] = (int) get_query_var( 'paged' );
		}

		return $args;
	}

	public function output( $attr, $content, $shortcode_tag ) {

		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

		$output = '';

		$post_query = new WP_Query( $this->getQueryArgs( $attr ) );

		if ( $post_query->have_posts() ) :

			$output .= '<div class="' . join( ' ', $this->getClasses( $attr ) ) . '">';
			ob_start();

			while ( have_posts() ) : the_post();
				get_template_part( $this->template_base, get_post_type() );
			endwhile;

			$output .= ob_get_contents();
			ob_end_clean();

			$output .= get_the_posts_navigation();
			$output .= '</div>';

			wp_reset_postdata();
			wp_reset_query();

		endif;

		return $output;

	}

}
