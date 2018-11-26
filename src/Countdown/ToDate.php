<?php 
namespace Svbk\WP\Shortcakes\Countdown;

use Svbk\WP\Shortcakes\Shortcake;
use Svbk\WP\Helpers;
use DateInterval;
use DateTime;
use DateTimeImmutable;

class ToDate extends Shortcake {
    
	public $shortcode_id = 'countdown';
	public $icon = 'dashicons-backup';
	public $classes = array( 'countdown' );

	public static $defaults = array(
		'date' => '',
		'recurrence' => '',
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
				'recurrence' => array(
					'label'  => esc_html__( 'Recurrence', 'svbk-shortcakes' ),
					'attr'   => 'recurrence',
					'type'   => 'text',
					'description' => sprintf( sprintf( __( 'Insert the recurrence period (ex. 3M: 3 months, 1D: 1 day, T5M: 1 minutes, 2MT10M: 2 months and 10 minutes, etc) or one of the accepted by <a href="%s">PHP DateInterval</a>', 'svbk-shortcakes' ), 'http://php.net/manual/en/dateinterval.construct.php' ) ),
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
	}

	public function output( $attr, $content, $shortcode_tag ) {

        static $index = 0;
        
        $index++;

		$attr = $this->shortcode_atts( self::$defaults, $attr, $shortcode_tag );

        $date = new DateTime($attr['date']);
        $id = $attr['id'] ?: ('countdown-' . $index); 
        
        
    	if( $attr['recurrence'] ) {
	        try {
		       	$recurrence = new DateInterval('P' . $attr['recurrence'] );	
	        } catch( Exception $e ) {
	        	$recurrence = null;
	        }
    	}
       
        if( !empty( $recurrence ) ) {
        
	        $now = new DateTime('now');        	
        	
	        while( $date <= $now )  {
	        	
				if ( version_compare(PHP_VERSION, '5.6.0') >= 0) {
				    $check_date = DateTimeImmutable::createFromMutable($date);
				} else {
				    $check_date = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.uP', $date->format('Y-m-d\TH:i:s.uP'));
				}
				
	        	$date->add( $recurrence );

				//Check if there has been no increment
				if ( ! ($date > $check_date) ) {
					break;
				}
	        	
	        }
	        
        }

        $format = $attr['format'] ?: __('%D days %H:%M:%S', 'svbk-shortcakes');

        $output = '<div id="' . $id . '" ' . $this->renderClasses( $this->getClasses($attr) ) . '></div>';
        $output .= '
        <script type="text/javascript">
            (function($){
                $(document).ready(function(){
                    $("#' . $id .'")
                    .countdown("' . $date->format('Y/m/d') . '", function(event) {
                        $(this).html(
                            event.strftime(\'' . $format . '\')
                        );
                    });
                });
            })(jQuery);
        </script>
        ';
        
        return $output;
    
	}    
    
}