<?php
/**
 * Created by PhpStorm.
 * User: @PingHarsha
 * Date: 20-03-2018
 * Time: 12:58 PM
 */


if (!function_exists('fre_escrow_payment_gateway_mangopay_setting')) {

    function fre_escrow_payment_gateway_mangopay_setting($groups)
    {

        $groups[] = array(
            'args' => array(
                'title' => __("MANGOPAY API", ET_DOMAIN),
                'id' => 'use-escrow-mangopay',
                'class' => '',
                'name' => 'escrow_mangopay_api',
                // 'desc' => __("Your Paypal Adaptive API", ET_DOMAIN)
            ),

            'fields' => array(
                array(
                    'id' => 'use_mangopay_escrow',
                    'type' => 'switch',
                    'title' => __("use mangopay escrow", ET_DOMAIN),
                    'name' => 'use_mangopay_escrow',
                    'class' => ''
                ),
                array(
                    'id' => 'client_id',
                    'type' => 'text',
                    'name' => 'client_id',
                    'label' => __("Your MANGOPAY client ID", ET_DOMAIN),
                    'class' => ''
                ),
                array(
                    'id' => 'client_secret',
                    'type' => 'text',
                    'name' => 'client_secret',
                    'label' => __("Your MANGOPAY client passphrase", ET_DOMAIN),
                    'class' => ''
                ),
                array(
                    'id' => 'mangopay_escrow_wallet_id',
                    'type' => 'text',
                    'name' => 'mangopay_escrow_wallet_id',
                    'label' => __("Your MANGOPAY Escrow Wallet ID", ET_DOMAIN),
                    'class' => ''
                ),
                array(
                    'id' => 'mangopay_escrow_user_id',
                    'type' => 'text',
                    'name' => 'mangopay_escrow_user_id',
                    'label' => __("Your MANGOPAY Escrow User Id", ET_DOMAIN),
                    'class' => ''
                ),
            )
        );
        return $groups;
    }
}
add_filter('fre_escrow_payment_gateway_settings', 'fre_escrow_payment_gateway_mangopay_setting');
if (!function_exists('fre_credit_disable_escrow')) {
    function fre_credit_disable_escrow($name)
    {
        if ($name == "escrow_mangopay_api") {
            $credit_api = ae_get_option('escrow_credit_settings');
            $mangopay_api = ae_get_option('escrow_mangopay_api');
            if ($mangopay_api['use_mangopay_escrow']) {
                $credit_api['use_credit_escrow'] = false;
                ae_update_option('escrow_credit_settings', $credit_api);
            }
        }
    }
}
function jst()
{
    $sourceLocation = plugins_url('/MangopayFreEscrow/assets/mangopay.js');
    $bankSourceLocation = plugins_url('/MangopayFreEscrow/assets/bank.js');
    ?>
    <script type='text/javascript' src="<?php echo $sourceLocation ?>"></script>
    <script type='text/javascript' src="<?php echo $bankSourceLocation ?>"></script>
    <?php
}

add_action('ae_save_option', 'fre_credit_disable_escrow');
if (is_page_template('page-profile.php') || et_load_mobile()) {
    add_action('wp_head', 'jst');
}

