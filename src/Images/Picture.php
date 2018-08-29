<?php

namespace Svbk\WP\Shortcakes\Images;

use Svbk\WP\Shortcakes\Shortcake;

class Picture extends Responsive {

	public $shortcode_id = 'picture';
	public $icon = 'dashicons-format-gallery';
	public $classes = array( 'content-image' );

	public $images = 2;

	public $renderOrder = array(
		'wrapperBegin',
		'sources',
		'image',
		'wrapperEnd',
	);

	public function title() {
		return __( 'Picture', 'svbk-shortcakes' );
	}

	public function ui_args() {

		$args = parent::ui_args();

		unset( $args['inner_content'] );

		return $args;

	}

	public function defaults( $images_count ) {
		
		$defaults = self::$defaults;
		
		for ($image_index = 1; $image_index <= $images_count; $image_index++) {
			$defaults[ 'source_' . $image_index ] = '';
			$defaults[ 'size_' . $image_index ] = '';
			$defaults[ 'media_' . $image_index ] = '';
		}
		
		return $defaults;
	}

	public function fields() {

		$fields = parent::fields();

		$source_fields = array();
		
		$fields['image_id']['label'] = esc_html__( 'Fallback Image', 'svbk-shortcakes' );
		$fields['image_id']['description'] = esc_html__( 'The image printed in non supporting browser or if not any other source is matching', 'svbk-shortcakes' );
		$fields['size']['label'] = esc_html__( 'Size for Fallback', 'svbk-shortcakes' );
		
		$sizes = $fields['size']['options'];		
		
		unset($fields['link']);
		
		for ($image_index = 1; $image_index <= $this->images; $image_index++) {
		
			$source_fields['source' . $image_index ] = array(
				'label'       => esc_html__( 'Image', 'svbk-shortcakes' ) . ' ' . $image_index,
				'attr'        => 'source_' . $image_index,
				'type'        => 'attachment',
				'libraryType' => array( 'image' ),
				'addButton'   => esc_html__( 'Select Image', 'svbk-shortcakes' ),
				'frameTitle'  => esc_html__( 'Select Image', 'svbk-shortcakes' ),
			);

			$source_fields['size' . $image_index] = array(
				'label'       => esc_html__( 'Size', 'svbk-shortcakes' ) . ' ' . $image_index,
				'attr'        => 'size_' . $image_index,
				'type'        => 'select',
				'options'     => $sizes,
			);
			
			$source_fields['media' . $image_index] = array(
				'label'       => esc_html__( 'Media', 'svbk-shortcakes' ) . ' ' . $image_index,
				'attr'        => 'media_' . $image_index,
				'type'        => 'text',
			);
		
		}
		
		return $source_fields + $fields;
	}

	public function renderOutput( $attr, $content, $shortcode_tag ) {
		
		$attr = $this->shortcode_atts( $this->defaults( $this->images ), $attr, $shortcode_tag );

		$output = parent::renderOutput($attr, $content, $shortcode_tag);

		$output['wrapperBegin'] = '<picture ' . $this->renderClasses( $this->getClasses( $attr ) ) . '">';
		
		for ($image_index = 1; $image_index <= $this->images; $image_index++) {
			
			$image  = $attr[ 'source_'	. $image_index ];
			$size	= $attr[ 'size_'	. $image_index ];
			$media  = trim($attr[ 'media_'	. $image_index ]);
		
			if ( !$image || !$size ) { continue; };
			
			$size = $this->parseSize( $size );
			
			$output['sources'][$image_index] = '<source ' . 
				'media="' . esc_attr( $media ) . '" ' .
				'srcset="' . esc_attr( wp_get_attachment_image_srcset($image, $size) ) . '" ' .
				'sizes="' . esc_attr( wp_get_attachment_image_sizes($image, $size) ) . '" ' .
			'>';
		}

		$output['wrapperEnd'] = '</picture>';

		return $output;

	}

}
