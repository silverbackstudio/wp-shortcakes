<?php

namespace Svbk\WP\Shortcakes;

class GoogleMap_Directions extends Base {
    
    public $shortcode_id = 'gmap_directions';
    public $title = 'Google Maps Directions';

    function fields(){
        
        return array(
    		array(
    			'label'  => esc_html__( 'Lat', 'turini' ),
    			'attr'   => 'lat',
    			'type'   => 'text',
    			'encode' => true
    		),
    		array(
    			'label'  => esc_html__( 'Long', 'turini' ),
    			'attr'   => 'lng',
    			'type'   => 'text',
    			'encode' => true
    		), 	
    		array(
    			'label'  => esc_html__( 'Zoom', 'turini' ),
    			'attr'   => 'zoom',
    			'type'   => 'number',
    		),
    		array(
    			'label'  => esc_html__( 'Marker Title', 'turini' ),
    			'attr'   => 'marker_title',
    			'type'   => 'text',
    			'encode' => true
    		),
    		array(
    			'label'  => esc_html__( 'Marker Icon', 'turini' ),
    			'attr'   => 'marker_icon',
    			'type'   => 'url',
    			'encode' => true
    		), 	    		
    	);
    	
    }
    
    function ui_args(){
        
        $ret = parent::ui_args();
        
        unset($ret['inner_content']);
        
        return $ret;
        
    }
    
    function output( $attr, $content, $shortcode_tag ) {
    	$attr = shortcode_atts( array(
    		'lat' => '',
    		'lng' => '',
    		'zoom' => '',
    		'marker_title' => '',
    		'marker_icon' => '',
    	), $attr, $shortcode_tag );
    
        // $output = '<div class="gmap-container"';
            
        // if( $attr['lat'] && $attr['lng'] ){
        //     $output .=  ' data-map-lat="'.esc_attr($attr['lat']).'" data-map-lng="'.esc_attr($attr['lng']).'"';
        // }
        // if( $attr['zoom'] ){
        //     $output .=  ' data-map-zoom="'.esc_attr($attr['zoom']).'"';
        // }
        // if( $attr['marker_title'] ){
        //     $output .=  ' data-map-title="'.esc_attr($attr['marker_title']).'"';
        // }
        // if( $attr['marker_icon'] ){
        //     $output .=  ' data-map-marker="'.esc_attr($attr['marker_icon']).'"';
        // }
        
        // $output .= ' >';
            
        // $output .= '<div class="google-map" ></div>
		      //  <div class="map-lock">
        //         <span class="unlock-label">' . __('Unlock map', 'turini') . '</span>
        //         <span class="lock-label">' . __('Lock map', 'turini') . '</span>
        //     </div>
        // </div>';
        
        
    		ob_start();
    		?>
        	<div class="gmap-container" id="reach-us" data-map-lng="<?php echo esc_attr($attr['lng']); ?>" data-map-lat="<?php echo esc_attr($attr['lat']); ?>">
        		<div class="map-locker locked">
        			<div class="google-map"></div>
        			<div class="map-lock">
        				<span class="unlock-label"><?php _e('Unlock map','gazelle'); ?></span>
        				<span class="lock-label"><?php _e('Lock map','gazelle'); ?></span>
        			</div>
        		</div>
            	<div id="directions">
            		<form id="ask-directions" class="gmap-directions-form" data-target-map="#reach-us .google-map">
            			
            			<label for="directionsOrigin"><?php _e('Directions','gazelle'); ?></label>
            			<input type="text" id="directionsOrigin" class="gmaps-directions-origin gmaps-autocomplete"  name="origin"/>
            			<button type="submit" class="submit"><span><?php _e('Get route','gazelle'); ?></span></button>
            			
            			<div class="travel-modes gmaps-travel-modes">
            				<input type="radio" id="travelModeWalking" name="travelMode" value="WALKING" checked="checked"  />
            				<label for="travelModeWalking" class="icon-directions_walk"><span><?php _e('Walking','turini'); ?></span></label>
            				<input type="radio" id="travelModeDriving" name="travelMode" value="DRIVING" />
            				<label for="travelModeDriving" class="icon-directions_car"><span><?php _e('Driving','turini'); ?></span></label>				
            				<input type="radio" id="travelModeTransit" name="travelMode" value="TRANSIT" />
            				<label for="travelModeTransit" class="icon-directions_transit"><span><?php _e('Transit','turini'); ?></span></label>					
            			</div>
            		</form>
            	</div>         		
        		<div class="map-directions">
        			<h4 class="action-button"><?php _e('Show Route','gazelle'); ?></h4>
        		</div>
        	</div>	
        <?php return ob_get_clean();        
        
        return $output;
        
    }
    
}
