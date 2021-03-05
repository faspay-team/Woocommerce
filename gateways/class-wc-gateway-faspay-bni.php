<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_faspay_BNI extends faspay_Payment_Gateway {
    var $sub_id = 'faspay_bni';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - BNI';
	    $this->payment_method 	= '801';
	    $this->pay_type 		= '1'; 
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/bni.png', dirname(__FILE__) );
	}
 }

?>
