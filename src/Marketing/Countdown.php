<?php 
namespace Svbk\WP\Shortcakes\Marketing;

use Svbk\WP\Shortcakes\Shortcake;
use Svbk\WP\Helpers;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use DatePeriod;
use Exception;

class Countdown extends Shortcake {
    
	public $shortcode_id = 'countdown';
	public $icon = 'dashicons-backup';
	public $classes = array( 'countdown' );

	public static $defaults = array(
		'date' => '',
		'interval' => '',
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
					'description' => sprintf( __( 'Insert date in the dd-mm-YYYY format, or one of the accepted by <a href="%s">PHP strtotime</a>. If no date is specified the current time is used.', 'svbk-shortcakes' ), 'http://php.net/manual/en/function.strtotime.php' ),
				),
				'interval' => array(
					'label'  => esc_html__( 'Interval', 'svbk-shortcakes' ),
					'attr'   => 'interval',
					'type'   => 'text',
					'description' => sprintf( sprintf( __( 'Insert the interval (ex. P3M: 3 months, P1D: 1 day, PT5M: 5 minutes, P2MT10M: 2 months and 10 minutes, etc) or one of the accepted by <a href="%s">PHP DateInterval</a>', 'svbk-shortcakes' ), 'http://php.net/manual/en/dateinterval.construct.php' ) ),
				),
				'recurrent' => array(
					'label'  => esc_html__( 'Recurrent', 'svbk-shortcakes' ),
					'attr'   => 'recurrent',
					'type'   => 'text',
					'description' =>  __( 'Set the countdown as recurrent. Uses the "date" ad start point and the "interval" as the recurrent period', 'svbk-shortcakes' ),
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
                    	expires = $(this).data(\'expires\');
                    	
                    	if( $.isNumeric( expires ) ) {
                    		expires += new Date().getTime();
                    	}
                    	
                    	$(this).countdown( expires, function(event) {
                        	$(this).html( event.strftime($(this).data(\'format\') ) );
                    	});
                	});
                });                	
        })(jQuery);' 
       );
		
		
	}

	public function expires( $attr ){
		
		$now = new DateTimeImmutable('now');        	
       	$expireDate = $attr['date'] ? new DateTimeImmutable($attr['date']) : null;

		$targetDate = new DateTime('now');
		$targetDate->setTimezone( new DateTimeZone( get_option('timezone_string') ?: (get_option('gmt_offset').'00') )  );

        if ( $expireDate ) {
        	$targetDate->setTimestamp($expireDate->getTimestamp());
        }

    	if( $attr['interval'] ) {
	       
	        try {
		       	$interval = new DateInterval( $attr['interval'] );
	    		$targetDate->add( $interval );		       	
	        } catch( Exception $e ) {
	        	$interval = null;
	        }
	        
    	}        
		
    	if( $interval && filter_var( $attr['recurrent'], FILTER_VALIDATE_BOOLEAN ) ) {

			$recurrence_ends = $now->add( $interval );
        	$periods = new DatePeriod( $expireDate, $interval, $recurrence_ends );

			// Set the last occurrence
        	foreach( $periods as $date ) {
        		$targetDate = $date;
        	}
        
    	}		
		
       return $targetDate;
		
	}

	public function output( $attr, $content, $shortcode_tag ) {

        static $index = 0;
        
        $index++;

		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );
        $id = $attr['id'] ?: ('countdown-' . $index); 
		
		$date = $this->expires($attr);
        $format = $attr['format'] ?: __('%D days %H:%M:%S', 'svbk-shortcakes');
		$expires =  $date->format('Y/m/d H:i:s');
		
		// If no absolute date is specified, use relative format to support cached pages.
		if ( empty($attr['date']) ) {
			$now = new DateTime('now');
			$expires = ($date->getTimestamp() - $now->getTimestamp()) * 1000;
		}

        $output = '<div id="' . $id . '" ' . $this->renderClasses( $this->getClasses($attr) ) . ' data-expires="' . esc_attr($expires) . '" data-format="' . $format . '" ></div>';

        return $output;
    
	}    
    
}