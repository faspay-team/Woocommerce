<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_faspay_Akulaku extends faspay_Payment_Gateway {
    var $sub_id = 'faspay_akulaku';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - Akulaku';
	    $this->payment_method 	= '807';
	    $this->pay_type 		= '1'; 
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/akulaku.png', dirname(__FILE__) );
	}
 }

?>
