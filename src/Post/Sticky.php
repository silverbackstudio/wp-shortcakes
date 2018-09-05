<?php

namespace Svbk\WP\Shortcakes\Post;

class Sticky extends Latest {

	public $shortcode_id = 'sticky_posts';
	public $icon = 'dashicons-pressthis';
	public $classes = array( 'sticky-posts', 'post-thumbs' );

	public function title() {
		return __( 'Sticky Posts', 'svbk-shortcakes' );
	}

	protected function getQueryArgs( $attr ) {

		$query_args = parent::getQueryArgs( $attr );

		$query_args['post__in'] = get_option( 'sticky_posts' );
		$query_args['ignore_sticky_posts'] = 1;

		return $query_args;

	}

}
