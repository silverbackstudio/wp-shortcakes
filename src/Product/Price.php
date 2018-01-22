<?php
namespace Svbk\WP\Shortcakes\Product;

use Svbk\WP\Shortcakes\Shortcake;

class Price extends Shortcake {

	public $shortcode_id = 'product_price';
	public $icon = 'dashicons-tag';
	public $classes = array( 'product-price' );

	public $platform = 'woocommerce';

	public static $defaults = array(
		'product_id' => 0,
		'template' => '{{formatted}}',
		'template_sale' => '',
		'class' => '',
	);

	public function title() {
		return __( 'Product Price', 'svbk-shortcakes' );
	}

	public function fields() {
		return array(
			'product_id' => array(
				'label'  => esc_html__( 'Price', 'svbk-shortcakes' ),
				'attr'   => 'product_id',
				'type'   => 'post_select',
				'query'    => array(
					'post_type' => $this->post_type(),
				),
				'multiple' => false,
				'description' => esc_html__( 'Select the product', 'svbk-shortcakes' ),
			),
			'template' => array(
				'label'  => esc_html__( 'Template', 'svbk-shortcakes' ),
				'attr'   => 'template',
				'type'   => 'textarea',
				'encode' => true,
				'meta' => array(
					'placeholder' => '{{formatted}}',
				),
				'description' => esc_html__( 'The template when the product is not on sale. Use: {{price}} {{regular_price}} {{sale_price}} {{formatted}} placeholders', 'svbk-shortcakes' ),
			),
			'template_sale' => array(
				'label'  => esc_html__( 'Template on Sale', 'svbk-shortcakes' ),
				'attr'   => 'template_sale',
				'type'   => 'textarea',
				'encode' => true,
				'description' => esc_html__( 'The template when the product is on sale. Use: {{price}} {{regular_price}} {{sale_price}} {{formatted}} placeholders', 'svbk-shortcakes' ),
			),			
			'class' => array(
				'label'  => esc_html__( 'Classes', 'svbk-shortcakes' ),
				'attr'   => 'class',
				'type'   => 'text',
			),				
		);
	}
	
	public function post_type() {
		return 'product';
	}

	public function ui_args() {

		$args = parent::ui_args();

		unset($args['inner_content']);

		return $args;

	}

	public function getPrices( $product ){
		return array(
			'price' => wc_price( wc_get_price_to_display( $product ) ) . $product->get_price_suffix(),
			'regular_price' => wc_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ) ) . $product->get_price_suffix(),
			'sale_price' => wc_price( wc_get_price_to_display( $product, array( 'price' => $product->get_price() ) ) ) . $product->get_price_suffix(),
			'formatted' => $product->get_price_html(),
		);		
	}
	
	public function isOnSale( $prices, $product ){
		return $product->is_on_sale();
	}
	
	public function wrapReplacement( $search ){
		return '{{' . $search . '}}';
	}	

	public function renderOutput( $attr, $content, $shortcode_tag ) {
		
		global $product;
		
		$orig_product = $product;
		
		$output = parent::renderOutput( $attr, $content, $shortcode_tag );
		
		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );
		
		if( !function_exists('wc_get_product') ) {
			return $output;
		}
		if( $attr['product_id'] ) {
			$product = wc_get_product( $attr['product_id'] );
		}
		
		if( !$product ) {
			return $output;	
		}

		$prices = $this->getPrices( $product );	

		$template = ( $attr['template_sale'] && $this->isOnSale($prices, $product) ) ? $attr['template_sale'] : $attr['template'];
		
		$output['content'] = do_shortcode( str_replace( 
			array_map( array($this, 'wrapReplacement'), array_keys( $prices )), 
			array_values( $prices ), 
			urldecode($template)
		) );

		$product = $orig_product;

		return $output;
	}	

}
