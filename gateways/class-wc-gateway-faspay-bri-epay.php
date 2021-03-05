<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_Faspay_BRI_Epay extends Faspay_Payment_Gateway {
    var $sub_id = 'faspay_bri_epay';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - BRI ePay';
	    $this->payment_method 	= '401';
	    $this->pay_type 		= '1';
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/epay.png', dirname(__FILE__) );
	}
 }

?>
