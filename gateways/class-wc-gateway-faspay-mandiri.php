<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_faspay_Mandiri extends faspay_Payment_Gateway {
    var $sub_id = 'faspay_mandiri';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - Mandiri';
	    $this->payment_method 	= '802';
	    $this->pay_type 		= '1'; 
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/mandiricp.png', dirname(__FILE__) );
	}
 }

?>
