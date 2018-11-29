<?php
/*
Plugin Name: Collect.chat — Free and Beautiful Conversational Chatbot for Lead Generation and Data Collection
Version: 1.1.12
Plugin URI: https://collect.chat
Description: Collect data and leads from your visitors using our automated chatbot. Zero coding involved. Engage every single visitor on your site. 
Author: Collect.chat
Author URI:https://profiles.wordpress.org/collectchat/
License: GPL v2
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'admin_menu', 'collectchat_admin_menu' );
function collectchat_admin_menu() {
    add_options_page( __('Collect.chat', 'collectchat' ), __('Collect.chat', 'collectchat' ), 'manage_options', 'collectchat-plugin', 'collectchat_options_page' );
}


add_action( 'admin_init', 'collectchat_admin_init' );

function collectchat_admin_init() {

  	register_setting( 'collectchat-settings-group', 'collectchat-plugin-settings' ); 
  	add_settings_section( 'collectchat-section-1', __( 'Collect.chat Script', 'collectchat' ), 'collectchat_section_1_view', 'collectchat-plugin' );
  	
  	add_settings_section( 'collectchat-section-2', __( 'Remove Bot from', 'collectchat' ), 'collectchat_section_2_view', 'collectchat-plugin' );
  	

  	add_settings_field( 'script', __( 'Script', 'collectchat' ), 'collectchat_script_callback', 'collectchat-plugin', 'collectchat-section-1' );
	
	add_settings_field( 'home', __( 'Home', 'collectchat' ), 'collectchat_home_callback', 'collectchat-plugin', 'collectchat-section-2' );
	// Post types specific
	
	add_settings_field( 'allpages', __( 'All Pages', 'collectchat' ), 'collectchat_allpages_callback', 'collectchat-plugin', 'collectchat-section-2' );
	add_settings_field( 'allposts', __( 'All Posts', 'collectchat' ), 'collectchat_allposts_callback', 'collectchat-plugin', 'collectchat-section-2' );

	// Woocommerce Support
	if (in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		add_settings_field( 'cart', __( 'Cart', 'collectchat' ), 'collectchat_cart_callback', 'collectchat-plugin', 'collectchat-section-2' );
		add_settings_field( 'checkout', __( 'Checkout', 'collectchat' ), 'collectchat_checkout_callback', 'collectchat-plugin', 'collectchat-section-2' );
	}
	
	
}
/* 
 * THE ACTUAL PAGE 
 * */
function collectchat_options_page() {
?>
  <div class="wrap">
      <h2><?php _e('Collect.chat Options', 'collectchat'); ?></h2>
      <form action="options.php" method="POST">
        <?php settings_fields('collectchat-settings-group'); ?>
        <?php do_settings_sections('collectchat-plugin'); ?>
        <?php submit_button(); ?>
      </form>
  </div>
<?php }
/*
* THE SECTIONS
* */
function collectchat_section_1_view() {
	_e( "Please copy the  code snippet from <a href='https://collect.chat' target='_blank'>Collect.chat</a> and paste it  here.", 'collectchat' );
	echo "<p>Don't have a Collect.chat account? <a href='https://collect.chat/signup' target='_blank'> Click here to register</a>. <p>";

}
function collectchat_section_2_view() {
	_e( 'Tick against the pages where you want to hide the chatbot.', 'collectchat' );
}
/*
* THE FIELDS
* */
function collectchat_script_callback() {
	
	$settings = (array) get_option( 'collectchat-plugin-settings' );
	$field = "script";
	if(isset($settings[$field])){
		$value =$settings[$field];
	}
		if(isset($value)){
			echo '<textarea rows="7" cols="50" name="collectchat-plugin-settings[script]">'.$value.'</textarea>';
		}
		else{
			echo '<textarea rows="7" cols="50" name="collectchat-plugin-settings[script]"></textarea>';
		}
	
}
// CHECKBOX - Name: plugin_options[chkbox1]
function collectchat_home_callback() {
	$options =get_option('collectchat-plugin-settings');
	if(isset($options['home']) && $options['home']==1 ){
		echo "<input type='checkbox' name='collectchat-plugin-settings[home]' value='1' checked/>";
	}					
	else{ 
		echo "<input type='checkbox' name='collectchat-plugin-settings[home]' value='1'/>";
	}	
	
}
function collectchat_allpages_callback() {
	$options =get_option('collectchat-plugin-settings');
	if(isset($options['allpages']) && $options['allpages']==1 ){
		echo "<input type='checkbox' name='collectchat-plugin-settings[allpages]' value='1' checked/>";
	}					
	else{ 
		echo "<input type='checkbox' name='collectchat-plugin-settings[allpages]' value='1'/>";
	}	
}

function collectchat_allposts_callback() {
	$options =get_option('collectchat-plugin-settings');
	if(isset($options['allposts']) && $options['allposts']==1 ){
		echo "<input type='checkbox' name='collectchat-plugin-settings[allposts]' value='1' checked/>";
	}					
	else{ 
		echo "<input type='checkbox' name='collectchat-plugin-settings[allposts]' value='1'/>";
	}	
}
function collectchat_cart_callback() {
	$options =get_option('collectchat-plugin-settings');
	if(isset($options['cart']) && $options['cart']==1 ){
		echo "<input type='checkbox' name='collectchat-plugin-settings[cart]' value='1' checked/>";
	}					
	else{ 
		echo "<input type='checkbox' name='collectchat-plugin-settings[cart]' value='1'/>";
	}	
}
function collectchat_checkout_callback() {
	$options =get_option('collectchat-plugin-settings');
	if(isset($options['checkout']) && $options['checkout']==1 ){
		echo "<input type='checkbox' name='collectchat-plugin-settings[checkout]' value='1' checked/>";
	}					
	else{ 
		echo "<input type='checkbox' name='collectchat-plugin-settings[checkout]' value='1'/>";
	}	
}

add_action("wp_footer","collectchat_footer_script",100,1);
function collectchat_footer_script($pos_id){

	$settings = (array) get_option( 'collectchat-plugin-settings' );
	
	if(isset($settings['script'])){
		$script =$settings['script'];
	}
	if(isset($settings['home']) && $settings['home']==1){
		if((is_home() || is_front_page() )){
			return;
		}
	}
	if(isset($settings['allpages'])&& $settings['allpages']==1){
		if (is_page()) {
			return;
		}
	}
	if(isset($settings['allposts']) && $settings['allposts']==1){
		if ('post' == get_post_type() && !is_home()) {
			return ;
		}
		
	}
	if (in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
				if(isset($settings['cart'])){
						if(is_cart() && $settings['cart']==1) {
							return;
						}
				}		
				if(isset($settings['checkout'])){
					if(is_checkout() && $settings['checkout']==1) {
						return;
					}
				}	
	}			
	
	echo $script;

}


function plugin_add_settings_link_collectchat( $links ) {
    $settings_link = '<a href="options-general.php?page=collectchat-plugin">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link_collectchat' );

?>