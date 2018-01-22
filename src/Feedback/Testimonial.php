<?php 
namespace Svbk\WP\Shortcakes\Feedback;

use WP_Query;
use Svbk\WP\Shortcakes\Shortcake;


class Testimonial extends Shortcake {

	public $shortcode_id = 'testimonial';
	public $post_type = 'testimonial';
	public $icon = 'dashicons-thumbs-up';	
	public $query_args = array();

	public $classes = array('single-testimonial', 'testimonial');

	public static $defaults = array(
		'post_id' => 0,
	);

	public function title() {
		return __( 'Single Testimonial', 'svbk-shortcakes' );
	}

	static function register( $options = array() ) {

		$instance = parent::register( $options );

		return $instance;
	}

	public function fields() {
		return array(
				'post_id' => array(
					'label'  => esc_html__( 'Testimonial', 'svbk-shortcakes' ),
					'attr'   => 'post_id',
					'type'   => 'post_select',
					'query'    => array(
						'post_type' => $this->post_type,
						'post_status' => 'any'
					),
					'multiple' => false,
					'description' => esc_html__( 'Select the testimonial to show', 'svbk-shortcakes' ),
				),
			);
	}

	protected function getQueryArgs( $attr ) {
		return array_merge(array(
			'p' => intval($attr['post_id']),
			'post_type' => $this->post_type,
			'post_status' => 'publish',
		), $this->query_args );

	}

	public function output( $attr, $content, $shortcode_tag ) {

		$output = '';

		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

		$testimonials = new WP_Query( $this->getQueryArgs( $attr ) );

		if ( $testimonials->have_posts() ) {

			if ( locate_template( 'template-parts/content-' . $this->post_type . '.php' ) != '' ) {

				ob_start();

				while ( $testimonials->have_posts() ) : $testimonials->the_post();
					get_template_part( 'template-parts/content', $this->post_type );
				endwhile;

				$output .= ob_get_contents();
				ob_end_clean();

			} else {

				while ( $testimonials->have_posts() ) : $testimonials->next_post();

					$video = get_field('video', $testimonials->post->ID );
					$rating = get_field('rating', $testimonials->post->ID);

					$output .= '<blockquote ' . $this->renderClasses( get_post_class('', $testimonials->post) ) .  ' >';
					$output .= apply_filters( 'the_content', $testimonials->post->post_content );
					$output .= '	<footer class="author">';
					$output .= '		<cite class="name">' . get_the_title( $testimonials->post ) . '</cite>';
					
					if ( $video ) :
						$output .= $video;
					else:
						$output .= '	<div class="picture">' . get_the_post_thumbnail( $testimonials->post->ID, 'small' ) . '</div>';
					endif;
					
					$output .= '		<span class="role">' . get_field( 'role', $testimonials->post->ID ) . '</span>';
					
					if( $rating ) :
						$output .= '	<div class="rating ' . esc_attr('rating-' . $rating) . '>';
						$output .= '	<span class="screen-reader-text">' . __('Rating', 'svbk-shortcakes') . ': ' . esc_html($rating) . '</span>';
					endif;					
					
					$output .= '	</footer>';
					$output .= '</blockquote>';

				endwhile;
			}

			wp_reset_query();
			wp_reset_postdata();

		}// End if().

		return $output;

	}

}
