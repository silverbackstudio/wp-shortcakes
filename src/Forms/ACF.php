<?php

namespace Svbk\WP\Shortcakes\Forms;

use Svbk\WP\Shortcakes\Shortcake;

class ACF extends Shortcake {

	public $shortcode_id = 'acf_form';

	public static $defaults = array(
		'submit_value' => 'Submit',
		'field_groups' => '',
		'post_title' => false,
		'post_content' => false,
	);

	public static function register( $options = array() ) {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		/* Checks to see if the acf pro plugin is activated  */
		if ( is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
			return parent::register( $options );
		}

	}

	public function title() {
		return __( 'ACF Form', 'svbk-shortcakes' );
	}

	public function fields() {

		$fields_groups = wp_list_pluck( acf_get_field_groups(), 'title', 'key' );

		return array(
			array(
				'label'  => esc_html__( 'Form Group', 'svbk-shortcakes' ),
				'attr'   => 'field_groups',
				'type'   => 'select',
				'options' => $fields_groups,
				'description' => esc_html__( 'Select the ACF Form group to display', 'svbk-shortcakes' ),
			),
			array(
				'label'  => esc_html__( 'Submit Button Label', 'svbk-shortcakes' ),
				'attr'   => 'submit_value',
				'type'   => 'text',
				'encode' => true,
				'description' => esc_html__( 'Submit Button label text', 'svbk-shortcakes' ),
			),
			array(
				'label'  => esc_html__( 'Show title field', 'svbk-shortcakes' ),
				'attr'   => 'post_title',
				'type'   => 'checkbox',
				'description' => esc_html__( 'Show the post title field', 'svbk-shortcakes' ),
			),
			array(
				'label'  => esc_html__( 'Show content field', 'svbk-shortcakes' ),
				'attr'   => 'post_content',
				'type'   => 'checkbox',
				'description' => esc_html__( 'Show the post content field', 'svbk-shortcakes' ),
			),
		);
	}

	public function output( $attr, $content, $shortcode_tag ) {

		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

		$attr['field_groups'] = (array) $attr['field_groups'];

		foreach ( $attr as &$attrValue ) {
			if ( ! is_array( $attrValue ) ) {
				$attrValue = urldecode( $attrValue );
			}
		}

		ob_start();

		acf_form( $attr );

		$output = ob_get_contents();
		ob_end_clean();

		return $output;

	}


}
