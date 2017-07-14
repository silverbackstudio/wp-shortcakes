<?php 

namespace Svbk\WP\Shortcakes\Post;

use Svbk\WP\Shortcakes\Shortcake;

class Archive extends Shortcake {
    
    public $shortcode_id = 'post_archive';
    public $template_base = 'template-parts/content';
    public $classes = array('post-archive', 'post-list' );  
    public $icon = 'dashicons-schedule';

    public static $defaults = array(
    		'post_type' => 'post',
    		'posts_per_page'=> 6
    );

    public function title(){
        return __('Post Archive', 'svbk-shortcakes');
    }

    function fields(){

        $custom_post_types = wp_list_pluck( get_post_types( array('public' => true, '_builtin' => false) , 'objects' ), 'label', 'name');

        //array_walk($custom_post_types, array(__CLASS__, 'castSelect' ));

        return array(
    		array(
    			'label'    => __( 'Select Post Type', 'svbk-shortcakes' ),
    			'attr'     => 'post_type',
    			'type'     => 'select',
    			'options' => $custom_post_types,
    			'multiple' => false,
    		),
    		array(
    			'label'    => __( 'Post Count', 'svbk-shortcakes' ),
    			'attr'     => 'posts_per_page',
    			'type'     => 'number',
    		),    		
        );
    }
    
    function ui_args(){
        $ui_args = parent::ui_args();

        unset($ui_args['inner_content']);
    
        return $ui_args;
    }    

    protected function getQueryArgs($attr){
        
        $args = array(
        	'post_type' => $attr['post_type'],
        	'posts_per_page' => (int)$attr['posts_per_page'],
        );
        
        if(get_query_var('paged')>1){
            $args['paged'] = (int)get_query_var('paged');
        }
        
        return $args;
    }
    
    function output( $attr, $content, $shortcode_tag ) {
        
    	$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );
    
        $output = '';

        if( !post_type_exists($attr['post_type']) ){
            return __('Post type dosen\'t exists', 'svbk-shortcodes');
        }

        query_posts( $this->getQueryArgs($attr) );

        if ( have_posts() ) : 

            $output .= '<div class="' . join(' ', $this->getClasses($attr) ) . '">';
            ob_start();
            
        	while ( have_posts() ) : the_post();
        	    get_template_part($this->template_base, get_post_type() );
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