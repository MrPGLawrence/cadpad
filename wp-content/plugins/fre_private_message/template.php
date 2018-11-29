<?php
/**
 * Plugin  template
 * @param void
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
*/
/**
  * Message button
  * @param void
  * @return void
  * @since 1.0
  * @package FREELANCEENGINE
  * @category PRIVATE MESSAGE
  * @author Tambh
  */
if( !function_exists('ae_private_message_button') ) {
    function ae_private_message_button($bid, $project)
    {
        global $user_ID;
        $to_user = ae_private_msg_user_profile((int)$bid->post_author);
        $response = ae_private_message_created_a_conversation(array('bid_id'=>$bid->ID));
        if ($user_ID == (int)$project->post_author && $project->post_status == 'publish') {
            if( $response['success'] ){
                $data = array(
                    'bid_id'=> $bid->ID,
                    'to_user'=> $to_user,
                    'project_id'=> $project->ID,
                    'project_title'=> $project->post_title,
                    'from_user'=> $user_ID
                );
        ?>
                <button rel="<?php echo $project->ID; ?>" title=""
                        class="btn btn-link btn-send-msg btn-open-msg-modal">
                    <i class="fa fa-comment"></i><span><?php _e('Message', ET_DOMAIN) ?></span>
                    <script type="data/json"  class="privatemsg_data">
                        <?php  echo json_encode( $data ) ?>
                    </script>
                </button>
        <?php }else{ ?>
                <button class="btn btn-link btn-send-msg btn-redirect-msg" title=""
                         data-conversation="<?php echo $response['conversation_id'] ?>">
                    <i class="fa fa-comment"></i><span><?php _e('Message', ET_DOMAIN) ?></span>
                </button>
        <?php
            }
        }
    }
}
/**
  * Private message modal
  * @param void
  * @return void
  * @since 1.0
  * @package FREELANCEENGINE
  * @category PRIVATE MESSAGE
  * @author Tambh
  */
if( !function_exists( 'ae_private_message_modal' ) ){
    function ae_private_message_modal(){ ?>
        <!-- MODAL Send Message -->
        <div class="modal fade private_message_modal" id="modal_msg">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <i class="fa fa-times"></i>
                        </button>
                        <h4 class="modal-title"><?php _e('Send Message', ET_DOMAIN); ?></h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            <form role="form" id="private_msg_form" class="form-horizontal" >
                                <div class="form-group">

                                    <label for="inputEmailTo" class="col-md-2 col-xs-2 control-label"><?php _e('To:', ET_DOMAIN); ?> </label>
                                    <div class="col-md-10 col-xs-10">
                                        <div class="avatar-freelancer-bidding">
                                            <?php
                                                if(is_ssl()){
                                                    $host = 'https://secure.gravatar.com';
                                                } else {
                                                    $host = 'http://0.gravatar.com';
                                                }
                                            ?>
                                            <a class="private_msg_user_link" href=""><span class="avatar-profile">
                                                <img alt="" src="<?php echo $host;?>/avatar/6334f9d721de4b0fd7ff770bb0134336?s=70&amp;d=&amp;r=G" class="avatar photo avatar-default" ></span>
                                            </a>
                                        </div>
                                        <div class="info-profile-freelancer-bidding">
                                            <span class="name-profile"><?php _e('user name', ET_DOMAIN); ?></span>
                                            <span class="position-profile"><?php _e('Job title', ET_DOMAIN); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="fre-input-field form-group">
                                    <label for="inputSubject" class="col-md-2 col-xs-12 control-label"><?php _e('Subject:', ET_DOMAIN); ?></label>
                                    <div class="col-md-10 col-xs-12">
                                        <input id="inputSubject" name="post_title" type="text" class="form-control width100p" value="<?php _e('no subject', ET_DOMAIN); ?>">
                                    </div>
                                </div>
                                <div class="fre-input-field form-group">
                                    <label for="inputPassword3" class="col-md-2 col-xs-12 control-label"><?php _e('Message:', ET_DOMAIN); ?></label>
                                    <div class="col-md-10 col-xs-12">
                                        <textarea name="post_content" id="" cols="30" rows="10"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-2 col-md-10">
                                        <button type="submit" class="fre-btn btn-send-msg-modal"><?php _e('Send Message', ET_DOMAIN); ?></button>
                                    </div>
                                </div>
                                <input type="hidden" name="from_user" value="" />
                                <input type="hidden" name="to_user" value="" />
                                <input type="hidden" name="project_id" value="" />
                                <input type="hidden" name="project_name" value="" />
                                <input type="hidden" name="bid_id" value="" />
                                <input type="hidden" name ="is_conversation" value="1" />
                                <input type="hidden" name ="conversation_status" value="unread" />
                                <input type="hidden" name="sync_type" value="conversation" />


                            </form>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
<?php }
}
if( !function_exists('ae_private_message_add_profile_tab_template') ) {
    /**
     * add profile tab content html
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    function ae_private_message_add_profile_tab_template()
    {
        global $user_ID;
        $number = ae_private_message_get_new_number($user_ID);
        $class = '';
        ?>
        <li>
            <a href="#tab_private_msg" role="tab" data-toggle="tab" class="ae-private-message-conversation-show">
                <span>
                     <?php _e('Messages', ET_DOMAIN);
                        $num = $number;
                        if($number > 100){
                            $num = '99+';
                        }
                        if($num <= 0){
                            $num = 0;
                            $class = 'hidden';
                        }
                        echo '<span class="msg-number '. $class .' "> ' . $num . ' </span>';
                    ?>
                </span>
            </a>
        </li>
    <?php }
}
if( !function_exists('ae_private_message_add_profile_tab_template_on_mobile') ){
    /**
     * add profile tab content html
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    function ae_private_message_add_profile_tab_template_on_mobile(){ ?>
        <li>
            <a href="<?php echo et_get_page_link('profile'); ?>#tab_private_msg" class="link-menu-nav">
                <?php _e('Messages', ET_DOMAIN) ?>
            </a>
        </li>
<?php
    }
}
if( !function_exists('ae_private_message_add_profile_tab_content_template') ){
    function ae_private_message_add_profile_tab_content_template(){ ?>
        <div class="tab-pane conversation-panel tab-profile " id="tab_private_msg">
            <div class="private-message-conversation-contents" >
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="group-filter-conversation">
                            <i class="fa fa-search"></i>
                            <input type="text" class="input-item form-control text-field field-search-conversation search"
                                   id=" " name="s" placeholder="<?php _e('Enter keyword', ET_DOMAIN) ;?>">
                            <?php
                                global $user_ID;
                                $name = 'conversation_status';
                                if( ae_user_role($user_ID) == EMPLOYER ||ae_user_role($user_ID) == 'administrator'){
                                    $name = 'post_status';
                                }
                            ?>
                            <select class="status-filter filter-conversation select-filter-conversation"
                                    data-chosen-width="20%" data-chosen-disable-search="1"
                                    data-placeholder="Select a status" name="<?php echo $name; ?>">
                                <option value=""><?php _e('All', ET_DOMAIN); ?></option>
                                <option value="unread"><?php _e('UnRead', ET_DOMAIN); ?></option>
                            </select>

                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <ul class="list-unstyled list-conversation">
                            <?php
                                $args = array();
                                $args = ae_private_message_default_query_args($args, true);
                                global $ae_post_factory, $post, $wp_query;
                                query_posts($args);
                                $post_object = $ae_post_factory->get('ae_private_message');
                                $conversation_data = array();
                                if( have_posts() ):
                                    while( have_posts() ) : the_post();
                                        $convert    = $post_object->convert($post);
                                        $conversation_data[]  = $convert;
                                    endwhile;
                                else:
                                    _e('<li class="no-result"><span class="no-message"><i class="fa fa-exclamation"></i>You have not created any conversation yet.<br/>
    Please come back to your bidders list in Project detail for starting a conversation with bidders.</span></li>', ET_DOMAIN);
                                endif;
                            ?>
                        </ul>
                    </div>
                    <div class="col-md-12">
                         <div class="paginations-wrapper">
                            <?php
                                $paginate  = 'page';
                                if(et_load_mobile() ){
                                    $paginate = 'load_more';
                                }
                                ae_pagination($wp_query, get_query_var('paged'), $paginate);
                                wp_reset_query();
                            ?>
                         </div>
                    </div>
                    <?php echo '<script type="data/json" class="ae_private_conversation_data" >'.json_encode($conversation_data).'</script>';?>
                </div>
            </div>
            <div class="private-message-reply-contents">
                <?php ae_private_message_reply_content();?>
            </div>
        </div>
<?php }
}
if( !function_exists('ae_private_message_loop_item') ){
    /**
      * Private message conversation loop item
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    function ae_private_message_loop_item(){ 
        global $user_ID;
?>
        <script type="text/template" id="ae-private-message-loop">
                <div class="avatar-conversation ">
                    <span class="avatar-img">
                        {{=conversation_author_avatar}}
                    </span>
                </div>
                <div class="content-conversation conversation-item-{{=ID}}">
                    <a href="#" class="ae-private-message-conversation-wrapper action" data-action="show">
                        <span class="name-conversation">{{= conversation_author_name }}</span>
                        <span class="title-conversation">
                            <?php _e('Project', ET_DOMAIN); ?> <span class="text-underline">{{= project_name }}</span>
                        </span>
                        <div class="detail-conversation">
                            <p>
                                <?php if(ae_user_role($user_ID) == FREELANCER){ ?>
                                    <# if( !conversation_latest_reply ) { #>
                                        <strong>
                                        <# if(conversation_status == 'unread'){ #> <i class="fa fa-mail-reply"></i> <# } #>
                                        {{= post_title }} <span>-</span></strong> {{= strip_tag_post_content}}
                                    <# }else{ #>
                                        <strong>
                                        <# if(conversation_status == 'unread'){ #> <i class="fa fa-mail-reply"></i> <# } #>
                                        {{= post_title }} <span>-</span></strong> {{= conversation_latest_reply }}
                                    <# } #>
                                <?php }else{ ?>
                                    <# if( !conversation_latest_reply ) { #>
                                        <strong>
                                        <# if(post_status == 'unread'){ #> <i class="fa fa-mail-reply"></i> <# } #>
                                        {{= post_title }} <span>-</span></strong> {{= strip_tag_post_content}}
                                    <# }else{ #>
                                        <strong>
                                        <# if(post_status == 'unread'){ #> <i class="fa fa-mail-reply"></i> <# } #>
                                        {{= post_title }} <span>-</span></strong> {{= conversation_latest_reply }}
                                    <# } #>
                                <?php }?>
                            </p>
                        </div>
                    </a>
                    <button type="button" class="btn-delete-conversation  action archive" data-action="archive" title=""  data-toggle="tooltip" data-placement="top" data-original-title="Archive conversation">
                        <span><?php _e( ' Archive', ET_DOMAIN); ?></span>
                    </button>

                    <span class="date-conversation">{{= last_date}}</span>
                </div>
        </script>
<?php    }

}

if( !function_exists('ae_private_message_add_notification_menu_template') ){
    /**
      * header menu message template
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    function ae_private_message_add_notification_menu_template(){ ?>
        <li role="presentation">
            <a href="<?php echo et_get_page_link('profile').'#tab_private_msg' ?>" class="trigger-overlay trigger-messages">
                <!-- <i class="fa fa-inbox"></i> -->
                <?php
                    global $user_ID;
                    $message_number = get_user_meta($user_ID, 'total_unread', true);
                    _e("Inbox", ET_DOMAIN);
                    if( $message_number ) {
                        echo ' <span class="notify-number">(' . $message_number . ')</span>';
                    }
                ?>
            </a>
        </li>
<?php }
}

if( !function_exists('ae_private_message_reply_content') ){
    function ae_private_message_reply_content(){ ?>
        <div class="row">
                <div class="col-md-12  col-sm-12 col-xs-12">
                <?php if( ! et_load_mobile()) { ?>
                    <div class="title-conversation-detail">
                        <div class="avatar-conversation ">
                            <span class="avatar-img">
                            </span>
                        </div>
                        <div class="content-conversation">
                            <span class="name-conversation"></span>
                            <span class="title-conversation"></span>
                            <div class="detail-conversation"></div>
                            <button type="button" class="btn-delete-conversation  archive" data-action="archive" title="" data-toggle="tooltip" data-placement="top" data-original-title="Archive conversation">
                                <span><i class="fa fa-trash"> </i>  <?php _e('Archive', ET_DOMAIN) ?></span>
                            </button>
                        </div>
                    </div>
                <?php } ?>
                    <div class="conversation-detail">
                        <div class="row lists-conversation">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="ScrollbarConversation" id="ScrollbarConversation1">
                                    <ul class="item-message-conversation ae-pr-msg-reply-list">
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row search-conversation ae-pr-msg-reply-form">
                            <form role="form" id="private_msg_reply_form" class="form-horizontal validateNumVal" novalidate="novalidate">
                                <div class="col-msg">
                                    <div class="left-reply-conversation">
                                        <textarea id=" " name="post_title" placeholder="<?php _e('Type here to reply', ET_DOMAIN) ?>" data-autosize-on="true" class="form-control field-reply-msg" required ></textarea>
                                        <input type="hidden" name="post_content" value="" />
                                        <input type="hidden" name="post_parent" value="" />
                                        <input type="hidden" name="sync_type" value="reply" />
                                        <div class="right-reply-conversation">
                                            <button type="submit" class="btn-reply-conversation disable-reply ae-pr-msg-reply-submit " href=""><i class="fa fa-paper-plane"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
<?php   }
}
if( !function_exists('ae_private_message_rely_loop_item') ){
    /**
     * Private message reply loop item
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    function ae_private_message_reply_loop_item(){ ?>
        <script type="text/template" id="ae-private-message-reply-loop">
            <span class="date-conversation">{{= conversation_date }}</span>
            <div class="msg-conversation-detail">
                <div class="p">
                    {{= post_content }}
                </div>
                <button type="button" class="btn-delete-msg-conversation btn-link action archive-reply" data-action="archive_reply" title="" data-toggle="tooltip" data-placement="top" data-original-title="Archive message">
                    <span><i class="fa fa-trash"> </i> </span>
                </button>
            </div>
        </script>
    <?php    }

}
function ae_private_message_redirect(){
    if( isset($_GET['pr_msg_c_id']) ){
        $conversation = ae_private_message_get_conversation($_GET['pr_msg_c_id']);
        if( $conversation && ae_private_message_user_can_view_conversation($conversation) ){
            echo '<script type="data/json" class="ae_private_conversation_redirect_data" >'.json_encode($conversation).'</script>';
        }
    }
}