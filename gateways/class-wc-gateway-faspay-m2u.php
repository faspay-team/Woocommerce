<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_faspay_M2u extends faspay_Payment_Gateway {
    var $sub_id = 'faspay_m2u';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - M2U';
	    $this->payment_method 	= '814';
	    $this->pay_type 		= '1'; 
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/maybank_m2u.png', dirname(__FILE__) );
	}
 }

?>
