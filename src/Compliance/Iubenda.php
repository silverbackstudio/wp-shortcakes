<?php
namespace Svbk\WP\Shortcakes\Compliance;

use Svbk\WP\Shortcakes\Shortcake;
use Svbk\WP\Helpers;

class Iubenda extends Shortcake {

	public $shortcode_id = 'iubenda-embed';
	public $icon = 'dashicons-editor-paragraph';
	public $classes = array( 'iubenda-embed' );
	public $iubenda = null;

	public static $defaults = array(
		'policy_id' => null,
		'policy_type' => 'privacy-policy',
		'extended' => 1,
		'remove_styles' => 1,
		'strip_headings' => 1,
	);

	public function __construct( $properties ) {
		
		$this->iubenda = new Helpers\Compliance\Iubenda( Helpers\Config::get( array(), 'iubenda' ) );		
		
		parent::__construct( $properties );
	}

	public function title() {
		return __( 'Iubenda Embed', 'svbk-shortcakes' );
	}

	public function fields() {
	    
		return array(
			'policy_id' => array(
				'label'  => esc_html__( 'Policy ID', 'svbk-shortcakes' ),
				'attr'   => 'policy_id',
				'type'   => 'text',
				'description' => esc_html__( 'Insert the policy ID to embed it', 'svbk-shortcakes' ),
			),
			'policy_type' => array(
				'label'  => esc_html__( 'Cookie Policy ID', 'svbk-shortcakes' ),
				'attr'   => 'policy_type',
				'type'   => 'radio',
				'options' => array(
				    'privacy-policy' => esc_html__( 'Privacy', 'svbk-shortcakes' ),
				    'cookie-policy' => esc_html__( 'Cookie', 'svbk-shortcakes' )
				),
			),
			'remove_styles' => array(
				'label'  => esc_html__( 'Remove Default Styles', 'svbk-shortcakes' ),
				'attr'   => 'remove_styles',
				'type'   => 'checkbox',
				'description' => esc_html__( 'Remove Default Iubenda Styles', 'svbk-shortcakes' ),
			),	
			'strip_headings' => array(
				'label'  => esc_html__( 'Remove Policy Headings', 'svbk-shortcakes' ),
				'attr'   => 'strip_headings',
				'type'   => 'checkbox',
				'description' => esc_html__( 'Strips the first H1/H2 tags from the policy', 'svbk-shortcakes' ),
			),	
		);
	}

	public function ui_args() {

		$args = parent::ui_args();

		unset($args['inner_content']);

		return $args;

	}
	
	public function renderOutput( $attr, $content, $shortcode_tag ) {
		
		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

        $output = '';

        $policy_html = $this->iubenda->getPolicy( array_filter( $attr ) );

        if( $attr['strip_headings'] ) {
            $policy_html = preg_replace('/<h1[^>]*>([\s\S]*?)<\/h1[^>]*>/', '', $policy_html, 1);
            $policy_html = preg_replace('/<h2[^>]*>([\s\S]*?)<\/h2[^>]*>/', '', $policy_html, 1);
        }

        if( $policy_html ) {
		    $output = $policy_html;
        }

		return $output;

	}	

}
