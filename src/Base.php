<?php

namespace Svbk\WP\Shortcakes;

add_action( 'after_setup_theme', __NAMESPACE__.'\\Base::load_texdomain' );

abstract class Base {
    
    public $shortcode_id = 'shortcake_base';
    public $title = 'Base Shortcode';
    public $post_types;


    static function register($post_types=array('page')){
        
        $class = get_called_class();
        
        $instance = new $class;
        $instance->post_types = $post_types;
        
        add_action( 'init', array($instance, 'add'), 12 );
        add_action( 'register_shortcode_ui', array($instance, 'register_ui') );
        add_action( 'after_setup_theme', array(__CLASS__, 'load_texdomain') );

        return $instance;
    }

    public static function load_texdomain(){
        load_textdomain( 'svbk-shortcakes', dirname(__DIR__).'/languages/svbk-shortcakes' . '-' . get_locale() . '.mo'   ); 
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
    		'post_type' => $this->post_types,
    
    		/*
    		 * Register UI for the "inner content" of the shortcode. Optional.
    		 * If no UI is registered for the inner content, then any inner content
    		 * data present will be backed-up during editing.
    		 */
    		'inner_content' => array(
    			'label'        => esc_html__( 'Content', 'svbk-shortcakes' ),
    			'description'  => esc_html__( 'Insert content here', 'svbk-shortcakes' ),
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
