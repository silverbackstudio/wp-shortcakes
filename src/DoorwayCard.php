<?php

namespace Svbk\WP\Shortcakes;

class Doorway_Card {
    
    const SHORTCODE_ID = 'doorway_card';

    static function register(){
        
        $instance = new self;
        
        add_action( 'init', array($instance, 'add') );
        add_action( 'register_shortcode_ui', array($instance, 'register_ui') );
        
        return $instance;
    }
    
    function add(){
        add_shortcode( self::SHORTCODE_ID, array($this, 'output') );
    }        
    
    static function fields(){
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
    	);
    }
    
    static function ui_args(){
    	/*
    	 * Define the Shortcode UI arguments.
    	 */
    	return array(
    		/*
    		 * How the shortcode should be labeled in the UI. Required argument.
    		 */
    		'label' => esc_html__( 'Doorway Card', 'turini' ),
    
    		/*
    		 * Include an icon with your shortcode. Optional.
    		 * Use a dashicon, or full URL to image.
    		 */
    		'listItemImage' => 'dashicons-admin-links',
    
    		/*
    		 * Limit this shortcode UI to specific posts. Optional.
    		 */
    		'post_type' => array( 'page' ),
    
    		/*
    		 * Register UI for the "inner content" of the shortcode. Optional.
    		 * If no UI is registered for the inner content, then any inner content
    		 * data present will be backed-up during editing.
    		 */
    		'inner_content' => array(
    			'label'        => esc_html__( 'Contenuto', 'shortcode-ui-example' ),
    			'description'  => esc_html__( 'Insert content here', 'shortcode-ui-example' ),
    		),
    
    		/*
    		 * Define the UI for attributes of the shortcode. Optional.
    		 *
    		 * See above, to where the the assignment to the $fields variable was made.
    		 */
    		'attrs' => self::fields(),
    	);        
    }
    
    function register_ui(){
    	shortcode_ui_register_for_shortcode( self::SHORTCODE_ID, self::ui_args() );        
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
    			<p><?php echo  $content; ?></p>
    			<a class="action-button" href="<?php echo $link; ?>"><?php echo $attr[ 'link_label' ]; ?></a>
    		</div>
    	</section>
    	<?php
    
    	return ob_get_clean();
    }
    
}
