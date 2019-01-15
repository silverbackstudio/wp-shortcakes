<?php 
namespace Svbk\WP\Shortcakes\Marketing;

use Svbk\WP\Shortcakes\Shortcake;
use Svbk\WP\Helpers;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;

class Countdown extends Shortcake {
    
	public $shortcode_id = 'countdown';
	public $icon = 'dashicons-backup';
	public $classes = array( 'countdown' );

	public static $defaults = array(
		'date' => '',
		'timeout' => '',
		'recurrent' => '',
		'format' => '',
		'id' => '',
		'class' => '',
	);

	public function title() {
		return __( 'CountDown', 'svbk-shortcakes' );
	}

	public function fields() {
		return array(
				'date' => array(
					'label'  => esc_html__( 'CountDown', 'svbk-shortcakes' ),
					'attr'   => 'date',
					'type'   => 'text',
					'description' => sprintf( __( 'Insert date in the dd-mm-YYYY format, or one of the accepted by <a href="%s">PHP strtotime</a>', 'svbk-shortcakes' ), 'http://php.net/manual/en/function.strtotime.php' ),
				),
				'timeout' => array(
					'label'  => esc_html__( 'Timeout', 'svbk-shortcakes' ),
					'attr'   => 'timeout',
					'type'   => 'text',
					'description' => sprintf( sprintf( __( 'Insert the recurrence period (ex. 3M: 3 months, 1D: 1 day, T5M: 5 minutes, 2MT10M: 2 months and 10 minutes, etc) or one of the accepted by <a href="%s">PHP DateInterval</a>', 'svbk-shortcakes' ), 'http://php.net/manual/en/dateinterval.construct.php' ) ),
				),
				'recurrent' => array(
					'label'  => esc_html__( 'Recurrent', 'svbk-shortcakes' ),
					'attr'   => 'recurrent',
					'type'   => 'checkout',
					'description' =>  __( 'Set the timeout as recurrent', 'svbk-shortcakes' ),
				),				
				'format' => array(
					'label'  => esc_html__( 'Format', 'svbk-shortcakes' ),
					'attr'   => 'format',
					'type'   => 'text',
					'encode' => true,
					'description' => sprintf( __( 'Insert date format as specified in <a href="%s">JS strftime</a>', 'svbk-shortcakes' ), 'http://hilios.github.io/jQuery.countdown/documentation.html#format' ),
				),	
				'id' => array(
					'label'  => esc_html__( 'HTML ID', 'svbk-shortcakes' ),
					'attr'   => 'id',
					'type'   => 'text',
					'description' => '',
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

		unset($args['inner_content']);

		return $args;
	}

	public function register_scripts(){
		parent::register_scripts();
		
		Helpers\Assets\Script::enqueue( 'jquery-countdown', '/dist/jquery.countdown.js', [ 'version' => '2', 'deps' => 'jquery' ] );
		wp_add_inline_script( 'jquery-countdown', '
		(function($){
                $(document).ready(function(){
                    $(".countdown").each(function(){
                    	$(this).countdown( $(this).data(\'expires\'), function(event) {
                        	$(this).html( event.strftime($(this).data(\'format\') ) );
                    	});
                	});
                });                	
        })(jQuery);' 
       );
		
		
	}

	public function expires( $attr ){
		
		$now = new DateTime('now');        	

       	$expireDate = $attr['date'] ? new DateTime($attr['date']) : $now;

    	if( $attr['timeout'] ) {
	        
	        try {
		       	$timeout = new DateInterval('P' . $attr['timeout'] );
	        } catch( Exception $e ) {
	        	$timeout = null;
	        }
	        
	        if ( $now <= $expireDate ) {
	    		$expireDate->add( $timeout );
	        }
	        
    	}        
        
        if( $timeout && filter_var( $attr['recurrent'], FILTER_VALIDATE_BOOLEAN ) ) {
        
	        while( $expireDate <= $now )  {
	        	
				if ( version_compare(PHP_VERSION, '5.6.0') >= 0) {
				    $check_date = DateTimeImmutable::createFromMutable($expireDate);
				} else {
				    $check_date = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.uP', $expireDate->format('Y-m-d\TH:i:s.uP'));
				}
				
	        	$expireDate->add( $timeout );

				//Check if there has been no increment
				if ( ! ($expireDate > $check_date) ) {
					break;
				}
	        	
	        }
	        
        }		
		
       return $expireDate;
		
	}

	public function output( $attr, $content, $shortcode_tag ) {

        static $index = 0;
        
        $index++;

		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

        $id = $attr['id'] ?: ('countdown-' . $index); 

		$date = $this->expires($attr);
		$date->setTimezone( new DateTimeZone( get_option('timezone_string') ?: (get_option('gmt_offset').'00') )  );
        
        $format = $attr['format'] ?: __('%D days %H:%M:%S', 'svbk-shortcakes');

        $output = '<div id="' . $id . '" ' . $this->renderClasses( $this->getClasses($attr) ) . ' data-expires="' . esc_attr($date->format('Y/m/d H:i:s')) . '" data-format="' . $format . '" ></div>';

        return $output;
    
	}    
    
}