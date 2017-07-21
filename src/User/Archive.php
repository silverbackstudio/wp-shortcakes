<?php

namespace Svbk\WP\Shortcakes\User;

use Svbk\WP\Shortcakes\Shortcake;

class Archive extends Shortcake {

	public $defaults = array(
			'role' => '',
	);

	public $shortcode_id = 'user_list';
	public $template = '<div class="avatar">%s</div><div class="name fn">%s</div><div class="description">%s</div>';
	public $classes = 'users';
	public $icon = 'dashicons-groups';


	public function title() {
		return __( 'User List', 'svbk-shortcakes' );
	}

	public function fields() {

		$roles = get_editable_roles();

		return array(
			'role' => array(
				'label'    => esc_html__( 'Select Post', 'svbk-shortcakes' ),
				'attr'     => 'role',
				'type'     => 'select',
				'options'    => wp_list_pluck( $roles, 'name' ),
				'multiple' => false,
			),
			'class' => array(
				'label'  => esc_html__( 'Classes', 'svbk-shortcakes' ),
				'attr'   => 'class',
				'type'   => 'text',
			),
		);
	}

	public function renderOutput( $attr, $content, $shortcode_tag ) {

		$attr = $this->shortcode_atts( $this->defaults, $attr, $shortcode_tag );

		$output = '';

		$users = get_users( $attr );

		$output .= '<div class="' . join( ' ', $this->getClasses( $attr ) ) . '" >';

		foreach ( $users as $user ) {

			$output .= '<div class="user profile" >';

			$output .= sprintf(
				$this->template,
				get_avatar( $user->ID, '256' ),
				esc_html( $user->display_name ),
				esc_html( $user->description )
			);

			$output .= '</div>';
		}

		$output .= '</div>';

		return $output;

	}

}
