<?php
if (isset($_POST['role'])) {
    switch ($_POST['role']) {
        case 'employer':
            wp_update_user( array( 'ID' => $_POST['id'], 'role' => 'freelancer' ) );
            break;
        case 'freelancer':
            wp_update_user( array( 'ID' => $_POST['id'], 'role' => 'employer' ) );
            break;
    }
}
wp_die();
?>