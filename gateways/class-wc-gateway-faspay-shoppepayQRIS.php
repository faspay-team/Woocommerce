<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_faspay_ShoppepayQRIS extends faspay_Payment_Gateway {
    var $sub_id = 'faspay_shoppepayqris';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - shoppepayQRIS';
	    $this->payment_method 	= '711';
	    $this->pay_type 		= '1'; 
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/shoppepayQRIS.png', dirname(__FILE__) );
	}
 }

?>
