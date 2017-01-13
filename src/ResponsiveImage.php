<?php

namespace Svbk\WP\Shortcakes;

class ResponsiveImage extends Base {
    
    public $shortcode_id = 'responsive_image';
    public $title = 'Responsive Image';

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
    	$attr = shortcode_atts( array(
    		'image_id' => '',
    		'size' => '',
    		), $attr, $shortcode_tag );

        return wp_get_attachment_image( $attr['image_id'], $attr['size'] );
        
    }
    
}
