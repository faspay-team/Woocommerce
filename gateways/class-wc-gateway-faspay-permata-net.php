<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_Faspay_Permata_Net extends Faspay_Payment_Gateway {
    var $sub_id = 'faspay_permata_net';
        public function __construct() {
	    parent::__construct();
        $this->method_title = 'Faspay - Permata Net';
         $this->payment_method = '402';
	    $this->pay_type = '1';
	    $this->reserve2 = '30_days';
	    $this->reserve3 = 'permatanet';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/va_permata.png', dirname(__FILE__) );
	}
 }

?>
