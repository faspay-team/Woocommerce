<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_faspay_Dana extends faspay_Payment_Gateway {
    var $sub_id = 'faspay_dana';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - Dana';
	    $this->payment_method 	= '819';
	    $this->pay_type 		= '1'; 
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/dana.png', dirname(__FILE__) );
	}
 }

?>
