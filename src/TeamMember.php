<?php

namespace Svbk\WP\Shortcakes;

class TeamMember extends Base {
    
    public $shortcode_id = 'team_member';
    public $title = 'Team Member';

    function fields(){
        
        $users = array();
        
        foreach(get_users(array( 'fields' => array( 'display_name' ) )) as $user_id=>$username){
            $users[$user_id] = $username->display_name;  
        }
        
        return array(
    		array(
    			'label'  => esc_html__( 'Utente', 'turini' ),
    			'attr'   => 'user',
    			'type'   => 'select',
    			'options'     => $users,    			
    			'encode' => false,
    			'meta'   => array(
    				'placeholder' => esc_html__( 'Insert title', 'turini' ),
    			),
    		),
 		
    	);
    }
    
    // function ui_args(){
        
    //     $ret = parent::ui_args();
        
    //     unset($ret['inner_content']);
        
    //     return $ret;
        
    // }
    
    function output( $attr, $content, $shortcode_tag ) {
    	$attr = shortcode_atts( array(
    		'user' => wp_get_current_user(),
    	), $attr, $shortcode_tag );
    
        $user = get_user_by('id', $attr['user']); 
        
        $output  = '<div class="author vcard dialogable">';
		$output .=    get_avatar( $user->ID );
		$output .= '    <div class="author-info">';
		$output .= '        <span class="fn n">'.esc_html( $user->display_name ).'</span>';
		
		if($role = get_the_author_meta('team_role', $user->ID)) {
		    $output .= '    <p class="author-role" >' . $role . '</p>';
		}
		
		$output .= '        <a class="author-email" href="mailto:' . $user->user_email . '">' . $user->user_email . '</a>';
		
		if($description = get_the_author_meta('description', $user->ID)){
		    $output .= '    <p class="author-description" >' . $description . '</p>';
		}
		
		$output .= '        <button class="dialog-open">'.__('Read more', 'turini').'</button>';    
		$output .= '    </div>';
		
		$output .= '</div>';
        
        return $output;
        
    }
    
}
