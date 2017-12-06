<?php

namespace Svbk\WP\Shortcakes\Images;

use Svbk\WP\Shortcakes\Shortcake;

use Svbk\WP\Shortcakes\Content\Link;

class Responsive extends Link {

	public $shortcode_id = 'responsive_image';
	public $icon = 'dashicons-format-image';
	public $classes = array( 'content-image' );

	public $post_query = array(
		'post_type' => 'any',
	);

	public $taxonomy;

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
		'class' => '',
		'link_post' => '',
		'link_term' => '',
		'link_url' => '',
		'link_target' => 0,
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
	
	public function remapFields(){
		return array(
			'post_id' => 'link_post',
			'term' => 'link_term',
			'url' => 'link_url',
			'target' => 'link_target',
		);
	}

	public function fields() {

		$sizes = array_combine( get_intermediate_image_sizes(), get_intermediate_image_sizes() );

		$sizes = array_merge(
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

		$link_fields = parent::fields();

		foreach( $this->remapFields() as $old=>$new ){
			$link_fields[$new] = $link_fields[$old];
			$link_fields[$new]['attr'] = $new;
			unset($link_fields[$old]);
		}
		
		return array_merge(
			array(
				'image_id' => array(
					'label'       => esc_html__( 'Image', 'svbk-shortcakes' ),
					'attr'        => 'image_id',
					'type'        => 'attachment',
					'libraryType' => array( 'image' ),
					'addButton'   => esc_html__( 'Select Image', 'svbk-shortcakes' ),
					'frameTitle'  => esc_html__( 'Select Image', 'svbk-shortcakes' ),
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
				'size' => array(
					'label'       => esc_html__( 'Size', 'svbk-shortcakes' ),
					'attr'        => 'size',
					'type'        => 'select',
					'options'     => $sizes,
				),
			),
			$link_fields
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

	public function renderOutput( $attr, $content, $shortcode_tag ) {
		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

		$link_attr = $attr;
		$remap = array_flip( $this->remapFields() );

		foreach( $remap as $image => $link ){
			$link_attr[$link] = $attr[$image];
			unset($link_attr[$image]);
		}

		unset( $link_attr['class'] );

		$output = parent::renderOutput($link_attr, $content, $shortcode_tag);

		$output['wrapperBegin'] = '<figure ' . $this->renderClasses( $this->getClasses( $attr ) ) . '">';
		$output['image'] = wp_get_attachment_image( $attr['image_id'], $attr['size'] );

		if ( $content ) {
			$output['caption'] = '<figcaption class="caption">' . $output['content'] . '</figcaption>';
		}

		$output['wrapperEnd'] = '</figure>';

		return $output;

	}

}
