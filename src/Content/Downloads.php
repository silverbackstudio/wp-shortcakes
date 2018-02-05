<?php
namespace Svbk\WP\Shortcakes\Content;

use Svbk\WP\Shortcakes\Shortcake;

class Downloads extends Shortcake {

	public $shortcode_id = 'downloads';
	public $icon = 'dashicons-download';
	public $logged_only = false;
	
	public $download_arg = 'download_file';
	
	public $classes = array( 'downloads' );

	public $renderOrder = array(
		'wrapperBegin',
		'title',
		'content',
		'downloadsBegin',
		'downloads' => [
			'linkBegin',
			'name',
			'filetype',
			'size',			
			'caption',
			'description',
			'linkEnd',
		],
		'wrapperEnd',
		'downloadsEnd',
	);

	public static $defaults = array(
		'title' =>'',
		'files' => 0,
		'class' => '',
		'hide_size' => false,
		'hide_type' => false,
		'hide_caption' => false,
		'hide_description' => false,
		'logged_only' => '',
	);

	public static function register( $options = array() ) {

		$instance = parent::register( $options );

		add_action( 'init', array( $instance, 'download' ) );

		return $instance;
	}

	public function title() {
		return __( 'Downloads', 'svbk-shortcakes' );
	}

	public function fields() {
		return array(
			'title' => array(
				'label'  => esc_html__( 'Title', 'svbk-shortcakes' ),
				'attr'   => 'title',
				'type'   => 'text',
				'default' => '',
				'encode' => true,
			),			
			'files'=>array(
				'label'       => __( 'Files', 'svbk-shortcakes' ),
				'attr'        => 'files',
				'description' => esc_html__( 'You can select multiple files.', 'svbk-shortcakes' ),
				'type'        => 'attachment',
				'multiple'    => true,
				'addButton'   => __( 'Select Files', 'svbk-shortcakes' ),
				'frameTitle'  => __( 'Select Files', 'svbk-shortcakes' ),
			),
			'logged_only' => array(
				'label'  => esc_html__( 'Logged Only', 'svbk-shortcakes' ),
				'attr'   => 'logged_only',
				'type'   => 'checkbox',
				'default' => self::$defaults['logged_only'],
				'description' => esc_html__( 'Allow downloads only to logged in users', 'svbk-shortcakes' ),
			),
			'hide_size' => array(
				'label'  => esc_html__( 'Hide Size', 'svbk-shortcakes' ),
				'attr'   => 'hide_size',
				'type'   => 'checkbox',
				'default' => false,
				'description' => esc_html__( 'Hide file size for each download', 'svbk-shortcakes' ),
			),	
			'hide_type' => array(
				'label'  => esc_html__( 'Hide Type', 'svbk-shortcakes' ),
				'attr'   => 'hide_type',
				'type'   => 'checkbox',
				'default' => false,
				'description' => esc_html__( 'Hide file type for each download', 'svbk-shortcakes' ),
			),	
			'hide_caption' => array(
				'label'  => esc_html__( 'Hide Caption', 'svbk-shortcakes' ),
				'attr'   => 'hide_caption',
				'type'   => 'checkbox',
				'default' => false,
				'description' => esc_html__( 'Hide file caption for each download', 'svbk-shortcakes' ),
			),	
			'hide_description' => array(
				'label'  => esc_html__( 'Hide Description', 'svbk-shortcakes' ),
				'attr'   => 'hide_description',
				'type'   => 'checkbox',
				'default' => false,
				'description' => esc_html__( 'Hide file description for each download', 'svbk-shortcakes' ),
			),				
			'class' => array(
				'label'  => esc_html__( 'Classes', 'svbk-shortcakes' ),
				'attr'   => 'class',
				'type'   => 'text',
			),				
		);
	}

	public function ui_args() {

		$args = parent::ui_args();

		$args['inner_content']['label'] = __( 'Description', 'svbk-shortcakes' );

		return $args;

	}

	public function getLink( $file_id ) {
		return add_query_arg( $this->download_arg, $file_id, get_home_url( ) );
	}

	public function download() {
		
		$file_id = filter_input( INPUT_GET, $this->download_arg, FILTER_VALIDATE_INT );

		if( !$file_id || ( 'publish' !== get_post_status( $file_id ) ) ) { 
			return;
		}
		
		$file_url = wp_get_attachment_url( $file_id );
		
		if( apply_filters('shortcode_download_restrict_file', $this->logged_only && ! is_user_logged_in(), $file_id ) ) {
			wp_die( __('File download not allowed', 'svbk-shortcakes') );
		}
			
		if( $file_url && class_exists('WC_Download_Handler') ) {
			\WC_Download_Handler::download( $file_url, 1 );
		} 
		
	}

	public function renderOutput( $attr, $content, $shortcode_tag ) {
		
		static $instance = 0;
		
		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

		$output = parent::renderOutput($attr, $content, $shortcode_tag);

		$file_ids = explode(',', $attr['files']);
		
		if( empty($file_ids) ) {
			return array();
		}
		
		$id = $this->shortcode_id;
		
		if( $instance++ > 1 ) {
			$id .= '-' . $instance; 
		}
		
		$output['wrapperBegin'] = '<section id="' . $id . '" ' . $this->renderClasses( $this->getClasses( $attr ) ) . ' >';
		
		if( $attr['title'] ) {
			$output['title'] = '<h2>' . urldecode($attr['title']) . '</h2>';
		}
		
		$single_template = $this->renderOrder['downloads'];

		$output['downloadsBegin'] = '<ul>';
	
		$output['downloads'] = '';
		
		foreach( $file_ids as $file_id ) {
			
			$download = array();
			
			$file = get_post( absint($file_id) );
			
			if( !$file || ('attachment' !== get_post_type($file) ) ) {
				continue;
			}
			
			$link = $this->getLink( $file->ID );
			$attached_file = get_attached_file( $file->ID );
			$filetype = wp_check_filetype($attached_file);

			$download['linkBegin'] = '<li ' . $this->renderClasses( get_post_class( $filetype['ext'], $file->ID ) ) . ' ><a class="download" href="' . esc_url( $link ) . '" target="_blank"  >';
			
			$download['name'] = '<span class="entry-title">' . get_the_title( $file ) . '</span>';

			if( !filter_var( $attr['hide_caption'], FILTER_VALIDATE_BOOLEAN ) && ( $caption = wp_get_attachment_caption($file->ID) ) ) {
				$download['caption'] = ' <span class="caption">' . $caption . '</span>';
			}
			
			if( !filter_var( $attr['hide_description'], FILTER_VALIDATE_BOOLEAN ) && ($description = get_post_field( 'post_content', $file->ID ) ) ) {
				$download['description'] =  ' <span class="description">' . $description . '</span>';
			}
			
			if( !filter_var( $attr['hide_type'], FILTER_VALIDATE_BOOLEAN ) && !empty($filetype['ext']) ) {
				$download['filetype'] = ' <span class="filetype">' . $filetype['ext'] . '</span>';
			}

			if( !filter_var( $attr['hide_size'], FILTER_VALIDATE_BOOLEAN ) && ($size = filesize( get_attached_file( $file->ID ) ) ) ) {
				$download['size'] = ' <span class="filesize">' . size_format($size) . '</span>';
			}

			$download['linkEnd'] = '</a></li>';
		
			$output['downloads'] .= $this->outputParts($download, $single_template);			
			
		}
		
		$output['downloadsEnd'] = '</ul>';
		
		$output['wrapperEnd'] = '</section>';

		return $output;

	}	

}
