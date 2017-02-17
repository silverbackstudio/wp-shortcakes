<?php

namespace Svbk\WP\Shortcakes;

class PreviewCard_Term extends PreviewCard {
    
    public $shortcode_id = 'preview_card_term';
    public $taxonomy = 'category';
    
    public function title(){
        
        if($this->title){
            return $this->title;
        }
        
        $taxonomy = get_taxonomy($this->taxonomy);
        $labels = get_taxonomy_labels($taxonomy);
        
        return sprintf(__('Term Preview Card [%s]', 'svbk-shortcakes'), $labels->name);
    }
    
    function fields(){
         
        $fields = parent::fields();
     
        foreach ($fields as &$field) {
            if($field['attr'] === 'url'){
        		$field = array(
        			'label'    => esc_html__( 'Select Term', 'svbk-shortcakes' ),
        			'attr'     => 'linked_term',
        			'type'     => 'term_select',
        			'taxonomy' => $this->taxonomy,
        			'multiple' => false,
        		);
            }
        }
         
        return $fields;
         
    }
     
    protected function getLink($attr){
        
        $link = get_term_link((int)$attr[ 'linked_term' ], $this->taxonomy);
        
        if($link && !is_wp_error($link)){
            return $link;
        }
        
        return '';
    }     
    
    protected function getTitle($attr){
        
        if($attr['title']){
            return $attr['title'];
        }
        
        $term = get_term($attr[ 'linked_term' ], $this->taxonomy);

        if($term && !is_wp_error($term)){
           return apply_filters( 'single_term_title', $term->name);
        }

        return '';
    }
    
    protected function shortcode_atts($defaults, $attr, $shortcode_tag){
        
        unset($defaults['linked_post']);
        
        $defaults['linked_term'] = '';
        
        return parent::shortcode_atts($defaults, $attr, $shortcode_tag);
    }
    
    
}