<?php
namespace Svbk\WP\Shortcakes\Content;

use Svbk\WP\Shortcakes\Shortcake;

class ReadingTime extends Shortcake {

	public $shortcode_id = 'reading_time';
	public $icon = 'dashicons-clock';
	public $classes = array( 'reading-time' );

	public $post_type = 'post';

	public $renderOrder = array(
		'content',
	);

	public static $defaults = array(
		'words_per_minute' => 200,
	);

	public function title() {
		return __( 'Reading Time', 'svbk-shortcakes' );
	}

	public function fields() {
		return array(
			'words_per_minute' => array(
				'label'  => esc_html__( 'Custom words per minute', 'svbk-shortcakes' ),
				'attr'   => 'words_per_minute',
				'type'   => 'number',
				'description' => esc_html__( 'Use a different words per minute value', 'svbk-shortcakes' ),
				'default' => self::$defaults['words_per_minute'],
			),
		);
	}

	public function ui_args() {
		$args = parent::ui_args();

		unset( $args['inner_content'] );

		return $args;
	}

	public function init() {
		
		add_action( 'save_post_' . $this->post_type, array( self::class, 'update_word_count'), 10, 3 );
		
		parent::init();
	}
	
	public static function update_word_count( $post_ID, $post, $update ){
		$content = apply_filters( 'the_content', $post->post_content );
		$words = str_word_count( strip_tags( $content ) );
		$reading_time = ceil( $words / self::$defaults['words_per_minute'] );		
		
		update_post_meta( $post_ID, 'word_count', $words );
	}

	public function renderOutput( $attr, $content, $shortcode_tag ) {
		
		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

		$output = parent::renderOutput($attr, $content, $shortcode_tag);

		$word_count = get_post_meta( get_the_ID(), 'word_count', true );
		
		if ( ! $word_count ) {
			return $output;
		}

		$minutes = ceil( $word_count / $attr['words_per_minute'] );

		$est = sprintf( _n('%s min', '%s mins', $minutes, 'svbk-shortcakes'), $minutes ) ;

		$classes = $this->getClasses( $attr );

		$output['content'] = '<span ' . $this->renderClasses( $classes ) . ' data-wpm="' . esc_attr( $attr['words_per_minute'] ) . '" data-wc="' . esc_attr( $word_count ) . '">' . 
			'<span ' . $this->renderClasses( $classes, '__label' ) . '>' . _x('Reading', 'post reading time label', 'svbk-shortcakes') . ': </span>' . 
			'<span ' . $this->renderClasses( $classes, '__value' ) . '>' . $est . '</span>' .
		'</span>';
	
		return $output;

	}	

}
