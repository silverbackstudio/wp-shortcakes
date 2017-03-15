<?php namespace Svbk\WP\Shortcakes;

class Link extends Shortcake {
    
    public $shortcode_id = 'section';
    public $classes = array('link');
    public $post_query = array( 'post_type' => array('page', 'post') ); 
    
    public static $defaults = array(
		'post_id' => 0,
	);

    public function title(){
        return __('Link', 'svbk-shortcakes');
    }  

    function fields(){
        return array(        
        		array(
        			'label'  => esc_html__( 'Post/Page to Link', 'svbk-shortcakes' ),
        			'attr'   => 'post_id',
        			'type'   => 'post_select',
        			'query'    => $this->post_query,
        			'multiple' => false, 			
        			'description' => esc_html__( 'Select the post to link', 'svbk-shortcakes' ),
        		),
            );
    }
    
    function ui_args(){
        
        $args = parent::ui_args();
        
        $args['inner_content']['label'] = __('Link Label', 'svbk-shortcakes');
        
        return $args;
        
    }
    
    function output( $attr, $content, $shortcode_tag ) {
        
    	$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );
        
        $label = $content ?: get_the_title($attr['post_id']);
        
        return '<a class="'.esc_attr(join(' ', $this->classes)).'" href="'.get_permalink($attr['post_id']).'">'.$content.'</a>';
    	
    }

}