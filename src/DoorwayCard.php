<?php

namespace Svbk\WP\Shortcakes;

class DoorwayCard extends Base {
    
    public static $defaults = array(
    		'head_image' => 0,
    		'title' => '',
    		'enable_markdown' => false,
    		'url' => '',
    		'target' => '',
    		'linked_post' => '',
    		'link_label' => '',
    		'classes' => '',
    );
    
    public $shortcode_id = 'doorway_card';
    public $title = 'Doorway Card';
    public $post_query = array( 'post_type' => 'page' );
    public $image_size = 'post-thumbnail';

    function fields(){
        return array(
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
    			'label'       => esc_html__( 'Image', 'svbk-shortcakes' ),
    			'attr'        => 'head_image',
    			'type'        => 'attachment',
    			'libraryType' => array( 'image' ),
    			'addButton'   => esc_html__( 'Select Image', 'svbk-shortcakes' ),
    			'frameTitle'  => esc_html__( 'Select Image', 'svbk-shortcakes' ),
    		),
            array(
    			'label'         => esc_html__( 'Image Size', 'svbk-shortcakes' ),
    			'attr'          => 'image_size',
    			'type'          => 'select',
    			'options'       => array(
    				array( 'value' => 'thumbnail', 'label' => 'Thumbnail' ),
    				array( 'value' => 'medium', 'label' => 'Medium'  ),
    				array( 'value' => 'large', 'label' => 'Large' ),
    				array( 'value' => 'full', 'label' => 'Full'  ),
    			),
    		),      		
    		array(
    			'label'    => esc_html__( 'Select Page', 'svbk-shortcakes' ),
    			'attr'     => 'linked_post',
    			'type'     => 'post_select',
    			'query'    => $this->post_query,
    			'multiple' => false,
    		),
    		array(
    			'label'    => esc_html__( 'URL', 'svbk-shortcakes' ),
    			'attr'     => 'url',
    			'type'     => 'url',
				'description' => esc_html__( 'This URL will be used instead of Page permalink.', 'svbk-shortcakes' ),
    		),       		
    		array(
    			'label'  => esc_html__( 'Button Label', 'svbk-shortcakes' ),
    			'attr'   => 'link_label',
    			'type'   => 'text',
    			'encode' => false,
    			'meta'   => array(
    				'placeholder' => esc_html__( 'Insert button label', 'svbk-shortcakes' ),
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
    			'label'    => esc_html__( 'Custom Classes', 'svbk-shortcakes' ),
    			'attr'     => 'classes',
    			'type'     => 'text',
    		),
    	);
    }
    
    function renderOutput($attr, $content, $shortcode_tag){
    
    	$attr = shortcode_atts( self::$defaults, $attr, $shortcode_tag );    
    
    	$link = $attr['url'] ?: get_permalink($attr[ 'linked_post' ]);
    	$image = wp_get_attachment_image($attr['head_image'], $this->image_size) ?: '<div class="image-placeholder"></div>';

    	$title = $attr['title'] ?: get_the_title($attr[ 'linked_post' ]);
    	
    	$target = $attr['target'] ? ' target="_blank" ' : '';
   
    	if($attr['enable_markdown']){
            $content = str_replace(array("\n", '<p>'), "", $content);
            $content = str_replace(array("<br />", "<br>", "<br/>"), "\n", $content);
            $content = str_replace("</p>", "\n\n", $content);      	    
    	    
            $md = new \Michelf\Markdown;
            $content = $md->transform($content);
    	}
    	
    	$output['wrapperBegin']  = '<div class="doorway-card ' . esc_attr( trim($attr['classes']) ) . '">';
    	
    	if($title){
    	    $output['headerBegin'] = '<div class="card-header">';
            $output['title'] = sprintf( $link ? '<h2 class="card-title"><a href="%2$s" %3$s >%1$s</a></h2>':'<h2 class="card-title">%1$s</h2>', $title, esc_attr($link), $target) ;
    	    $output['headerEnd'] = '</div>';
    	}
    	
    	if($image){
    	    $output['image'] = sprintf( $link ? '<a href="%2$s" %3$s >%1$s</a>':'%1$s', $image, esc_attr($link), $target);
    	}
    	
    	$output['contentBegin'] = '<div class="card-text">';
        $output['content'] = '  <div class="card-content">' . $content . '</div>';
        
        if($link && $attr[ 'link_label' ]){
            $output['button'] = '  <a class="action-button" href="' . esc_attr($link) . '" ' . $target . ' >' . $attr[ 'link_label' ] . '</a>';
        }
    	
    	$output['contentEnd'] = '</div>';
    	$output['wrapperEnd'] = '</div>';  
    	
    	return $output;
        
    }    
    
    function output( $attr, $content, $shortcode_tag ) {
        return join('', $this->renderOutput($attr, $content, $shortcode_tag));
    }  
    
}
