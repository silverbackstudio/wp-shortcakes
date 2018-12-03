<?php

namespace Svbk\WP\Shortcakes\Post;

use Svbk\WP\Shortcakes\Shortcake;
use WP_Query;

class Latest extends Shortcake {

	public $shortcode_id = 'latest_posts';

	public $icon = 'dashicons-pressthis';

	public $post_type = 'post';
	public $query_args = array();
	public $classes = array( 'latest-posts', 'post-thumbs' );
	public $template_base = 'template-parts/thumb';
	public $taxonomy = 'category';

	public static $defaults = array(
		'category' => 0, // for back compat.
		'taxonomy' => 0,
		'count' => 3,
		'offset' => 0,
	);

	public $renderOrder = array(
		'wrapperBegin',
		'content',
		'wrapperEnd',
	);

	public function title() {
		return __( 'Latest Posts', 'svbk-shortcakes' );
	}

	public function ui_args() {

		$args = parent::ui_args();

		unset( $args['inner_content'] );

		return $args;

	}

	public function fields() {
		
		$fields  = array(
			'count' => array(
				'label'       => esc_html__( 'Post Count', 'svbk-shortcakes' ),
				'description' => esc_html__( 'The number of posts shown', 'svbk-shortcakes' ),
				'attr'        => 'count',
				'type'        => 'number',
				'meta'        => array(
				'placeholder' => self::$defaults['count'],
				'min'         => '1',
				'max'         => '9',
				'step'        => '1',
				),
			),
			'offset' => array(
				'label'       => esc_html__( 'Offset', 'svbk-shortcakes' ),
				'description' => esc_html__( 'The number of posts to skip', 'svbk-shortcakes' ),
				'attr'        => 'offset',
				'type'        => 'number',
				'meta'        => array(
				'placeholder' => self::$defaults['offset'],
				'min'         => '1',
				'step'        => '1',
				),
			),
		);
		
		if ( $this->taxonomy && taxonomy_exists( $this->taxonomy ) ) {
			
			$taxonomy = get_taxonomy( $this->taxonomy );
			
			$fields['taxonomy'] = array(
				'label'       => $taxonomy->label,
				'description' => esc_html__( 'The filter the posts by taxonomy', 'svbk-shortcakes' ),
				'attr'        => 'taxonomy',
				'type'     => 'term_select',
				'taxonomy' => $this->taxonomy,
			);
		}
		
		return $fields;
		
	}

	protected function getQueryArgs( $attr ) {

		$args = array(
			'post_type' => $this->post_type,
			'post_status' => 'publish',
			'orderby' => 'date',
			'posts_per_page' => $attr['count'],
		);

		if ( ($attr['offset'] > 0) && ! empty( $attr['paged'] ) ) {
			$args['offset']  = $attr['count'] * $attr['paged'];
		}

		if ( $this->taxonomy && $attr['taxonomy']  ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $this->taxonomy,
					'field' => 'id',
					'terms' => intval( $attr['taxonomy'] )
				)
			);
		}
		
		return array_merge($args, $this->query_args );
	}

	public function renderOutput( $attr, $content, $shortcode_tag ) {

		$output = array();

		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

		if ( $attr['category'] && ! $attr['taxonomy'] ) {
			$attr['taxonomy'] = $attr['category'];
		}

		if ( defined( 'SHORTCODE_UI_DOING_PREVIEW' ) && SHORTCODE_UI_DOING_PREVIEW ) {

			$output['wrapperBegin'] = '<div ' . $this->renderClasses( $this->getClasses( $attr ) ) . ' >';
			$output['content'] = '<h2>{{' . ($this->title ?: $this->title()) . '}}</h2>';
			$output['wrapperEnd'] = '</div>';

		} else {

			$output['wrapperBegin'] = '<div ' . $this->renderClasses( $this->getClasses( $attr ) ) . ' >';

			$postsQuery = new WP_Query( $this->getQueryArgs( $attr ) );

			ob_start();

			while ( $postsQuery->have_posts() ) : $postsQuery->the_post();
				get_template_part( $this->template_base, get_post_type() );

			endwhile; // End of the loop.

			$output['content'] = ob_get_clean();

			wp_reset_query();
			wp_reset_postdata();

			$output['wrapperEnd'] = '</div>';

		}

		return $output;

	}


}
