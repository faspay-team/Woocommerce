<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_faspay_ShoppepayApp extends Faspay_Payment_Gateway {
    var $sub_id = 'faspay_shoppepayapp';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - shoppepayApp';
	    $this->payment_method 	= '713';
	    $this->pay_type 		= '1'; 
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/shoppepayApp.png', dirname(__FILE__) );
	}
 }

?>
