<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_Gateway_faspay_Indodana extends faspay_Payment_Gateway {
	var $sub_id = 'indodana';
	public function __construct() {
		parent::__construct();
		$this->method_title 	= 'Faspay - Indodana';
		$this->payment_method 	= '820';
		$this->pay_type 		= '1'; 
		$this->reserve2 = '30_days';
	    //payment gateway logo
		$this->icon = plugins_url('/assets/indodana.png', dirname(__FILE__) );
	}
}

?>
