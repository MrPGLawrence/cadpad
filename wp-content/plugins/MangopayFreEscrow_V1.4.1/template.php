<?php
/**
 * Created by PhpStorm.
 * User: ping
 * Date: 21-03-2018
 * Time: 04:01 PM
 */
/**
 * Card form template
 * @param void
 * @return void
 * @since 1.0
 * @package AE_ESCROW
 */
function fre_mangopay_account_form()
{
    global $user_ID;
    $user_data = get_user_by('ID', $user_ID);
    ?>
    <div class="fre-input-field">
        <label class="fre-field-title"><?php _e("Email", ET_DOMAIN); ?></label>
        <input name="mangopay_email" tabindex="19" id="mangopay_email" type="text" size="20" data-mangopay="email"
               class="bg-default-input not_empty" placeholder="youremail@gmail.com" readonly
               value="<?php echo $user_data->user_email ?>"/>
    </div>

    <div class="fre-input-field name_card">
        <label class="fre-field-title" for="first_name"><?php _e('First Name', ET_DOMAIN); ?></label>
        <input tabindex="23" name="first_name" id="first_name" data-mangopay="name" class="bg-default-input not_empty"
               type="text" readonly value="<?php echo $user_data->user_firstname ?>"/>
    </div>
    <div class="fre-input-field name_card">
        <label class="fre-field-title" for="last_name"><?php _e('Last Name', ET_DOMAIN); ?></label>
        <input tabindex="24" name="last_name" id="last_name" data-mangopay="name" class="bg-default-input not_empty"
               type="text" readonly value="<?php echo $user_data->user_lastname ?>"/>
    </div>
    <div class="fre-input-field">
        <?php
        $today = date("Y-m-d");
        ?>
        <label for="dob" class="fre-field-title"><?php _e('Date of Birth', ET_DOMAIN); ?><span
                    class="description required"><?php _e('', 'mangopay'); ?></span></label>
        <br> <input type="date" name="dob" id="dob" class="col-xs-12 fre-input-field" max="<?php echo $today ?>"
                    placeholder="<?php _e('Birthday', ET_DOMAIN); ?>">
        <input type="hidden" value="fre-mangopay-escrow-customer" name="action"></div>
    <div class="fre-input-field">

        <label for="country" class="fre-field-title"><?php _e('Country', ET_DOMAIN); ?></label>

        <?php

        ae_tax_dropdown('country', array(

                'attr' => 'data-chosen-width="100%" data-chosen-disable-search="" data-placeholder="' . __("Select country", ET_DOMAIN) . '"',

                'class' => 'fre-chosen-single',

                'hide_empty' => false,

                'hierarchical' => true,
                'name' => 'country',
                'value' => 'slug',

                'id' => 'country',

                'show_option_all' => __("Select country", ET_DOMAIN)

            )

        );

        ?>

    </div>
    <div class="fre-input-field">

        <label for="nationality" class="fre-field-title"><?php _e('Nationality', ET_DOMAIN); ?></label>

        <?php

        ae_tax_dropdown('country', array(

                'attr' => 'data-chosen-width="100%" data-chosen-disable-search="" data-placeholder="' . __("Select country", ET_DOMAIN) . '"',

                'class' => 'fre-chosen-single',

                'hide_empty' => false,

                'hierarchical' => true,

                'value' => 'slug',
                'name' => 'nationality',
                'id' => 'nationality',

                'show_option_all' => __("Select Nationality", ET_DOMAIN)

            )

        );

        ?>

    </div>
<?php }

/**
 * Update Mangopay Account button
 * @param void
 * @return void
 * @since 1.0
 * @package AE_ESCROW
 */
function fre_update_mangopay_button()
{ ?>

    <?php
    global $user_ID;
    $ae_escrow_mangopay = mangopay_escrow::getInstance();
    $ae_escrow_mangopay->init();

    if (!$ae_escrow_mangopay->ae_get_mangopay_user_id($user_ID)) {
        ?>
        <li>

    <span class="update-mangopay-container">
			<a href="#" class="btn-update-mangopay">
				<?php
                global $user_ID;
                $ae_escrow_mangopay = mangopay_escrow::getInstance();
                $ae_escrow_mangopay->init();
                if ($ae_escrow_mangopay->ae_get_mangopay_user_id($user_ID)) {

                } else {
                    echo '<i class="fa fa-user-plus"></i>';
                    _e('Create Mangopay Account', ET_DOMAIN);
                } ?>
			</a>
        </span>
        </li>
        <?php
    }
    ?>
<?php }

/**
 * Mangopay Account modal
 * @param void
 * @return void
 * @since 1.0
 * @package AE_ESCROW
 */
function fre_update_mangopay_info_modal()
{ ?>
    <div class="modal fade" id="mangopay_escrow_modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button style="z-index:1000;" data-dismiss="modal" class="close">
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="info slogan">
                        <h4 class="modal-title"><span
                                    class="plan_name"><?php _e("Create Mangopay Account", ET_DOMAIN); ?></span></h4>
                    </div>
                </div>
                <div class="modal-body">
                    <form class="modal-form fre-modal-form" id="mangopay_form" novalidate="novalidate" autocomplete="on"
                          data-ajax="false">
                        <?php fre_mangopay_account_form(); ?>
                        <div class="fre-form-btn">
                            <button class="fre-normal-btn btn-submit" type="submit"
                                    id="submit_mangopay"> <?php _e('Create', ET_DOMAIN); ?> </button>
                            <span class="fre-form-close" data-dismiss="modal">Cancel</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * The field for users update their Mangopay account
 * @param string $html of user escrow field
 * @return string $html
 * @since 1.0
 * @package AE_ESCROW
 */
function ae_mangopay_recipient_field($html)
{
    $ae_escrow_mangopay = mangopay_escrow::getInstance();
    $ae_escrow_mangopay->init();
    global $user_ID;
    if ($ae_escrow_mangopay->is_use_mangopay_escrow()) {
        ob_start();
        if (ae_user_role($user_ID) == FREELANCER) {
            fre_update_mangopay_button();
        }
        $html = ob_get_clean();
    }
    echo $html;
}

add_action('ae_escrow_stripe_user_field', 'ae_mangopay_recipient_field');
/**
 * Mangopay email field
 * @param void
 * @return void
 * @since 1.0
 * @package AE_ESCROW
 */
function ae_mangopay_email()
{
    global $user_ID;
    ?>
    <div class="form-group">
        <div class="form-group-control">
            <label><?php _e('Your Stripe Account', ET_DOMAIN) ?></label>
            <input type="stripe_email" class="form-control" id="stripe_email" name="stripe_email"
                   value="<?php echo get_user_meta($user_ID, 'stripe_email', true); ?>"
                   placeholder="<?php _e('Enter your Stripe email', ET_DOMAIN) ?>">
        </div>
    </div>
    <div class="clearfix"></div>
<?php }

/**
 * Notification
 * @param void
 * @return void
 * @since 1.0
 * @package AE_ESCROW
 */
function ae_mangopay_escrow_notification($msg = '')
{ ?>
    <script type="text/javascript" id="user-confirm">
        (function ($, Views, Models) {
            $(document).ready(function () {
                <?php if( !empty($msg) ){?>
                var msg = "<?php echo $msg;?>";
                <?php } else { ?>
                var msg = "<?php _e('Your account has been successfully connected to Mangopay!', ET_DOMAIN); ?>";
                <?php } ?>
                alert(msg);
                window.location.href = "<?php echo et_get_page_link('profile'); ?>"
            });
        })(jQuery, window.Views, window.Models);
    </script>
<?php }

/**
 * disable paypal field
 * @param void
 * @return void
 * @since 1.0
 * @package AE_ESCROW
 */
add_filter('ae_escrow_recipient_field_html', 'ae_escrow_mangopay_field_html');
function ae_escrow_mangopay_field_html($html)
{
    $ae_escrow_mangopay = mangopay_escrow::getInstance();
    if ($ae_escrow_mangopay->is_use_mangopay_escrow()) {
        return '';
    }
    return $html;
}