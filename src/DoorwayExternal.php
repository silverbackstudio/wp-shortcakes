<?php

namespace Svbk\WP\Shortcakes;

class DoorwayExternal extends Base {
    
    public $shortcode_id = 'doorway_ext';
    public $title = 'Doorway External';

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
    			'label'  => esc_html__( 'Link Label', 'turini' ),
    			'attr'   => 'link_label',
    			'type'   => 'text',
    			'encode' => true,
    			'meta'   => array(
    				'placeholder' => esc_html__( 'Insert title', 'turini' ),
    			),
    		),    		
    	);
    }
    
    function output( $attr, $content, $shortcode_tag ) {
    	$attr = shortcode_atts( array(
    		'head_image' => 0,
    		'title' => '',
    		'url' => '',
    		'link_label' => '',
    	), $attr, $shortcode_tag );
    
    	// Shortcode callbacks must return content, hence, output buffering here.
    	ob_start();
    	
    	$link = esc_attr($attr[ 'url' ]);
    	
    	?>
    	<div class="doorway-card">
    	    <div class="card-header">
    	        <a href="<?php echo $link; ?>"><?php echo wp_get_attachment_image($attr['head_image'], 'post-thumbnail') ?: '<div class="image-placeholder"></div>' ; ?></a>
    	        <?php if( $attr['title'] ): ?>
    	        <h2><a href="<?php echo $link; ?>"><?php echo $attr['title']; ?></a></h2>
    	        <?php endif; ?>
    	    </div>
    		<div class="card-content"><?php $parsedown = new \Parsedown(); echo $parsedown->text($content); ?></div>
    		<a class="action-button" href="<?php echo $link; ?>"><?php echo $attr[ 'link_label' ]; ?></a>
    	</div>
    	<?php
    
    	return ob_get_clean();
    }
    
}
