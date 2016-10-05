<?php

namespace Svbk\WP\Shortcakes;

class TeamMember extends Base {
    
    public $shortcode_id = 'team_member';
    public $title = 'Team Member';

    function fields(){
        
        $users = array();
        
        foreach(get_users(array( 'fields' => array('ID', 'display_name' ) )) as $user){
            $users[$user->ID] = $user->display_name;  
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
    			'type'     => 'url'
    		),  
    		array(
    			'label'    => esc_html__( 'Show role', 'svbk-shortcakes' ),
    			'attr'     => 'show_role',
    			'type'     => 'checkbox'
    		),  
    		array(
    			'label'    => esc_html__( 'Show description', 'svbk-shortcakes' ),
    			'attr'     => 'show_desc',
    			'type'     => 'checkbox'
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
    			'encode' => true,
    			'meta'   => array(
    				'placeholder' => esc_html__( 'Contact me', 'svbk-shortcakes' ),
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
    		'user' => get_current_user_id(),
    		'url' => '',
    		'link_dest' => 'dialog',
    		'show_role' => 'false',
    		'show_desc' => 'false',
    		'button_label' => _('Read More'),
    	), $attr, $shortcode_tag );

    
        $user = get_user_by('id', $attr['user']); 
        
        if(!is_a($user, 'WP_User')) {
            return __('User not found', 'svbk-shortcakes');
        }
        
        
        $output = '';
        
        
        $output  .= '<div class="author vcard ' . ( ('dialog' === $attr['link_dest']) ? 'dialogable' : '' ) . '">';
        if('dialog' === $attr['link_dest']) {
            $output  .= '<div class="dialog-content">';
        }        
		$output .=    get_avatar( $user->ID );
		$output .= '    <div class="author-info">';
		$output .= '        <span class="fn n">'.esc_html( $user->display_name ).'</span>';
		
		if( $attr['show_role'] == 'true' ) {
		
    		if($role = get_the_author_meta('team_role', $user->ID)) {
    		    $output .= '    <p class="author-role" >' . $role . '</p>';
    		}
    		
    		$output .= '        <a class="author-email" href="mailto:' . $user->user_email . '">' . $user->user_email . '</a>';
    		
		}
		
		if( ($attr['show_desc'] == 'true') && ($description = get_the_author_meta('description', $user->ID) ) ){
		    $output .= '    <p class="author-description" >' . $description . '</p>';
		}
		
		switch ($attr['link_dest']) {
    		case 'dialog':
    		    $output .= '        <button class="action-button dialog-open">' . $attr['button_label']. '</button>';    
    		    break;
    		case 'url':
    		    $output .= '        <a href="' . esc_attr($attr['url']) . '" class="action-button">' . $attr['button_label']. '</a>'; 
    		    break;
    		case 'mail':
    		    $output .= '        <a href="mailto:' . $user->user_email . '" class="action-button">' . $attr['button_label']. '</a>'; 
    		    break;
    		case 'profile':
    		    $output .= '        <a href="' . get_author_posts_url($user->ID) . '" class="action-button">' . $attr['button_label']. '</a>'; 
    		    break;		    
		}
		
		$output .= '    </div>';
		
        if('dialog' === $attr['link_dest']) {
            $output  .= '</div>';
        }  		
		
		$output .= '</div>';
        
        return $output;
        
    }
    
}
