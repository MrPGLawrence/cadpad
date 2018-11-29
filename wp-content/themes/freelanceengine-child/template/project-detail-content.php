<?php
/**
 * The template for displaying project description, comment, taxonomy and custom fields
 * @since 1.0
 * @package FreelanceEngine
 * @category Template
 */
global $wp_query, $ae_post_factory, $post, $user_ID;
$post_object    = $ae_post_factory->get(PROJECT);
$convert = $project = $post_object->current_post;
?>
<div class="info-project-item-details">
    <div class="row">
        <div class="col-md-12">
            <div class="content-require-skill-project">
                <?php if($post->post_status == 'complete'): ?>
                <h3>Final Model:</h3>
                <?php if(is_user_logged_in()): ?>
                <?php
                $attachment_comments = get_comments(array(
                    'post_id' => $post->ID ,
                    'meta_query' => array(
                        array(
                            'key' => 'fre_comment_file',
                            'value' => '',
                            'compare' => '!='
                        )
                    )
                ));
                
                $attachments = array();
                foreach ($attachment_comments as $key => $value) {
                    $file_arr = get_comment_meta($value->comment_ID, 'fre_comment_file', true);
                    if(is_array($file_arr)){
                        $attachment = get_posts(array('post_type' => 'attachment', 'post__in' => $file_arr));
                        $attachments = wp_parse_args($attachments, $attachment);
                    }
                }
                ?>
                <?php
                    $attachments = array_reverse($attachments);
                    foreach ($attachments as $key => $value) {
                        $fileType = substr($value->post_title, -3);
                        if($fileType == "stl" || $fileType == "obj"):
                ?>
                        <div class="list-file-attack prev-list">
                            <div class="file-attack-name">
                                <a class="fre-btn preview-model" id="final-model" href="<?php echo $value->guid;?>" target="_Blank">View Model - <?php echo $value->post_title ?></a>
                            </div>
                        </div>
                    <?php 
                        endif;  
                    ?>
                <?php } ?>
                <?php else: ?>
                <span> Sorry, you must be logged in to view this model.<br> </span>
                <?php endif ?>
                <?php endif ?>
            <?php

                do_action('before_sidebar_single_project', $project);
                echo '<div class="row">';
                // list project attachment
                $attachment = get_children( array(
                        'numberposts' => -1,
                        'order' => 'ASC',
                        'post_parent' => $post->ID,
                        'post_type' => 'attachment'
                      ), OBJECT );
                if(!empty($attachment)) {
                    echo '<div class="col-md-12">';
                    echo '<h3 class="title-content">'. __("Attachments:", ET_DOMAIN) .'</h3>';
                    echo '<ul class="list-file-attack-report">';
                    foreach ($attachment as $key => $att) {
                        $file_type = wp_check_filetype($att->post_title, array('jpg' => 'image/jpeg',
                                                                                'jpeg' => 'image/jpeg',
                                                                                'gif' => 'image/gif',
                                                                                'png' => 'image/png',
                                                                                'bmp' => 'image/bmp'
                                                                            )
                                                    );
                        $class="text-ellipsis";
                        if(isset($file_type['ext']) && $file_type['ext']) $class="image-gallery text-ellipsis";
                        echo '<li>
                                <a class="'.$class.'" target="_blank" rel="noopener noreferrer"  href="'.$att->guid.'"><img class="project-img" src="'.$att->guid.'"</a>
                            </li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }
                echo '<div class="col-md-6">';
                list_tax_of_project( get_the_ID(), __('Skills required:',ET_DOMAIN), 'skill' );
                echo '</div>';

                echo '<div class="col-md-6">';
                list_tax_of_project( get_the_ID(), __('Category:',ET_DOMAIN)  );
                echo '</div>';
                echo '</div>';
                if(function_exists('et_render_custom_field')) {
                    et_render_custom_field($project);
                }
                do_action('after_sidebar_single_project', $project);
            ?>
            </div>
        </div>
        <div class="col-md-12">
            <div class="content-require-project active">
                <h4><?php _e('Project description:',ET_DOMAIN);?></h4>
                <?php the_content(); ?>
                <?php do_action( 'after_single_project_content', $project); ?>
            </div>
            <?php if(!ae_get_option('disable_project_comment')) { ?>
            <div class="comments" id="project_comment">
                <?php comments_template('/comments.php', true)?>
            </div>
            <?php } ?>

        </div>
    </div>
</div>