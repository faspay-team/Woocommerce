<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_Faspay_BCA extends Faspay_Payment_Gateway {
    var $sub_id = 'faspay_bca';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - BCA';
	    $this->payment_method 	= '702';
	    $this->pay_type 		= '1';
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/bca.png', dirname(__FILE__) );
	}
 }

?>
