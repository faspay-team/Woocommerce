<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_Gateway_Faspay_CIMB extends Faspay_Payment_Gateway {
	var $sub_id = 'faspay_cimb';
	public function __construct() {
		parent::__construct();
		$this->method_title 	= 'Faspay - CIMB';
		$this->payment_method 	= '825';
		$this->pay_type 		= '1';
		$this->reserve2 = '30_days';
	    //payment gateway logo
		$this->icon = plugins_url('/assets/cimbniaga.png', dirname(__FILE__) );
	}
}

?>
