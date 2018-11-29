<?php
add_filter('comment_flood_filter', '__return_false');
add_action( 'wp_footer', 'cs_adjust_notify_time', 100 );
function cs_adjust_notify_time(){
    ?>
    <script type="text/javascript">
        (function($, Models, Collections, Views) {
            $(document).ready(function() {
                Views.Front.prototype.showNotice =  function(params) {
                    var view = this;
                    $('div.notification').remove();
                    var notification = $(view.noti_templates({
                        msg: params.msg,
                        type: params.notice_type
                    }));
                    if ($('#wpadminbar').length !== 0) {
                        notification.addClass('having-adminbar');
                    }
                    notification.hide().prependTo('body').fadeIn('fast').delay(3000).fadeOut(2000, function() {
                        $(this).remove();
                    });
                }
            });
         })(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);
    </script>
    <?php
}

add_filter('ae_after_login_link','fre_change_link_after_login', 999);
function fre_change_link_after_login($link){
    $link = et_get_page_link('profile').'#tab_project_details';
    return $link;
}

function my_theme_scripts() {
    wp_enqueue_script( 'featherlight-js', get_stylesheet_directory_uri() . '/assets/js/jquery.magnific-popup.min.js', array( 'jquery' ), '1.0.0', true );
    wp_enqueue_style( 'featherlight-css',  get_stylesheet_directory_uri() .'/assets/css/magnific-popup.css', array(), null, 'all' );
    wp_enqueue_script( 'Three', get_stylesheet_directory_uri() . '/assets/js/three.min.js', array( 'jquery' ), '1.0.0', true );
    wp_enqueue_script( 'Detector', get_stylesheet_directory_uri() . '/assets/js/Detector.js', array( 'jquery' ), '1.0.0', true );
    wp_enqueue_script( 'OrbitControls', get_stylesheet_directory_uri() . '/assets/js/OrbitControls.js', array( 'jquery' ), '1.0.0', true );
    wp_enqueue_script( 'OBJLoader', get_stylesheet_directory_uri() . '/assets/js/OBJLoader.js', array( 'jquery' ), '1.0.0', true );
    wp_enqueue_script( 'STLLoader', get_stylesheet_directory_uri() . '/assets/js/STLLoader.js', array( 'jquery' ), '1.0.0', true );
    wp_enqueue_script( 'script-child-min', get_stylesheet_directory_uri() . '/script.min.js', array( 'jquery' ), '1.0.0', true );
    wp_enqueue_script( 'model-viewer-min', get_stylesheet_directory_uri() . '/new-model-viewer-4.js', array( 'jquery' ), '1.1.0', true );
    $translation_array = array( 'templateUrl' => get_stylesheet_directory_uri() );
    wp_localize_script( 'script-child-min', 'object_name', $translation_array );

    wp_dequeue_script('profile');
    wp_enqueue_script('profile-child', get_stylesheet_directory_uri() . '/assets/js/profile.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'my_theme_scripts' );

add_action( 'admin_enqueue_scripts', 'my_enqueue' );
function my_enqueue($hook) {
    if( 'index.php' != $hook ) {
	// Only applies to dashboard panel
	return;
    }
	// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
	wp_localize_script( 'script-child-min', 'ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
}

add_action('wp_ajax_change_user_role', 'update_user_role');
function update_user_role(){
    $result;
    $newRole = "";
    switch ($_POST['role']) {
        case 'employer':
            $args = array(
                'ID'    => $_POST['id'],
                'role'  => 'freelancer'
            );
            $result = wp_update_user($args);
            $newRole = "freelancer";
            break;
        case 'freelancer':
            $args = array(
                'ID'    => $_POST['id'],
                'role'  => 'employer'
            );
            $result = wp_update_user($args);
            $newRole = "employer";
            break;
    }
    
    $return = array(
        'previousRole'   => $_POST['role'],
        'newRole'       => $newRole
    );
    echo json_encode((object)$return);

    wp_die();
}

add_filter( 'ae_get_mail_header', 'cs_custom_header_email' );
function cs_custom_header_email(){
    $logo_url = get_template_directory_uri() . "/img/logo-de.png";
    $options = AE_Options::get_instance();
    
    $site_logo = $options->site_logo;
    if (!empty($site_logo)) {
    $logo_url = $site_logo['large'][0];
    }
    
    $logo_url = apply_filters('ae_mail_logo_url', $logo_url);
    $customize = et_get_customization();
    $mail_header = '<html>
    <head>
    </head>
    <body style="font-family: Arial, sans-serif;font-size: 0.9em;margin: 0; padding: 0; color: #222222;">
    <div style="margin: 0px auto; width:600px; border: 1px solid ' . $customize['background'] . '">
    <table width="100%" cellspacing="0" cellpadding="0">
    <tr style="background: #232323; height: 63px; vertical-align: middle;">
    <td style="padding: 10px 5px 10px 20px; width: 20%;">
    <img style="max-height: 100px" src="' . $logo_url . '" alt="' . get_option('blogname') . '">
    </td>
    <td style="padding: 10px 20px 10px 5px">
    <span style="text-shadow: 0 0 1px #151515; color: #b0b0b0;">' . get_option('blogdescription') . '</span>
    </td>
    </tr>
    <tr><td colspan="2" style="height: 5px; background-color: ' . $customize['background'] . ';"></td></tr>
    <tr>
    <td colspan="2" style="background: #ffffff; color: #222222; line-height: 18px; padding: 10px 20px;">';
    return $mail_header;
}

add_filter( 'ae_get_mail_footer', 'cs_custom_mail_footer' );
function cs_custom_mail_footer() {
    $info = apply_filters('ae_mail_footer_contact_info', get_option('blogname') . ' <br> ' . get_option('admin_email') . ' <br>');
    $customize = et_get_customization();
    $copyright = apply_filters('get_copyright', ae_get_option('copyright'));
    $mail_footer = '</td>
    </tr>
    <tr>
    <td colspan="2" style="background: #232323; padding: 10px 20px; color: #666;">
    <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
    <td style="vertical-align: top; text-align: left; width: 50%;">' . $copyright . '</td>
    <td style="text-align: right; width: 50%;">' . $info . '</td>
    </tr>
    </table>
    </td>
    </tr>
    </table>
    </div>
    </body>
    </html>';
    return $mail_footer;
}
?>