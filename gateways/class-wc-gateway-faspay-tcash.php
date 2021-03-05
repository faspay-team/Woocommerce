<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_Faspay_Tcash extends Faspay_Payment_Gateway {
    var $sub_id = 'faspay_tcash';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - LinkAja';
	    $this->payment_method 	= '302';
	    $this->pay_type 		= '1';
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/tcash.png', dirname(__FILE__) );
	}
 }

?>
