<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_faspay_Indomaret extends faspay_Payment_Gateway {
    var $sub_id = 'faspay_indomaret';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - Indomaret';
	    $this->payment_method 	= '706';
	    $this->pay_type 		= '1'; 
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	   $this->icon = plugins_url('/assets/indomaret.png', dirname(__FILE__) );
	}
 }

?>
