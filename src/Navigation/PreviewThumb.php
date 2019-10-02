<?php
namespace Svbk\WP\Shortcakes\Navigation;

use Svbk\WP\Shortcakes\Shortcake;

class PreviewThumb extends Shortcake {

	public $shortcode_id = 'navigation_previewthumb';
	public $icon = 'dashicons-leftright';
	public $classes = array( 'navigation', 'post-navigation', 'preview-thumb' );
	
	public $taxonomy = 'category';

	public $renderOrder = array(
		'navBegin',
		'previous',
		'next',
		'navEnd',
	);

	public static $defaults = array(
	    'taxonomy' => '',
		'class' => '',
		'next_label' => '',
		'prev_label' => '',
	);

	public function title() {
		return __( 'Navigation Preview Thumb', 'svbk-shortcakes' );
	}

	public function fields() {
		return array(
			'class' => array(
				'label'  => esc_html__( 'Classes', 'svbk-shortcakes' ),
				'attr'   => 'class',
				'type'   => 'text',
			),				
            'next_label' => array(
				'label'       => __( 'Next Label', 'svbk-shortcakes' ),
				'attr'        => 'next_label',
				'type'     => 'text',
			),
            'prev_label' => array(
				'label'       => __( 'Prev Label', 'svbk-shortcakes' ),
				'attr'        => 'prev_label',
				'type'     => 'text',
			),			
            'taxonomy' => array(
				'label'       => __( 'Taxonomy Slug', 'svbk-shortcakes' ),
				'description' => esc_html__( 'Choose only posts in this taxonomy', 'svbk-shortcakes' ),
				'attr'        => 'taxonomy',
				'type'     => 'text',
			)			
		);
	}

	public function ui_args() {

		$args = parent::ui_args();

		unset( $args['inner_content'] ); 

		return $args;

	}

    public function renderLink( $adjacent_post ) {
		global $post;
		
		$current_post = $post;
		$post = $adjacent_post;		
		setup_postdata( $adjacent_post ); 
        
        ob_start();
        
		get_template_part( 'template-parts/pagination', get_post_type() );
        
        $link_html = ob_get_contents();
        
        if ( !$link_html ) {
            get_template_part( 'template-parts/thumb', get_post_type() );
            $link_html = ob_get_contents();
        }

        ob_end_clean();
        
		$post = $current_post;     
		
		return $link_html;
    }

	public function renderOutput( $attr, $content, $shortcode_tag ) {
		
		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

		$output = parent::renderOutput($attr, $content, $shortcode_tag);

        $in_same_term = !empty( $attr['taxonomy'] );

		$next_post = get_adjacent_post( $in_same_term, '', false, $attr['taxonomy'] ?: 'category' );
		$prev_post = get_adjacent_post( $in_same_term, '', true, $attr['taxonomy'] ?: 'category' );

		if ( $next_post || $prev_post ) {
		
			$output['navBegin'] = '<nav ' . $this->renderClasses( $this->getClasses( $attr ) ) . ' role="navigation">';
			$output['navTitle'] =   '<h2 class="screen-reader-text">' . __( 'Article navigation', 'svbk-shortcakes' ) . '</h2>';
            $output['linksBegin'] =       '<div class="nav-links">' ;
			
			if( $prev_post ) {
				$output['previous']['containerBegin'] = '<div class="nav-previous">';
			    $output['previous']['label'] =          '<h3>' . ( $attr['prev_label'] ?: __( 'Previous', 'svbk-shortcakes' ) ) . '</h3>';
                $output['previous']['link'] =           $this->renderLink( $prev_post );
				$output['previous']['containerEnd'] =   '</div>';
			}
			
			if( $next_post ) {
				$output['next']['containerBegin'] = '<div class="nav-next">';
				$output['next']['label'] =          '<h3>' . ( $attr['next_label'] ?: __( 'Next', 'svbk-shortcakes' ) ) . '</h3>';
                $output['next']['link'] =           $this->renderLink( $next_post );
				$output['next']['containerEnd'] =   '</div>';			    
			}			

			$output['linksEnd'] =   '</div>';
			$output['navEnd'] = '</nav>';
        
		}
		
		return $output;

	}	

}