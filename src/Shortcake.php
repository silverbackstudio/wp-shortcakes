<?php

namespace Svbk\WP\Shortcakes;

add_action( 'after_setup_theme', __NAMESPACE__ . '\\Shortcake::load_texdomain' );

use Svbk\WP\Forms\Renderer;
use Svbk\WP\Helpers;

abstract class Shortcake {

	public $shortcode_id = 'shortcake_base';
	public $title = '';
	public $icon = 'dashicons-admin-links';
	public $classes = array();

	public $attach_to = array( 'page' );

	public $show_content = true;
	
	public $renderOrder = array(
		'content',
	);
	
	public $staticOutput = array(
		'content' => ''
	);

	public function title() {
		return __( 'Base Shortcode', 'svbk-shortcakes' );
	}

	protected function icon() {
		return $this->icon;
	}

	public function __construct( $properties ) {
		self::configure( $this, $properties );
	}
	
	protected static function configure( &$target, $properties ) {
		
		foreach ( $properties as $property => $value ) {
			if ( ! property_exists( $target, $property ) ) {
				continue;
			}

			if ( is_array( $target->$property ) ) {
				$target->$property = array_merge( $target->$property, (array)$value );
			} else {
				$target->$property = $value;
			}
		}
		
	}

	public function register_scripts(){ }

	public static function castSelect( &$value, $key ) {
		$value = array(
			'label' => $value,
			'value' => $key,
		);
	}

	public static function selectOptions( $options ) {

		$output = array();

		foreach ( $options as $key => $value ) {
			$output[] = array(
			'label' => $value,
			'value' => $key,
			);
		}

		return $output;
	}

	protected function getClasses( $attr ) {

		$instance_classes = array();

		if ( ! empty( $attr['class'] ) ) {
			$instance_classes = array_merge( $instance_classes, preg_split( '/[\s,]+/', $attr['class'], -1, PREG_SPLIT_NO_EMPTY ) );
		}

		if ( ! empty( $attr['classes'] ) ) {
			$instance_classes = array_merge( $instance_classes, preg_split( '/[\s,]+/', $attr['classes'], -1, PREG_SPLIT_NO_EMPTY ) );
		}

		$classes = array_merge( (array) $this->classes, $instance_classes );

		if ( ! empty( $classes ) ) {
			return array_map( 'trim', $classes );
		}
		
		return array();
	}

	protected static function renderClasses( $classes, $suffix = null ) {

		if ( empty( $classes ) ) {
			return '';
		}
		
		if ( !empty( $suffix )  ) {
			array_walk($classes, function(&$class) use ( $suffix ) { $class .= $suffix; });
		}		

		return  'class="' . esc_attr( join( ' ', $classes ) ) . '"';
	}

	public static function register( $options = array() ) {

		$class = get_called_class();

		$instance = new $class($options);

		add_action( 'init', array( $instance, 'init' ), 12 );
		add_action( 'register_shortcode_ui', array( $instance, 'register_ui' ) );
		add_action( 'after_setup_theme', array( __CLASS__, 'load_texdomain' ) );
		add_action( 'wp_enqueue_scripts', array( $instance, 'register_scripts' ) );

		return $instance;
	}

	public static function load_texdomain() {
		load_textdomain( 'svbk-shortcakes', dirname( __DIR__ ) . '/languages/svbk-shortcakes-' . get_locale() . '.mo' );
	}

	public function init() {
		$this->add();
	}

	public function add() {
		add_shortcode( $this->shortcode_id, array( $this, 'output' ) );
	}
	
	public function remove() {
		remove_shortcode( $this->shortcode_id );
	}	

	abstract function fields();

	protected function shortcode_atts( $defaults, $attributes = array(), $shortcode_tag = '' ) {

		if ( ! $shortcode_tag ) {
			$shortcode_tag = $this->shortcode_id;
		}

		$attributes = shortcode_atts( $defaults, $attributes, $shortcode_tag );

		array_walk( $attributes, array( $this, 'field_decode' ) );

		return $attributes;
	}

	public function ui_args() {
		/*
		* Define the Shortcode UI arguments.
		*/
		return array(

		/*
		* How the shortcode should be labeled in the UI. Required argument.
		*/
		'label' => $this->title ?: $this->title(),

		/*
		* Include an icon with your shortcode. Optional.
		* Use a dashicon, or full URL to image.
		*/
		'listItemImage' => $this->icon(),

		/*
		* Limit this shortcode UI to specific posts. Optional.
		*/
		'post_type' => $this->attach_to,

		/*
		* Register UI for the "inner content" of the shortcode. Optional.
		* If no UI is registered for the inner content, then any inner content
		* data present will be backed-up during editing.
		*/
		'inner_content' => array(
		'label'        => esc_html__( 'Content', 'svbk-shortcakes' ),
		'description'  => esc_html__( 'Insert content here', 'svbk-shortcakes' ),
		),

		/*
		* Define the UI for attributes of the shortcode. Optional.
		*
		* See above, to where the the assignment to the $fields variable was made.
		*/
		'attrs' => array_values( $this->fields() ),
		);
	}

	public function register_ui() {
		shortcode_ui_register_for_shortcode( $this->shortcode_id, $this->ui_args() );
	}

	public function setRenderPosition( $parts, $after, $position = 'after' ) {

		$parts = (array) $parts;

		$this->renderOrder = Renderer::arrayInsert( array_diff( $this->renderOrder, $parts ), $parts, $after, $position );
	}

	public static function field_decode( &$value ) {
		
		$value = html_entity_decode( urldecode( $value ) );
		
		return $value;
	}

	protected function renderOutput( $attr, $content, $shortcode_tag ) {
		
		$contents = $this->staticOutput;
		
		if( empty($content) ) {
			return $contents;
		}
		
		$has_shortcodes = strpos($content, '[');
		
		if( $has_shortcodes !== false ) {
			$content = do_shortcode($content);
		}
		
		$contents['content'] = $content;
		
		return $contents;
	}

	protected function outputParts( $output, $order = null ) {

		if ( empty( $order ) ) {
			$order = array_keys( $output );
		}

		$output_html = '';

		foreach ( $order as $key_part => $part ) {

			if ( is_array( $part ) ) {
				$part = $key_part;
			}

			if ( ! array_key_exists( $part, $output ) ) {
				continue;
			}

			if ( is_array( $output[ $part ] ) ) {
				$output_html .= $this->outputParts( $output[ $part ] );
			} else {
				$output_html .= $output[ $part ];
			}
		}

		return $output_html;

	}

	public function output( $attr, $content, $shortcode_tag ) {

		$output = $this->renderOutput( $attr, $content, $shortcode_tag );

		if ( is_array( $output ) ) {
			$order = $this->renderOrder;
			$output = $this->outputParts( $output, $order );
		}

		return $output;
	}
}
