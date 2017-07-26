<?php namespace Svbk\WP\Shortcakes\Feedback;

use WP_Query;
use Svbk\WP\Shortcakes\Shortcake;


class Testimonial extends Shortcake {

	public $shortcode_id = 'testimonial';
	public $post_type = 'testimonial';
	public $query_args = array();
	public $register_cpt = false;

	public static $defaults = array(
		'count' => 2,
	);

	public function title() {
		return __( 'Single Testimonial', 'svbk-shortcakes' );
	}

	static function register( $options = array() ) {

		$instance = parent::register( $options );

		if ( $instance->register_cpt ) {
			add_action( 'init', array( $instance, 'register_cpts' ) );
		}

		return $instance;
	}

	public function loadMoreFilters() {
		return array(
			'count' => FILTER_VALIDATE_INT,
			'paged' => FILTER_VALIDATE_INT,
			'offset' => FILTER_VALIDATE_INT,
			'load_more' => FILTER_VALIDATE_BOOLEAN,
		);
	}

	public function loadMore() {

		$data = filter_input_array( INPUT_POST, $this->loadMoreFilters() );

		echo $this->output( $data, '', $this->shortcode_id, false );
		exit;
	}

	public function fields() {
		return array(
				array(
					'label'  => esc_html__( 'Reviews Count', 'svbk-shortcakes' ),
					'attr'   => 'count',
					'type'   => 'number',
					'encode' => true,
					'description' => esc_html__( 'How many testimonials to show', 'svbk-shortcakes' ),
					'meta'   => array(
						'placeholder' => self::$defaults['count'],
					),
				),
				array(
					'label'  => esc_html__( 'Show Load More', 'svbk-shortcakes' ),
					'attr'   => 'load_more',
					'type'   => 'checkbox',
					'default' => self::$defaults['load_more'],
					'description' => esc_html__( 'Show the AJAX "Load More" button', 'svbk-shortcakes' ),
				),
				array(
					'label'  => esc_html__( 'Offset', 'svbk-shortcakes' ),
					'attr'   => 'offset',
					'type'   => 'number',
					'description' => esc_html__( 'Skip the first N testimonials', 'svbk-shortcakes' ),
					'meta'   => array(
						'placeholder' => self::$defaults['offset'],
					),
				),
			);
	}

	protected function getQueryArgs( $attr ) {

		if ( ($attr['offset'] > 0) && ! empty( $attr['paged'] ) ) {
			$attr['offset']  = $attr['count'] * $attr['paged'];
		}

		return array_merge(array(
			'post_type' => $this->post_type,
			'post_status' => 'publish',
			'orderby' => 'date',
			'posts_per_page' => $attr['count'],
			'paged' => $attr['paged'],
			'offset' => $attr['offset'],
		), $this->query_args );

	}

	public function output( $attr, $content, $shortcode_tag, $container = true ) {

		$output = '';

		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

		$testimonials = new WP_Query( $this->getQueryArgs( $attr ) );

		if ( $testimonials->have_posts() ) {

			$html_data_atts = '';

			foreach ( $attr as $key => $val ) {
				$html_data_atts .= 'data-' . esc_html( $key ) . '="' . esc_attr( $val ) . '" ';
			}

			if ( $container ) {
				$output .= '<aside class="testimonials" ' . $html_data_atts . '>';
			}

			if ( locate_template( 'template-parts/content-' . $this->post_type . '.php' ) != '' ) {

				ob_start();

				while ( $testimonials->have_posts() ) : $testimonials->the_post();
					get_template_part( 'template-parts/content', $this->post_type );
				endwhile;

				$output .= ob_get_contents();
				ob_end_clean();

			} else {

				while ( $testimonials->have_posts() ) : $testimonials->next_post();

					$output .= '<blockquote class="testimonial">';
					$output .= apply_filters( 'the_content', $testimonials->post->post_content );
					$output .= '<footer class="author">';
					$output .= '<cite class="name">' . get_the_title( $testimonials->post ) . '</cite>';
					$output .= '<div class="picture">' . get_the_post_thumbnail( $testimonials->post->ID, 'small' ) . '</div>';
					$output .= '<span class="role">' . get_field( 'author_role', $testimonials->post->ID ) . '</span>';
					$output .= '</footer>';
					$output .= '</blockquote>';

				endwhile;
			}

			wp_reset_query();
			wp_reset_postdata();

			if ( $attr['load_more'] && (intval( $attr['paged'] ) < $testimonials->max_num_pages) ) {
				$output .= '<button class="button loadmore">' . __( 'Show more testimonials', 'svbk-shortcakes' ) . '</button>';
			}

			if ( $container ) {
				$output .= '</aside>';
			}
		}// End if().

		return $output;

	}

}
