<?php

namespace Svbk\WP\Shortcakes\Forms;

use Svbk\WP\Shortcakes\Shortcake;

class Input extends Shortcake {

	public $shortcode_id = 'form_input';
	public $icon = 'dashicons-editor-ol';
	public $dataAttribute = 'fdata';

	public static $defaults = array(
		'field' => '',
	);

	public function title() {
		return __( 'Form Input Data', 'svbk-shortcakes' );
	}

	public function fields() {

		return array(
			array(
				'label'  => esc_html__( 'Data Field', 'svbk-shortcakes' ),
				'attr'   => 'field',
				'type'   => 'text',
			),
		);

	}

	public function output( $attr, $content, $shortcode_tag ) {

		$attr = $this->shortcode_atts( self::$defaults ,$attr, $shortcode_tag );

		if ( defined( 'SHORTCODE_UI_DOING_PREVIEW' ) && (SHORTCODE_UI_DOING_PREVIEW === true) ) {
			return false;
		}

        if( empty( $attr['field'] ) ) {
            return 'no field specified';
        }
        
        $data = filter_input( INPUT_GET,$this->dataAttribute, FILTER_SANITIZE_STRING );
        
        if( empty($data) ) {
            return 'no data available';
        }     
        
        $data = base64_decode($data);
        
        if( false === $data ) {
            return 'data decode failed';
        }             

        $data = unserialize($data);

        if( (false === $data) || !isset( $data[ $attr['field'] ] ) ) {
            return 'data unserialization failed';
        }

		return esc_html($data[ $attr['field'] ]);

	}

}
