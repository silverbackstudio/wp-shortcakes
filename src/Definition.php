<?php

namespace Svbk\WP\Shortcakes;

class Definition extends Base {
    
    public $shortcode_id = 'dfn';
    public $title = 'Definition';

    public $footnotes = array();


    static function  register($post_types=array('page')){
        
        $self = parent::register($post_types);
        
        add_filter('the_content', array($self, 'inline_shortcode'), 1);
        add_filter('the_content', array($self, 'add_footnotes'), 99);
    
    }

    function fields(){
        
        return array(
    		array(
    			'label'    => esc_html__( 'Select Definition', 'svbk-shortcakes' ),
    			'attr'     => 'definition_post',
    			'type'     => 'post_select',
    			'query'    => array( 'post_type' => 'definition' ),
    			'multiple' => false,
    		),    
    		array(
    			'label'    => esc_html__( 'Abbreviation', 'svbk-shortcakes' ),
    			'attr'     => 'abbr',
    			'type'     => 'checkbox'
    		),      		
    	);
    	
    }
    
    function add_footnotes($content){
        
        if(empty($this->footnotes)) {
            return $content;
        }
        
        $output = '<aside id="footnotes"><dl>';
        
        $dfns = new \WP_Query( array('post_type'=> 'definition', 'include' => $this->footnotes) );
        
        while( $dfns->have_posts() ): $dfns->the_post();
        	$output .= '<dt id="dfn-' . get_the_ID() . '" ><dfn>' . get_the_title() . '</dfn></dt>';
        	$output .= '<dd>' . get_the_content() . '</dd>';
        endwhile;
        
        $output .= '</dl></aside>';
        
        $this->footnotes = array();
        
        wp_reset_query();
        wp_reset_postdata();
        
        return $content . $output;
    }
    
    function inline_shortcode($content){
        
        return $content;
        
        
    }
    
    function output( $attr, $content, $shortcode_tag ) {
    	$attr = shortcode_atts( array(
    		'definition_post' => false,
    		'abbr' => false,
    	), $attr, $shortcode_tag );
    
        $output = '';
    
        if($attr['definition_post']){
         
            $output .= '<a class="sidenote" href="#dfn-' . esc_attr($attr['definition_post']) . '">' . $content . '</a>';
            
            $this->footnotes[] = $attr['definition_post'];
        }
        
        return $output;
        
    }
    
}
