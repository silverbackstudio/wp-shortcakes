<?php

namespace Svbk\WP\Shortcakes;

abstract class Base {
    
    public $shortcode_id = 'shortcake_base';
    public $title = 'Base Shortcode';
    public $post_types;

    static function register($post_types=array('page')){
        
        $class = get_called_class();
        
        $instance = new $class;
        
        $instance->post_type = $post_types;
        
        add_action( 'init', array($instance, 'add') );
        add_action( 'register_shortcode_ui', array($instance, 'register_ui') );
        
        return $instance;
    }
    
    function add(){
        add_shortcode( $this->shortcode_id, array($this, 'output') );
    }        
    
    abstract function fields();
    
    function ui_args(){
    	/*
    	 * Define the Shortcode UI arguments.
    	 */
    	return array(
    		/*
    		 * How the shortcode should be labeled in the UI. Required argument.
    		 */
    		'label' => $this->title,
    
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
    		'attrs' => $this->fields(),
    	);        
    }
    
    function register_ui(){
    	shortcode_ui_register_for_shortcode( $this->shortcode_id, $this->ui_args() );        
    }
    
    abstract function output( $attr, $content, $shortcode_tag );
    
}
