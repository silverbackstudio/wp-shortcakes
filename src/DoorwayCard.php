<?php

namespace Svbk\WP\Shortcakes;

class DoorwayCard extends Base {
    
    public $shortcode_id = 'doorway_card';
    public $title = 'Doorway Card';

    function fields(){
        return array(
    		array(
    			'label'       => esc_html__( 'Image', 'turini' ),
    			'attr'        => 'head_image',
    			'type'        => 'attachment',
    			'libraryType' => array( 'image' ),
    			'addButton'   => esc_html__( 'Select Image', 'turini' ),
    			'frameTitle'  => esc_html__( 'Select Image', 'turini' ),
    		),
    		array(
    			'label'  => esc_html__( 'Title', 'turini' ),
    			'attr'   => 'title',
    			'type'   => 'text',
    			'encode' => false,
    			'meta'   => array(
    				'placeholder' => esc_html__( 'Insert title', 'turini' ),
    			),
    		),
    		array(
    			'label'    => esc_html__( 'URL', 'turini' ),
    			'attr'     => 'url',
    			'type'     => 'url'
    		),       		
    		array(
    			'label'    => esc_html__( 'Select Page', 'shortcode-ui-example' ),
    			'attr'     => 'linked_post',
    			'type'     => 'post_select',
    			'query'    => array( 'post_type' => 'page' ),
    			'multiple' => false,
    		),
    		array(
    			'label'  => esc_html__( 'Link Label', 'turini' ),
    			'attr'   => 'link_label',
    			'type'   => 'text',
    			'encode' => true,
    			'meta'   => array(
    				'placeholder' => esc_html__( 'Insert title', 'turini' ),
    			),
    		),   
    		array(
    			'label'    => esc_html__( 'Open in new window', 'turini' ),
    			'attr'     => 'target',
    			'type'     => 'checkbox'
    		),      		
    		array(
    			'label'    => esc_html__( 'Enable Markdown', 'turini' ),
    			'attr'     => 'enable_markdown',
    			'type'     => 'checkbox',
    		),        		
    	);
    }
    
    function output( $attr, $content, $shortcode_tag ) {
        
    	$attr = shortcode_atts( array(
    		
    		'head_image' => 0,
    		'heading' => '',
    		'title' => '',
    		'enable_markdown' => false,
    		'url' => '',
    		'target' => '',
    		'linked_post' => '',
    		'link_label' => '',
    		
    	), $attr, $shortcode_tag );
    
    	$link = $attr['url'] ?: get_permalink($attr[ 'linked_post' ]);
    	
    	$image = wp_get_attachment_image($attr['head_image'], 'post-thumbnail') ?: '<div class="image-placeholder"></div>';
    	
    	$title = $attr['title'] ?: get_the_title($attr[ 'linked_post' ]);
    	
   
    	if($attr['enable_markdown']){
    	    $parsedown = new \Parsedown(); 
    	    $content = $parsedown->text(strip_tags($content));
    	}
    	
    	$output  = '<div class="doorway-card">';
    	$output .= '  <div class="card-header">';
        $output .=      '<a href="'.esc_attr($link).'">' . $image . '</a>';
        $output .=      '<h2><a href="' . esc_attr($link) . '"><span>' . $attr['heading'] . '</span>' . $title . '</a></h2>';
    	$output .= '  </div>';
        $output .= '  <div class="card-content">' . $content . '</div>';
        $output .= '  <a class="action-button" href="' . esc_attr($link) . '">' . $attr[ 'link_label' ] . '</a>';
    	$output .= '</div>';
    
    	return $output;
    }
    
}
