<?php

add_action( 'admin_init', 'sidebar_login_options_init' );
add_action( 'admin_menu', 'sidebar_login_options_add_page' );

/**
 * Define Options
 */
global $sidebar_login_options;

$sidebar_login_options = (
	array( 
		array(
			'', 
			array(
				array(
					'name' 		=> 'sidebarlogin_heading', 
					'std' 		=> __('লগইন', 'sblogin'), 
					'label' 	=> __('লগ আউটের শিরোনাম', 'sblogin'),  
					'desc'		=> __('ইউজার লগআউট হওয়ার পর উইজেট এর শিরোনাম।', 'sblogin')
				),
				array(
					'name' 		=> 'sidebarlogin_welcome_heading', 
					'std' 		=> __('স্বাগতম %username%', 'sblogin'), 
					'label' 	=> __('লগইন শিরোনাম', 'sblogin'),  
					'desc'		=> __('ইউজার লগআউট হওয়ার পর উইজেট এর শিরোনাম।', 'sblogin')
				),
			)
		),
		array(
			__('রিডিরেক্ট (Redirect)', 'sblogin'), 
			array(
				array(
					'name' 		=> 'sidebarlogin_login_redirect', 
					'std' 		=> '', 
					'label' 	=> __('লগইন ইউআরএল', 'sblogin'),  
					'desc'		=> __('লগইন হওয়ার পর ইউজার কোন ঠিকানায় যাবে তা লিখুন', 'sblogin'),
					'placeholder' => 'http://'
				),
				array(
					'name' 		=> 'sidebarlogin_logout_redirect', 
					'std' 		=> '', 
					'label' 	=> __('লগআউট ইউআরএল', 'sblogin'),  
					'desc'		=> __('লগআউট হওয়ার পর ইউজার কোন ঠিকানায় যাবে তা লিখুন', 'sblogin'),
					'placeholder' => 'http://'
				),
			)
		),
		array(
			__('লিংকসমূহ', 'sblogin'), 
			array(
				array(
					'name' 		=> 'sidebarlogin_register_link', 
					'std' 		=> '1', 
					'label' 	=> __('রেজিস্টার/নিবন্ধনের লিংক প্রদর্শন', 'sblogin'),  
					'desc'		=> sprintf( __('এটি কাজ করার জন্য <a href="%s" target="_blank">\'Anyone can register\'</a> সেটিংসটি অবশ্যই চালু থাকতে হবে।', 'sblogin'), admin_url('options-general.php')),
					'type' 		=> 'checkbox'
				),
				array(
					'name' 		=> 'sidebarlogin_forgotton_link', 
					'std' 		=> '1', 
					'label' 	=> __('পাসওয়ার্ড পুনরোদ্ধারের লিংক প্রদর্শন', 'sblogin'),  
					'desc'		=> '',
					'type' 		=> 'checkbox'
				),
				array(
					'name' 		=> 'sidebar_login_avatar', 
					'std' 		=> '1', 
					'label' 	=> __('লগইন অ্যাভাটার প্রদর্শন', 'sblogin'),  
					'desc'		=> '',
					'type' 		=> 'checkbox'
				),
				array(
					'name' 		=> 'sidebarlogin_logged_in_links', 
					'std' 		=> "<a href=\"".get_bloginfo('wpurl')."/wp-admin/\">".__('ড্যাশবোর্ড','sblogin')."</a>\n<a href=\"".get_bloginfo('wpurl')."/wp-admin/profile.php\">".__('প্রোফাইল','sblogin')."</a>", 
					'label' 	=> __('লগইন লিংকসমূহ', 'sblogin'),  
					'desc'		=> sprintf( __('প্রত্যেক লাইনে একটি লিংক লিখুন। লগআউট লিংক না দিলেও চলবে। টিপস: যেকোন লিংকের পর <code>|true</code> যোগ করুন যাতে এটি শুধু এডমিনরাই দেখতে পারে অথবা বিকল্প হিসেবে <code>|user_capability</code>  ব্যবহার করতে পারেন সেক্ষেত্রে লিংকটি যে যেই লেভেলের ইউজার শুধু সেই দিখতে পাবে (বিস্তারিত <a href=\'http://codex.wordpress.org/Roles_and_Capabilities\' target=\'_blank\'>Roles and Capabilities</a>).<br/>আপনি চাইলে <code>%%USERNAME%%</code> এবং <code>%%USERID%%</code> কোডটি ব্যবহার করতে পারেন যা ইউজার প্রদত্ত তথ্য দ্বারা পরিবর্তিত হবে। সাধারণত এরকম হবে: <br/>&lt;a href="%s/wp-admin/"&gt;ড্যাশবোর্ড&lt;/a&gt;<br/>&lt;a href="%s/wp-admin/profile.php"&gt;প্রোফাইল&lt;/a&gt;', 'sblogin'), get_bloginfo('wpurl'), get_bloginfo('wpurl') ),
					'type' 		=> 'textarea'
				),
			)
		)
	)
);
	
/**
 * Init plugin options to white list our options
 */
function sidebar_login_options_init() {

	global $sidebar_login_options;

	foreach($sidebar_login_options as $section) {
		foreach($section[1] as $option) {
			if (isset($option['std'])) add_option($option['name'], $option['std']);
			register_setting( 'bn-sidebar-login', $option['name'] );
		}
	}

	
}

/**
 * Load up the menu page
 */
function sidebar_login_options_add_page() {
	add_options_page(__('Bangla Sidebar Login','sblogin'), __('Bangla Sidebar Login','sblogin'), 'manage_options', 'bn-sidebar-login', 'sidebar_login_options');
}

/**
 * Create the options page
 */
function sidebar_login_options() {
	global $sidebar_login_options;

	if ( ! isset( $_REQUEST['settings-updated'] ) ) $_REQUEST['settings-updated'] = false;

	?>
	<div class="wrap">
		<?php screen_icon(); echo "<h2>" .__( 'Bangla Sidebar Login Options','sblogin') . "</h2>"; ?>
		
		<form method="post" action="options.php">
		
			<?php settings_fields( 'bn-sidebar-login' ); ?>
	
			<?php
			foreach($sidebar_login_options as $section) {
			
				if ($section[0]) echo '<h3 class="title">'.$section[0].'</h3>';
				
				echo '<table class="form-table">';
				
				foreach($section[1] as $option) {
					
					echo '<tr valign="top"><th scope="row">'.$option['label'].'</th><td>';
					
					if (!isset($option['type'])) $option['type'] = '';
					
					switch ($option['type']) {
						
						case "checkbox" :
						
							$value = get_option($option['name']);
							
							?><input id="<?php echo $option['name']; ?>" name="<?php echo $option['name']; ?>" type="checkbox" value="1" <?php checked( '1', $value ); ?> /><?php
						
						break;
						case "textarea" :
							
							$value = get_option($option['name']);
							
							?><textarea id="<?php echo $option['name']; ?>" class="large-text" cols="50" rows="10" name="<?php echo $option['name']; ?>" placeholder="<?php if (isset($option['placeholder'])) echo $option['placeholder']; ?>"><?php echo esc_textarea( $value ); ?></textarea><?php
						
						break;
						default :
							
							$value = get_option($option['name']);
							
							?><input id="<?php echo $option['name']; ?>" class="regular-text" type="text" name="<?php echo $option['name']; ?>" value="<?php esc_attr_e( $value ); ?>" placeholder="<?php if (isset($option['placeholder'])) echo $option['placeholder']; ?>" /><?php
						
						break;
						
					}
					
					if ($option['desc']) echo '<span class="description">'.$option['desc'].'</span>';
					
					echo '</td></tr>';
				}
				
				echo '</table>';
				
			}
			?>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'সেভ করুন', 'sblogin'); ?>" />
			</p>
		</form>
	</div>
	<?php
}