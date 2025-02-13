<?php
/**
 * Plugin Name: Bangla Sidebar Login
 * Plugin URI: http://webaloy.net/archives/531
 * Description: It is an ajax enabled Bangla wordpress login form through which you can add a Bangla wordpress login widget in your website's sidebar.
 * Version:       1.0
 * Author:        Mirza Md. Hasan
 * Author URI:    http://webaloy.net/
 * License:       GNU General Public License, v2 (or newer)
 * License URI:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
/*  Copyright 2012 Mirza Md. Hasan (webaloy.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation using version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

load_plugin_textdomain('sblogin', false	, 'bn-sidebar-login/I18n/');
if (is_admin()) include( WP_PLUGIN_DIR . '/bn-sidebar-login/admin.php' );

/* Call via function */
function sidebarlogin( $args = '' ) {
	
	if (!is_array($args)) parse_str($args, $args);
	
	$defaults = array(
		'before_widget'=>'',
		'after_widget'=>'',
		'before_title'=>'<h2>',
		'after_title'=>'</h2>'
	);
	$args = array_merge($defaults, $args);
	
	widget_wp_sidebarlogin($args);
}

/* The widget */
function widget_wp_sidebarlogin($args) {
	global $user_ID, $current_user;
	
	/* To add more extend i.e when terms came from themes - suggested by dev.xiligroup.com */
	$defaults = array(
		'thelogin'=>'',
		'thewelcome'=>'',
		'theusername'=>__('ইউজারনেম:','sblogin'),
		'thepassword'=>__('পাসওয়ার্ড:','sblogin'),
		'theremember'=>__('আমায় মনে রাখুন','sblogin'),
		'theregister'=>__('সদস্য হতে চান?','sblogin'),
		'thepasslostandfound'=>__('পাসওয়ার্ড পুনরুদ্ধার','sblogin'),
		'thelostpass'=>	__('পাসওয়ার্ড ভুলে গেছেন?','sblogin'),
		'thelogout'=> __('লগ আউট','sblogin')
	);
	
	$args = array_merge($defaults, $args);
	extract($args);		
	
	get_currentuserinfo();

	if ($user_ID != '') {
	
		// User is logged in
		global $current_user;
  		get_currentuserinfo();
		
		if (empty($thewelcome)) $thewelcome = str_replace('%username%',ucwords($current_user->display_name),get_option('sidebarlogin_welcome_heading'));
		
		echo $before_widget . $before_title .$thewelcome. $after_title;
		
		if (get_option('sidebar_login_avatar')=='1') echo '<div class="avatar_container">'.get_avatar($user_ID, $size = '38').'</div>';
		
		echo '<ul class="pagenav">';
		
		if(isset($current_user->user_level) && $current_user->user_level) $level = $current_user->user_level;
				
		$links = do_shortcode(trim(get_option('sidebarlogin_logged_in_links')));
		
		$links = explode("\n", $links);
		if (sizeof($links)>0)
		foreach ($links as $l) {
			$l = trim($l);
			if (!empty($l)) {
				$link = explode('|',$l);
				if (isset($link[1])) {
					$cap = strtolower(trim($link[1]));
					if ($cap=='true') {
						if (!current_user_can( 'manage_options' )) continue;
					} else {
						if (!current_user_can( $cap )) continue;
					}
				}
				// Parse %USERNAME%
				$link[0] = str_replace('%USERNAME%',sanitize_title($current_user->user_login),$link[0]);
				$link[0] = str_replace('%username%',sanitize_title($current_user->user_login),$link[0]);
				// Parse %USERID%
				$link[0] = str_replace('%USERID%',$current_user->ID,$link[0]);
				$link[0] = str_replace('%userid%',$current_user->ID,$link[0]);
				echo '<li class="page_item">'.$link[0].'</li>';
			}
		}
		
		$redir = trim(stripslashes(get_option('sidebarlogin_logout_redirect')));
		if (!$redir || empty($redir)) $redir = sidebar_login_current_url('nologout');
		
		echo '<li class="page_item"><a href=" ' . wp_logout_url( $redir ) . '">' . $thelogout . '</a></li></ul>';
		
	} else {
	
		// User is NOT logged in!!!
		
		if (empty($thelogin)) $thelogin = get_option('sidebarlogin_heading');
		
		echo $before_widget . $before_title .'<span>'. $thelogin .'</span>' . $after_title;

		global $login_errors;

		if ( is_wp_error($login_errors) && $login_errors->get_error_code() ) {
			
			foreach ($login_errors->get_error_messages() as $error) {
				$error = apply_filters('sidebar_login_error', $error);
				echo '<div class="login_error">' . $error . "</div>\n";
				break;
			}
				
		}
		
		// Get redirect URL
		$redirect_to = trim(stripslashes(get_option('sidebarlogin_login_redirect')));
		
		if ( empty( $redirect_to ) ) {
			if ( isset( $_REQUEST['redirect_to'] ) ) 
				$redirect_to = esc_url( $_REQUEST['redirect_to'] );
			else
				$redirect_to = sidebar_login_current_url('nologout');
		}
		
		if ( force_ssl_admin() ) 
			$redirect_to = str_replace( 'http:', 'https:', $redirect_to );
		
		// login form
		$sidebarlogin_post_url = ( force_ssl_login() || force_ssl_admin() ) ? str_replace('http://', 'https://', sidebar_login_current_url() ) : sidebar_login_current_url();
		
		$login_form_args = apply_filters( 'sidebar_login_form_args', array(
	        'echo' 				=> true,
	        'redirect' 			=> esc_attr( $redirect_to ), 
	        'label_username' 	=> $theusername,
	        'label_password' 	=> $thepassword,
	        'label_remember' 	=> $theremember,
	        'label_log_in' 		=> __('লগইন &raquo;', 'sblogin'),
	        'remember' 			=> true,
	        'value_remember' 	=> true 
	    ) );
		
		wp_login_form( $login_form_args );
			
		// Output other links
		$links = '';	
		if ( get_option('users_can_register') && get_option('sidebarlogin_register_link') == '1' ) { 

			if ( ! is_multisite() ) {
			
				$links .= '<li><a href="' . apply_filters( 'sidebar_login_register_url', site_url('wp-login.php?action=register', 'login') ) . '" rel="nofollow">' . $theregister . '</a></li>';

			} else {
				
				$links .= '<li><a href="' . apply_filters( 'sidebar_login_register_url', site_url('wp-signup.php', 'login') ) . '" rel="nofollow">' . $theregister . '</a></li>';

			}
		}
		if ( get_option( 'sidebarlogin_forgotton_link' ) == '1' )
			$links .= '<li><a href="' . apply_filters( 'sidebar_login_lostpassword_url', wp_lostpassword_url() ) . '" rel="nofollow">' . $thelostpass . '</a></li>';

		if ($links)
			echo '<ul class="sidebarlogin_otherlinks">' . $links . '</ul>';	
	}		
		
	// echo widget closing tag
	echo $after_widget;
}

/* Init widget/styles/scripts */
function widget_wp_sidebarlogin_init() {
	
	$plugin_url = (is_ssl()) ? str_replace('http://','https://', WP_PLUGIN_URL) : WP_PLUGIN_URL;
	
	// CSS
	$sidebar_login_css = $plugin_url . '/bn-sidebar-login/style.css';
    wp_register_style('wp_sidebarlogin_css_styles', $sidebar_login_css);
    wp_enqueue_style('wp_sidebarlogin_css_styles');
    
	// Scripts
	$block_ui = $plugin_url . '/bn-sidebar-login/js/blockui.js';
	$sidebar_login_script = $plugin_url . '/bn-sidebar-login/js/bn-sidebar-login.js';
	
	wp_register_script('blockui', $block_ui, array('jquery'), '1.0' );
	wp_register_script('bn-sidebar-login', $sidebar_login_script, array('jquery', 'blockui'), '1.0' );
	wp_enqueue_script('jquery');
	wp_enqueue_script('blockui');
	wp_enqueue_script('bn-sidebar-login');
	
	// Pass variables to script
	$sidebar_login_params = array(
		'ajax_url' 				=> ( is_ssl() || force_ssl_admin() || force_ssl_login() ) ? str_replace('http:', 'https:', admin_url('admin-ajax.php')) : str_replace('https:', 'http:', admin_url('admin-ajax.php')),
		'login_nonce' 			=> wp_create_nonce("bn-sidebar-login-action")
	);
	wp_localize_script( 'bn-sidebar-login', 'sidebar_login_params', $sidebar_login_params );
	
	// Register widget
	class SidebarLoginMultiWidget extends WP_Widget {
	    function SidebarLoginMultiWidget() {  
	        $widget_ops = array('description' => __( 'Bangla Sidebar Login.','sblogin') );
			$this->WP_Widget('wp_sidebarlogin', __('Bangla Sidebar Login','sblogin'), $widget_ops);  
	    }
	    function widget($args, $instance) {    
	        
	        widget_wp_sidebarlogin($args);
	
	    }
	} 
	register_widget('SidebarLoginMultiWidget');
	
}

add_action('init', 'widget_wp_sidebarlogin_init', 1);

/**
 * Process ajax login
 */
add_action('wp_ajax_sidebar_login_process', 'sidebar_login_ajax_process');
add_action('wp_ajax_nopriv_sidebar_login_process', 'sidebar_login_ajax_process');

function sidebar_login_ajax_process() {

	check_ajax_referer( 'bn-sidebar-login-action', 'security' );
	
	// Get post data
	$creds = array();
	$creds['user_login'] 	= $_REQUEST['user_login'];
	$creds['user_password'] = $_REQUEST['user_password'];
	$creds['remember'] 		= esc_attr($_REQUEST['remember']);
	$redirect_to 			= esc_attr($_REQUEST['redirect_to']);
	
	// Check for Secure Cookie
	$secure_cookie = '';
	
	// If the user wants ssl but the session is not ssl, force a secure cookie.
	if ( ! force_ssl_admin() ) {
		$user_name = sanitize_user( $_REQUEST['user_login'] );
		if ( $user = get_user_by('login',  $user_name ) ) {
			if ( get_user_option('use_ssl', $user->ID) ) {
				$secure_cookie = true;
				force_ssl_admin(true);
			}
		}
	}
	
	if ( force_ssl_admin() ) $secure_cookie = true;
	if ( $secure_cookie=='' && force_ssl_login() ) $secure_cookie = false;

	// Login
	$user = wp_signon($creds, $secure_cookie);
	
	// Redirect filter
	if ( $secure_cookie && strstr($redirect_to, 'wp-admin') ) $redirect_to = str_replace('http:', 'https:', $redirect_to);

	// Result
	$result = array();
	
	if ( ! is_wp_error($user) ) :
		$result['success'] = 1;
		$result['redirect'] = $redirect_to;
	else :
		$result['success'] = 0;
		if ( $user->errors ) {
			foreach ($user->errors as $error) {
				$result['error'] = $error[0];
				break;
			}
		} else {
			$result['error'] = __('অনুগ্রহ করে লগইন করার জন্য আপনার ইউজারনেম এবং পাসওয়ার্ড প্রদান করুন।', 'sblogin');
		}
	endif;
	
	header('content-type: application/json; charset=utf-8');

	echo $_GET['callback'] . '(' . json_encode($result) . ')';

	die();
}


/* Get Current URL */
if ( !function_exists('sidebar_login_current_url') ) {
	function sidebar_login_current_url( $url = '' ) {
	
		$pageURL  = force_ssl_admin() ? 'https://' : 'http://';
		$pageURL .= esc_attr( $_SERVER['HTTP_HOST'] );
		$pageURL .= esc_attr( $_SERVER['REQUEST_URI'] );
	
		if ($url != "nologout") {
			if (!strpos($pageURL,'_login=')) {
				$rand_string = md5(uniqid(rand(), true));
				$rand_string = substr($rand_string, 0, 10);
				$pageURL = add_query_arg('_login', $rand_string, $pageURL);
			}	
		}
		
		return strip_tags( $pageURL );
	}
}