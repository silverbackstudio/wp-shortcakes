<?php

namespace Svbk\WP\Shortcakes\User;

use Svbk\WP\Shortcakes\Shortcake;

class Thumb extends Shortcake {

	public $shortcode_id = 'team_member';

	public static $defaults  = array(
		'user' => 0,
		'url' => '',
		'link_dest' => 'dialog',
		'show_role' => 'false',
		'show_desc' => 'false',
		'button_label' => 'Read More',
	);

	public $icon = 'dashicons-admin-users';

	public function title() {
		return __( 'Team Member', 'svbk-shortcakes' );
	}

	public function fields() {

		$users = array();

		$users_get = get_users(
			array(
				'fields' => array(
					'ID',
					'display_name',
				),
			)
		);

		foreach ( $users_get as $user ) {
			$users[ $user->ID ] = $user->display_name;
		}

		return array(
			array(
				'label'  => esc_html__( 'User', 'svbk-shortcakes' ),
				'attr'   => 'user',
				'type'   => 'select',
				'options'     => $users,
				'encode' => false,
				'meta'   => array(
					'placeholder' => esc_html__( 'Insert title', 'svbk-shortcakes' ),
				),
			),
			array(
				'label'    => esc_html__( 'URL', 'svbk-shortcakes' ),
				'attr'     => 'url',
				'type'     => 'url',
			),
			array(
				'label'    => esc_html__( 'Show role', 'svbk-shortcakes' ),
				'attr'     => 'show_role',
				'type'     => 'checkbox',
			),
			array(
				'label'    => esc_html__( 'Show description', 'svbk-shortcakes' ),
				'attr'     => 'show_desc',
				'type'     => 'checkbox',
				),
				array(
				'label'       => esc_html__( 'Link destination', 'svbk-shortcakes' ),
				'attr'        => 'link_dest',
				'type'        => 'select',
				'options'     => array(
					'url'     => esc_html__( 'URL', 'svbk-shortcakes' ),
					'mail'    => esc_html__( 'Mail', 'svbk-shortcakes' ),
					'dialog'   => esc_html__( 'Popup', 'svbk-shortcakes' ),
					'profile' => esc_html__( 'Profile', 'svbk-shortcakes' ),
				),
			),
			array(
				'label'  => esc_html__( 'Button Label', 'svbk-shortcakes' ),
				'attr'   => 'button_label',
				'type'   => 'text',
				'meta'   => array(
					'placeholder' => esc_html__( 'Contact me', 'svbk-shortcakes' ),
				),
			),
		);
	}

	public function output( $attr, $content, $shortcode_tag ) {
		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

		$user = get_user_by( 'id', $attr['user'] );

		if ( ! is_a( $user, 'WP_User' ) ) {
			return __( 'User not found', 'svbk-shortcakes' );
		}

		$output = '';

		$output  .= '<div class="author vcard ' . ( ('dialog' === $attr['link_dest']) ? 'dialogable' : '' ) . '">';
		if ( 'dialog' === $attr['link_dest'] ) {
			$output  .= '<div class="dialog-content">';
		}
		$output .= get_avatar( $user->ID, 240 );
		$output .= '    <div class="author-info">';
		$output .= '        <span class="fn n">' . esc_html( $user->display_name ) . '</span>';

		if ( 'true' == $attr['show_role'] ) {

			if ( $role = get_the_author_meta( 'team_role', $user->ID ) ) {
				$output .= '    <p class="author-role" >' . $role . '</p>';
			}

			$output .= '        <a class="author-email" href="mailto:' . $user->user_email . '">' . $user->user_email . '</a>';

		}

		if ( ( 'true' == $attr['show_desc'] ) && ($description = get_the_author_meta( 'description', $user->ID ) ) ) {
			$output .= '    <p class="author-description" >' . $description . '</p>';
		}

		$button_label = $attr['button_label'];

		switch ( $attr['link_dest'] ) {
			case 'dialog':
				$output .= '        <button class="action-button dialog-open">' . $button_label . '</button>';
				break;
			case 'url':
				$output .= '        <a href="' . esc_attr( $attr['url'] ) . '" class="action-button">' . $button_label . '</a>';
				break;
			case 'mail':
				$output .= '        <a href="mailto:' . $user->user_email . '" class="action-button">' . $button_label . '</a>';
				break;
			case 'profile':
				$output .= '        <a href="' . get_author_posts_url( $user->ID ) . '" class="action-button">' . $button_label . '</a>';
				break;
		}

		$output .= '    </div>';

		if ( 'dialog' === $attr['link_dest'] ) {
			$output  .= '</div>';
		}

		$output .= '</div>';

		return $output;

	}

}
