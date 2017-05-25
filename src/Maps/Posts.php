<?php

namespace Svbk\WP\Shortcakes\Maps;

use Svbk\WP\Shortcakes\Shortcake;
use WP_Query;

class Posts extends Shortcake {
    
    public $shortcode_id = 'posts_map';
    public $post_type = 'post';
    
    public $locationAttribute = 'coordinates';
    
    public $query_args = array();    
    
    public $mapSettings = array(
        'zoom' => 13,
        'draggable' => false
    );
    
    public $markerSettings = array(
    );    
    
    public $markerCluster = array(
        'imagePath' => 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
    );    
    
    public static $defaults = array(
		'count' => 5,
    );    
    
    public function title(){
        return __('MembersMap', 'propertymanagers');
    }
    
    function fields(){
        return array(        
        		array(
        			'label'  => esc_html__( 'Members Count', 'propertymanagers' ),
        			'attr'   => 'count',
        			'type'   => 'number',
        			'encode' => true,
        			'description' => esc_html__( 'How many members to show', 'propertymanagers' ),
        			'meta'   => array(
        				'placeholder' =>  self::$defaults['count'],
        			),
        		)
            );
    }

    protected function getQueryArgs($attr){

    	return array_merge(array(
    	    'post_type' => $this->post_type,
    	    'post_status' => 'publish',
    	    'orderby' => 'date',
    	    'posts_per_page' => -1,
    	), $this->query_args );
    	
    }
    
    public function output( $attr, $content, $shortcode_tag) {
        
        static $index = 0;
        
        $output = '';

        $attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );         

        $members = new WP_Query( $this->getQueryArgs($attr) );
        
        $locations  = array();
        while ($members->have_posts()) { $members->next_post();
            
            $location = get_field($this->locationAttribute, $members->post->ID);
        
            if($location){
                $locations[] = array (
                    'position' => array(
                        'lat' => floatval($location['lat']), 
                        'lng' => floatval($location['lng']), 
                    ),
                    'title' => $members->post->post_title  
                );
            }
        }     
        
        $index++;

    	$output .= '<aside class="gmap-container '. join('', $this->classes) .'" id="gmap-container-' . $index . '" >
    			<div class="google-map"></div>';

        $output .= '
        <script>

        (function($){
            $("#gmap-container-' . $index . '").one(\'gmaps-ready\', function(){

                var $container = $(this);
                var map = new google.maps.Map( $(this).find(\'.google-map\').get(0), ' . json_encode( $this->mapSettings ) . ');
                var markerBounds = new google.maps.LatLngBounds();
                var markerOptions = ' . json_encode( (object)$this->markerSettings ) . ';
                
                if(typeof markerOptions.icon === \'object\' ){
                    markerOptions.icon.size = new google.maps.Size(markerOptions.icon.size[0], markerOptions.icon.size[1]);
                    markerOptions.icon.anchor = new google.maps.Point(markerOptions.icon.anchor[0], markerOptions.icon.anchor[1]);
                    markerOptions.icon.scaledSize = new google.maps.Size(markerOptions.icon.scaledSize[0], markerOptions.icon.scaledSize[1]);
                    markerOptions.icon.origin =  new google.maps.Point(0, 0);
                }

                locations.forEach(function(location) {
                  markerBounds.extend( new google.maps.LatLng(location.position.lat, location.position.lng) );
                  return new google.maps.Marker( $.extend(markerOptions, location, { map: map } ) );
                });
    
                google.maps.event.addListenerOnce(map, \'idle\', function() {
                     map.fitBounds(markerBounds);
                });
                
                //Map locker
                $container.on(\'click\',\'.map-lock button\', function(){
                    $container.toggleClass(\'locked\');
                    var locked = $(this).hasClass(\'lock\');
                    map.setOptions({ draggable: locked, scrollwheel: locked });
                }).addClass(\'locked\');                
                map.setOptions({ draggable: false, scrollwheel: false });
                
            });
        
            var locations = ' . json_encode($locations) . ';
        
        })(jQuery);
        
        </script>';

        $output .= '</aside>';
            
    	return $output;
    	
    }
    
}
