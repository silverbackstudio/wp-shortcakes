<?php

namespace Svbk\WP\Shortcakes;

class DoorwayMD extends Base {
    
    public $shortcode_id = 'doorway_md';
    public $title = 'Doorway Markdown';
    
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
    				'placeholder' => esc_html__( 'A fancy title', 'turini' ),
    			),
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
    				'placeholder' => esc_html__( 'READ MORE', 'turini' ),
    			),
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
    		'linked_post' => '',
    		'link_label' => '',
    	), $attr, $shortcode_tag );
    
    	// Shortcode callbacks must return content, hence, output buffering here.
    	ob_start();
    	
    	$link = get_permalink($attr[ 'linked_post' ]);
    	
    	?>
    	<section class="doorway-card">
    	    <header class="card-header">
    	        <a href="<?php echo $link; ?>"><?php echo wp_get_attachment_image($attr['head_image'], 'post-thumbnail'); ?></a>
    	        <h2><a href="<?php echo $link; ?>"><span><?php echo $attr['heading']; ?></span> <?php echo $attr['title']; ?></a></h2>
    	    </header>
    		<div class="card-content">
    			<p><?php $parsedown = new \Parsedown(); echo $parsedown->text($content); ?></p>
    			<a class="action-button" href="<?php echo $link; ?>"><?php echo $attr[ 'link_label' ]; ?></a>
    		</div>
    	</section>
    	<?php
    
    	return ob_get_clean();
    }
    
}
