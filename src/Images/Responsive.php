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
    			'label'       => esc_html__( 'Size', 'svbk-shortcakes' ),
    			'attr'        => 'size',
    			'type'        => 'select',
    			'options'     => $sizes
    		),    		
    	);
    }
    
    function renderOutput( $attr, $content, $shortcode_tag ) {
    	$attr = $this->shortcode_atts( $this->defaults, $attr, $shortcode_tag );

        $output['wrapperBegin'] = '<figure class="'.join(' ', $this->classes).'">';
        $output['image'] = wp_get_attachment_image( $attr['image_id'], $attr['size'] );
        
        if($content){
            $output['caption'] = '<figcaption class="caption">' .$content .'</figcaption>';
        }
        $output['wrapperEnd'] = '</figure>';
        
        return $output;
        
    }
    
}