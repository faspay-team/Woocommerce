<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_Faspay_Kredivo extends Faspay_Payment_Gateway {
    var $sub_id = 'faspay_kredivo';
        public function __construct() {
	    parent::__construct();
        $this->method_title = 'Faspay - Kredivo';
        $this->name = 'kredivo';
	    $this->payment_method = '709';
	    $this->pay_type = '1';
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/kredivo.jpg', dirname(__FILE__) );
	}
 }

?>
