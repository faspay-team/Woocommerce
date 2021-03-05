<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_Faspay_Danamon_Online_Banking extends Faspay_Payment_Gateway {
    var $sub_id = 'faspay_danamon';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - Danamon';
	    $this->payment_method 	= '708';
	    $this->pay_type 		= '1';
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/danamon_va.png', dirname(__FILE__) );
	}
 }

?>
