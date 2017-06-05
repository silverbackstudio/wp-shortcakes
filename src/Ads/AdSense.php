<?php

namespace Svbk\WP\Shortcakes\Ads;

use Svbk\WP\Shortcakes\Shortcake;

class AdSense extends Shortcake {
    
    public $shortcode_id = 'adsense_adunit';
    public $google_ad_client = '';

    public $defaults = array(
	    'ad_slot' => '',
	    'size' => '',
	);

    public function title(){
        return __('AdSense AdUnit', 'svbk-shortcakes');
    }    

    function fields(){
        
        return array(
    		array(
    			'label'       => esc_html__( 'Ad Unit ID', 'svbk-shortcakes' ),
    			'attr'        => 'ad_slot',
    			'type'        => 'text',
    			'required'    => true,
    		),
    		array(
    			'label'       => esc_html__( 'Size', 'svbk-shortcakes' ),
    			'attr'        => 'size',
    			'type'        => 'select',
    			'options'     => array(
    			    'auto' => __('Adaptive', 'svbk-shortcakes'),
    			    'leaderboard' => __('Leaderboard', 'svbk-shortcakes'),
    			    'large_rectangle' => __('Leaderboard', 'svbk-shortcakes'),
    			    'leaderboard' => __('Leaderboard', 'svbk-shortcakes'),
    			 )
    		),    		
    	);
    }
    
    function ui_args(){ 
        
        $ui_args = parent::ui_args();
        
        unset($ui_args['inner_content']);
        
        return $ui_args;
    }
    
    function output( $attr, $content, $shortcode_tag ) {
        
    	$attr = $this->shortcode_atts( $this->defaults, $attr, $shortcode_tag );
    	
        if( ! $this->google_ad_client || ! $attr['ad_slot'] ) {
            return;
        }    	
        
        if( defined('SHORTCODE_UI_DOING_PREVIEW') && SHORTCODE_UI_DOING_PREVIEW ) {
            return sprintf( __('AdSense AdUnit Banner ID: %s', 'svbk-shortcakes'), $attr['ad_slot'] );
        }             
    	
    	$format = '';
    	
        switch( $attr['size'] ){
            case 'auto':
                $style = 'display:block;';
                $format = 'data-ad-format="auto"';
                break;
            case 'leaderboard':
                $style = 'display:inline-block;width:728px;height:90px;';
                break;                
            case 'large_rectangle':
                $style = 'display:inline-block;width:336px;height:280px;';
                break;                
            case 'skyscraper_large':
                $style = 'display:inline-block;width:300px;height:600px;';
                break;                
        }

        $output = '<ins class="adsbygoogle" style="' . $style . '" data-ad-client="' . $this->google_ad_client . '" data-ad-slot="' . $attr['ad_slot'] . '" ' . $format . '></ins>';
        $output .= '<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
        
        return $output;
        
    }
    
}