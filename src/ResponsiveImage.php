<?php

namespace Svbk\WP\Shortcakes;

class ResponsiveImage extends Shortcake {
    
    public $shortcode_id = 'responsive_image';
    public static $defualts = array(
	    'image_id' => '',
	    'size' => '',
	);

    public function title(){
        return __('Responsive Image', 'svbk-shortcakes');
    }    

    function fields(){
        
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
    			'label'       => esc_html__( 'Size', 'shortcode-ui-example' ),
    			'attr'        => 'size',
    			'type'        => 'select',
    			'options'     => array_combine(get_intermediate_image_sizes(),get_intermediate_image_sizes())
    		),    		
    	);
    }
    
    function output( $attr, $content, $shortcode_tag ) {
    	$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

        return wp_get_attachment_image( $attr['image_id'], $attr['size'] );
        
    }
    
}
