<?php

namespace Svbk\WP\Shortcakes;

class Index extends Base {
    
    public $shortcode_id = 'indexed_content';
    public $title = 'Indexed Content';
    public $sections = array();
    public $current_index = array();

    function fields(){
        
        return array(
    		array(
    			'label'  => esc_html__( 'Title', 'svbk-shortcakes' ),
    			'attr'   => 'title',
    			'type'   => 'text'
    			),
    		array(
    			'label'  => esc_html__( 'Slug', 'svbk-shortcakes' ),
    			'attr'   => 'slug',
    			'type'   => 'text',
    			'encode' => false
    		),
    		array(
    			'label'  => esc_html__( 'Index group', 'svbk-shortcakes' ),
    			'attr'   => 'group',
    			'type'   => 'text'
    		)    		
    	);
    	
    }
    
    function add(){
        
        parent::add();
        
        add_shortcode('index', array($this, 'index_shortcode'));
        add_shortcode('index-real', array($this, 'index_shortcode'));
        
        add_filter('the_content', 'do_shortcode', 12);
    }
    
    
    function render_index($sections){
        
        $output = '<ol id="index" class="content-index">';
        
        foreach($sections as $section){
            $output .= sprintf('<li><a class="anchor" href="#%s">%s</a></li>', $section['slug'], $section['title']);
        }
        
        $output .=  '</ol>';
        
        return $output;
    }
    
    function replace_index($content){
        
        $this->current_index = 0;
        
        $prepend = $this->render_index();
        
        $this->sections = array();
        
        return preg_replace('/\[index([^\]])?\]/', $prepend, $content, 1);
    }
    
    function index_shortcode($attr, $content, $shortcode_tag){
        
        $attr = shortcode_atts( array(
        	      'group' => 'default',
        ), $attr );    
        
        if('index' == $shortcode_tag){
            
            $new_tag = '[index-real ';
            
            foreach($attr as $key=>$value){
                $new_tag .= $key.'="'.$value.'" ';
            }
            
            $new_tag .= ']';
            
            return $new_tag;
        }
        
        
        if( ! isset($this->sections[$attr['group']]) ){
            return '';
        }
        
        $sections = &$this->sections[$attr['group']];
        
        $prepend = $this->render_index($sections);
        
        return $prepend;
    }
        
    
    function output( $attr, $content, $shortcode_tag ) {
        
    	$attr = shortcode_atts( array(
    		'title' => '',
    		'slug' => sanitize_title(isset($attr['title']) ? $attr['title'] : uniqid()),
    		'group' => 'default',
    		), $attr, $shortcode_tag );

        
        $output = apply_filters('svbk-shortcake-image', '', $attr, $content, $shortcode_tag);
        
        if($output){
            return $output;
        }

        $sections = &$this->sections[$attr['group']];
        $index = &$this->current_index[$attr['group']];
        
        $index++;
        
        $sections[$index] = array(
            'title' => $attr['title'],
            'slug' => $attr['slug']
        );
        
        $template = '<section id="%1$s" class="index-section">'
                        .'<header class="section-header">'
                            .'<h3 class="section-title" ><span class="index-counter">%4$s</span>&nbsp;%2$s</h3>'
                            .'<a class="anchor to-top" href="#index" title="' . __('Go to index', 'svbk-shortcakes') . '">&uarr;</a>'
                            .'<div class="section-content">%3$s</div>'
                    .'</section>';
    
        $output .= sprintf( $template, $attr['slug'], esc_html($attr['title']), $content, $index);

        return $output;
        
    }
    
}