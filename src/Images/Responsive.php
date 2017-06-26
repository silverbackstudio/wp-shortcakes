<?php

namespace Svbk\WP\Shortcakes\Images;

use Svbk\WP\Shortcakes\Shortcake;

class Responsive extends Shortcake {
    
    public $shortcode_id = 'responsive_image';
    
    public $classes = array('content-image');
    
    public $renderOrder = array(
        'wrapperBegin',
        'image',
        'caption',
        'wrapperEnd'
    );
    
    public $defaults = array(
	    'image_id' => '',
        'alignment' => array(),
        'class' => array(),	    
	    'size' => '',
	);

    public function title(){
        return __('Responsive Image', 'svbk-shortcakes');
    }    

    function fields(){
        
        $sizes = array_combine( get_intermediate_image_sizes(), get_intermediate_image_sizes() );
        
        $sizes = array_merge($sizes, apply_filters( 'image_size_names_choose', array(
            'thumbnail' => __( 'Thumbnail' ),
            'medium'    => __( 'Medium' ),
            'large'     => __( 'Large' ),
            'full'      => __( 'Full Size' )
            ) 
        ) );
        
        return array(
    		array(
    			'label'       => esc_html__( 'Image', 'svbk-shortcakes' ),
    			'attr'        => 'image_id',
    			'type'        => 'attachment',
    			'libraryType' => array( 'image' ),
    			'addButton'   => esc_html__( 'Select Image', 'svbk-shortcakes' ),
    			'frameTitle'  => esc_html__( 'Select Image', 'svbk-shortcakes' ),
    		),
    		array(
    			'label'       => esc_html__( 'Alignment', 'svbk-shortcakes' ),
    			'attr'        => 'alignment',
    			'type'        => 'select',
    			'options'     => array(
    				array( 'value' => '', 'label' => esc_html__( 'None', 'svbk-shortcakes' ) ),
    				array( 'value' => 'left', 'label' => esc_html__( 'Align Left', 'svbk-shortcakes' ) ),
    				array( 'value' => 'center', 'label' => esc_html__( 'Align Center', 'svbk-shortcakes' ) ),
    				array( 'value' => 'right', 'label' => esc_html__( 'Align Right', 'svbk-shortcakes' ) ),
    			),
    		),    		
    		array(
    			'label'       => esc_html__( 'Size', 'svbk-shortcakes' ),
    			'attr'        => 'size',
    			'type'        => 'select',
    			'options'     => $sizes
    		),    
        	array(
        	    'label'  => esc_html__( 'Classes', 'svbk-shortcakes' ),
        		'attr'   => 'class',
        		'type'   => 'text',
        	),       		
    	);
    }
    
    protected function getClasses($attr){
    
        return array_merge( 
            (array) $this->classes, 
            $attr['alignment'] ? array( 'align' . $attr['alignment'] ) : array(),
            $attr['size'] ? array( 'size-' . $attr['size'] ) : array(),
            $attr['image_id'] ? array( 'wp-image-' . $attr['image_id'] ) : array(),
            (array) $attr['class']
        );
    
    }
    
    function renderOutput( $attr, $content, $shortcode_tag ) {
    	$attr = $this->shortcode_atts( $this->defaults, $attr, $shortcode_tag );

        $output['wrapperBegin'] = '<figure class="' . esc_attr( join( ' ', $this->getClasses($attr) ) ) . '">';
        $output['image'] = wp_get_attachment_image( $attr['image_id'], $attr['size'] );
        
        if($content){
            $output['caption'] = '<figcaption class="caption">' .$content .'</figcaption>';
        }
        $output['wrapperEnd'] = '</figure>';
        
        return $output;
        
    }
    
}