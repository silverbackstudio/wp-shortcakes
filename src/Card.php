<?php

namespace Svbk\WP\Shortcakes;

class Card extends Base {
    
    public $shortcode_id = 'card';
    public $title = 'Card';

    function fields(){
        return array(
    		array(
    			'label'       => esc_html__( 'Image', 'svbk-shortcakes' ),
    			'attr'        => 'head_image',
    			'type'        => 'attachment',
    			'libraryType' => array( 'image' ),
    			'addButton'   => esc_html__( 'Select Image', 'svbk-shortcakes' ),
    			'frameTitle'  => esc_html__( 'Select Image', 'svbk-shortcakes' ),
    		),
    		array(
    			'label'  => esc_html__( 'Title', 'svbk-shortcakes' ),
    			'attr'   => 'title',
    			'type'   => 'text',
    			'encode' => false,
    			'description' => esc_html__( 'This title will replace the Page title', 'svbk-shortcakes' ),
    			'meta'   => array(
    				'placeholder' => esc_html__( 'Insert title', 'svbk-shortcakes' ),
    			),
    		),
    		array(
    			'label'    => esc_html__( 'Enable Markdown', 'svbk-shortcakes' ),
    			'attr'     => 'enable_markdown',
    			'type'     => 'checkbox',
    		),        	
    		array(
    			'label'    => esc_html__( 'Classes', 'svbk-shortcakes' ),
    			'attr'     => 'classes',
    			'type'     => 'text',
    		),        		
    	);
    }
    
    function output( $attr, $content, $shortcode_tag ) {
        
    	$attr = shortcode_atts( array(
    		
    		'head_image' => 0,
    		'title' => '',
    		'enable_markdown' => false,
    		'classes' => '',
    		
    	), $attr, $shortcode_tag );
    
    	$image = wp_get_attachment_image($attr['head_image'], 'post-thumbnail') ?: '<div class="image-placeholder"></div>';
    	
    	$title = $attr['title'] ?: get_the_title($attr[ 'linked_post' ]);
   
    	if($attr['enable_markdown']){
            $content = \Michelf\MarkdownExtra::defaultTransform($content);
    	}
    	
    	$output  = '<div class="card ' . esc_attr( $attr['classes'] ) . '">';
    	$output .= '  <div class="card-header">';
        $output .=      $image;
        $output .=      '<h3>' . $title . '</h3>';
    	$output .= '  </div>';
        $output .= '  <div class="card-content">' . $content . '</div>';
    	$output .= '</div>';
    
    	return $output;
    }
    
}
