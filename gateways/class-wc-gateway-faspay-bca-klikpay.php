<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_Faspay_BCA_Klikpay extends Faspay_Payment_Gateway {
    var $sub_id = 'faspay_bca_klikpay';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - BCA Klikpay';
	    $this->payment_method 	= '405';
	    $this->pay_type 		= '1';
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/klikpay.png', dirname(__FILE__) );
	}
 }

?>
