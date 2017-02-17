<?php

namespace Svbk\WP\Shortcakes;

class GoogleMapDirections extends Shortcake {
    
    public $shortcode_id = 'gmap_directions';

    public static $defaults = array(
		'lat' => '',
		'lng' => '',
		'zoom' => '',
		'marker_title' => '',
		'marker_icon' => '',
	);

    public function title(){
        return __('Google Maps Directions', 'svbk-shortcakes');
    }

    function fields(){
        
        return array(
    		array(
    			'label'  => esc_html__( 'Lat', 'svbk-shortcakes' ),
    			'attr'   => 'lat',
    			'type'   => 'text',
    			'encode' => true
    		),
    		array(
    			'label'  => esc_html__( 'Long', 'svbk-shortcakes' ),
    			'attr'   => 'lng',
    			'type'   => 'text',
    			'encode' => true
    		), 	
    		array(
    			'label'  => esc_html__( 'Zoom', 'svbk-shortcakes' ),
    			'attr'   => 'zoom',
    			'type'   => 'number',
    		),
    		array(
    			'label'  => esc_html__( 'Marker Title', 'svbk-shortcakes' ),
    			'attr'   => 'marker_title',
    			'type'   => 'text',
    			'encode' => true
    		),
    		array(
    			'label'  => esc_html__( 'Marker Icon', 'svbk-shortcakes' ),
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
    	$attr = $this->shortcode_atts( self::$defaults , $attr, $shortcode_tag );
    
		ob_start();
		?>
    	<div class="gmap-container" id="reach-us" data-map-lng="<?php echo esc_attr($attr['lng']); ?>" data-map-lat="<?php echo esc_attr($attr['lat']); ?>">
    		<div class="map-locker locked">
    			<div class="google-map"></div>
    			<div class="map-lock">
    				<button class="unlock-label"><span class="label"><?php _e('Unlock map','svbk-shortcakes'); ?></span></button>
    				<button class="lock-label"><span class="label"><?php _e('Lock map','svbk-shortcakes'); ?></span></button>
    			</div>
    		</div>
        	<div id="directions">
        		<form id="ask-directions" class="gmap-directions-form" data-target-map="#reach-us .google-map">
        			
        			<label for="directionsOrigin"><?php _e('Directions', 'svbk-shortcakes'); ?></label>
        			<input type="text" id="directionsOrigin" class="gmaps-directions-origin gmaps-autocomplete"  name="origin"/>
        			<button type="submit" class="submit"><span class="screen-reader-text"><?php _e('Get route', 'svbk-shortcakes'); ?></span></button>
        			
        			<div class="travel-modes gmaps-travel-modes">
        				<input type="radio" id="travelModeWalking" name="travelMode" value="WALKING" checked="checked"  />
        				<label for="travelModeWalking" class="icon-directions_walk"><span class="screen-reader-text"><?php _e('Walking', 'svbk-shortcakes'); ?></span></label>
        				<input type="radio" id="travelModeDriving" name="travelMode" value="DRIVING" />
        				<label for="travelModeDriving" class="icon-directions_car"><span class="screen-reader-text"><?php _e('Driving', 'svbk-shortcakes'); ?></span></label>				
        				<input type="radio" id="travelModeTransit" name="travelMode" value="TRANSIT" />
        				<label for="travelModeTransit" class="icon-directions_transit"><span class="screen-reader-text"><?php _e('Transit', 'svbk-shortcakes'); ?></span></label>					
        			</div>
        		</form>
        	</div>         		
    		<div class="map-directions">
    		    <a href="http://maps.google.com/maps?daddr=<?php echo esc_attr($attr['lat']); ?>,<?php echo esc_attr($attr['lng']); ?>&amp;ll=&amp;saddr=" class="open-navigation action-button" target="_blank"><?php _e('Navigate', 'svbk-shortcakes') ?></a>
    			<h4 class="action-button"><?php _e('Show Route','svbk-shortcakes'); ?></h4>
    		</div>
    	</div>	
        <?php return ob_get_clean();        
        
        return $output;
        
    }
    
}
