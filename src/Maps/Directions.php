<?php

namespace Svbk\WP\Shortcakes\Maps;

use Svbk\WP\Shortcakes\Shortcake;

class Directions extends Shortcake {

	public $shortcode_id = 'gmap_directions';

	public static $defaults = array(
		'lat' => '',
		'lng' => '',
		'zoom' => 14,
		'directions' => false,
		'marker_title' => '',
		'marker_icon' => '',
		'classes' => '',
	);

	public function title() {
		return __( 'Google Maps Directions', 'svbk-shortcakes' );
	}

	public function fields() {

		return array(
			array(
				'label'  => esc_html__( 'Lat', 'svbk-shortcakes' ),
				'attr'   => 'lat',
				'type'   => 'text',
				'encode' => true,
			),
			array(
				'label'  => esc_html__( 'Long', 'svbk-shortcakes' ),
				'attr'   => 'lng',
				'type'   => 'text',
				'encode' => true,
			),
			array(
				'label'  => esc_html__( 'Zoom', 'svbk-shortcakes' ),
				'attr'   => 'zoom',
				'type'   => 'number',
				'default' => self::$defaults['zoom'],
			),
			array(
				'label'  => esc_html__( 'Show directions', 'svbk-shortcakes' ),
				'attr'   => 'directions',
				'type'   => 'checkbox',
			),
			array(
				'label'  => esc_html__( 'Marker Title', 'svbk-shortcakes' ),
				'attr'   => 'marker_title',
				'type'   => 'text',
				'encode' => true,
			),
			array(
				'label'  => esc_html__( 'Marker Icon', 'svbk-shortcakes' ),
				'attr'   => 'marker_icon',
				'type'   => 'url',
				'encode' => true,
			),
			array(
				'label'  => esc_html__( 'Class', 'svbk-shortcakes' ),
				'attr'   => 'classes',
				'type'   => 'text',
				'encode' => true,
			),
		);

	}

	public function ui_args() {

		$ret = parent::ui_args();

		unset( $ret['inner_content'] );

		return $ret;

	}

	public function output( $attr, $content, $shortcode_tag ) {
		$attr = $this->shortcode_atts( self::$defaults , $attr, $shortcode_tag );

		static $index = 0;
        $index++;
		ob_start();
		?>
		<div class="gmap-container <?php echo esc_attr( $attr['classes'] ); ?>" id="gmap-container-<?php echo $index; ?>" data-map-lng="<?php echo esc_attr( $attr['lng'] ); ?>" data-map-lat="<?php echo esc_attr( $attr['lat'] ); ?>">
		<div class="google-map"></div>
		
		<?php if ( $attr['directions'] ) : ?>
			<div id="directions">
				<form id="ask-directions" class="gmap-directions-form" data-target-map="#gmap-container-<?php echo $index; ?> .google-map">
			
					<label for="directionsOrigin"><?php esc_attr_e( 'Directions', 'svbk-shortcakes' ); ?></label>
					<input type="text" id="directionsOrigin" class="gmaps-directions-origin gmaps-autocomplete"  name="origin"/>
					<button type="submit" class="submit"><span class="screen-reader-text"><?php esc_attr_e( 'Get route', 'svbk-shortcakes' ); ?></span></button>
			
					<div class="travel-modes gmaps-travel-modes">
						<input type="radio" id="travelModeWalking" name="travelMode" value="WALKING" checked="checked"  />
						<label for="travelModeWalking" class="icon-directions_walk"><span class="screen-reader-text"><?php esc_attr_e( 'Walking', 'svbk-shortcakes' ); ?></span></label>
						<input type="radio" id="travelModeDriving" name="travelMode" value="DRIVING" />
						<label for="travelModeDriving" class="icon-directions_car"><span class="screen-reader-text"><?php esc_attr_e( 'Driving', 'svbk-shortcakes' ); ?></span></label>
						<input type="radio" id="travelModeTransit" name="travelMode" value="TRANSIT" />
						<label for="travelModeTransit" class="icon-directions_transit"><span class="screen-reader-text"><?php esc_attr_e( 'Transit', 'svbk-shortcakes' ); ?></span></label>
					</div>
				</form>
			</div>
			<div class="map-directions">
				<a href="http://maps.google.com/maps?daddr=<?php echo esc_attr( $attr['lat'] ); ?>,<?php echo esc_attr( $attr['lng'] ); ?>&amp;ll=&amp;saddr=" class="open-navigation action-button" target="_blank"><?php esc_attr_e( 'Navigate', 'svbk-shortcakes' ) ?></a>
				<h4 class="action-button"><?php esc_attr_e( 'Show Route','svbk-shortcakes' ); ?></h4>
			</div>
			<?php endif; ?>
		</div>
		<?php return ob_get_clean();

		return $output;

	}

}
