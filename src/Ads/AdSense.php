<?php

namespace Svbk\WP\Shortcakes\Ads;

use Svbk\WP\Shortcakes\Shortcake;
use Svbk\WP\Helpers;

class AdSense extends Shortcake {
    
    public $shortcode_id = 'adsense_adunit';
    public $google_ad_client = '';

    public $defaults = array(
	    'ad_slot' => '',
	    'ad_size' => 'auto',
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
    			'meta'     => [ 'placeholder' => $this->defaults['ad_slot'] ]
    		),
    		array(
    			'label'       => esc_html__( 'Ad Size', 'svbk-shortcakes' ),
    			'attr'        => 'ad_size',
    			'type'        => 'select',
    			'options'     => Helpers\Ads\AdSense::adunit_sizes(),
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
    	
        $adsense = new Helpers\Ads\AdSense( $this->google_ad_client );
    	
        return $adsense->adunit_code( $attr['ad_slot'], $attr['ad_size'] );
        
    }
    
}