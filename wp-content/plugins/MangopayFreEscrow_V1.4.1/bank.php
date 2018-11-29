<?php
/**
 * Created by PhpStorm.
 * User: ping
 * Date: 14-04-2018
 * Time: 10:42 AM
 */

/**
 * Card form template
 * @param void
 * @return void
 * @since 1.0
 * @package AE_ESCROW
 * @category STRIPE
 * @author Tambh
 */
function fre_mangopay_bank_account_form()
{
    global $user_ID;
    $user_data = get_user_by('ID', $user_ID);
    ?>

    <!--    <ul class="nav nav-tabs">-->
    <!--        <li class="active"><a data-toggle="tab" href="#home">IBAN</a></li>-->
    <!--        <li><a data-toggle="tab" href="#menu1">GB</a></li>-->
    <!--        <li><a data-toggle="tab" href="#menu2">OTHER</a></li>-->
    <!--    </ul>-->
    <select class="col-md-12 fre-input-field" name="cases" id="cases">
        <option>Select one</option>
        <option value="iban">IBAN</option>
        <option value="uk">UK</option>
        <option value="other">OTHER</option>
    </select><br>


    <!--<div class="tab-content">-->
    <div id="iban" class="tab-pane formhide fade in">
        <form class="modal-form fre-modal-form" id="mangopay_form" novalidate="novalidate" autocomplete="on"
              data-ajax="false">
            <input type="hidden" value="fre-mangopay-escrow-iban" name="action">
            <div class="fre-input-field">
                <label class="fre-field-title"><?php _e("IBAN", ET_DOMAIN); ?></label>
                <input name="iban_value" tabindex="19" id="iban_value" type="text" size="22" data-mangopay="iban"
                       class="bg-default-input not_empty" placeholder="GB15MIDL40051512345678"/>

            </div>

            <div class="fre-input-field name_card">
                <label class="fre-field-title" for="bic_value"><?php _e('BIC', ET_DOMAIN); ?></label>
                <input tabindex="23" name="bic_value" id="bic_value" data-mangopay="bic"
                       class="bg-default-input not_empty" placeholder="MIDLGB22" type="text"/>
            </div>
            <div class="fre-input-field name_card">
                <label class="fre-field-title"
                       for="account_holder_name"><?php _e('Account holder\'s name', ET_DOMAIN); ?></label>
                <input tabindex="24" name="account_holder_name" id="account_holder_name"
                       data-mangopay="account_holder_name"
                       class="bg-default-input not_empty" type="text"
                />
            </div>
            <div class="fre-input-field name_card">
                <label class="fre-field-title"
                       for="account_holder_address"><?php _e('Account holder\'s address', ET_DOMAIN); ?></label>
                <input tabindex="24" name="account_holder_address" id="account_holder_address"
                       data-mangopay="account_holder_address"
                       class="bg-default-input not_empty" type="text"
                />
            </div>

            <div class="fre-input-field name_card">
                <label class="fre-field-title"
                       for="account_holder_city"><?php _e('Account holder\'s city', ET_DOMAIN); ?></label>
                <input tabindex="24" name="account_holder_city" id="account_holder_city"
                       data-mangopay="account_holder_city"
                       class="bg-default-input not_empty" type="text"
                />
            </div>
            <div class="fre-input-field name_card">
                <label class="fre-field-title"
                       for="account_holder_postcode"><?php _e('Account holder\'s postcode', ET_DOMAIN); ?></label>
                <input tabindex="24" name="account_holder_postcode" id="account_holder_postcode"
                       data-mangopay="account_holder_postcode"
                       class="bg-default-input not_empty" type="text"
                />
            </div>

            <div class="fre-input-field">

                <label for="country"
                       class="fre-field-title"><?php _e('Account holder\'s country', ET_DOMAIN); ?></label>

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
            <div class="fre-form-btn">
                <button class="fre-normal-btn btn-submit" type="submit"
                        id="submit_mangopay"> <?php _e('Submit', ET_DOMAIN); ?> </button>
                <span class="fre-form-close" data-dismiss="modal">Cancel</span>

            </div>
        </form>
    </div>
    <div id="uk" class="tab-pane formhide fade in">
        <form class="modal-form fre-modal-form" id="mangopay_form" novalidate="novalidate" autocomplete="on"
              data-ajax="false">
            <input type="hidden" value="fre-mangopay-escrow-uk" name="action">
            <div class="fre-input-field">
                <label class="fre-field-title"><?php _e("Account Number", ET_DOMAIN); ?></label>
                <input name="gb_account_number" tabindex="19" id="gb_account_number" type="text" size="22"
                       data-mangopay="gb_account_number"
                       class="bg-default-input not_empty" placeholder="11696419"/>

            </div>
            <div class="fre-input-field">
                <label class="fre-field-title"><?php _e("Sort Code", ET_DOMAIN); ?></label>
                <input name="gb_account_sortcode" tabindex="19" id="gb_account_sortcode" type="text" size="6"
                       data-mangopay="gb_account_sortcode"
                       class="bg-default-input not_empty" placeholder="010039"/>

            </div>
            <div class="fre-input-field name_card">
                <label class="fre-field-title"
                       for="account_holder_name"><?php _e('Account holder\'s name', ET_DOMAIN); ?></label>
                <input tabindex="24" name="account_holder_name" id="account_holder_name"
                       data-mangopay="account_holder_name"
                       class="bg-default-input not_empty" type="text"
                />
            </div>
            <div class="fre-input-field name_card">
                <label class="fre-field-title"
                       for="account_holder_address"><?php _e('Account holder\'s address', ET_DOMAIN); ?></label>
                <input tabindex="24" name="account_holder_address" id="account_holder_address"
                       data-mangopay="account_holder_address"
                       class="bg-default-input not_empty" type="text"
                />
            </div>

            <div class="fre-input-field name_card">
                <label class="fre-field-title"
                       for="account_holder_city"><?php _e('Account holder\'s city', ET_DOMAIN); ?></label>
                <input tabindex="24" name="account_holder_city" id="account_holder_city"
                       data-mangopay="account_holder_city"
                       class="bg-default-input not_empty" type="text"
                />
            </div>
            <div class="fre-input-field name_card">
                <label class="fre-field-title"
                       for="account_holder_postcode"><?php _e('Account holder\'s postcode', ET_DOMAIN); ?></label>
                <input tabindex="24" name="account_holder_postcode" id="account_holder_postcode"
                       data-mangopay="account_holder_postcode"
                       class="bg-default-input not_empty" type="text"
                />
            </div>

            <div class="fre-input-field">

                <label for="country"
                       class="fre-field-title"><?php _e('Account holder\'s country', ET_DOMAIN); ?></label>

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
            <div class="fre-form-btn">
                <button class="fre-normal-btn btn-submit" type="submit"
                        id="submit_mangopay"> <?php _e('Submit', ET_DOMAIN); ?> </button>
                <span class="fre-form-close" data-dismiss="modal">Cancel</span>

            </div>
        </form>
    </div>
    <div id="other" class="tab-pane formhide fade in">
        <form class="modal-form fre-modal-form" id="mangopay_form" novalidate="novalidate" autocomplete="on"
              data-ajax="false">
            <input type="hidden" value="fre-mangopay-escrow-other" name="action">
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
            <div class="fre-input-field name_card">
                <label class="fre-field-title" for="other_bic_value"><?php _e('BIC', ET_DOMAIN); ?></label>
                <input tabindex="23" name="other_bic_value" id="other_bic_value" data-mangopay="bic"
                       class="bg-default-input not_empty" placeholder="CRLYFRPP" type="text"/>
            </div>
            <div class="fre-input-field">
                <label class="fre-field-title"><?php _e("Account Number", ET_DOMAIN); ?></label>
                <input name="other_account_number" tabindex="19" id="other_account_number" type="text" size="22"
                       data-mangopay="gb_account_number"
                       class="bg-default-input not_empty" placeholder="11696419"/>

            </div>
            <div class="fre-input-field name_card">
                <label class="fre-field-title"
                       for="account_holder_name"><?php _e('Account holder\'s name', ET_DOMAIN); ?></label>
                <input tabindex="24" name="account_holder_name" id="account_holder_name"
                       data-mangopay="account_holder_name"
                       class="bg-default-input not_empty" type="text"
                />
            </div>
            <div class="fre-input-field name_card">
                <label class="fre-field-title"
                       for="account_holder_address"><?php _e('Account holder\'s address', ET_DOMAIN); ?></label>
                <input tabindex="24" name="account_holder_address" id="account_holder_address"
                       data-mangopay="account_holder_address"
                       class="bg-default-input not_empty" type="text"
                />
            </div>

            <div class="fre-input-field name_card">
                <label class="fre-field-title"
                       for="account_holder_city"><?php _e('Account holder\'s city', ET_DOMAIN); ?></label>
                <input tabindex="24" name="account_holder_city" id="account_holder_city"
                       data-mangopay="account_holder_city"
                       class="bg-default-input not_empty" type="text"
                />
            </div>
            <div class="fre-input-field name_card">
                <label class="fre-field-title"
                       for="account_holder_postcode"><?php _e('Account holder\'s postcode', ET_DOMAIN); ?></label>
                <input tabindex="24" name="account_holder_postcode" id="account_holder_postcode"
                       data-mangopay="account_holder_postcode"
                       class="bg-default-input not_empty" type="text"
                />
            </div>

            <div class="fre-input-field">

                <label for="account_holder_country"
                       class="fre-field-title"><?php _e('Account holder\'s country', ET_DOMAIN); ?></label>

                <?php

                ae_tax_dropdown('country', array(

                        'attr' => 'data-chosen-width="100%" data-chosen-disable-search="" data-placeholder="' . __("Select country", ET_DOMAIN) . '"',

                        'class' => 'fre-chosen-single',

                        'hide_empty' => false,

                        'hierarchical' => true,
                        'name' => 'account_holder_country',
                        'value' => 'slug',

                        'id' => 'account_holder_country',

                        'show_option_all' => __("Select country", ET_DOMAIN)

                    )

                );

                ?>

            </div>

            <div class="fre-form-btn">
                <button class="fre-normal-btn btn-submit" type="submit"
                        id="submit_mangopay"> <?php _e('Submit', ET_DOMAIN); ?> </button>
                <span class="fre-form-close" data-dismiss="modal">Cancel</span>

            </div>
        </form>
    </div>


    <!--</div>-->


<?php }

function fre_mangopay_payout_form()
{
    $ae_escrow_mangopay = mangopay_escrow::getInstance();
    $ae_escrow_mangopay->init();
    global $user_ID;
    $UserId = $ae_escrow_mangopay->ae_get_mangopay_user_id($user_ID);
    $walletID = get_user_meta($user_ID, 'ae_mangopay_Wallet_id', true);
    if ($walletID != '') {
        $bankID = get_user_meta($user_ID, 'mangopay_bank_details', true);
        if ($bankID != '') {
            $walletDetails = $ae_escrow_mangopay->mp_wallet_details($walletID);
            $currency = $walletDetails->Balance->Currency;
            $amount = $walletDetails->Balance->Amount;
            $amount = $amount / 100;
            $details = array(
                "BankAccountId" => $bankID,
                "UserId" => $UserId
            );
            $bankDetails = $ae_escrow_mangopay->mp_bank_details($details); ?>
            <div class="fre-input-field">
                <input type="hidden" value="fre-mangopay-escrow-payout" name="action">
                <input type="hidden" value="<?php echo $UserId ?>" name="AuthorId">
                <input type="hidden" value="<?php echo $walletID ?>" name="DebitedWalletID">
                <input type="hidden" value="<?php echo $currency ?>" name="Currency">
                <input type="hidden" value="<?php echo $amount ?>" name="Amount">
                <input type="hidden" value="<?php echo $bankID ?>" name="BankAccountId">
                <input type="hidden" value="0" name="Fee">
            </div>

            <table class="table table-hover table-responsive">
                <thead>
                <tr>
                    <th>Bank Account Details</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Mangopay Bank ID</td>
                    <td><?php
                        echo $bankID;
                        ?></td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td><?php echo $bankDetails->OwnerName ?></td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td>
                        <table class="table table-hover table-responsive">
                            <tbody>
                            <tr>
                                <td>Address Line</td>
                                <td><?php echo $bankDetails->OwnerAddress->AddressLine1 ?></td>
                            </tr>
                            <tr>
                                <td>City</td>
                                <td><?php echo $bankDetails->OwnerAddress->City ?></td>
                            </tr>
                            <tr>
                                <td>Post Code</td>
                                <td><?php echo $bankDetails->OwnerAddress->PostalCode ?></td>
                            </tr>
                            <tr>
                                <td>Country</td>
                                <td><?php echo $bankDetails->OwnerAddress->Country ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>Type</td>
                    <td><?php echo $bankDetails->Type ?></td>
                </tr>
                <tr>
                    <td>Sort Code</td>
                    <td><?php echo $bankDetails->Details->SortCode ?></td>
                </tr>
                <tr>
                    <td>Account Number</td>
                    <td><?php echo $bankDetails->Details->AccountNumber ?></td>
                </tr>
                <tr>
                    <td>Currency</td>
                    <td><?php echo $currency ?></td>
                </tr>
                <tr>
                    <td>Amount</td>
                    <td><?php echo $amount ?></td>
                </tr>
                </tbody>
            </table>
        <?php }
    }


}

/**
 * Update Mangopay Account button
 * @param void
 * @return void
 * @since 1.0
 * @package AE_ESCROW
 */
function fre_update_mangopay_bank_button()
{ ?>

    <?php
    global $user_ID;
    $ae_escrow_mangopay = mangopay_escrow::getInstance();
    $ae_escrow_mangopay->init();
    $walletInfo = get_user_meta($user_ID, 'ae_mangopay_Wallet_id', true);
    if ($walletInfo != '') {
        $walletDetails = $ae_escrow_mangopay->mp_wallet_details($walletInfo);
        $currency = $walletDetails->Balance->Currency;
        $amount = $walletDetails->Balance->Amount;
        if ($ae_escrow_mangopay->ae_get_mangopay_user_id($user_ID) && $amount != 0) {
            ?>
            <li>

            <span class="payout-mangopay-container">
                <a href="#" class="btn-mangopay-payout">
                    <?php
                    global $user_ID;
                    $ae_escrow_mangopay = mangopay_escrow::getInstance();
                    $ae_escrow_mangopay->init();
                    if ($currency == 'GBP') {
                        $currency = '£';
                    }
                    if ($ae_escrow_mangopay->ae_get_mangopay_user_id($user_ID)) {
                        echo '<i class="fa fa-money"></i>';
                        _e('Create a payout of amount &nbsp;' . $currency . $amount / 100, ET_DOMAIN);

                    } ?>
                </a>
            </span>
            </li>
            <?php
        }

    }

    if ($ae_escrow_mangopay->ae_get_mangopay_user_id($user_ID)) {
        ?>
        <li>

            <span class="update-mangopay-container">
                <a href="#" class="btn-update-mangopay-bank">
                    <?php
                    global $user_ID;
                    $ae_escrow_mangopay = mangopay_escrow::getInstance();
                    $ae_escrow_mangopay->init();
                    if ($ae_escrow_mangopay->ae_get_mangopay_user_id($user_ID)) {
                        echo '<i class="fa fa-bank"></i>';
                        _e('Update Bank Details ', ET_DOMAIN);

                    } ?>
                </a>
            </span>
        </li>
<!--        <li>-->
<!---->
<!--            <span class="update-mangopay-kyc-container">-->
<!--                <a href="#" class="btn-update-mangopay-kyc">-->
<!--                    --><?php
//                    global $user_ID;
//                    $ae_escrow_mangopay = mangopay_escrow::getInstance();
//                    $ae_escrow_mangopay->init();
//                    if ($ae_escrow_mangopay->ae_get_mangopay_user_id($user_ID)) {
//                        echo '<i class="fa fa-file-pdf-o"></i>';
//                        _e('Update KYC Details ', ET_DOMAIN);
//
//                    } ?>
<!--                </a>-->
<!--            </span>-->
<!--        </li>-->

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
function fre_update_mangopay_bank_info_modal()
{ ?>
    <div class="modal fade" id="mangopay_escrow_modal_bank" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button style="z-index:1000;" data-dismiss="modal" class="close">
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="info slogan">
                        <h4 class="modal-title"><span
                                    class="plan_name"><?php _e("Link your bank to Mangopay account", ET_DOMAIN); ?></span>
                        </h4>
                    </div>
                </div>
                <div class="modal-body">

                    <?php fre_mangopay_bank_account_form(); ?>

                </div>
            </div>
        </div>
    </div>

    <?php
}

function fre_update_mangopay_payout_modal()
{ ?>
    <div class="modal fade" id="mangopay_escrow_modal_payout" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button style="z-index:1000;" data-dismiss="modal" class="close">
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="info slogan">
                        <h4 class="modal-title"><span
                                    class="plan_name"><?php _e("Create a Payout to your bank linked to Mangopay", ET_DOMAIN); ?></span>
                        </h4>
                    </div>
                </div>
                <div class="modal-body">
                    <form class="modal-form fre-modal-form" id="mangopay_form" novalidate="novalidate"
                          autocomplete="on"
                          data-ajax="false">
                        <?php
                        fre_mangopay_payout_form();
                        ?>
                        <div class="fre-form-btn">
                            <button class="fre-normal-btn btn-submit" type="submit"
                                    id="submit_mangopay"> <?php _e('Create', ET_DOMAIN); ?> </button>
                            <span class="fre-form-close" data-dismiss="modal">&nbsp;Cancel</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
}

function fre_update_mangopay_kyc_modal()
{ ?>
    <div class="modal fade" id="mangopay_escrow_modal_kyc" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button style="z-index:1000;" data-dismiss="modal" class="close">
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="info slogan">
                        <h4 class="modal-title"><span
                                    class="plan_name"><?php _e("Create a Payout to your bank linked to Mangopay", ET_DOMAIN); ?></span>
                        </h4>
                    </div>
                </div>
                <div class="modal-body">
                    <form class="modal-form fre-modal-form" id="mangopay_form" novalidate="novalidate"
                          autocomplete="on"
                          data-ajax="false">
                        <div class="fre-form-btn">
                            <button class="fre-normal-btn btn-submit" type="submit"
                                    id="submit_mangopay"> <?php _e('Create', ET_DOMAIN); ?> </button>
                            <span class="fre-form-close" data-dismiss="modal">&nbsp;Cancel</span>
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
function ae_mangopay_bank_recipient_field($html)
{
    $ae_escrow_mangopay = mangopay_escrow::getInstance();
    $ae_escrow_mangopay->init();
    global $user_ID;
    if ($ae_escrow_mangopay->is_use_mangopay_escrow()) {
        ob_start();
        if (ae_user_role($user_ID) == FREELANCER) {
            fre_update_mangopay_bank_button();
        }
        $html = ob_get_clean();
    }
    echo $html;
}

add_action('ae_escrow_stripe_user_field', 'ae_mangopay_bank_recipient_field');

/**
 * disable paypal field
 * @param void
 * @return void
 * @since 1.0
 * @package AE_ESCROW
 */
add_filter('ae_escrow_recipient_field_html', 'ae_escrow_mangopay_bank_field_html');
function ae_escrow_mangopay_bank_field_html($html)
{
    $ae_escrow_mangopay = mangopay_escrow::getInstance();
    if ($ae_escrow_mangopay->is_use_mangopay_escrow()) {
        return '';
    }
    return $html;
}