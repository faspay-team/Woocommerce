<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

 class WC_Gateway_Faspay_Danamon_OBK extends Faspay_Payment_Gateway {
    var $sub_id = 'faspay_danamon_obk';
        public function __construct() {
	    parent::__construct();
        $this->method_title 	= 'Faspay - Danamon Online Banking';
	    $this->payment_method 	= '701';
	    $this->pay_type 		= '1';
	    $this->reserve2 = '30_days';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/danamononline.png', dirname(__FILE__) );
	}
 }

?>
