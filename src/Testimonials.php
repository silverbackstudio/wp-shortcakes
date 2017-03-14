<?php namespace Svbk\WP\Shortcakes;

use WP_Query;

// Add This JS to theme
// $('.testimonials').on('click', '.loadmore', function(){
    
//     var container = $(this).closest('.testimonials');
//     var data = container.data();
    
//     data.paged++; 
//     data.action = 'testimonials';
    
//     container.addClass('loading');
    
//     $.post( ajaxurl, data, function(response){

//         $('.loadmore', container).remove();
        
//         container
//             .append(response)
//             .data(data)
//             .removeClass('loading');
//     });
// });

class Testimonials extends Shortcake {
    
    public $shortcode_id = 'testimonials';
    public $post_type = 'testimonials';
    public $register_cpt = true;

    public static $defaults = array(
        'count' => 2,
    	'paged' => 1,
    	'load_more' => 1,
    	'offset' => 0,
    );

    public function title(){
        return __('Testimonials', 'svbk-shortcakes');
    } 

    static function register($options=array()){
        
        $instance = parent::register($options);
        
        if($instance->register_cpt){
            add_action( 'init', array($instance, 'register_cpts') );
        }
        
        add_action( 'wp_ajax_testimonials', array($instance, 'loadMore') );
        add_action( 'wp_ajax_nopriv_testimonials', array($instance, 'loadMore') );
        
        return $instance;
    }

    public function register_cpts() {

    	$labels = array(
    		"name" => __( 'Testimonials', 'svbk-shortcakes' ),
    		"singular_name" => __( 'Testimonial', 'svbk-shortcakes' ),
    	);
    
    	$args = array(
    		"label" => __( 'Testimonials', 'svbk-shortcakes' ),
    		"labels" => $labels,
    		"description" => "",
    		"public" => true,
    		"publicly_queryable" => true,
    		"show_ui" => true,
    		"show_in_rest" => false,
    		"rest_base" => "",
    		"has_archive" => true,
    		"show_in_menu" => true,
    		"exclude_from_search" => true,
    		"capability_type" => "post",
    		"map_meta_cap" => true,
    		"hierarchical" => false,
    		"rewrite" => true,
    		"query_var" => true,
    		"menu_icon" => "dashicons-admin-comments",
    		"supports" => array( "title", "editor", "thumbnail",  "excerpt" ),
    		"taxonomies" => array(  ),
    	);
    
    	register_post_type( $this->post_type, $args );
    	
    }
    
    public function loadMore(){
        
        $data = $this->shortcode_atts( self::$defaults, $_POST );  
        
        echo $this->output( $data, '', $this->shortcode_id, false );
        exit;
    }

    function fields(){
        return array(        
        		array(
        			'label'  => esc_html__( 'Reviews Count', 'svbk-shortcakes' ),
        			'attr'   => 'count',
        			'type'   => 'number',
        			'encode' => true,
        			'description' => esc_html__( 'How many testimonials to show', 'svbk-shortcakes' ),
        			'meta'   => array(
        				'placeholder' =>  self::$defaults['count'],
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
        		)           		
            );
    }

    protected function getQueryArgs($attr){

    	return array(
    	    'post_type' => $this->post_type,
    	    'post_status' => 'publish',
    	    'posts_per_page' => $attr['count'],
    	    'paged' => $attr['paged'],
    	    'offset' => $attr['offset'],
    	);
    	
    }
    
    public function output( $attr, $content, $shortcode_tag, $container=true ) {
        
        $output = '';
        
        $attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );         
        
        $testimonials = new WP_Query( $this->getQueryArgs($attr) );
        
        if($testimonials->have_posts()){
            
            $html_data_atts = '';
            
            foreach($attr as $key=>$val){
                $html_data_atts .= 'data-'.esc_html($key).'="'.esc_attr($val).'" '; 
            }
            
            if($container){
                $output .= '<aside class="testimonials" '.$html_data_atts.'>';
            }
            
            while( $testimonials->have_posts() ): $testimonials->next_post();
                $output .= '<blockquote class="testimonial">';
                $output .= apply_filters( 'the_content', $testimonials->post->post_content );
                $output .= '<footer class="author">';
                $output .=  '<cite class="name">'. get_the_title( $testimonials->post ) .'</cite>';
                $output .=  '<div class="picture">' . get_the_post_thumbnail( $testimonials->post->ID, 'small' ) . '</div>';
                $output .=  '<span class="role">'. get_field( 'author_role', $testimonials->post->ID ) .'</span>';
                $output .= '</footer>';
                $output .= '</blockquote>';
            endwhile;
            
            if(intval($attr['paged']) < $testimonials->max_num_pages){
                $output .= '<button class="button loadmore">'.__('Show more testimonials', 'svbk-shorcakes').'</button>';
            }
            
            if ($container) {
            $output .= '</aside>';
            }
        }
    	
    	return $output;
    	
    }

}