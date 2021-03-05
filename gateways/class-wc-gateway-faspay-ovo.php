<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_faspay_Ovo extends faspay_Payment_Gateway {
    var $sub_id = 'faspay_ovo';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - OVO';
	    $this->payment_method 	= '812';
	    $this->pay_type 		= '1'; 
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/ovo.png', dirname(__FILE__) );
	}
 }

?>
