<?php namespace Svbk\WP\Shortcakes\Feedback;

use WP_Query;
use Svbk\WP\Shortcakes\Shortcake;

class Testimonials extends Shortcake {

	public $shortcode_id = 'testimonials';
	public $icon = 'dashicons-thumbs-up';
	public $post_type = 'testimonial';
	public $taxonomy = 'testimonial_type';
	public $query_args = array();
	public $register_cpt = true;
	public $register_post_fields = true;
	public $post_type_args = array();

	public $classes = array('testimonials-group'  );

	public static $defaults = array(
		'count' => 2,
		'type' => '',
		'paged' => 1,
		'load_more' => 0,
		'offset' => 0,
	);

	public function title() {
		return __( 'Testimonials', 'svbk-shortcakes' );
	}

	static function register( $options = array() ) {

		$instance = parent::register( $options );

		if ( $instance->register_cpt ) {
			add_action( 'init', array( $instance, 'register_cpts' ) );
		}

		if( $instance->register_post_fields ){
			add_action( 'init', array( $instance, 'register_post_fields' ) );
		}		
		
		add_action( 'wp_enqueue_scripts', array( $instance, 'scripts' ) );
		add_action( 'wp_ajax_svbk_'. $instance->shortcode_id, array( $instance, 'loadMore' ) );
		add_action( 'wp_ajax_nopriv_svbk_' . $instance->shortcode_id, array( $instance, 'loadMore' ) );

		return $instance;
	}

	public function register_cpts() {

		$labels = array(
			'name' => __( 'Testimonials', 'svbk-shortcakes' ),
			'singular_name' => __( 'Testimonial', 'svbk-shortcakes' ),
		);

		$args = array(
			'label' => $labels['name'],
			'labels' => $labels,
			'description' => '',
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_rest' => false,
			'rest_base' => '',
			'has_archive' => true,
			'show_in_menu' => true,
			'exclude_from_search' => true,
			'capability_type' => 'post',
			'map_meta_cap' => true,
			'hierarchical' => false,
			'rewrite' => true,
			'query_var' => true,
			'menu_icon' => 'dashicons-admin-comments',
			'supports' => array( 'title', 'editor', 'thumbnail',  'excerpt', 'author' ),
			'taxonomies' => array(),
		);

		$final_args = wp_parse_args( $this->post_type_args, $args );

		register_post_type( $this->post_type, $final_args );
		
		if( $this->taxonomy  ) {
			
			/**
			 * Taxonomy: Tipi Testimonianza.
			 */
		
			$labels = array(
				"name" => __( "Testimonial Types", 'svbk-shortcakes' ),
				"singular_name" => __( "Testimonial Type", 'svbk-shortcakes' ),
			);
		
			$args = array(
				"label" => __( "Testimonial Types", 'svbk-shortcakes' ),
				"labels" => $labels,
				"public" => true,
				"hierarchical" => true,
				"label" =>  __("Testimonial Types", 'svbk-shortcakes' ),
				"show_ui" => true,
				"show_in_menu" => true,
				"show_in_nav_menus" => true,
				"query_var" => true,
				"rewrite" => array( 'slug' => $this->taxonomy , 'with_front' => true, ),
				"show_admin_column" => false,
				"show_in_rest" => false,
				"rest_base" => "",
				"show_in_quick_edit" => false,
			);
			register_taxonomy( $this->taxonomy  , array( $this->post_type), $args );	
			
		}

	}
	
	public function register_post_fields(){
		
		if( !function_exists('acf_add_local_field_group')) {
			return false;
		}
		
		acf_add_local_field_group(array (
			'key' => 'group_595371cb7c557',
			'title' => __('Testimonials Details', 'svbk-shortcakes'),
			'fields' => array (
				
				array (
					'key' => 'field_595371d1c1b78',
					'label' => __('Role', 'svbk-shortcakes'),
					'name' => 'role',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5a65a2efd42eb',
					'label' => __('Rating', 'svbk-shortcakes'),
					'name' => 'rating',
					'type' => 'radio',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'choices' => array(
						1 => '1',
						2 => '2',
						3 => '3',
						4 => '4',
						5 => '5',
					),
					'allow_null' => 1,
					'other_choice' => 0,
					'save_other_choice' => 0,
					'default_value' => '',
					'layout' => 'horizontal',
					'return_format' => 'value',
				),
				array (
					'key' => 'field_59537080f554b',
					'label' => __('Video', 'svbk-shortcakes'),
					'name' => 'video',
					'type' => 'oembed',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'width' => '',
					'height' => '',
				),				

				
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => $this->post_type,
					),
				),
			),
			'menu_order' => 0,
			'position' => 'acf_after_title',
			'style' => 'seamless',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => 1,
			'description' => '',
		));
		
		
		
	}

	public function scripts(){
		wp_enqueue_script('jquery');
		wp_localize_script( 'jquery', 'ajaxurl',  admin_url( 'admin-ajax.php' ) );
	}

	public function loadMoreFilters() {
		return array(
			'count' => FILTER_VALIDATE_INT,
			'paged' => array(
				'filter' => FILTER_VALIDATE_INT,
				'default' => 0,
			),
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
			'type' => array(
				'label'    => __( 'Select Category', 'svbk-shortcakes' ),
				'attr'     => 'type',
				'type'     => 'term_select',
				'taxonomy' => $this->taxonomy,
				'multiple' => true,
				),	
			'count' => array(
				'label'  => __( 'Reviews Count', 'svbk-shortcakes' ),
				'attr'   => 'count',
				'type'   => 'number',
				'encode' => true,
				'description' => esc_html__( 'How many testimonials to show', 'svbk-shortcakes' ),
				'meta'   => array(
					'placeholder' => self::$defaults['count'],
				),
			),
			'load_more' => array(
				'label'  =>__( 'Show Load More', 'svbk-shortcakes' ),
				'attr'   => 'load_more',
				'type'   => 'checkbox',
				'default' => self::$defaults['load_more'],
				'description' => esc_html__( 'Show the AJAX "Load More" button', 'svbk-shortcakes' ),
			),
			'offset' => array(
				'label'  => __( 'Offset', 'svbk-shortcakes' ),
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

		if ( $attr['load_more'] && ( $attr['offset'] > 0 ) && ( $attr['paged'] > 1 ) ) {
			$attr['offset']  = $attr['offset'] + $attr['count'] * ($attr['paged'] - 1);
		} 

		$query_args = array_merge(
			array(
				'post_type' => $this->post_type,
				'post_status' => 'publish',
				'orderby' => 'date',
				'posts_per_page' => $attr['count'],
				'paged' => $attr['paged'],
				'offset' => $attr['offset'],
			), 
		$this->query_args );
		
		if( $this->taxonomy  && $attr['type'] ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => $this->taxonomy,
					'field'    => 'term_id',
					'terms'    => array( $attr['type']  ),
				),
			);
		}

		// Setting offset to 0 breaks pagination
		if( intval($query_args['offset']) === 0 ) {
			unset( $query_args['offset'] );
		}

		return $query_args;

	}

	public function getClasses( $attr, $term = null ){
		
		$classes = parent::getClasses($attr);
	
		$classes[] = 'testimonials-group-' . $this->post_type;
		
		if( $this->taxonomy && $term && !is_wp_error($term) ) {
			$classes[] = 'testimonials-type-' . $term->slug;
		}
		
		return $classes;
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

			if ( $attr['type'] ) {
				$term =  get_term( $attr['type'], $this->taxonomy );
			}
			
			if ( isset( $term ) && ! is_wp_error( $term ) && ( locate_template( 'template-parts/content-' . $this->post_type . '-' . $term->slug . '.php' ) != '' ) ) {
				$template =  $this->post_type . '-' . $term->slug ;
			}
			
			if ( empty( $template ) && ( locate_template( 'template-parts/content-' . $this->post_type . '.php' ) != '' ) ) {
				$template =  $this->post_type;
			}	

			if ( $container ) {
				$output .= '<aside ' . $this->renderClasses( $this->getClasses( $attr, isset($term) ? $term : null ) ) . ' ' . $html_data_atts . '>';
			}

			if ( ! empty($template) ) {

				ob_start();

				while ( $testimonials->have_posts() ) : $testimonials->the_post();
					get_template_part( 'template-parts/content', $template );
				endwhile;

				$output .= ob_get_contents();
				ob_end_clean();
				
				wp_reset_postdata();
				
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

			if ( $attr['load_more'] && (intval( $attr['paged'] ) < $testimonials->max_num_pages) ) {
				$output .= '<button class="button loadmore">' . __( 'Show more testimonials', 'svbk-shortcakes' ) . '</button>';
				add_action( 'wp_footer', array( $this, 'print_loadmore_script'), 99 );
			}

			if ( $container ) {
				$output .= '</aside>';
			}
		}// End if().

		return $output;

	}
	
	public function print_loadmore_script(){  ?>
		<script>
		(function($){
			$('.testimonials-group-<?php echo esc_attr($this->post_type); ?>').on('click', '.loadmore', function(){
	    		var container = $(this).closest('.testimonials-group');
	      		var data = container.data();
	    		data.paged++;
	    		data.action = '<?php echo 'svbk_' . esc_attr($this->shortcode_id); ?>';
	    		container.addClass('loading');
	    		
	    		$.post( ajaxurl, data, function(response){
	          		$('.loadmore', container).remove();
	          		container
		          		.append(response)
		          		.data(data)
		          		.removeClass('loading');	
	          
	        		$(document.body).trigger( 'post-load' );
		  		});
			});		
		})(jQuery);
		</script>
	<?php
	}

}
