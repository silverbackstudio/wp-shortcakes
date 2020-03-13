<?php
namespace Svbk\WP\Shortcakes\Post;

class Link extends Svbk\WP\Shortcakes\Content\Link {

	public $shortcode_id = 'post_link';
	public $icon = 'dashicons-admin-links';
	public $classes = array( 'link', 'post-link' );
	
	public $post_query = array(
		'post_type' => array( 'page', 'post' ),
	);

	public static $defaults = array(
		'post_id' => 0,
		'class' => '',
	);

	public function title() {
		return __( 'Post Link', 'svbk-shortcakes' );
	}

	public function fields() {
		
		$fields = parent::fields();		
		
		unset( $fields['url'] );
		
		$fields['label'] = array(
			'label'  => esc_html__( 'Post/Page to Link', 'svbk-shortcakes' ),
			'attr'   => 'post_id',
			'type'   => 'post_select',
			'query'    => $this->post_query,
			'multiple' => false,
			'description' => esc_html__( 'Select the post to link', 'svbk-shortcakes' ),
		);
		
		return $fields;
	}

	public function getLink( $attr ) {

		if ( $attr['post_id'] ) {
			return get_permalink( $attr['post_id'] );
		}

		return '';
	}

}
