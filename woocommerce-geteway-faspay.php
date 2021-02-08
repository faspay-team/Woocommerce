<?php
/*
Plugin Name: Faspay Payment Gateway
Description: Faspay Payment Gateway Version: 3.0.0
Version: 3.0.0
Author: Faspay Development Team Author
Plugin URI: https://www.faspay.co.id
*/
include_once( dirname( __FILE__ ) .'/faspay-install.php' );
register_activation_hook( __FILE__ , 'faspay_activation_process' );
register_deactivation_hook( __FILE__ , 'faspay_uninstallation_process' );

if (version_compare(PHP_VERSION, '5.3.0', '<')) {
	throw new Exception('PHP version >= 5.3.0 required');
}

if (!defined('ABSPATH')) {
	exit;
}

add_action('plugins_loaded', 'woocommerce_faspay_init', 0);

function woocommerce_faspay_init() {
	if (!class_exists('WC_Payment_Gateway')) {
		return;
	}

	//include global configuration file

	include_once dirname(__FILE__) . '/includes/admin/class-wc-faspay-settings.php';
	
	if (!class_exists('Faspay_Payment_Gateway')) {

		load_plugin_textdomain('wc-gateway-name', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');


		class Faspay_Payment_Gateway extends WC_Payment_Gateway {

			/** @var bool whether or not logging is enabled */
			public static $log_enabled = false;
			public static $option_prefix = 'faspay';

			/** @var WC_Logger Logger instance */
			public static $log = false;

			public function __construct() {

				//plugin id
				$this->id = $this->sub_id;

				$this->reserve3 = '';

				//payment method will be set in each child class.
				$this->payment_method = '';

				//true only in case of direct payment method, false in our case
				$this->has_fields = false;

				//set faspay global configuration
				//redirect URL
				$this->redirect_url 	= "https://dev.faspay.co.id/cvr/300011/10";

				//Load settings
				$this->init_form_fields();
				$this->init_settings();

				// Define user set variables
				//$this->title 		= $this->settings['title'];
				//$this->enabled 		= $this->settings['enabled'];
				//$this->description 	= $this->settings['description'];

				$this->title       		  	= $this->get_option( 'title' );
				$this->description 		  	= $this->get_option( 'description' );
				$this->enabled     		  	= $this->get_option( 'enabled' );

				// set  variables from global configuration
				$this->merchantName 	= get_option('faspay_merchant_name');
				$this->merchantCode 	= get_option('faspay_merchant_code');
				$this->merchantPass 	= get_option('faspay_merchant_password');
				$this->methodcc			= get_option('faspay_merchant_allow_cc');
				$this->getmidcc 		= get_option('faspay_merchant_mid_cc');
				$this->payment_url 		= get_bloginfo('wpurl')."/wp-content/plugins/woocommerce-gateway-faspay/payment.php";
				self::$log_enabled 		= get_option('faspay_debug');
				// remove trailing slah and add one for our need.
				$this->endpoint = rtrim(get_option('faspay_endpoint'), '/');

				self::$log_enabled = get_option('faspay_debug') == 'yes' ? true : false;


				if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ){
					// Actions

					add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(&$this, 'process_admin_options'));
					//add_action('wp_enqueue_scripts','pe_fontawesome_local');
					// add_action('woocommerce_available_payment_gateways', 'woocs_filter_gateways');
				}elseif (version_compare( WOOCOMMERCE_VERSION, '2.0.0', '<' )) {
					throw new Exception('Woocommerce version >= 2.0.0 required');
				}else{
					throw new Exception('Something Wrong With This Version');
				}
			}

			function pe_fontawesome_local(){
				wp_enqueue_style('font-awesome', get_stylesheet_directory_uri() . '/css/font-awesome.min.css'); 
			}

			public function thankyou_page(){}

			public function get_bcaparam(){
				global $woocommerce;
				$data = ["klikpaycode"=>$this->settings['klikpaycode'],"clearkey"=>$this->settings['clearkey'],"iduser"=>$this->merchantCode,"passuser"=>$this->merchantPass,"statusmix"=>$this->settings['statusmix']];
				return $data;
			}

			public function get_param(){
				global $woocommerce;
				$data = ["iduser"=>$this->merchantCode,"passuser"=>$this->merchantPass];
				return $data;
			}

			public function get_crecc($type,$cre){
				$no = explode('_', $cre);
				if ($type == 'mid') {
					if ($no[1]=='1') {
						$mid = 'mid_all';
					}else{
						$mid = 'mid_'.$no[1];
					}
					return $this->settings[$mid]; 
				}

				if ($type == 'pass') {
					if ($no[1]=='1') {
						$pas = 'pass_all';
					}else{
						$pas = 'pass_'.$no[1];
					}

					return $this->settings[$pas];
				}

				if ($type == 'env') {
					if ($no[1] == '1') {
						$env = 'environtcc';
					}else{
						$env = 'environtcc'.$no[1];
					}
					return $this->settings[$env];
				}

				if ($type == 'minprc') {
					if ($no[1] == '1') {
						$prc = 'minamount_all';
					}else{
						$prc = 'minamount_'.$no[1];
					}
					return $this->settings[$prc];
				}
			}

			public function get_midbca(){
				$data = ['midfull'=>$this->settings['midfull'],'mid3bln'=>$this->settings['mid3ins'],'mid6bln'=>$this->settings['mid6ins'],'mid12bln'=>$this->settings['mid12ins'],'mid24bln'=>$this->settings['mid24bln']];
				$data['status3bln'] 	= $this->settings['status3ins'];
				$data['status6bln'] 	= $this->settings['status6ins'];
				$data['status12bln']	= $this->settings['status12ins'];
				$data['status24bln']	= $this->settings['status24ins'];
				$data['minprice3ins']	= $this->settings['minprice3ins'];
				$data['minprice6ins']	= $this->settings['minprice6ins'];
				$data['minprice12ins']	= $this->settings['minprice12ins'];
				$data['minprice24ins']	= $this->settings['minprice24ins'];
				return $data;
			}

			function init_form_fields() {
				if (isset($_GET['section'])) {
					$method = $_GET['section'];
				} else {
					$method = '';
				}

				$tes = preg_match('/faspay_creditcard/', $method);
				if ($tes) {
					$metcc = $method;
				}else{
					$metcc = '';
				}
				switch ($method) {
					case $metcc:
					$piece = explode('_', $metcc);

					if (count($piece) >= 3) {
						if ($piece[3] != null || $piece[3] != "") {
							$this->form_fields = $this->get_mid_form($piece[3],$piece[1]);
						}						
					}


					break;

					case 'faspay_bca_klikpay':
					$this->form_fields = array(
						'header' => array(
							'title'=>__('Configuration for channel '.$method.' :','woothemes'),
							'type'=>'title',
						),
						'environtbca' => array(
							'title' => __('Environment', 'woothemes'),
							'label' => __('<strong>Switch to Live Environment</strong>', 'woothemes'),
							'type' => 'select',
							'options'=> array(
								'development' => __('Development','woothemes'),
								'production' => __('Production','woothemes'),
							), 
							'description' => '',
							'default' => 'development',
							'id'=> self::$option_prefix . '_environtment',
						),
						'enabled' => array(
							'title' => __('Enable/Disable', 'woothemes'),
							'label' => __('<strong>Enable faspay</strong>', 'woothemes'),
							'type' => 'checkbox', 'description' => 'For add this channel for payment method in check out.',
							'default' => 'no',
						),
						'title' => array(
							'title' => __('Title', 'woothemes'), 
							'type' =>'text',
							'description' => __('', 'woothemes'),
							'default' => __($method, 'woothemes'),
						),
						'expdate' => array(
							'title' => __('Expire Date','woothemes'),
							'type' => 'int',
							'description' => __('Expire date transaction for this channel','woothemes'),
							'default' => '1',
						),
						'description' => array(
							'title' => __('Description', 'woothemes'),
							'type' => 'text', 
							'description' => __('','woothemes'), 
							'default' => 'Payment using Faspay.',
						),
						'title2' => array(
							'title'=>__('BCA Klikpay Parameters :','woothemes'),
							'type'=>'title',
						),
						'clearkey' => array(
							'title'=>__('Clear Key','woothemes'),
							'type'=>'text',
						),
						'klikpaycode'=> array(
							'title'=>__('KlikPay Code','woothemes'),
							'type'=>'text',
						),
						'midfull'=> array(
							'title'=>__('MID FULL','woothemes'),
							'type'=>'text',
						),
						'config3'=> array(
							'title'=>__('Configuration for 3 Mounth Installment','woothemes'),
							'type'=>'title',
						),
						'mid3ins'=>array(
							'title'=>__('MID','woothemes'),
							'type'=>'text',
						),
						'minprice3ins'=>array(
							'title'=>__('Minimum Price','woothemes'),
							'type'=>'text',
						),
						'status3ins'=>array(
							'title'=>__('Status','woothemes'),
							'type'=>'select',
							'options'=>array(
								'active'=>__('Active','woothemes'),
								'disabled'=>__('Disabled','woothemes'),
							),
							'default'=>'disabled',
						),
						'config6'=> array(
							'title'=>__('Configuration for 6 Mounth Installment','woothemes'),
							'type'=>'title',
						),
						'mid6ins'=>array(
							'title'=>__('MID','woothemes'),
							'type'=>'text',
						),
						'minprice6ins'=>array(
							'title'=>__('Minimum Price','woothemes'),
							'type'=>'text',
						),
						'status6ins'=>array(
							'title'=>__('Status','woothemes'),
							'type'=>'select',
							'options'=>array(
								'active'=>__('Active','woothemes'),
								'disabled'=>__('Disabled','woothemes'),
							),
							'default'=>'disabled',
						),
						'config12'=> array(
							'title'=>__('Configuration for 12 Mounth Installment','woothemes'),
							'type'=>'title',
						),
						'mid12ins'=>array(
							'title'=>__('MID','woothemes'),
							'type'=>'text',
						),
						'minprice12ins'=>array(
							'title'=>__('Minimum Price','woothemes'),
							'type'=>'text',
						),
						'status12ins'=>array(
							'title'=>__('Status','woothemes'),
							'type'=>'select',
							'options'=>array(
								'active'=>__('Active','woothemes'),
								'disabled'=>__('Disabled','woothemes'),
							),
							'default'=>'disabled',
						),
						'config24'=> array(
							'title'=>__('Configuration for 24 Mounth Installment','woothemes'),
							'type'=>'title',
						),
						'mid24ins'=>array(
							'title'=>__('MID','woothemes'),
							'type'=>'text',
						),
						'minprice24ins'=>array(
							'title'=>__('Minimum Price','woothemes'),
							'type'=>'text',
						),
						'status24ins'=>array(
							'title'=>__('Status','woothemes'),
							'type'=>'select',
							'options'=>array(
								'active'=>__('Active','woothemes'),
								'disabled'=>__('Disabled','woothemes'),
							),
							'default'=>'disabled',
						),
						'configmix'=>array(
							'title'=>__('Configuration for Mix','woothemes'),
							'type'=>'title',
						),
						'statusmix'=>array(
							'title'=>__('Mix Status','woothemes'),
							'type'=>'select',
							'options'=>array(
								'active'=>__('Active','woothemes'),
								'disabled'=>__('Disabled','woothemes'),
							),
							'default'=>'disabled',
						),
					);
break;

default:
$this->form_fields = array(
	'header' => array(
		'title'=>__('Configuration for channel '.$method.' :','woothemes'),
		'type'=>'title',
	),
	'environt' => array(
		'title' => __('Environment', 'woothemes'),
		'label' => __('<strong>Switch to Live Environment</strong>', 'woothemes'),
		'type' => 'select',
		'options'=> array(
			'development' => __('Development','woothemes'),
			'production' => __('Production','woothemes'),
		), 
		'description' => '',
		'default' => 'development',
		'id'=> self::$option_prefix . '_environtment',
	),
	'enabled' => array(
		'title' => __('Enable/Disable', 'woothemes'),
		'label' => __('<strong>Enable faspay</strong>', 'woothemes'),
		'type' => 'checkbox', 'description' => 'For add this channel for payment method in check out.',
		'default' => 'no',
	),
	'title' => array(
		'title' => __('Title', 'woothemes'), 
		'type' =>'text',
		'description' => __('', 'woothemes'),
		'default' => __($method, 'woothemes'),
	),
	'expdate' => array(
		'title' => __('Expire Date','woothemes'),
		'type' => 'int',
		'description' => __('Expire date transaction for this channel','woothemes'),
		'default' => '1',
	),
	'description' => array(
		'title' => __('Description', 'woothemes'),
		'type' => 'text', 
		'description' => __('','woothemes'), 
		'default' => 'Payment using Faspay.',
	),
);
break;
}

}

public function get_mid_form($numb,$method){
	if ($numb == '1') {
		$forms = array(
			'header' => array(
				'title'=>__('Configuration for channel '.$method.'_mid1 :','woothemes'),
				'type'=>'title',
			),
			'environtcc' => array(
				'title' => __('Environment', 'woothemes'),
				'label' => __('<strong>Switch to Live Environment</strong>', 'woothemes'),
				'type' => 'select',
				'options'=> array(
					'development' => __('Development','woothemes'),
					'production' => __('Production','woothemes'),
				), 
				'description' => '',
				'id'=> self::$option_prefix . '_environtment',
				'default'=> 'development',
			),
			'enabled' => array(
				'title' => __('Enable/Disable', 'woothemes'),
				'label' => __('<strong>Enable faspay</strong>', 'woothemes'),
				'type' => 'checkbox', 'description' => 'For add this channel for payment method in check out.',
				'default' => 'no',
			),
			'title' => array(
				'title' => __('Title', 'woothemes'), 
				'type' =>'text',
				'description' => __('', 'woothemes'),
				'default' => __($_GET['section'], 'woothemes'),
			),
			'description' => array(
				'title' => __('Description', 'woothemes'),
				'type' => 'text', 
				'description' => __('','woothemes'), 
				'default' => 'Payment using Faspay.',
			),
			'parameter_all' => array(
				'title' => __( 'Configuration for MID 1 :', 'woothemes' ), 
				'type' => 'hidden',  
			),
			'status_all' => array(
				'title' => __( 'Status 1', 'woothemes' ), 
				'label' => __( 'Enable MID 1', 'woothemes' ), 
				'type' => 'checkbox', 
				'description' => '', 
				'default' => 'no'
			),		
			'mid_all' => array(
				'title' => __( 'MID 1', 'woothemes' ), 
				'type' => 'text',  
			),		
			'pass_all' => array(
				'title' => __( 'Pasword 1', 'woothemes' ), 
				'type' => 'text',  
			),
			'minamount_all' => array(
				'title' => __('Minimum Amount','woothemes'),
				'type' => 'number',
			),
		);
	}else{
		$forms = array(
			'header'.$numb => array(
				'title' =>__('Configuration for channel '.$method.'_mid'.$numb.' :','woothemes'),
				'type'=>'title',
			),
			'environtcc'.$numb => array(
				'title' => __('Environment', 'woothemes'),
				'label' => __('<strong>Switch to Live Environment</strong>', 'woothemes'),
				'type' => 'select',
				'options'=> array(
					'development' => __('Development','woothemes'),
					'production' => __('Production','woothemes'),
				), 
				'description' => '',
				'id'=> self::$option_prefix . '_environtment',
				'default'=> 'development',
			),
			'enabled' => array(
				'title' => __('Enable/Disable', 'woothemes'),
				'label' => __('<strong>Enable faspay</strong>', 'woothemes'),
				'type' => 'checkbox', 'description' => 'For add this channel for payment method in check out.',
				'default' => 'no',
			),
			'title' => array(
				'title' => __('Title', 'woothemes'), 
				'type' =>'text',
				'description' => __('', 'woothemes'),
				'default' => __($_GET['section'], 'woothemes'),
			),
			'description' => array(
				'title' => __('Description', 'woothemes'),
				'type' => 'text', 
				'description' => __('','woothemes'), 
				'default' => 'Payment using Faspay.',
			),
			'parameter_mid'.$numb => array(
				'title' => __( 'Configuration for MID '.$numb.' :', 'woothemes' ), 
				'type' => 'hidden',  
			),
			'mid_'.$numb => array(
				'title' => __( 'MID '.$numb, 'woothemes' ), 
				'type' => 'text',  
			),		
			'pass_'.$numb => array(
				'title' => __( 'Pasword '.$numb, 'woothemes' ), 
				'type' => 'text',  
			),
			'minamount_'.$numb => array(
				'title' => __('Minimum Amount','woothemes'),
				'type' => 'number',
			),
		);
	}
	return $forms;
}

public function admin_options() {
	echo '<table class="form-table">';
	$this->generate_settings_html();
	echo '</table>';
}
			//FunctionKeyGeneratorBCA	
function genKeyId($clearKey){	
	return strtoupper(bin2hex($this->str2bin($clearKey)));
}

function genSignature($klikPayCode, $transactionDate, $transactionNo, $amount, $currency, $keyId){

	        					//Signature Step 1
	$tempKey1 = $klikPayCode . $transactionNo . $currency . $keyId;
	$hashKey1 = $this->getHash($tempKey1);					 

	        					// Signature Step 2
	$expDate = explode("/",substr($transactionDate,0,10));
	$strDate = $this->intval32bits($expDate[0] . $expDate[1] . $expDate[2]);
	$amt = $this->intval32bits($amount);
	$tempKey2 = $strDate + $amt;
	$hashKey2 = $this->getHash((string)$tempKey2);

	        					// Generate Key Step 3
	$signature = abs($hashKey1 + $hashKey2);

	return $signature; 
}

function genAuthKey($klikPayCode, $transactionNo, $currency, $transactionDate, $keyId){

	$klikPayCode = str_pad($klikPayCode, 10, "0");
	$transactionNo = str_pad($transactionNo, 18, "A");
	$currency = str_pad($currency, 5, "1");
	$value_1 = $klikPayCode . $transactionNo . $currency . $transactionDate . $keyId;
	$hash_value_1 = strtoupper(md5($value_1));

	if (strlen($keyId) == 32)
		$key = $keyId . substr($keyId,0,16);
	else if (strlen($keyId) == 48)
		$key = $keyId;

	return strtoupper(bin2hex(mcrypt_encrypt(MCRYPT_3DES, hex2bin($key), hex2bin($hash_value_1), MCRYPT_MODE_ECB)));
}

function convertHex2bin($data){
	$len = strlen($data);
	return pack("H" . $len, $data);
}

function str2bin($data){
	$len = strlen($data);
	return pack("a" . $len, $data);
}

function intval32bits($value){
	if ($value > 2147483647)
		$value = ($value - 4294967296);
	else if ($value < -2147483648)
		$value = ($value + 4294967296);

	return $value;
}

function getHash($value){
	$h = 0;
	for ($i = 0;$i < strlen($value);$i++){
		$h = $this->intval32bits($this->add31T($h) + ord($value[$i]));
	}
	return $h;
}

function add31T($value){
	$result = 0;
	for($i=1;$i <= 31;$i++){
		$result = $this->intval32bits($result + $value);
	}

	return $result;
}

function process_payment($order_id) {
	global $woocommerce;
	global $wpdb;
	$order = new WC_Order($order_id);
				//print_r($order);exit;
	$this->log('Generating payment form for order ' . $order->get_order_number() . '. Notify URL: ' . $this->redirect_url);
				//endpoint for inquiry
	if ($this->settings['environt'] == 'development') {
		$this->log('Environment : Development');
		$url = "https://dev.faspay.co.id/cvr/300011/10";
	}else{
		$this->log('Environment : Production');
		$url = "https://web.faspay.co.id/cvr/300011/10";
					//$url = "https://dev.faspay.co.id/cvr/300011/10";
	}
				//merchant user info taken from billing name
	$current_user = $order->billing_first_name . " " . $order->billing_last_name;

	if (is_user_logged_in()) {
		$current_user = wp_get_current_user()->user_login;
	} 
	else {
		$current_user = "GUEST";
				// 	$srv = get_bloginfo('wpurl'); 
				// 	return array(	
				// 			'result' => 'success', 'redirect' => "$srv"."/my-account",
				// 			);
	}

	$barang = [];
	foreach ($order->get_items() as $item) {
		$sub = [
			'id'		=>$item['product_id'],
			'product'	=>$item['name'],
			'qty'		=>$item['qty'],
			'amount'	=> $this->payment_method == '820' ? ($item['line_subtotal']/$item['qty'])*100.00 : $item['line_subtotal']*100.00,				
						
			'payment_plant'=>'01',
		];
		if ($this->payment_method == '405') {
			$sub["tenor"]="00";
			$sub['merchant_id']=$this->settings['midfull'];
		}
		array_push($barang, $sub);
	}

	$disc = $order->get_total_discount()*100.00;
	foreach ($order->get_items() as $item) {
		$billgross += $item[line_subtotal]*100.00-$disc;
	}

	if ($this->payment_method != '500') {
					//generate Signature
		$parambca = array();
		$signature 	= sha1(md5($this->merchantCode.$this->merchantPass.$order_id));
		$date_exp 	= date('Y-m-d H:i:s',strtotime($order->order_date.'+'.$this->settings['expdate'].' days'));
					// Prepare Parameters
		$channeldirect = array('302','305','401','700','701','307','709','406','807','814','812','713','819','820');

		if ($this->payment_method == '405') {
			$params = array(
				'request' 				=> 'Post Data Transaksi',
				'merchant_id' 			=> (int) substr($this->merchantCode, 3),
				'merchant' 				=> $this->merchantName,
				'email'					=> $order->billing_email,
				'bill_no' 				=> $order_id,
				'bill_reff' 			=> $order_id,
				'bill_date' 			=> $order->order_date,
				'bill_expired' 			=> $date_exp,
				'bill_desc' 			=> 'Pembelian di '.$this->merchantName,
				'bill_currency'			=> 'IDR',
				'bill_gross'			=> $billgross,
				'bill_tax'				=> '0.00',
				'bill_miscfee'			=> ($order->order_shipping)*100,
				'bill_total'			=> intval($order->order_total)*100,
				'cust_no'				=> $order->customer_user,
				'cust_name'				=> $order->billing_first_name." ".$order->billing_last_name,
				'payment_channel'		=> $this->payment_method,
				'terminal'				=> '10',
				'billing_name'			=> $order->billing_first_name." ".$order->billing_last_name,
				'billing_lastname'		=> $order->billing_last_name,
				'billing_address'		=> $order->billing_address_1,
				'billing_address_city'	=> $order->billing_city,
				'billing_address_state'	=> $order->billing_state,
				'billing_address_poscode'=> $order->billing_postcode,
				'billing_address_country_code'=> $order->billing_country,
				'reserve1'			=> '',
				'reserve2'			=> $this->reserve2,
				'signature'			=> $signature,
			);
		}else{
			$params = array(
				'request' 				=> 'Post Data Transaksi',
				'merchant_id' 			=> (int) substr($this->merchantCode, 3),
				'merchant' 				=> $this->merchantName,
				'email'					=> $order->billing_email,
				'bill_no' 				=> $order_id,
				'bill_reff' 			=> $order_id,
				'bill_date' 			=> $order->order_date,
				'bill_expired' 			=> $date_exp,
				'bill_desc' 			=> 'Pembelian di '.$this->merchantName,
				'bill_currency'			=> 'IDR',
				'bill_gross'			=> $billgross,
				'bill_tax'				=> '0.00',
				'bill_miscfee'			=> ($order->order_shipping)*100,
				'bill_total'			=> ($order->order_total)*100,
				'cust_no'				=> $order->customer_user,
				'cust_name'				=> $order->billing_first_name." ".$order->billing_last_name,
				'payment_channel'		=> $this->payment_method,
				'item'					=> $barang,
				'pay_type'				=> $this->pay_type,
				'terminal'				=> '10',
				'billing_name'			=> $order->billing_first_name." ".$order->billing_last_name,
				'billing_lastname'		=> $order->billing_last_name,
				'billing_address'		=> $order->billing_address_1,
				'billing_address_city'	=> $order->billing_city,
				'billing_address_state'	=> $order->billing_state,
				'billing_address_poscode'=> $order->billing_postcode,
				'billing_address_country_code'=> $order->billing_country,
				'reserve1'			=> '',
				'reserve2'			=> $this->reserve2,
				'signature'			=> $signature,
			);
			if ($this->payment_method == '807') {
				$akulaku = array(
					'shipping_msisdn' 				=> preg_replace("/[^0-9.]/", '', $order->billing_phone),
					'shipping_address'				=> $order->billing_address_1,
					'shipping_address_city' 		=> $order->billing_city,
					'shipping_address_region'		=> $order->billing_state,
					'shipping_address_poscode'		=> $order->billing_postcode,
					'receiver_name_for_shipping'	=> $order->billing_first_name." ".$order->billing_last_name
				);
				$params = array_merge($params,$akulaku);
			}
			if ($this->payment_method == '820') {
				$indodana = array(
					'msisdn' 						=> preg_replace("/[^0-9.]/", '', $order->billing_phone),
					'receiver_name_for_shipping'	=> $order->shipping_first_name." ".$order->shipping_last_name,
					'shipping_address_city'			=> $order->shipping_city,
					'shipping_address_poscode'		=> $order->shipping_postcode,
					'shipping_address_country_code'	=> $order->shipping_country
				);
				$params = array_merge($params,$indodana);
			}
		}	

		if ($this->payment_method == '405') {
			$this->log('processing bca klikpay');

			$clearKey			= $this->settings['clearkey'];
			$klikPayCode		= $this->settings['klikpaycode'];
			            // $transactionNo		= $resp->trx_id;
			$transactionDate	= date("d/m/Y H:i:s", strtotime($order->order_date));
			$totalAmount 		= $billgross/100.00;
			$currency			= "IDR";
			$srv				= get_bloginfo('wpurl');

			$keyId = $this->genKeyId($clearKey);
			            // $signature_bca = $this->genSignature($klikPayCode, $transactionDate, $transactionNo, $totalAmount, $currency, $keyId);

			$postbca = array(
				"klikPayCode"		=> $klikPayCode,
				"totalAmount"		=> $totalAmount.'.00',
				"currency"			=> 'IDR',
				"payType"			=> '0'.$this->pay_type,
				"transactionDate"	=> date("d/m/Y H:i:s", strtotime($order->order_date)),
				"descp"				=> "Pembelian Barang di ". $this->merchantName,
				"miscFee"			=> $order->order_shipping,
			);

			$postall = array_merge($params,$postbca,$this->get_bcaparam(),$this->get_midbca());

			$insert_trx3 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_post (order_id, date_trx, total_amount, post_data) values ('$order_id', '".$order->order_date."', '".intval($order->order_total)."', '".json_encode($this->get_bcaparam())."')");

			$insert_trx = $wpdb->query("insert into ". $wpdb->prefix ."faspay_postdata (order_id, date_trx, total_amount, post_data) values ('$order_id', '".$order->order_date."', '".intval($order->order_total)."', '".json_encode($postall)."')");

			return array(	
				'result' => 'success', 'redirect' => "$srv"."/wp-content/plugins/woocommerce-gateway-faspay/paymentpage.php?order_id=".$order_id."&merchant=".$this->merchantName."&method=".$this->payment_method."&env=".$this->settings['environtbca'],
			);
		}

		$headers = array('Content-Type' => 'application/json');

					// show request for inquiry
		$this->log("create a request for inquiry");
		$this->log(var_export($params, true));

					// Send this payload to Authorize.net for processing
		$response = wp_remote_post($url, array(
			'method' => 'POST', 'body' => json_encode($params), 'timeout' => 90, 'sslverify' => false, 'headers' => $headers,
		));
		// print_r($response);exit;

					// Retrieve the body's resopnse if no errors found
		$response_body = wp_remote_retrieve_body($response);
		$response_code = wp_remote_retrieve_response_code($response);

		if (is_wp_error($response)) {
			throw new Exception(__('We are currently experiencing problems
				trying to connect to this payment gateway. Sorry for the
				inconvenience.', 'faspay'));
		}

		if (empty($response_body)) {
			throw new Exception(__('Faspay\'s Response was empty.',
				'faspay'));
		}

					// Parse the response into something we can read
		$resp = json_decode($response_body);

		if ($this->payment_method == '402' && $this->reserve3 == 'permatanet') {
			$srv			= get_bloginfo('wpurl');
			$post = array(
				"va_number"		=>$resp->trx_id,
				"amount" 			=>$order->order_total
			);
			$postall = array_merge($post,$this->get_param());
			$insert_trx = $wpdb->query("insert into ". $wpdb->prefix ."faspay_postdata (order_id, date_trx, total_amount, post_data) values ('$order_id', '".$order->order_date."', '".intval($order->order_total)."', '".json_encode($postall)."')");
			$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,order_id,date_expire,date_trx,total_amount, channel, status) values ('".$resp->trx_id."','$order_id','$date_exp', '".$order->order_date."', '".intval($order->order_total)."', '".$this->payment_method."','1')");
			$woocommerce->cart->empty_cart();
			return array(	
				'result' => 'success', 'redirect' => "$srv"."/wp-content/plugins/woocommerce-gateway-faspay/paymentpage.php?order_id=".$order_id."&merchant=".$this->merchantName."&method=".$this->payment_method."&env=".$this->settings['environt'],
			);	
		}elseif (in_array($this->payment_method,$channeldirect)) {
			if ($resp->response_code == '00') {
				$this->log($response_body);
				$cre = ['iduser'=>$this->merchantCode,'passuser'=>$this->merchantPass];
				$insert_trx = $wpdb->query("insert into ". $wpdb->prefix ."faspay_postdata (order_id, date_trx, total_amount, post_data) values ('$order_id', '".$order->order_date."', '".intval($order->order_total)."', '".json_encode($cre)."')");
				$insert_trx = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,order_id,date_expire,date_trx, total_amount, channel, status) values ('".$resp->trx_id."','$order_id','$date_exp', '".$order->order_date."', '".intval($order->order_total)."', '".$this->payment_method."','1')");
				$woocommerce->cart->empty_cart();
				return array(	
					'result' => 'success', 'redirect' => $resp->redirect_url,
				);
			}elseif($resp->response_code != '00' && $this->payment_method == '709' && $this->settings['environt'] != 'production'){
				wc_add_notice('Something error ('.$resp->response_desc.'). Untuk development maks harga 15.000', 'error');					
				$order->add_order_note( 'Error:' .  $resp->response_desc.' Please check post parameter');
			}
			else{
				wc_add_notice('Something error ('.$resp->response_desc.'). Please contact admin.', 'error');					
				$order->add_order_note( 'Error:' .  $resp->response_desc.' Please check post parameter');
			}
		}
		else{
							//log response from server
			$this->log('response body: ' . $response_body);
			$this->log('direct : '.$resp->redirect_url);

			if ($response_code == '200') {
				switch ($resp->response_code) {
					case '00':
					$cre = ['id'=>$this->merchantCode,'pass'=>$this->merchantPass];
										//save reference Code from faspay
					$this->log('Inquiry Success for order id ' . $order->get_order_number() . ' with trx id ' . $resp->trx_id);

										// Redirect to payment page
										if (isset($resp->web_url)) { //shopee qris
											$insert_trx = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,order_id,date_expire,date_trx, total_amount, channel, status, trx_id_cc) values ('".$resp->trx_id."','$order_id','$date_exp', '".$order->order_date."', '".intval($order->order_total)."', '".$this->payment_method."','1', '".$resp->web_url."')");
											$rootPath = ABSPATH.'wp-content/plugins/woocommerce-gateway-faspay/includes/assets/qris/';
											$url = $rootPath.$resp->trx_id.'.png';
											file_put_contents($url, file_get_contents($resp->web_url));
										}else{
											$insert_trx = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,order_id,date_expire,date_trx, total_amount, channel, status) values ('".$resp->trx_id."','$order_id','$date_exp', '".$order->order_date."', '".intval($order->order_total)."', '".$this->payment_method."','1')");
										}
										
										$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_postdata (order_id,date_trx,total_amount,post_data) values('$order_id','".$order->order_date."','".intval($order->order_total)."','".json_encode($cre)."')");
										$this->log('Redirect to '.$this->payment_url."?trx_id=".$resp->trx_id."&order_id=".$order_id."&store=".$this->merchantName)."&merchant=".substr($this->merchantCode, 3);
										
										return array(	
											'result' => 'success', 'redirect' => $this->payment_url."?trx_id=".$resp->trx_id."&order_id=".$order_id."&store=".$this->merchantName."&merchant=".substr($this->merchantCode, 3)."&ch=".$this->payment_method,
										);
										break;
										default:
										$this->log('Inquiry failed for order id ' . $order->get_order_number() . ' with trx id ' . $resp->trx_id.' Please check post parameter');
										wc_add_notice('Something error. Please contact admin', 'error');					
										$order->add_order_note( 'Error:' .  $resp->response_desc.' Please check post parameter');
										break;
									}
								} else {
									$this->log('Inquiry failed for order Id ' . $order->get_order_number());
								// Transaction was not succesful Add notice to the cart

									if ($response_code = "400") {	
										wc_add_notice($resp->Message, 'error');					
									// Add note to the order for your reference
										$order->add_order_note( 'Error:' .  $resp->Message);
									}
									else
									{
										wc_add_notice("error processing payment", 'error');
									// Add note to the order for your reference
										$order->add_order_note( 'Error: error processing payment.');
									}
									return;
								}  
							}

				} else { //END FOR DEBIT
					$this->log('Payment using creditcard');

					function genSignaturereq($mid, $pass, $MerchanttranId, $amount, $tranId){
						$signature = sha1(strtoupper('##'.$mid.'##'.$pass.'##'.$MerchanttranId.'##'.$amount.'##'.$tranId.'##'));
						return $signature; 
					}

					function getNumFormat($num,$dec){
						$amount = number_format((float)$num, $dec, '.', '');
						return $amount;
					}


		        	// $merchant_id 	= $this->settings['mid_all'];
			        // $password 		= $this->settings['pass_all'];
					$merchant_id 	= $this->get_crecc('mid',$this->cc_type);
					$password 		= $this->get_crecc('pass',$this->cc_type);
					$setenv 		= $this->get_crecc('env',$this->cc_type);
					$minprice 		= $this->get_crecc('minprc',$this->cc_type);
					$merchant_name 	= $this->merchantName;
					if ($setenv != 'production') {
						$url 		= 'https://fpgdev.faspay.co.id/payment';
					}else{
						$url 		= 'https://fpg.faspay.co.id/payment';
					}
					$bill_gross 	= $billgross;
					$shipping		= ($order->order_shipping);
					$email 			= $order->billing_email;
					$bill_date 		= $order->order_date;
					$bill_expired 	= '+'.$this->settings['expdatecc'].' '.'day';
					$expired_date 	= date('Y-m-d H:i:s',strtotime($bill_expired,strtotime($bill_date)));
					$items 			= $barang;
					$total 			= intval($order->order_total);
					$mi_signature 	= genSignaturereq($merchant_id,$password,$order_id,$total,0);
					$cust_no 		= $order->customer_user;			
					$phone 			= preg_replace("/[^0-9.]/", '', $order->billing_phone);
					$address 		= $order->billing_address_1;
					$city 			= $order->billing_city;
					$postcode 		= $order->billing_postcode;
					$country_code 	= $order->billing_country;
					$state		 	= $order->billing_state;
					$custname 		= $order->billing_first_name." ".$order->billing_last_name;
					$srv			= get_bloginfo('wpurl');
					$return_url		= "$srv"."/wp-content/plugins/woocommerce-gateway-faspay/thanks.php";
					$this->log('orderid: '.$order_id.'merchantid: '.$merchant_id.' password: '.$password.' total: '.intval($order->order_total));
		        	// $signaturecc 	= strtoupper(sha1(strtoupper('##'.$merchant_id.'##'.$password.'##'.$order_id.'##'.$total.'##'.'0'.'##')));
					$signaturecc 	= strtoupper(sha1(strtoupper('##'.$merchant_id.'##'.$password.'##'.$order_id.'##'.intval($order->order_total).'##'.'0'.'##'))); 


					$buyer = array(
						"CUSTNAME" 					=> $custname,
						"CUSTEMAIL"					=> $email,
						"PHONE_NO"						=> $phone,
						"BILLING_ADDRESS"				=> $address,
						"BILLING_ADDRESS_CITY"			=> $city,
						"BILLING_ADDRESS_REGION"		=> '',
						"BILLING_ADDRESS_STATE"		=> $state,
						"BILLING_ADDRESS_POSCODE"		=> $postcode,
						"BILLING_ADDRESS_COUNTRY_CODE"	=> $country_code,
						"RECEIVER_NAME_FOR_SHIPPING"	=> '',
						"SHIPPING_ADDRESS" 			=> '',
						"SHIPPING_ADDRESS_CITY" 		=> '',
						"SHIPPING_ADDRESS_REGION"		=> '',
						"SHIPPING_ADDRESS_STATE"		=> '',
						"SHIPPING_ADDRESS_POSCODE"		=> '',
						"SHIPPING_ADDRESS_COUNTRY_CODE"=> '',
						"SHIPPINGCOST"					=> $shipping, 
					);

					$merchant = array(
						"MERCHANTID" 	=> $merchant_id,
						"TXN_PASSWORD"	=> $password,
						"SIGNATURE" 	=> $signaturecc,
					);

					$param = array(
						"MERCHANT_TRANID" 	=> $order_id,
						"AMOUNT"				=> intval($order->order_total),
						"CURRENCYCODE"			=> $order->get_currency(),
						"PAYMENT_METHOD"		=> '1',
						"RESPONSE_TYPE"		=> '2',
						"RETURN_URL"			=> $return_url,
						"DESCRIPTION"			=> 'Pembayaran '.$this->merchantName,
					);

					$iteems			= $order->get_Items();
					$iteemcount 	= 1;
					$itemOrdered 	= array();

					foreach ($iteems as $itemm ) {
						$merger = array(
							"MREF".$iteemcount => $itemm->get_Name()." : ".getNumFormat($order->get_formatted_line_subtotal($itemm),0),
						);
						$itemOrdered = array_merge($itemOrdered,$merger);
						$iteemcount++;
					}

					$post = array(
						"TRANSACTIONTYPE"			=> '1',
						"LANG" 					    => '',
			            "MPARAM1" 						=> '',// direct, isi dengan direct
			            "MPARAM2" 						=> '',
			            "CUSTOMER_REF"	 				=> '',
			            "PYMT_IND"    					=> '', //selalu diisi 'tokenization',
			            "PYMT_CRITERIA"   				=> '', //'registration' = pendaftaran  atau 'payment' = setelah bayar
			            "PYMT_TOKEN"					=> '', //diisi dengan token
			            "paymentoption"                 => '0', // 0 kartu yang disimpan, 1 pakai kartu baru
			            "FRISK1"						=> '',
			            "FRISK2"						=> '',
			            "DOMICILE_ADDRESS"				=> '',
			            "DOMICILE_ADDRESS_CITY"			=> '',
			            "DOMICILE_ADDRESS_REGION"		=> '',
			            "DOMICILE_ADDRESS_STATE"		=> '',
			            "DOMICILE_ADDRESS_POSCODE" 		=> '',
			            "DOMICILE_ADDRESS_COUNTRY_CODE"	=> '',
			            "DOMICILE_PHONE_NO"	 			=> '',
			            "style_merchant_name"           => 'black',
			            "style_order_summary"           => 'black',
			            "style_order_no"                => 'black',
			            "style_order_desc"              => 'black',
			            "style_amount"                  => 'black',
			            "style_background_left"         => '#fff',
			            "style_button_cancel"           => 'grey',
			            "style_font_cancel"             => 'white',
			            // "style_image_url"               => 'http://www.pikiran-rakyat.com/sites/files/public/styles/medium/public/image/2017/06/Logo%20HUT%20RI%20ke-72%20yang%20paling%20bener.jpg?itok=RsQpqpqD',
			        );

					$loads = array_merge($buyer,$merchant,$param,$itemOrdered,$post);

					$this->log('Post param: '.json_encode($loads));
					$insert_trx = $wpdb->query("insert into ". $wpdb->prefix ."faspay_postdata (order_id, date_trx, total_amount, post_data) values ('$order_id', '".$order->order_date."', '".intval($order->order_total)."', '".json_encode($loads)."')");
					$insert_trx = $wpdb->query("insert into ". $wpdb->prefix ."faspay_post (order_id, date_trx, total_amount, post_data) values ('$order_id', '".$order->order_date."', '".intval($order->order_total)."', '".$setenv."')");
					$woocommerce->cart->empty_cart();
					return array(	
						'result' => 'success', 'redirect' => "$srv"."/wp-content/plugins/woocommerce-gateway-faspay/paymentpage.php?order_id=".$order_id."&method=".$this->payment_method."&merchant=".$this->merchantName."&env=".$setenv
					);
				}


			}

			public static function log($message) {
				if (self::$log_enabled) {
					if (empty(self::$log)) {
						self::$log = new WC_Logger();
					}
					self::$log->add('faspay', $message);
				}
			}
		}
	}

	function get_orderid($trx_id){
		global $wpdb;
		$cekDB = $wpdb->get_results("select order_id from ".$wpdb->prefix."faspay_order where trx_id = '$trx_id'",ARRAY_A);
		$order_idd = $cekDB[0]['order_id'];
		return $order_idd;
	}

	function inquiry_status($trx_id,$order_id){
		$merchant_id 	= (int) substr($this->merchantCode, 3);
		$password 		= $this->merchantPass;
		$user_id 		= $this->merchantCode;

		$mi_signature 	= sha1(md5($user_id.$password.$order_id));
		$params 		= array(
			"request" 	=> "Inquiry Status Payment",
			"trx_id" 	=> $trx_id,
			"merchant_id"=> $merchant_id,
			"bill_no"	=> $order_id,
			"signature" => $mi_signature,
		);
		if ($this->settings['environtbca'] != 'production') {
			$url = 'https://dev.faspay.co.id/cvr/100004/10';
		}else{
			$url = 'https://web.faspay.co.id/cvr/100004/10';
		}
		$headers = array('Content-Type' => 'application/json');
		// Send this payload to Authorize.net for processing
		$response = wp_remote_post($url, array(
			'method' => 'POST', 'body' => json_encode($params), 'timeout' => 90, 'sslverify' => false, 'headers' => $headers,
		));
		$response_body = wp_remote_retrieve_body($response);
		$response_code = wp_remote_retrieve_response_code($response);

		if (is_wp_error($response)) {
			throw new Exception(__('We are currently experiencing problems
				trying to connect to this payment gateway. Sorry for the
				inconvenience.', 'faspay'));
		}

		if (empty($response_body)) {
			throw new Exception(__('Faspay\'s Response was empty.',
				'faspay'));
		}

		// Parse the response into something we can read
		$resp = json_decode($response_body);

		return $resp->payment_status_code;

	}

	function add_faspay_gateway($methods) {
		$methods[] 	= 'WC_Gateway_faspay_Mandiri';
		$methods[] 	= 'WC_Gateway_faspay_BCA';
		$methods[]	= 'WC_Gateway_faspay_BNI';
		$methods[] 	= 'WC_Gateway_faspay_VA_Permata';
		$methods[]	= 'WC_Gateway_faspay_Xl_Tunai';
		$methods[]	= 'WC_Gateway_faspay_BRI_MoCash';
		$methods[]	= 'WC_Gateway_faspay_BRI';
		$methods[]	= 'WC_Gateway_faspay_CreditCard';
		$methods[]	= 'WC_Gateway_faspay_Indomaret';
		$methods[]  = 'WC_Gateway_faspay_Alfamart';
		$methods[]	= 'WC_Gateway_faspay_Maybank';
		$methods[]	= 'WC_Gateway_faspay_Maybank_Mobile';
		$methods[]	= 'WC_Gateway_faspay_Mandiri_ATM';
		$methods[]	= 'WC_Gateway_faspay_Tcash';
		$methods[]	= 'WC_Gateway_faspay_Mandiri_Ecash';
		$methods[]	= 'WC_Gateway_faspay_Indosat_Dompetku';
		$methods[]	= 'WC_Gateway_faspay_BRI_Epay';
		$methods[]	= 'WC_Gateway_faspay_BCA_Klikpay';
		$methods[]	= 'WC_Gateway_faspay_Mandiri_Klikpay';
		$methods[]	= 'WC_Gateway_faspay_CIMB_Clicks';
		$methods[]	= 'WC_Gateway_faspay_Danamon_Online_Banking';
		$methods[]	= 'WC_Gateway_faspay_Sakuku';
		$methods[]	= 'WC_Gateway_faspay_Permata_net';
		$methods[] 	= 'WC_Gateway_faspay_Danamon_OBK';
		$methods[]	= 'WC_Gateway_faspay_Kredivo';
		$methods[] 	= 'WC_Gateway_faspay_Akulaku';
		$methods[]	= 'WC_Gateway_faspay_M2u';
		$methods[]	= 'WC_Gateway_faspay_Ovo';
		$methods[]	= 'WC_Gateway_faspay_ShoppepayQRIS';
		$methods[]	= 'WC_Gateway_faspay_ShoppepayApp';
		$methods[]	= 'WC_Gateway_faspay_Dana';
		$methods[]	= 'WC_Gateway_Faspay_Sinarmas';
		$methods[]	= 'WC_Gateway_Faspay_CIMB';
		$methods[]	= 'WC_Gateway_Faspay_Indodana';

		for ($i=2; $i <= get_option('faspay_merchant_mid_cc'); $i++) { 
			$methods[] = 'WC_Gateway_faspay_CreditCard_'.$i;
		}

		return $methods;
	}
	add_filter('woocommerce_payment_gateways', 'add_faspay_gateway');

	function woocs_filter_gateways($gateway_list){
		$no = 1;
		$ch = [];
		$cartot = WC()->cart->total;
		// unset($gateway_list['faspay_creditcard_mid_1']);
		foreach ($gateway_list as $key) {
			$seq =  $no == 1 ? 'all' : $no;
			if (isset($key->settings['minamount_'.$seq])) {
				if($key->settings['minamount_'.$seq] > $cartot) unset($gateway_list['faspay_creditcard_mid_'.$no]);
				$no++;
			}
		}
		return $gateway_list;
	}
	add_filter('woocommerce_available_payment_gateways','woocs_filter_gateways');

	foreach (glob(dirname(__FILE__) . '/includes/gateways/*.php') as $filename) {
		include_once $filename;
	}
}
