<?php

namespace Svbk\WP\Shortcakes\Post;

use Svbk\WP\Shortcakes\Shortcake;
use WP_Query;


class Thumb extends Shortcake {
    
    public $defaults = array(    
            'post_id' => ''
    );
    
    public $shortcode_id = 'post_thumb';
    
    public $post_query = array( 
        'post_type' => 'post', 
        'post_status' => 'publish' 
    );
    
    public $template = 'template-parts/thumb';

    public function title(){
        return __('Post Thumbnail', 'svbk-shortcakes');
    }

    function fields(){
        return array(
            'post_id' => array(
    			'label'    => esc_html__( 'Select Post', 'svbk-shortcakes' ),
    			'attr'     => 'post_id',
    			'type'     => 'post_select',
    			'query'    => $this->post_query,
    			'multiple' => false
    		)
    	);
    }
    
    protected function getQueryArgs($attr){

    	return array_merge( array(
    	    'p' => $attr[ 'post_id' ]
    	), $this->post_query );
    	
    }    
    
    function renderOutput($attr, $content, $shortcode_tag){
    
    	$attr = $this->shortcode_atts( $this->defaults, $attr, $shortcode_tag );      
    
        $output = '';
    	
    	$post_query = new WP_Query( $this->getQueryArgs($attr) );
    	
    	if( $post_query->have_posts() ) {
    	
            ob_start();
            
            while(  $post_query->have_posts() ): $post_query->the_post();
        	    get_template_part( $this->template, get_post_type() );
            endwhile;
            
            $output .= ob_get_contents();
            ob_end_clean();                
            
        } 
        
		wp_reset_query();
		wp_reset_postdata();   
    	
    	return $output;
        
    }    
    
}
