<?php

namespace Svbk\WP\Shortcakes;

class Form extends Shortcake {
    
    public $defaults = array(
		'title' => '',
		'hidden' => false,
		'privacy_link' => '',
		'file' => '',
		'open_button_label' => 'Open',
		'submit_button_label' => 'Submit',
    );
    
    public $shortcode_id = 'svbk-form';
    public $field_prefix = 'frm';    
    public $action = 'svbk_form';
    public $formClass = '\Svbk\WP\Helpers\Form\Submission';
    public $confirmMessage = '';

    public $recipientEmail = 'webmaster@silverbackstudio.it';    
    public $recipientName = 'Webmaster'; 

    public $renderOrder = array(
        'wrapperBegin',
        'openButton',  
        'hiddenBegin',
        'closeButton',
        'hiddenContentBegin',
    	'formBegin',
    	'title',
    	'input',
    	'beginPolicySubmit',
        'policy',
        'submitButton',
        'messages',
        'endPolicySubmit',
        'formEnd',
        'hiddenContentEnd',
        'hiddenEnd',
        'wrapperEnd',
    );

    public function title(){
        return __('Form', 'svbk-shortcakes');
    }
    
    public static function register($options=array()){
        
        $instance = parent::register($options);
        
        add_action( "admin_post_nopriv_{$instance->action}", array($instance, 'processSubmission') );
        add_action( "admin_post_{$instance->action}", array($instance, 'processSubmission') ); 
        
        return $instance;
    }
    
    public function processSubmission(){
        
        $form = $this->getForm(true);
        
        $form->processSubmission();
        
        $errors = $form->getErrors();
        
        header('Content-Type: application/json');

        echo $this->formatResponse($errors, $form);
    }
    
    public function formatResponse($errors, $form) {
        
        
        if(!empty($errors)){
            
            return json_encode( array(
                'prefix' => $this->field_prefix,
                'status' => 'error', 
                'errors' => $errors
                )
            );
            
            return false;
        }
        
        return json_encode( 
            array(
                'prefix' => $this->field_prefix,
                'status'=>'success', 
                'message'=> $this->confirmMessage()
            ) 
        );        
        
    }
    
    public function confirmMessage(){
        return $this->confirmMessage ?: __('Thanks for your request, we will reply as soon as possible.', 'svbk-shortcakes');
    }
    
    public function fields(){
        return array(
    		'title' => array(
    			'label'  => esc_html__( 'Title', 'svbk-shortcakes' ),
    			'attr'   => 'title',
    			'type'   => 'text',
    			'encode' => false,
    			'description' => esc_html__( 'This title will replace the Page title', 'svbk-shortcakes' ),
    			'meta'   => array(
    				'placeholder' => esc_html__( 'Insert title', 'svbk-shortcakes' ),
    			),
    		),   
    		'hidden' => array(
    			'label'       => esc_html__( 'Hidden', 'svbk-shortcakes' ),
    			'attr'        => 'hidden',
                'type'        => 'select',
    			'options'     => array(
    				array( 'value' => false, 'label' => esc_html__( 'Always Visible', 'svbk-shortcakes', 'shortcode-ui' ) ),
    				array( 'value' => 'lightbox', 'label' => esc_html__( 'Lightbox', 'svbk-shortcakes', 'shortcode-ui' ) ),
    				array( 'value' => 'collapse', 'label' => esc_html__( 'Collapse', 'svbk-shortcakes', 'shortcode-ui' ) ),
    			),    			
    		),    		
    		'open_button_label' => array(
    			'label'  => esc_html__( 'Open button label', 'svbk-shortcakes' ),
    			'attr'   => 'open_button_label',
    			'type'   => 'text',
    			'encode' => true,
    			'description' => esc_html__( 'The label for the lightbox open button', 'svbk-shortcakes' ),
    			'meta'   => array(
    				'placeholder' => $this->defaults['open_button_label'],
    			),
    		),     
    		'submit_button_label' => array(
    			'label'  => esc_html__( 'Submit button label', 'svbk-shortcakes' ),
    			'attr'   => 'submit_button_label',
    			'type'   => 'text',
    			'encode' => true,
    			'description' => esc_html__( 'The label for submit button', 'svbk-shortcakes' ),
    			'meta'   => array(
    				'placeholder' => $this->defaults['submit_button_label'],
    			),
    		),         		
    		'classes' => array(
    			'label'    => esc_html__( 'Custom Classes', 'svbk-shortcakes' ),
    			'attr'     => 'classes',
    			'type'     => 'text',
    		),
    	);
    }
    
    protected function getForm($set_send_params=false){
        
        $formClass = $this->formClass;
        
        $form = new $formClass;
        
        $form->field_prefix = $this->field_prefix;
        $form->action = $this->action;
        
        if($set_send_params){
            $form->recipientEmail = $this->recipientEmail;
            $form->recipientName = $this->recipientName;            
        }
        
        return $form;
    }
    

    public function renderOutput($attr, $content, $shortcode_tag){
    
        static $index = 0;
        
        $index++;
    
    	$attr = $this->shortcode_atts( $this->defaults, $attr, $shortcode_tag );      
        $form = $this->getForm();

        $output = $form->renderParts( $this->action, $attr );

        $output['wrapperBegin'] = '<div class="whitepaper-dl svbk-form-container'.($attr['hidden']?(' svbk-hidden svbk-'.$attr['hidden']):'').'" id="' . $this->field_prefix . '-container-' . $index  . '">';
    
        if($attr['title']){
            $output['title'] = '<h2 class="form-title">'.$attr['title'].'</h2>';
        }
    
        switch($attr['hidden']){
            case 'collapse':
                $output['openButton'] = '<a class="button svbk-show-content svbk-collapse-open" href="#' . $this->field_prefix . '-container-' . $index .'" >' . urldecode( $attr['open_button_label'] ) . '</a>';
                $output['hiddenBegin'] = '<div class="svbk-collapse-container">';
                $output['closeButton'] = '<a class="button svbk-hide-content svbk-collapse-close" href="#' . $this->field_prefix . '-container-' . $index .'" ><span>' . __('Close', 'svbk-shortcakes') . '</span></a>';                
                $output['hiddenContentBegin'] = '<div class="svbk-form-content svbk-collapse-content">';
                $output['hiddenContentEnd'] = '</div>';
                $output['hiddenEnd'] = '</div>';                
                break;
            case 'lightbox':
                $output['openButton'] = '<a class="button svbk-show-content svbk-lightbox-open" href="#' . $this->field_prefix . '-container-' . $index .'" >' . urldecode( $attr['open_button_label'] ) . '</a>';
                $output['hiddenBegin'] = '<div class="svbk-lightbox-container">';
                $output['closeButton'] = '<a class="button svbk-hide-content svbk-collapse-close" href="#' . $this->field_prefix . '-container-' . $index .'" ><span>' . __('Close', 'svbk-shortcakes') . '</span></a>';                
                $output['hiddenContentBegin'] = '<div class="svbk-form-content svbk-lightbox-content">';
                $output['hiddenContentEnd'] = '</div>';
                $output['hiddenEnd'] = '</div>';                   
                break;                
        }
        
        $output['beginPolicySubmit'] = '<div class="form-policy-submit-wrapper">';
        $output['endPolicySubmit'] = '</div>';        

        $output['wrapperEnd'] = '</div>';
    	
    	return $output;
        
    }    
    
}
