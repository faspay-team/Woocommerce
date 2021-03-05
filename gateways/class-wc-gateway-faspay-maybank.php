<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_Faspay_Maybank extends Faspay_Payment_Gateway {
    var $sub_id = 'faspay_maybank';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - Maybank';
	    $this->payment_method 	= '408';
	    $this->pay_type 		= '1';
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/maybank.png', dirname(__FILE__) );
	}
 }

?>
