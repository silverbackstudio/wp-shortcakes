<?php

namespace Svbk\WP\Shortcakes;

class PreviewCard extends Shortcake {
    
    public static $defaults = array(
    		'head_image' => 0,
    		'title' => '',
    		'enable_markdown' => false,
    		'url' => '',
    		'target' => '',
    		'link_label' => '',
    		'classes' => '',
    );
    
    public $shortcode_id = 'preview_card';
    public $image_size = 'post-thumbnail';

    public static $renderOrder = array(
    	'wrapperBegin',
        'headerBegin',
        'title',
        'headerEnd',
        'image',
        'contentBegin',
        'content',
        'button',
        'contentEnd',
        'wrapperEnd'
    );

    public function title(){
        return __('Preview Card', 'svbk-shortcakes');
    }

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
    
    protected function getLink($attr){
        return $attr['url'];
    }
    
    protected function getImage($attr){
        return wp_get_attachment_image($attr['head_image'], $this->image_size) ?: '<div class="image-placeholder"></div>';
    }  
    
    protected function getTitle($attr){
        return $attr['title'];
    }      
    
    protected function parseMarkdown($content){
            $content = str_replace(array("\n", '<p>'), "", $content);
            $content = str_replace(array("<br />", "<br>", "<br/>"), "\n", $content);
            $content = str_replace("</p>", "\n\n", $content);      	    
    	    
            $md = new \Michelf\Markdown;
            return $md->transform($content);        
    }
    
    public static function setRenderPosition($parts, $position){
        
        $parts = (array)$parts;
        
        self::$renderOrder = array_diff(self::$renderOrder, $parts);
        
        array_splice( self::$renderOrder, $position, 0, $parts ); 
    }


    function renderOutput($attr, $content, $shortcode_tag){
    
    	$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );      
    
    	$link = $this->getLink($attr);
    	$image = $this->getImage($attr);
    	$title = $this->getTitle($attr);
    
    	$target = $attr['target'] ? ' target="_blank" ' : '';
   
    	if($attr['enable_markdown']){
            $content = $this->parseMarkdown($content);
    	}
    	
    	$output['wrapperBegin']  = '<div class="post-thumb preview-card ' . esc_attr( trim($attr['classes']) ) . '">';
    	
    	if($title){
    	    $output['headerBegin'] = '<div class="entry-header">';
            $output['title'] = sprintf( $link ? '<h2 class="entry-title"><a href="%2$s" %3$s >%1$s</a></h2>':'<h2 class="entry-title">%1$s</h2>', $title, esc_attr($link), $target) ;
    	    $output['headerEnd'] = '</div>';
    	}
    	
    	if($image){
    	    $output['image'] = sprintf( $link ? '<a href="%2$s" %3$s >%1$s</a>':'%1$s', $image, esc_attr($link), $target);
    	}
    	
    	$output['contentBegin'] = '<div class="card-text">';
        $output['content'] = '  <div class="entry-content">' . $content . '</div>';
        
        if($link && $attr[ 'link_label' ]){
            $output['button'] = '  <a class="readmore" href="' . esc_attr($link) . '" ' . $target . ' >' . $attr[ 'link_label' ] . '</a>';
        }
    	
    	$output['contentEnd'] = '</div>';
    	$output['wrapperEnd'] = '</div>';  
    	
    	return $output;
        
    }    
    
    function output( $attr, $content, $shortcode_tag ) {
        
        $output = $this->renderOutput($attr, $content, $shortcode_tag);
        
        if(is_array($output)){
        
            $output_html = '';
            $parts = self::$renderOrder;
            
            foreach($parts as $part){
                if(array_key_exists($part, $output)){
                    $output_html .= $output[$part];
                }
            }
            
            $output = $output_html;
        }
        
        return $output;
    }  
    
}
