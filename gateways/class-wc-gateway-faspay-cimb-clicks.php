<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_Faspay_CIMB_Clicks extends Faspay_Payment_Gateway {
    var $sub_id = 'faspay_cimb_clicks';
        public function __construct() {
	    parent::__construct();
	    $this->method_title 	= 'Faspay - CIMB Clicks & Rekening Ponsel';
	    $this->payment_method 	= '700';
	    $this->pay_type 		= '1';
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/cimbclicks.png', dirname(__FILE__) );
	}
 }

?>
