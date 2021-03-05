<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_faspay_BRI_MoCash extends faspay_Payment_Gateway {
    var $sub_id = 'faspay_bri_mocash';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - BRI';
	    $this->payment_method 	= '400';
	    $this->pay_type 		= '1'; 
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/mocash.png', dirname(__FILE__) );
	}
 }

?>
