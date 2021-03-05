<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_Faspay_CreditCard_7 extends Faspay_Payment_Gateway {
    var $sub_id = 'faspay_creditcard_mid_7';
        public function __construct() {
	    parent::__construct();
        $this->method_title = 'Faspay - CreditCard 7';
	    $this->payment_method = '500';
	    $this->cc_type = 'mid_7';
	    $this->pay_type = '1';
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    //$this->icon = plugins_url('/assets/credit.png', dirname(__FILE__) );
	}
 }

?>
