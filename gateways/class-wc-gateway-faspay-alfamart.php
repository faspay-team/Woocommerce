<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_faspay_Alfamart extends faspay_Payment_Gateway {
    var $sub_id = 'faspay_alfamart';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - Alfamart';
	    $this->payment_method 	= '707';
	    $this->pay_type 		= '1'; 
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/alfamart.png', dirname(__FILE__) );
	}
 }

?>
