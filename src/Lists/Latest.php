<?php 

namespace Svbk\WP\Shortcakes\Lists;

use Svbk\WP\Shortcakes\Shortcake;
use WP_Query;

class Latest extends Shortcake {
 
    public $shortcode_id = 'latest_posts';
    public $post_type = 'post';
    public $query_args = array();
    public $classes = array('latest-posts');    

    public static $defaults = array(
		'count' => 3,
		'offset' => 0,
	);

    public $renderOrder = array(
    	'wrapperBegin',
        'content',
        'wrapperEnd'
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
			'count' => array(
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
			'offset' => array(
				'label'       => esc_html__( 'Offset', 'svbk-shortcakes' ),
				'description' => esc_html__( 'The number of posts to skip', 'svbk-shortcakes' ),
				'attr'        => 'offset',
				'type'        => 'number',
				'meta'        => array(
				    'placeholder'=>self::$defaults['offset'],
					'min'         => '1',
					'step'        => '1',
				),
			),			
    	);
    }    
    
    protected function getQueryArgs($attr){

        if( ($attr['offset'] > 0) && !empty($attr['paged'])){
            $attr['offset']  = $attr['count'] * $attr['paged'];
        }

    	return array_merge(array(
    	    'post_type' => $this->post_type,
    	    'post_status' => 'publish',
    	    'orderby' => 'date',
    	    'posts_per_page' => $attr['count'],
    	    //'paged' => $attr['paged'],
    	    'offset' => $attr['offset'],
    	), $this->query_args );
    	
    }    
    
    public function renderOutput( $attr, $content, $shortcode_tag ) {
        
        $output = '';
        
    	$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );
        
        if( defined('SHORTCODE_UI_DOING_PREVIEW') && SHORTCODE_UI_DOING_PREVIEW ) {
            
        	$output['wrapperBegin'] = '<div id="' . join('', $this->classes) . '" >';
        	$output['content'] = '<h2>{{'.($this->title ?: $this->title()).'}}</h2>';
        	$output['wrapperEnd'] = '</div>';
            
        } else {
        	
			$output['wrapperBegin'] = '<div class="' . join('', $this->classes) . ' post-thumbs">';

			$postsQuery = new WP_Query( $this->getQueryArgs($attr) );
			
			ob_start();
			
			while ( $postsQuery->have_posts() ) : $postsQuery->the_post();
				
				get_template_part( 'template-parts/thumb', get_post_type() );

			endwhile; // End of the loop.
			
			$output['content'] = ob_get_clean();
			
			wp_reset_query();
			wp_reset_postdata();
			
			$output['wrapperEnd'] = '</div>';

        }
    
        return $output;
        
    }
    
    
}