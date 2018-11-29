<?php

/*
Plugin Name: Mangopay Fre Escrow
Description: Plugin to enable Mangopay as an ESCROW payment service for Freelance Engine Theme
Version: 1.4.1
Author: CADPAD
Author URI: https://cadpad3d.co.uk
License: Commercial
*/
function require_plug_escrow()
{
    if (!class_exists('AE_Base')) {
        return 0;
    }
    include_once dirname(__FILE__).'/mangopay.php';
    include_once dirname(__FILE__).'/settings.php';
    include_once dirname(__FILE__).'/template.php';
    include_once dirname(__FILE__).'/bank.php';
    $ae_escrow_mangopay = mangopay_escrow::getInstance();
    if ($ae_escrow_mangopay->is_use_mangopay_escrow()) {
        $ae_escrow_mangopay->init();
    }
}

add_action('after_setup_theme', 'require_plug_escrow');
/**
 * @return int
 */
function ae_mangopay_escrow_scripts()
{
    if (!class_exists('AE_Base')) {
        return 0;
    }
    $ae_escrow_mangopay = mangopay_escrow::getInstance();
    if ($ae_escrow_mangopay->is_use_mangopay_escrow()) {
        if (is_page_template('page-profile.php') || et_load_mobile()) {
            wp_enqueue_style('ae_mangopay_escrow_css', plugin_dir_url(__FILE__).'assets/mangopay.css', array(), '1.0');
            wp_enqueue_script('ae_mangopay_escrow_js', plugin_dir_url(__FILE__).'assets/mangopay.js', array(
                'underscore',
                'backbone',
                'appengine',
            ), '1.0', true);
            wp_enqueue_script('ae_mangopay_escrow_bank_js', plugin_dir_url(__FILE__).'assets/bank.js', array(
                'underscore',
                'backbone',
                'appengine',
            ), '1.0', true);
            wp_enqueue_script('ae_mangopay_escrow_payout_js', plugin_dir_url(__FILE__).'assets/payout.js', array(
                'underscore',
                'backbone',
                'appengine',
            ), '1.0', true);
            wp_enqueue_script('ae_mangopay_escrow_kyc_js', plugin_dir_url(__FILE__).'assets/kyc.js', array(
                'underscore',
                'backbone',
                'appengine',
            ), '1.0', true);
        }
    }
}

add_action('wp_enqueue_scripts', 'ae_mangopay_escrow_scripts');
/**
 * @param $args
 *
 * @return WP_Error
 */
function ae_mangopay_escrow_before_insert_bid($args)
{
    if (ae_get_option('use_escrow')) {
        global $user_ID;
        $ae_escrow_mangopay = mangopay_escrow::getInstance();
        $ae_escrow_mangopay->init();
        $data = $ae_escrow_mangopay->ae_get_mangopay_kyc_info($user_ID);
        if ($ae_escrow_mangopay->is_use_mangopay_escrow() && $ae_escrow_mangopay->ae_get_mangopay_user_id($user_ID) == '') {
            return new WP_Error('update_stripe', __(' &nbsp; Please go to your profile for Mangopay account completion', ET_DOMAIN));
        }
        if ($ae_escrow_mangopay->is_use_mangopay_escrow() && $ae_escrow_mangopay->ae_get_mangopay_bank_id($user_ID) == '') {
            return new WP_Error('update_stripe', __(' &nbsp; Please go to your profile to link your bank detail with your Mangopay account', ET_DOMAIN));
        }
        if ($ae_escrow_mangopay->is_use_mangopay_escrow() && $data == null) {
            return new WP_Error('update_stripe', __(' &nbsp;<b>Mangopay</b>&nbsp;<i class="fa fa-file-pdf-o"></i>&nbsp; Please submit your "Proof of identity" or "Proof of address" via email &nbsp;<a href="mailto:kyc@cadpad3d.co.uk">kyc@cadpad3d.co.uk</a>', ET_DOMAIN));
        }
        if ($ae_escrow_mangopay->is_use_mangopay_escrow() && $data != null) {
            $type = $data['Type'];
            $status = $data['Status'];
            $RefusedReasonType = $data['RefusedReasonType'];
            switch ($type) {
                case    'IDENTITY_PROOF':
                    switch ($status) {
                        case    'VALIDATION_ASKED':
                            return new WP_Error('update_stripe', __(' &nbsp; Your "Identity Proof" is under process. Try to bid once the validation process succeeded.', ET_DOMAIN));
                            break;
                        case    'CREATED':
                            break;
                        case    'VALIDATED':
                            break;
                        case    'REFUSED':
                            $message = $RefusedReasonType;
                            $message_string = str_replace('_', ' ', $message);

                            return new WP_Error('update_stripe', __(' &nbsp; Your KYC has been declined by MANGOPAY.<span class="alert-info"> REASON:&nbsp;'.$message_string.'</span>', ET_DOMAIN));
                            break;
                    }

                    break;
                case    'ADDRESS_PROOF':
                    switch ($status) {
                        case    'VALIDATION_ASKED':
                            return new WP_Error('update_stripe', __(' &nbsp; Your "Identity Proof" is under process. Try to bid once the validation process succeeded.', ET_DOMAIN));
                            break;
                        case    'CREATED':
                            break;
                        case    'VALIDATED':
                            break;
                        case    'REFUSED':
                            $message = $RefusedReasonType;
                            $message_string = str_replace('_', ' ', $message);

                            return new WP_Error('update_stripe', __(' &nbsp; Your KYC has been declined by MANGOPAY. <span class="alert-info"> REASON:&nbsp;'.$message_string.'.</span>&nbsp; Try resubmitting your KYC document.', ET_DOMAIN));
                            break;
                    }
            }
        }
    }

    return $args;
}

add_filter('ae_pre_insert_bid', 'ae_mangopay_escrow_before_insert_bid', 12, 1);
