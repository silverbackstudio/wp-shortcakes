<?php

namespace Svbk\WP\Shortcakes;

add_action( 'after_setup_theme', __NAMESPACE__.'\\Shortcake::load_texdomain' );

abstract class Shortcake {
    
    public $shortcode_id = 'shortcake_base';
    public $title = '';
    public $icon = 'dashicons-admin-links';
    public $attach_to = array('page');
    
    public $renderOrder = array(
        'content',
    );    

    public function title(){
        return __('Base Shortcode', 'svbk-shortcodes');
    }

    public function __construct($properties){
        foreach($properties as $properties => $value){
            if(property_exists($this, $properties)) {
                $this->$properties = $value;
            }
        }      
    }

    public static function castSelect(&$value, $key){
        $value = array(
            'label'=>$value,
            'value'=>$key,
        );
    }

    static function register($options=array()){
        
        $class = get_called_class();
        
        $instance = new $class($options);
        
        //backward compatibility
        if(!array_key_exists('attach_to', $options)){
            $instance->attach_to = $options;
        }
        
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
    
    protected function shortcode_atts($defaults, $attr=array(), $shortcode_tag=''){
        
        if(!$shortcode_tag){
            $this->shortcode_id;
        }
        
        return shortcode_atts( $defaults, $attr, $shortcode_tag ); 
    }    
    
    function ui_args(){
    	/*
    	 * Define the Shortcode UI arguments.
    	 */
    	return array(
    		/*
    		 * How the shortcode should be labeled in the UI. Required argument.
    		 */
    		'label' => $this->title ?: $this->title(),
    
    		/*
    		 * Include an icon with your shortcode. Optional.
    		 * Use a dashicon, or full URL to image.
    		 */
    		'listItemImage' => $this->icon,
    
    		/*
    		 * Limit this shortcode UI to specific posts. Optional.
    		 */
    		'post_type' => $this->attach_to,
    
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
    
    public static function setRenderPosition($parts, $position){
        
        $parts = (array)$parts;
        
        $this->renderOrder = array_diff($this->renderOrder, $parts);
        
        array_splice( $this->renderOrder, $position, 0, $parts ); 
    }     
    
    function renderOutput($attr, $content, $shortcode_tag){
        return array('content'=>$content);
    }
    
    public function output( $attr, $content, $shortcode_tag ) {
        
        $output = $this->renderOutput($attr, $content, $shortcode_tag);
        
        if(is_array($output)){
        
            $output_html = '';
            $parts = $this->renderOrder;
            
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
