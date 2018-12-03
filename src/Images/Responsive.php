<?php

namespace Svbk\WP\Shortcakes\Images;

use Svbk\WP\Shortcakes\Shortcake;

class Responsive extends Shortcake {

	public $shortcode_id = 'responsive_image';
	public $icon = 'dashicons-format-image';
	public $classes = array( 'content-image' );

	public $renderOrder = array(
		'linkBegin',
		'wrapperBegin',
		'image',
		'caption',
		'wrapperEnd',
		'linkEnd',
	);

	public static $defaults = array(
		'image_id' => '',
		'alignment' => '',
		'link' => '',
		'alt' => '',
		'class' => '',
		'size' => '',
	);

	public function title() {
		return __( 'Responsive Image', 'svbk-shortcakes' );
	}

	public function ui_args() {

		$args = parent::ui_args();

		$args['inner_content']['label'] = __( 'Image Caption', 'svbk-shortcakes' );

		return $args;

	}
	
	public static function getAvailableSizes() {
		
		$sizes = array_combine( get_intermediate_image_sizes(), get_intermediate_image_sizes() );

		return array_merge(
			$sizes,
			apply_filters( 'image_size_names_choose',
				array(
					'thumbnail' => __( 'Thumbnail', 'svbk-shortcakes' ),
					'medium'    => __( 'Medium', 'svbk-shortcakes' ),
					'large'     => __( 'Large', 'svbk-shortcakes' ),
					'full'      => __( 'Full Size', 'svbk-shortcakes' ),
				)
			)
		);		
		
	}	

	public function fields() {

		$custom_sizes = get_intermediate_image_sizes();

		$size_options = array(
			array(
				'value' => '',
				'label' => esc_html__( '-- Default (Not Set) --', 'svbk-shortcakes' ),
			),
			array(
				'options' => array(),
				'label' => esc_html__( 'Native', 'svbk-shortcakes' ),
			),
			array(
				'options' => array(),
				'label' => esc_html__( 'Custom', 'svbk-shortcakes' ),
			)			
		);

		$native_sizes = apply_filters( 'image_size_names_choose',
				array(
					'thumbnail' => __( 'Thumbnail', 'svbk-shortcakes' ),
					'medium'    => __( 'Medium', 'svbk-shortcakes' ),
					'large'     => __( 'Large', 'svbk-shortcakes' ),
					'full'      => __( 'Full Size', 'svbk-shortcakes' ),
				)
		);	
		
		foreach( $native_sizes as $size => $label ) {
			$size_options[1]['options'][] = array(
				'value' => $size,
				'label' => $label,
			);
		}
		
		foreach( $custom_sizes as $size ) {
			$size_options[2]['options'][] = array(
				'value' => $size,
				'label' => $size,
			);
		}			
		
		return array(
			'image_id' => array(
				'label'       => esc_html__( 'Image', 'svbk-shortcakes' ),
				'attr'        => 'image_id',
				'type'        => 'attachment',
				'libraryType' => array( 'image' ),
				'addButton'   => esc_html__( 'Select Image', 'svbk-shortcakes' ),
				'frameTitle'  => esc_html__( 'Select Image', 'svbk-shortcakes' ),
			),
			'size' => array(
				'label'       => esc_html__( 'Size', 'svbk-shortcakes' ),
				'attr'        => 'size',
				'type'        => 'select',
				'options'     => $size_options,
			),			
			'alignment' => array(
				'label'       => esc_html__( 'Alignment', 'svbk-shortcakes' ),
				'attr'        => 'alignment',
				'type'        => 'select',
				'options'     => array(
					array(
						'value' => '',
						'label' => esc_html__( 'None', 'svbk-shortcakes' ),
					),
					array(
						'value' => 'left',
						'label' => esc_html__( 'Align Left', 'svbk-shortcakes' ),
					),
					array(
						'value' => 'center',
						'label' => esc_html__( 'Align Center', 'svbk-shortcakes' ),
					),
					array(
						'value' => 'right',
						'label' => esc_html__( 'Align Right', 'svbk-shortcakes' ),
					),
				),
			),
			'link' => array(
				'label'       => esc_html__( 'Link', 'svbk-shortcakes' ),
				'attr'        => 'size',
				'type'        => 'radio',
				'options'     => array(
					'' => esc_html__( 'None', 'svbk-shortcakes' ),
					'image' => esc_html__( 'Image', 'svbk-shortcakes' ),
					'attachment' => esc_html__( 'Attachment', 'svbk-shortcakes' ),
				)
			),			
			'alt' => array(
				'label'    => esc_html__( 'Alternative Text (alt)', 'svbk-shortcakes' ),
				'attr'     => 'alt',
				'type'     => 'text',
			),			
			'classes' => array(
				'label'    => esc_html__( 'Custom Classes', 'svbk-shortcakes' ),
				'attr'     => 'classes',
				'type'     => 'text',
			),			
		);
	}

	protected function getClasses( $attr ) {

		$classes = parent::getClasses( $attr );

		if( !empty( $attr['alignment'] ) ) {
			$classes[] = 'align' . $attr['alignment'];
		}

		if( !empty( $attr['size'] ) ) {
			$classes[] = 'size-' . $attr['size'];
		}

		if( !empty( $attr['image_id'] ) ) {
			$classes[] = 'wp-image-' . $attr['image_id'];
		}
		
		return $classes;

	}

	public function getLink( $attr ){
		
		$link = '';
		
		if( empty( $attr['link'] ) ) {
			return '';
		}
		
		switch( $attr['link'] ) {
			case 'image': 
				$link = wp_get_attachment_url( $attr['image_id'] );
				break;
			case 'attachment': 
				$link = get_attachment_link( $attr['image_id'] );
				break;
		}
		
		return $link;
	}
	
	public static function parseSize( $size ) {
		
		if ( is_numeric($size) ){
		    $size = array( intval( $size ), intval( $size ) );
		} elseif ( strpos($size, ",") > 0){
			$size = explode(',', $size, 2 );
		}		
	
		return $size;
		
	}
	

	public function renderOutput( $attr, $content, $shortcode_tag ) {
		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

		$output = parent::renderOutput($attr, $content, $shortcode_tag);
		
		$link = $this->getLink( $attr );
		
		if( $link ) {
			$output['linkBegin'] = '<a class="image-link" href="' . esc_url( $link ) . '">';
			$output['linkEnd'] = '</a>';
		}

		$output['wrapperBegin'] = '<figure ' . $this->renderClasses( $this->getClasses( $attr ) ) . '>';
		
		$size = $this->parseSize( $attr['size'] );
		
		$img_attr = array();
		
		if ( $attr['alt'] ) {
			$img_attr['alt'] = esc_html( $attr['alt'] );
		}
		
		$output['image'] = wp_get_attachment_image( $attr['image_id'], $size, false, $img_attr );

		if ( $content ) {
			$output['caption'] = '<figcaption class="caption">' . $output['content'] . '</figcaption>';
		}

		$output['wrapperEnd'] = '</figure>';

		return $output;

	}

}
