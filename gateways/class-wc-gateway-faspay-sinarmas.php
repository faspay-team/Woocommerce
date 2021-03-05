<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_Gateway_Faspay_Sinarmas extends Faspay_Payment_Gateway {
	var $sub_id = 'faspay_sinarmas';
	public function __construct() {
		parent::__construct();
		$this->method_title 	= 'Faspay - Sinarmas VA';
		$this->payment_method 	= '818';
		$this->pay_type 		= '1';
		$this->reserve2 = '30_days';
	    //payment gateway logo
		$this->icon = plugins_url('/assets/sinarmas.png', dirname(__FILE__) );
	}
}

?>
