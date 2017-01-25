<?php

namespace Svbk\WP\Shortcakes;

class DoorwayCard extends Base {
    
    public $shortcode_id = 'doorway_card';
    public $title = 'Doorway Card';

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
    			'label'    => esc_html__( 'URL', 'svbk-shortcakes' ),
    			'attr'     => 'url',
    			'type'     => 'url',
				'description' => esc_html__( 'This URL will be used instead of Page permalink.', 'svbk-shortcakes' ),

    		),       		
    		array(
    			'label'    => esc_html__( 'Select Page', 'svbk-shortcakes' ),
    			'attr'     => 'linked_post',
    			'type'     => 'post_select',
    			'query'    => array( 'post_type' => 'page' ),
    			'multiple' => false,
    		),
    		array(
    			'label'  => esc_html__( 'Link Label', 'svbk-shortcakes' ),
    			'attr'   => 'link_label',
    			'type'   => 'text',
    			'encode' => false,
    			'meta'   => array(
    				'placeholder' => esc_html__( 'Insert title', 'svbk-shortcakes' ),
    			),
    		),   
    		array(
    			'label'    => esc_html__( 'Open in new window', 'svbk-shortcakes' ),
    			'attr'     => 'target',
    			'type'     => 'checkbox'
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
    		'heading' => '',
    		'title' => '',
    		'enable_markdown' => false,
    		'url' => '',
    		'target' => '',
    		'linked_post' => '',
    		'link_label' => '',
    		'classes' => '',
    		
    	), $attr, $shortcode_tag );
    
    	$link = $attr['url'] ?: get_permalink($attr[ 'linked_post' ]);
    	
    	$image = wp_get_attachment_image($attr['head_image'], 'post-thumbnail') ?: '<div class="image-placeholder"></div>';
    	
    	$title = $attr['title'] ?: get_the_title($attr[ 'linked_post' ]);
    	
    	$target = $attr['target'] ? ' target="_blank" ' : '';
   
    	if($attr['enable_markdown']){
    	    $content = \Michelf\MarkdownExtra::defaultTransform($content);
    	}
    	
    	$output  = '<div class="doorway-card ' . esc_attr( $attr['classes'] ) . '">';
    	$output .= '  <div class="card-header">';
        $output .=      '<h2><a href="' . esc_attr($link) . '" ' . $target . ' ><span>' . $attr['heading'] . '</span>' . $title . '</a></h2>';
    	$output .= '  </div>';
    	$output .= '  <a href="' . esc_attr($link) . '" ' . $target . ' >' . $image . '</a>';
    	$output .= '  <div class="card-text">';
        $output .= '    <div class="card-content">' . $content . '</div>';
        $output .= '    <a class="action-button" href="' . esc_attr($link) . '" ' . $target . ' >' . $attr[ 'link_label' ] . '</a>';
    	$output .= '  </div>';
    	$output .= '</div>';
    
    	return $output;
    }
    
}
