<?php
/**
 * Created by PhpStorm.
 * User: ping
 * Date: 20-03-2018
 * Time: 02:24 PM
 */

class mangopay_escrow extends AE_Base
{

    const DEBUG = false;
    const TMP_DIR_NAME = 'mp_tmp';
    const SANDBOX_API_URL = 'https://api.sandbox.mangopay.com';
    const PROD_API_URL = 'https://api.mangopay.com';
    const SANDBOX_DB_URL = 'https://dashboard.sandbox.mangopay.com';    // Turns debugging messages on or off (should be false for production)
    const PROD_DB_URL = 'https://dashboard.mangopay.com';
    const LOGFILENAME = 'mp-transactions.log.php';
    private static $instance;
    public $client_id;
    public $client_secret;
    public $redirect_uri;
    private $mangoPayApi;
    private $mp_loaded = false;
    private $mp_production = false;

    private function __construct()
    {
    }

    public function connection_test()
    {

        if (!self::getInstance()->mp_loaded)
            $this->init();

        try {
            $pagination = new MangoPay\Pagination(1, 1);
            $sorting = new \MangoPay\Sorting();
            $sorting->AddField('CreationDate', \MangoPay\SortDirection::DESC);
            $result = $this->mangoPayApi->Users->GetAll($pagination, $sorting);
            $this->mp_loaded = true;
            return $result;

        } catch (MangoPay\Libraries\ResponseException $e) {

            echo '<div class="error"><p>' . __('MANGOPAY API returned:', 'mangopay') . ' ';
            MangoPay\Libraries\Logs::Debug('MangoPay\ResponseException Code', $e->GetCode());
            MangoPay\Libraries\Logs::Debug('Message', $e->GetMessage());
            MangoPay\Libraries\Logs::Debug('Details', $e->GetErrorDetails());
            echo '</p></div>';

        } catch (MangoPay\Libraries\Exception $e) {

            echo '<div class="error"><p>' . __('MANGOPAY API returned:', 'mangopay') . ' ';
            MangoPay\Libraries\Logs::Debug('MangoPay\Exception Message', $e->GetMessage());
            echo '</p></div>';

        } catch (Exception $e) {
            $error_message = __('Error:', 'mangopay') .
                ' ' . $e->getMessage();
            error_log(
                current_time('Y-m-d H:i:s', 0) . ': ' . $error_message . "\n\n",
                3,
                $this->logFilePath
            );

            echo '<div class="error"><p>' . __('MANGOPAY API returned:', 'mangopay') . ' ';
            echo '&laquo;' . $error_message . '&raquo;</p></div>';
        }
        return false;
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init()
    {
        $testmode = ae_get_option('test_mode');

        /** Setup tmp directory **/
        $tmp_path = $this->set_tmp_dir();

        $this->logFilePath = $tmp_path . '/' . self::LOGFILENAME;

        /** Initialize log file if not present **/
        if (!file_exists($this->logFilePath))
            file_put_contents($this->logFilePath, '<?php header("HTTP/1.0 404 Not Found"); echo "File not found."; exit; /*');

        /** Add a .htaccess to mp_tmp dir for added security **/
        $htaccess_path = $tmp_path . '/' . '.htaccess';
        if (!file_exists($htaccess_path))
            file_put_contents($htaccess_path, "order deny,allow\ndeny from all\nallow from 127.0.0.1");
        $htaccess_path = dirname($tmp_path) . '/' . '.htaccess';
        if (!file_exists($htaccess_path))
            file_put_contents($htaccess_path, "order deny,allow\ndeny from all\nallow from 127.0.0.1");

        /** Instantiate MP API **/
        require_once(dirname(__FILE__) . '/sdk/MangoPay/Autoloader.php');
        require_once('mock-storage.php');
        $mangopay_api = ae_get_option('escrow_mangopay_api');

        $this->mangoPayApi = new MangoPay\MangoPayApi();

        /** MANGOPAY API configuration **/

        $this->redirect_uri = et_get_page_link('process-payment') . '/?paymentType=mangopay';
        $this->mangoPayApi->Config->ClientId = $mangopay_api['client_id'];
        $this->mangoPayApi->Config->ClientPassword = $mangopay_api['client_secret'];
        $this->mangoPayApi->Config->TemporaryFolder = $tmp_path . '/';
        $this->mangoPayApi->OAuthTokenManager->RegisterCustomStorageStrategy(new \MangoPay\WPPlugin\MockStorage());
        if ($testmode) {
            $this->mangoPayApi->Config->BaseUrl = self::SANDBOX_API_URL;

        } else {
            $this->mangoPayApi->Config->BaseUrl = self::PROD_API_URL;
        }
        $this->init_ajax();
        return true;

    }


    private function set_tmp_dir()
    {
        $uploads = wp_upload_dir();
        $uploads_path = $uploads['basedir'];
        $prod_or_sandbox = 'sandbox';
        if ($this->mp_production)
            $prod_or_sandbox = 'prod';
        $tmp_path = $uploads_path . '/' . self::TMP_DIR_NAME . '/' . $prod_or_sandbox;
        wp_mkdir_p($tmp_path);
        return $tmp_path;
    }

    public function init_ajax()
    {

        $this->add_action('wp_footer', 'ae_mangopay_escrow_template');
        $this->add_action('wp_footer', 'ae_mangopay_escrow_bank_template');
        $this->add_action('wp_footer', 'ae_mangopay_escrow_payout_template');
        $this->add_action('wp_footer', 'ae_mangopay_escrow_kyc_template');
        $this->add_ajax('fre-mangopay-escrow-customer', 'ae_create_mangopay_customer');
        $this->add_ajax('fre-mangopay-escrow-payout', 'ae_payout_mangopay_payout');
        $this->add_ajax('fre-mangopay-escrow-iban', 'ae_update_mangopay_customer_bank_iban');
        $this->add_ajax('fre-mangopay-escrow-uk', 'ae_update_mangopay_customer_bank_uk');
        $this->add_ajax('fre-mangopay-escrow-other', 'ae_update_mangopay_customer_bank_other');
        $this->add_action('ae_escrow_payment_gateway', 'ae_escrow_mangopay_payment_gateway');
        $this->add_action('fre_finish_escrow', 'ae_escrow_mangopay_finish', 10, 2);
        $this->add_filter('fre_process_escrow', 'ae_escrow_mangopay_process', 10, 3);
        $this->add_action('ae_escrow_execute', 'ae_mangopay_escrow_execute', 10, 2);
        $this->add_action('ae_escrow_refund', 'ae_mangopay_escrow_refund', 10, 2);
        $this->add_action('fre_transfer_money_ajax', 'ae_mangopay_transfer_money_ajax', 10, 2);
    }

    public function ae_mangopay_escrow_template()
    {
        fre_update_mangopay_info_modal();
    }

    public function ae_mangopay_escrow_bank_template()
    {
        fre_update_mangopay_bank_info_modal();
    }

    public function ae_mangopay_escrow_payout_template()
    {
        fre_update_mangopay_payout_modal();
    }

    public function ae_mangopay_escrow_kyc_template()
    {
        fre_update_mangopay_payout_modal();
    }

    /**
     * Create Mangopay Account for freelancer and GBP Wallet
     *
     * @param $escrow_data
     *
     * @return void $payment_return
     * @since 1.0
     * @package AE_ESCROW
     */
    public function ae_create_mangopay_customer()
    {
        try {
            global $user_ID;
            $timestamp = strtotime($_REQUEST['dob']);
            $country = strtoupper($_REQUEST['country']);
            $nationality = strtoupper($_REQUEST['nationality']);
            $UserNatural = new \MangoPay\UserNatural();
            $UserNatural->Tag = "custom meta";
            $UserNatural->FirstName = $_REQUEST['first_name'];
            $UserNatural->LastName = $_REQUEST['last_name'];
            $UserNatural->Birthday = $timestamp;
            $UserNatural->CountryOfResidence = $country;
            $UserNatural->Nationality = $nationality;
            $UserNatural->Email = $_REQUEST['mangopay_email'];

            $Result = $this->mangoPayApi->Users->Create($UserNatural);
            if (null != $Result->Id) {
                update_user_meta($user_ID, 'ae_mangopay_user_id', $Result->Id);
                try {


                    $Wallet = new \MangoPay\Wallet();
                    $Wallet->Tag = "custom wallet";
                    $Wallet->Owners = array("$Result->Id");
                    $Wallet->Description = "My big project";
                    $Wallet->Currency = "GBP";
                    $Result = $this->mangoPayApi->Wallets->Create($Wallet);
                    update_user_meta($user_ID, 'ae_mangopay_Wallet_id', $Result->Id);
                } catch (MangoPay\Libraries\ResponseException $e) {
                    $e->GetErrorDetails();

                } catch (MangoPay\Libraries\Exception $e) {
                    $e->GetMessage();

                }
            }
            error_log('this is' . json_encode($Result));
        } catch (MangoPay\Libraries\ResponseException $e) {

            $e->GetErrorDetails();

        } catch (MangoPay\Libraries\Exception $e) {
            $e->GetMessage();

        }
        wp_send_json($Result);

    }

    public function ae_payout_mangopay_payout()
    {
        $result = '';
        try {
            $PayOut = new \MangoPay\PayOut();
            $PayOut->AuthorId = $_REQUEST['AuthorId'];
            $PayOut->DebitedWalletID = $_REQUEST['DebitedWalletID'];
            $PayOut->DebitedFunds = new \MangoPay\Money();
            $PayOut->DebitedFunds->Currency = $_REQUEST['Currency'];
            $PayOut->DebitedFunds->Amount = $_REQUEST['Amount'] * 100;
            $PayOut->Fees = new \MangoPay\Money();
            $PayOut->Fees->Currency = $_REQUEST['Currency'];
            $PayOut->Fees->Amount = $_REQUEST['Fee'];
            $PayOut->PaymentType = "BANK_WIRE";
            $PayOut->MeanOfPaymentDetails = new \MangoPay\PayOutPaymentDetailsBankWire();
            $PayOut->MeanOfPaymentDetails->BankAccountId = $_REQUEST['BankAccountId'];
            $result = $this->mangoPayApi->PayOuts->Create($PayOut);
        } catch (MangoPay\Libraries\ResponseException $e) {

            $e->GetErrorDetails();

        } catch (MangoPay\Libraries\Exception $e) {
            $e->GetMessage();

        }
        error_log('this is' . json_encode($result));
        wp_send_json($result);

    }

    public function ae_update_mangopay_customer_bank_iban()
    {
        try {
            global $user_ID;
            /** If there is an existing bank account, fetch it first to get the redacted info we did not store **/
            $ExistingBankAccount = null;
            $mp_user_id = $this->ae_get_mangopay_user_id($user_ID);
            $existing_account_id = get_user_meta($user_ID, 'mangopay_bank_details_iban', true);
            if ($existing_account_id) {
                try {
                    $ExistingBankAccount = $this->mangoPayApi->Users->GetBankAccount($mp_user_id, $existing_account_id);
                } catch (Exception $e) {
                    $ExistingBankAccount = null;
                }
            }
            $BankAccount = new \MangoPay\BankAccount();
            $BankAccount->Type = 'IBAN';
            $BankAccount->UserId = $mp_user_id;
            $detail_class_name = 'MangoPay\BankAccountDetails' . $BankAccount->Type;
            $BankAccount->Details = new $detail_class_name();
            $BankAccount->Details->IBAN = $_REQUEST['iban_value'];
            $BankAccount->Details->BIC = $_REQUEST['bic_value'];
            $BankAccount->OwnerName = $_REQUEST['account_holder_name'];
            $BankAccount->OwnerAddress = new \MangoPay\Address();
            $BankAccount->OwnerAddress->AddressLine1 = $_REQUEST['account_holder_address'];
            $BankAccount->OwnerAddress->City = $_REQUEST['account_holder_city'];
            $BankAccount->OwnerAddress->Country = $_REQUEST['country'];
            $BankAccount->OwnerAddress->PostalCode = $_REQUEST['account_holder_postcode'];
            try {
                $Result = $this->mangoPayApi->Users->CreateBankAccount($mp_user_id, $BankAccount);
            } catch (Exception $e) {
                $backlink = '<a href="javascript:history.back();">' . __('back', 'mangopay') . '</a>';
                wp_die(__('Error: Invalid bank account data.', 'mangopay') . ' ' . $backlink);
            }


            if (null != $Result->Id) {
                update_user_meta($user_ID, 'mangopay_bank_details_iban', $Result->Id);
                update_user_meta($user_ID, 'mangopay_bank_details', $Result->Id);

            }
            error_log('this is' . json_encode($Result));
        } catch (MangoPay\Libraries\ResponseException $e) {

            $e->GetErrorDetails();

        } catch (MangoPay\Libraries\Exception $e) {
            $e->GetMessage();

        }
        wp_send_json($Result);

    }

    public function ae_update_mangopay_customer_bank_uk()
    {
        try {
            global $user_ID;
            /** If there is an existing bank account, fetch it first to get the redacted info we did not store **/
            $ExistingBankAccount = null;
            $mp_user_id = $this->ae_get_mangopay_user_id($user_ID);
            $existing_account_id = get_user_meta($user_ID, 'mangopay_bank_details_uk', true);
            if ($existing_account_id) {
                try {
                    $ExistingBankAccount = $this->mangoPayApi->Users->GetBankAccount($mp_user_id, $existing_account_id);
                } catch (Exception $e) {
                    $ExistingBankAccount = null;
                }
            }
            $BankAccount = new \MangoPay\BankAccount();
            $BankAccount->Type = 'GB';
            $BankAccount->UserId = $mp_user_id;
            $detail_class_name = 'MangoPay\BankAccountDetails' . $BankAccount->Type;
            $BankAccount->Details = new $detail_class_name();
            $BankAccount->Details->AccountNumber = $_REQUEST['gb_account_number'];
            $BankAccount->Details->SortCode = $_REQUEST['gb_account_sortcode'];
            $BankAccount->OwnerName = $_REQUEST['account_holder_name'];
            $BankAccount->OwnerAddress = new \MangoPay\Address();
            $BankAccount->OwnerAddress->AddressLine1 = $_REQUEST['account_holder_address'];
            $BankAccount->OwnerAddress->City = $_REQUEST['account_holder_city'];
            $BankAccount->OwnerAddress->Country = $_REQUEST['country'];
            $BankAccount->OwnerAddress->PostalCode = $_REQUEST['account_holder_postcode'];
            try {
                $Result = $this->mangoPayApi->Users->CreateBankAccount($mp_user_id, $BankAccount);
            } catch (Exception $e) {
                wp_die(__('Error: Invalid bank account data.', 'mangopay'));
            }


            if (null != $Result->Id) {
                update_user_meta($user_ID, 'mangopay_bank_details_uk', $Result->Id);
                update_user_meta($user_ID, 'mangopay_bank_details', $Result->Id);
                error_log('this is' . json_encode($Result));


            }
            error_log('this is' . json_encode($Result));
        } catch (MangoPay\Libraries\ResponseException $e) {

            $e->GetErrorDetails();

        } catch (MangoPay\Libraries\Exception $e) {
            $e->GetMessage();

        }
        wp_send_json($Result);

    }

    public function ae_update_mangopay_customer_bank_other()
    {
        try {
            global $user_ID;
            /** If there is an existing bank account, fetch it first to get the redacted info we did not store **/
            $ExistingBankAccount = null;
            $mp_user_id = $this->ae_get_mangopay_user_id($user_ID);
            $existing_account_id = get_user_meta($user_ID, 'mangopay_bank_details_other', true);

            if ($existing_account_id) {
                try {
                    $ExistingBankAccount = $this->mangoPayApi->Users->GetBankAccount($mp_user_id, $existing_account_id);
                } catch (Exception $e) {
                    $ExistingBankAccount = null;
                }
            }
            $BankAccount = new \MangoPay\BankAccount();
            $BankAccount->Type = 'OTHER';
            $BankAccount->UserId = $mp_user_id;
            $detail_class_name = 'MangoPay\BankAccountDetails' . $BankAccount->Type;
            $BankAccount->Details = new $detail_class_name();
            $BankAccount->Details->AccountNumber = $_REQUEST['other_account_number'];
            $BankAccount->Details->BIC = $_REQUEST['other_bic_value'];
            $BankAccount->Details->Country = $_REQUEST['account_holder_country'];
            $BankAccount->OwnerName = $_REQUEST['account_holder_name'];
            $BankAccount->OwnerAddress = new \MangoPay\Address();
            $BankAccount->OwnerAddress->AddressLine1 = $_REQUEST['account_holder_address'];
            $BankAccount->OwnerAddress->City = $_REQUEST['account_holder_city'];
            $BankAccount->OwnerAddress->Country = $_REQUEST['country'];
            $BankAccount->OwnerAddress->PostalCode = $_REQUEST['account_holder_postcode'];
            try {
                $Result = $this->mangoPayApi->Users->CreateBankAccount($mp_user_id, $BankAccount);
            } catch (Exception $e) {
                wp_die(__('Error: Invalid bank account data.', 'mangopay'));
            }


            if (null != $Result->Id) {
                update_user_meta($user_ID, 'mangopay_bank_details_other', $Result->Id);
                update_user_meta($user_ID, 'mangopay_bank_details', $Result->Id);


            }
            error_log('this is' . json_encode($Result));
        } catch (MangoPay\Libraries\ResponseException $e) {

            $e->GetErrorDetails();

        } catch (MangoPay\Libraries\Exception $e) {
            $e->GetMessage();

        }
        wp_send_json($Result);

    }

    /**
     * Update Mangopay Account for freelancer
     *
     * @param null $user_id
     * @param null $mangopay_user_id
     * @return void $payment_return
     * @since 1.0
     * @package AE_ESCROW
     */
    public function ae_update_mangopay_user_id($user_id = null, $mangopay_user_id = null)
    {
        if (null != $user_id && null != $mangopay_user_id) {
            update_user_meta($user_id, 'ae_mangopay_user_id', $mangopay_user_id);
        }
    }

    /**
     * To check whether Plugin is enabled
     *
     * @param $escrow_data
     *
     * @return void $payment_return
     * @since 1.0
     * @package AE_ESCROW
     */
    public function is_use_mangopay_escrow()
    {
        $mangopay_api = ae_get_option('escrow_mangopay_api');

        $result = apply_filters('use_mangopay_escrow', $mangopay_api['use_mangopay_escrow']);

        return $result;
    }

    /**
     * To check PayIn
     *
     * @param $transaction_id
     * @return void $payment_return
     * @since 1.0
     * @package AE_ESCROW
     */
    public function get_payIn($transaction_id)
    {
        return $this->mangoPayApi->PayIns->Get($transaction_id);
    }

    /**
     * Process payment accept bid to payment gateway
     *
     * @param $escrow_data
     *
     * @return void $payment_return
     * @since 1.0
     * @package AE_ESCROW
     */
    public function ae_escrow_mangopay_payment_gateway($escrow_data)
    {
        global $user_ID;
        try {
            $mangopay_api = ae_get_option('escrow_mangopay_api');

            $escrow_data['recipient'] = $this->ae_get_mangopay_user_id($escrow_data['bid_author']);

            if (empty($escrow_data['recipient'])) {
                $response = array(
                    'success' => false,
                    'msg' => __('Has something wrong with the Mangopay account of freelancer.', ET_DOMAIN)
                );
                wp_send_json($response);
            }
            $bid_id = $escrow_data['bid_id'];
            $amount = $escrow_data['total'] * 100;
            $fees = $escrow_data['commission_fee'] * 100;
            $order_post = array(
                'post_type' => 'fre_order',
                'post_status' => 'pending',
                'post_parent' => $bid_id,
                'post_author' => $user_ID,
                'post_title' => 'Pay for accept bid',
                'post_content' => 'Pay for accept bid ' . $bid_id
            );

            $PayIn = new \MangoPay\PayIn();
            $PayIn->CreditedWalletId = $mangopay_api['mangopay_escrow_wallet_id'];
            $PayIn->Tag = 'ESCROW #' . $bid_id . ' Recipient: ' . $escrow_data['recipient'];
            $PayIn->AuthorId = $mangopay_api['mangopay_escrow_user_id'];
            $PayIn->PaymentType = 'CARD';
            $PayIn->PaymentDetails = new \MangoPay\PayInPaymentDetailsCard();
            $PayIn->PaymentDetails->CardType = 'CB_VISA_MASTERCARD';
            $PayIn->DebitedFunds = new \MangoPay\Money();
            $PayIn->DebitedFunds->Currency = $escrow_data['currency'];
            $PayIn->DebitedFunds->Amount = $amount;
            $PayIn->Fees = new \MangoPay\Money();
            $PayIn->Fees->Currency = $escrow_data['currency'];
            $PayIn->Fees->Amount = $fees;
            $PayIn->ExecutionDetails = new \MangoPay\PayInExecutionDetailsWeb();
            $PayIn->ExecutionDetails->ReturnURL = $this->redirect_uri;
            $PayIn->ExecutionDetails->Culture = 'EN';
            $result = $this->ae_mangopay_charge($PayIn);
            $bid = get_post($bid_id);

            if ($result && isset($result->Id)) {
                $freelancer_bank_id = get_user_meta($escrow_data['bid_author'], 'mangopay_bank_details', true);
                do_action('fre_accept_bid', $bid_id);
                $order_id = wp_insert_post($order_post);
                update_post_meta($order_id, 'fre_paykey', $result->Id);
                update_post_meta($order_id, 'gateway', 'mangopay');
                update_post_meta($bid_id, 'freelancer_escrow_user_id', $escrow_data['recipient']);
                update_post_meta($bid_id, 'freelancer_escrow_bank_id', $freelancer_bank_id);
                update_post_meta($bid_id, 'fre_bid_order', $order_id);
                update_post_meta($bid_id, 'commission_fee', $escrow_data['commission_fee']);
                update_post_meta($bid_id, 'payer_of_commission', $escrow_data['payer_of_commission']);
                update_post_meta($bid_id, 'fre_paykey', $result->Id);

                et_write_session('payKey', $result->Id);
                et_write_session('order_id', $order_id);
                et_write_session('bid_id', $bid_id);
                et_write_session('ad_id', $bid->post_parent);
                $response = array(
                    'success' => true,
                    'msg' => 'Success!',
                    'redirect_url' => $result->ExecutionDetails->RedirectURL,
                    'order_id' => $result->Id
                );
                wp_send_json($response);
            } else {
                wp_send_json(array(
                    'success' => false,
                    'msg' => __('charge failed', ET_DOMAIN)
                ));
            }
        } catch (\MangoPay\Libraries\ResponseException $ex) {
            $value = $ex->getJsonBody();
            $response = array(
                'success' => false,
                'msg' => $value['error']['message']
            );
            wp_send_json($response);
        }
        exit;
    }

    /**
     * To get Mangopay user details
     *
     * @param null $user_id
     * @return void $payment_return
     * @since 1.0
     * @package AE_ESCROW
     */
    public function ae_get_mangopay_user_id($user_id = null)
    {
        $mangopay_user_id = '';
        if (null != $user_id) {
            $mangopay_user_id = get_user_meta($user_id, 'ae_mangopay_user_id', true);
        }

        return apply_filters('ae_mangopay_user_id', $mangopay_user_id);
    }

    public function ae_get_mangopay_bank_id($user_id = null)
    {
        $mangopay_bank_id = '';
        if (null != $user_id) {
            $mangopay_bank_id = get_user_meta($user_id, 'mangopay_bank_details', true);
        }

        return apply_filters('ae_mangopay_bank_id', $mangopay_bank_id);
    }

    public function ae_get_mangopay_kyc_info($user_id)
    {

        $mangopay_user_id = get_user_meta($user_id, 'ae_mangopay_user_id', true);
        $null_result = '';
        if ($mangopay_user_id != null) {
            $kyc_docs = $this->mangoPayApi->Users->GetKycDocuments($mangopay_user_id);
            $size = sizeof($kyc_docs);
            if ($size != 0) {
                $result = array(
                    'Status' => $kyc_docs_status = $kyc_docs[$size - 1]->Status,
                    'Type' => $kyc_docs[$size - 1]->Type,
                    'RefusedReasonType' => $kyc_docs[$size - 1]->RefusedReasonType,

                );
                error_log(json_encode($result));

                return $result;
            } else
                return $kyc_docs;
        } else {
            error_log($null_result);
            return $null_result;
        }
    }

    /**
     * Create Mangopay charge
     *
     * @param $charge_obj
     * @return void $payment_return
     * @since 1.0
     * @package AE_ESCROW
     */
    public function ae_mangopay_charge($charge_obj)
    {

        $charge = $this->mangoPayApi->PayIns->Create($charge_obj);

        return $charge;
    }

    /**
     * Process payment accept bid
     *
     * @param $payment_return
     * @param $payment_type
     * @param $data
     * @return void $payment_return
     * @since 1.0
     * @package AE_ESCROW
     */
    public function ae_escrow_mangopay_process($payment_return, $payment_type, $data)
    {
        if ($payment_type == 'mangopay') {
            $response = $this->mangoPayApi->PayIns->Get($data['payKey']);
            $responseStatus = $response->Status;
            $payment_return['payment_status'] = false;
            if ($responseStatus == 'SUCCEEDED') {
                $payment_return['ACK'] = true;
                wp_update_post(array(
                    'ID' => $data['order_id'],
                    'post_status' => 'publish'
                ));
                // assign project
                $bid_action = Fre_BidAction::get_instance();
                $bid_action->assign_project($data['bid_id']);
                $payment_return['msg'] = __('Payment Success!', ET_DOMAIN);
            } else {
                $payment_return['msg'] = __('Payment failed!', ET_DOMAIN);
            }
        }

        return $payment_return;
    }

    /**
     * Get mangopay user wallet information
     *
     * @param $mp_user_id
     * @return void $payment_return
     * @since 1.0
     * @package AE_ESCROW
     */
    public function set_mp_wallet($mp_user_id)
    {

        $wallets = $this->mangoPayApi->Users->GetWallets($mp_user_id);

        return $wallets;
    }

    public function mp_wallet_details($mp_wallet_id)
    {

        try {

            $WalletId = $mp_wallet_id;

            $Wallet = $this->mangoPayApi->Wallets->Get($WalletId);

        } catch (MangoPay\Libraries\ResponseException $e) {
            $e->GetErrorDetails();

        } catch (MangoPay\Libraries\Exception $e) {
            $e->GetMessage();

        }

        return $Wallet;
    }

    public function mp_bank_details($details)
    {

        try {

            $UserId = $details['UserId'];
            $BankAccountId = $details['BankAccountId'];
            $BankAccount = $this->mangoPayApi->Users->GetBankAccount($UserId, $BankAccountId);


        } catch (MangoPay\Libraries\ResponseException $e) {
            $e->GetErrorDetails();

        } catch (MangoPay\Libraries\Exception $e) {
            $e->GetMessage();

        }

        return $BankAccount;
    }

    /**
     * Used for dispute reconvene for freelancer
     *
     * @param $project_id
     * @param $bid_id_accepted
     */
    public function ae_mangopay_escrow_execute($project_id, $bid_id_accepted)
    {
        $charge_id = get_post_meta($bid_id_accepted, 'fre_paykey', true);
        $destinationFreelancer = get_post_meta($bid_id_accepted, 'freelancer_escrow_user_id', true);
        $charge = $this->mangoPayApi->PayIns->Get($charge_id);
        $destinationWallet = $this->set_mp_wallet($destinationFreelancer);
        $destination_wallet_id = $destinationWallet[0];
        if ($charge) {
            $bid = get_post($bid_id_accepted);

            $bid_budget = $charge->amount;
            if ($bid && !empty($bid)) {

                //$destination         = $this->ae_get_mangopay_user_id( $bid->post_author );
                $bid_budget = get_post_meta($bid_id_accepted, 'bid_budget', true);
                $payer_of_commission = get_post_meta($bid_id_accepted, 'payer_of_commission', true);
                if ($payer_of_commission != 'project_owner') {
                    $commission_fee = get_post_meta($bid_id_accepted, 'commission_fee', true);
                } else {
                    $commission_fee = 0;
                }
            }
            $commission_fee = $commission_fee * 100;
            $amount = $bid_budget * 100;
            $amount = $amount - $commission_fee;

            $transfer_obj = array(
                "Amount" => $amount, // amount in cents
                "Currency" => $charge->Currency,
                "AuthorId" => $charge->CreditedUserId,
                "CreditedUserId" => $destinationFreelancer,
                "CreditedWalletId" => $destination_wallet_id->Id,
                "DebitedWalletId" => $charge->CreditedWalletId,
                "application_fee" => $commission_fee,
                "statement_descriptor" => __("Freelance escrow", ET_DOMAIN)
            );
            $transfer = $this->ae_mangopay_transfer($transfer_obj);
            if (!is_wp_error($transfer)) {


                $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
                if ($order) {
                    wp_update_post(array(
                        'ID' => $order,
                        'post_status' => 'completed'
                    ));
                }

                // success update project status
                wp_update_post(array(
                    'ID' => $project_id,
                    'post_status' => 'disputed'
                ));

                // success update project status
                wp_update_post(array(
                    'ID' => $bid_id_accepted,
                    'post_status' => 'disputed'
                ));

                // update meta when admin arbitrate
                if (isset($_REQUEST['comment']) && isset($_REQUEST['winner'])) {
                    $comment = $_REQUEST['comment'];
                    $winner = $_REQUEST['winner'];
                    update_post_meta($project_id, 'comment_of_admin', $comment);
                    update_post_meta($project_id, 'winner_of_arbitrate', $winner);
                }
                // send mail
                $mail = Fre_Mailing::get_instance();
                $mail->execute_payment($project_id, $bid_id_accepted);
                do_action('fre_resolve_project_notification', $project_id);

                wp_send_json(array(
                    'success' => true,
                    'msg' => __("Send payment successful.", ET_DOMAIN)
                ));
            } else {
                wp_send_json(array('success' => false, 'msg' => $transfer->get_error_message()));
            }
        } else {
            wp_send_json(array(
                'success' => false,
                'msg' => __("Invalid charge.", ET_DOMAIN)
            ));
        }
    }

    /**
     * used to transfer money during disputes and finished process
     * @param $charge_id
     * @param $transfer_obj
     *
     * @return WP_Error
     */
    public function ae_mangopay_transfer($transfer_obj)
    {


        $application_fee = $transfer_obj['application_fee'];
        $amount = $transfer_obj['Amount'];
        $amount = $amount - $application_fee;


        $transfer = new WP_Error('broke_default', __('Has something wrong', ET_DOMAIN));
        try {
            $Transfer = new \MangoPay\Transfer();
            $Transfer->Tag = $transfer_obj['statement_descriptor'];
            $Transfer->AuthorId = $transfer_obj['AuthorId'];
            $Transfer->CreditedUserId = $transfer_obj['CreditedUserId'];
            $Transfer->DebitedFunds = new \MangoPay\Money();
            $Transfer->DebitedFunds->Currency = 'GBP';
            $Transfer->DebitedFunds->Amount = $amount;
            $Transfer->Fees = new \MangoPay\Money();
            $Transfer->Fees->Currency = 'GBP';
            $Transfer->Fees->Amount = $application_fee;
            $Transfer->DebitedWalletId = $transfer_obj['DebitedWalletId'];
            $Transfer->CreditedWalletId = $transfer_obj['CreditedWalletId'];

            $transfer = $this->mangoPayApi->Transfers->Create($Transfer);

        } catch (\MangoPay\Libraries\ResponseException $e) {
            echo "Hello &nbsp;" . json_encode($transfer_obj['CreditedWalletId']);
            return new WP_Error('broke_mangopay', $e->GetErrorDetails());

        } catch (\MangoPay\Libraries\Exception $e) {
            return new WP_Error('broke_php', $e->GetMessage());
        }

        return $transfer;
    }

    /**
     * Create employer refund
     *
     * @param $project_id
     * @param $bid_id_accepted
     * @return void $payment_return
     * @since 1.0
     * @package AE_ESCROW
     */
    public function ae_mangopay_escrow_refund($project_id, $bid_id_accepted)
    {
        $charge_id = get_post_meta($bid_id_accepted, 'fre_paykey', true);
        $transfer_obj = array();
        if ($charge_id) {
            $charge = $this->mangoPayApi->PayIns->Get($charge_id);
            if ($charge) {
                $bid = get_post($bid_id_accepted);
                if ($bid && !empty($bid)) {
                    $bid_budget = get_post_meta($bid_id_accepted, 'bid_budget', true);
                    $payer_of_commission = get_post_meta($bid_id_accepted, 'payer_of_commission', true);
                    if ($payer_of_commission != 'project_owner') {
                        // freelancer
                        $bid_budget = $charge->Amount;
                    } else {
                        // employer
                        $bid_budget = get_post_meta($bid_id_accepted, 'bid_budget', true);

                        $transfer_obj = array(
                            "amount" => $bid_budget * 100, // amount in cents
                        );
                    }
                }
            }
        }
        $re = $this->ae_mangopay_refund($charge_id, $transfer_obj);
        if ($re) {
            $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
            if ($order) {
                wp_update_post(array(
                    'ID' => $order,
                    'post_status' => 'refund'
                ));
            }
            wp_update_post(array(
                'ID' => $project_id,
                'post_status' => 'disputed'
            ));
            wp_update_post(array(
                'ID' => $bid_id_accepted,
                'post_status' => 'disputed'
            ));

            // update meta when admin arbitrate
            if (isset($_REQUEST['comment']) && isset($_REQUEST['winner'])) {
                $comment = $_REQUEST['comment'];
                $winner = $_REQUEST['winner'];
                update_post_meta($project_id, 'comment_of_admin', $comment);
                update_post_meta($project_id, 'winner_of_arbitrate', $winner);
            }
            $mail = Fre_Mailing::get_instance();
            $mail->refund($project_id, $bid_id_accepted);
            do_action('fre_resolve_project_notification', $project_id);
            // send json back
            wp_send_json(array(
                'success' => true,
                'msg' => __("Send payment successful.", ET_DOMAIN),
                'data' => __('Success', ET_DOMAIN)
            ));
        } else {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Refund failed!', ET_DOMAIN)
            ));
        }
    }

    /**
     * @param $charge_id
     * @param $transfer_obj
     *
     * @return WP_Error
     */
    public function ae_mangopay_refund($charge_id, $transfer_obj)
    {
        try {
            $mangopay_api = ae_get_option('escrow_mangopay_api');
            $PayInId = $charge_id;

            $Refund = new \MangoPay\Refund();
            $Refund->Tag = "Refund";
            $Refund->AuthorId = $mangopay_api['mangopay_escrow_user_id'];
            $Refund->DebitedFunds = new \MangoPay\Money();
            $Refund->DebitedFunds->Currency = "GBP";
            $Refund->DebitedFunds->Amount = $transfer_obj['amount'];
            $Refund->Fees = new \MangoPay\Money();
            $Refund->Fees->Currency = "GBP";
            $Refund->Fees->Amount = 0;

            $Result = $this->mangoPayApi->PayIns->CreateRefund($PayInId, $Refund);


        } catch (\MangoPay\Libraries\ResponseException $e) {
            return new WP_Error($e->GetErrorDetails());

        } catch (\MangoPay\Libraries\Exception $e) {
            return new  WP_Error($e->GetMessage());

        }
        return $Result;

    }

    /**
     * @param $project_id
     * @param $bid_id_accepted
     */
    public function ae_escrow_mangopay_finish($project_id, $bid_id_accepted)
    {

        // execute payment and send money to freelancer
        $charge_id = get_post_meta($bid_id_accepted, 'fre_paykey', true);
        if ($charge_id) {
            $charge = $this->mangoPayApi->PayIns->Get($charge_id);
            if ($charge) {
                $bid = get_post($bid_id_accepted);
                $destination = '';
                $bid_budget = $charge->amount;
                if ($bid && !empty($bid)) {
                    $destination = $this->ae_get_mangopay_user_id($bid->post_author);
                    $destinationWallet = $this->set_mp_wallet($destination);
                    $destination_wallet_id = $destinationWallet[0];
                    $bid_budget = get_post_meta($bid_id_accepted, 'bid_budget', true);
                    $payer_of_commission = get_post_meta($bid_id_accepted, 'payer_of_commission', true);
                    if ($payer_of_commission != 'project_owner') {
                        $commission_fee = get_post_meta($bid_id_accepted, 'commission_fee', true);
                    } else {
                        $commission_fee = 0;
                    }
                }

                $commission_fee = $commission_fee * 100;
                $amount = $bid_budget * 100;
                $amount = $amount - $commission_fee;
                $transfer_obj = array(
                    "AuthorId" => $charge->CreditedUserId,
                    "Amount" => $amount, // amount in cents
                    "CreditedWalletId" => $destination_wallet_id->Id,
                    "DebitedWalletId" => $charge->CreditedWalletId,
                    "application_fee" => $commission_fee,
                    "statement_descriptor" => __("Freelance escrow", ET_DOMAIN)
                );
                $transfer = $this->ae_mangopay_transfer($transfer_obj);

                if (!is_wp_error($transfer)) {
                    $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
                    if ($order) {

                        wp_update_post(array(
                            'ID' => $order,
                            'post_status' => 'finish'
                        ));
                        $mail = Fre_Mailing::get_instance();
                        $mail->alert_transfer_money($project_id, $bid_id_accepted);
                        $mail->notify_execute($project_id, $bid_id_accepted);
                    }
                } else {
                    //delete the just review of fre account for employer account.
                    $comments = get_comments(array(
                        'status' => 'approve',
                        'type' => 'fre_review',
                        'post_id' => $project_id
                    ));
                    if (!empty($comments)) {
                        foreach ($comments as $comment) :
                            wp_delete_comment($comment->comment_ID);
                        endforeach;
                    }
                    wp_send_json(array('success' => false, 'msg' => $transfer->get_error_message()));

                }
            }
        } else {
            $mail = Fre_Mailing::get_instance();
            $mail->alert_transfer_money($project_id, $bid_id_accepted);
        }

    }

    public function ae_mangopay_transfer_money_ajax($project_id, $bid_id_accepted)
    {

        // execute payment and send money to freelancer
        $charge_id = get_post_meta($bid_id_accepted, 'fre_paykey', true);
        if ($charge_id) {
            $charge = $this->mangoPayApi->PayIns->Get($charge_id);
            $bid = get_post($bid_id_accepted);
            $destination = '';
            $bid_budget = $charge->Amount;
            if ($bid && !empty($bid)) {
                $destination = $this->ae_get_mangopay_user_id($bid->post_author);
                $bid_budget = get_post_meta($bid_id_accepted, 'bid_budget', true);
                $payer_of_commission = get_post_meta($bid_id_accepted, 'payer_of_commission', true);
                if ($payer_of_commission != 'project_owner') {
                    $commission_fee = get_post_meta($bid_id_accepted, 'commission_fee', true);
                } else {
                    $commission_fee = 0;
                }
            }
            $this->add_action('my_footer_hook', 'my_footer_echo', 10, 1);
            function my_footer_echo($url)
            {
                echo "The home url is $url";
            }

            $commission_fee = $commission_fee * 100;
            $amount = $bid_budget * 100;
            $amount = $amount - $commission_fee;

            $transfer_obj = array(
                "AuthorId" => $charge->CreditedUserId,
                "Amount" => $amount, // amount in cents
                "Currency" => $charge->Currency,
                "CreditedWalletId" => $destination,
                "DebitedWalletId" => $charge->CreditedWalletId,
                "application_fee" => $commission_fee,
                "statement_descriptor" => __("Freelance escrow", ET_DOMAIN)
            );
            $transfer = $this->ae_mangopay_transfer($transfer_obj);

            if (!is_wp_error($transfer)) {
                $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
                if ($order) {
                    wp_update_post(array(
                        'ID' => $order,
                        'post_status' => 'finish'
                    ));
                    $mail = Fre_Mailing::get_instance();
                    $mail->execute($project_id, $bid_id_accepted);
                    // send json back
                    wp_send_json(array(
                        'success' => true,
                        'msg' => __("The payment has been successfully transferred .", ET_DOMAIN)
                    ));
                }
            } else {
                $errors = $transfer->errors;
                if (isset($errors['broke_stripe'])) {
                    wp_send_json(array(
                        'success' => false,
                        'msg' => $errors['broke_stripe'][0]
                    ));
                } else if (isset($errors['broke_php'])) {
                    wp_send_json(array(
                        'success' => false,
                        'msg' => $errors['broke_php'][0]
                    ));
                }

            }
        } else {
            wp_send_json(array(
                'success' => false,
                'msg' => __("Invalid key.", ET_DOMAIN)
            ));
        }

    }


}