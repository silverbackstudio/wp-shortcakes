<?php 

namespace Svbk\WP\Shortcakes;

class LatestPosts extends Shortcake {
 
    public $shortcode_id = 'latest_posts';
    public $post_type = 'post';

    public static $defaults = array(
		'count' => 3,
	);

    public function title(){
        return __('Latest Posts', 'svbk-shortcakes');
    }     
    
    public function ui_args(){
        
        $args = parent::ui_args();
        
        unset($args['inner_content']);
        
        return $args;
        
    }    
    
    public function fields(){
        return array(
			array(
				'label'       => esc_html__( 'Post Count', 'svbk-shortcakes' ),
				'description' => esc_html__( 'The number of posts shown', 'svbk-shortcakes' ),
				'attr'        => 'count',
				'type'        => 'number',
				'meta'        => array(
				    'placeholder'=>self::$defaults['count'],
					'min'         => '1',
					'max'         => '9',
					'step'        => '1',
				),
			),
    	);
    }    
    
    public function output( $attr, $content, $shortcode_tag ) {
        
        $output = '';
        
    	$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );
        
        if( defined('SHORTCODE_UI_DOING_PREVIEW') && SHORTCODE_UI_DOING_PREVIEW ) {
            
        	$output .= '<div id="latest-posts" ><h2>{{'.($this->title ?: $this->title()).'}}</h2></div>';
            
        } else {
        	
			$output .= '<div class="latest-posts post-thumbs">';

			query_posts(array('posts_per_page'=>$attr['count'], 'post_type'=>$this->post_type));
			
			ob_start();
			
			while ( have_posts() ) : the_post();
				
				get_template_part( 'template-parts/thumb', get_post_type() );

			endwhile; // End of the loop.
			
			$output .= ob_get_clean();
			
			wp_reset_query();
			wp_reset_postdata();
			
			$output .= '</div>';
        	
        	return $output;
        	
        }
    
        return $output;
        
    }
    
    
}