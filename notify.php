<?php
require_once '../../../wp-config.php';
require_once '../../../wp-settings.php';


global $wpdb;
global $woocommerce;
 

$rawdata 	= file_get_contents("php://input");
$data 		= simplexml_load_string($rawdata);
$datacc 	= explode("&",$rawdata);
$response = array();
foreach($datacc as $string){
    $body = explode("=",$string);
    $key = isset($body[0]) ? $body[0] : null ;
    $value = isset($body[1]) ? $body[1] : null ;
    $response[$key] = $value;
}

if (isset($response['MERCHANT_TRANID'])){
	$orderidcc = (int)$response['MERCHANT_TRANID'];
}
$orderid 	= (int)$data->bill_no;
$trxid 		= $data->trx_id;
$boi 		= $data->merchant_id;
$codes 		= $data->payment_status_code;
if ($orderid == null || $orderid == '') {
	$query 	= $wpdb->get_results("select post_data from ".$wpdb->prefix."faspay_postdata where order_id = '".$orderidcc."' limit 1",ARRAY_A);
}else{
	$query 	= $wpdb->get_results("select post_data from ".$wpdb->prefix."faspay_postdata where order_id = '".$orderid."' limit 1",ARRAY_A);
}
if (isset($query[0])) {
	$datq		= str_replace ('\"','"', $query[0]['post_data']);
}
if(isset($datq)){
	$hapus 		= str_replace(array('{','}', '"'), '', $datq);
}
if(isset($hapus)){
	$balik 		= explode(',', $hapus);
}
if(isset($balik)){
	foreach ($balik as $key => $value) {
		$pecah = explode(':', $value);
		if (in_array($pecah[0] , array('RETURN_URL','style_image_url','callback','transactionDate','bill_date','bill_expired'))) {
			if ($pecah[0]=='transactionDate' || $pecah[0]=='bill_date' || $pecah[0]=='bill_expired') {
				$post[$pecah[0]] = $pecah[1].":".$pecah[2].":".$pecah[3];	
			}else{
				$post[$pecah[0]] = $pecah[1].":".$pecah[2];
			}
		}else{
			$post[$pecah[0]]  = $pecah[1];
		}
	}
}

$mid_mrc = isset($post['MERCHANTID']) ? $post['MERCHANTID'] : "";
$pass_mrc= isset($post['TXN_PASSWORD']) ? $post['TXN_PASSWORD'] : "";


function autoThings($transId,$orderId,$stat){
	global $wpdb;
	
	$orderr 	= new WC_Order($orderId);
	$param 		= $wpdb->get_results("select post_data from ".$wpdb->prefix."faspay_postdata where order_id = '".$orderId."'",ARRAY_A);
	$data		= str_replace ('\"','"', $param[0]['post_data']);
	$hapus 		= str_replace(array('{','}', '"'), '', $data);
	$balik 		= explode(',', $hapus);
	foreach ($balik as $key => $value) {
		$pecah = explode(':', $value);
		if (in_array($pecah[0] , array('RETURN_URL','style_image_url'))) {
			$post[$pecah[0]] = $pecah[1].":".$pecah[2];
		}else{
			$post[$pecah[0]]  = $pecah[1];
		}
	}
	$merchant_id = $post['MERCHANTID'];
	$password 	 = $post['TXN_PASSWORD'];
	$amount 	 = $post['AMOUNT'].'.00';
	$signature = strtoupper(sha1(strtoupper('##'.$merchant_id.'##'.$password.'##'.$orderId.'##'.$amount.'##'.$transId.'##')));
	$trxtype = "";
	if ($stat=="A") {
		$trxtype = '2';
	}elseif ($stat=="V") {
		$trxtype = '10';
	}

	$post = array(
		"PAYMENT_METHOD"	=> '1',
		"TRANSACTIONTYPE"	=> $trxtype,
		"MERCHANTID"		=> $merchant_id,
		"MERCHANT_TRANID"	=> $orderId,
		"TRANSACTIONID" 	=> $transId,
		"AMOUNT"			=> $amount,
		"RESPONSE_TYPE"		=> '3',
		"SIGNATURE"			=> $signature,
	);

	$data 	= http_build_query($post);
	$ch 	= curl_init();
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_URL, "https://fpgdev.faspay.co.id/payment/api");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$result = curl_exec($ch);
	curl_close($ch);
	$array = explode(";", $result);
	$response = array();
	foreach ($array as $string) {
		$body = explode("=", $string);
		$key  = $body[0];
		if ($body[1]) {
			$value = $body[1];
		}else{
			$value = "NULL";
		}
		$response[$key] = "NULL";
	}
	return $response;
}

function sigcc($mid_mrc,$pass_mrc,$merchant_trandid, $amount, $txn_status){
	$signature = sha1(strtoupper('##'.$mid_mrc.'##'.$pass_mrc.'##'.$merchant_trandid.'##'.$amount.'##'.$txn_status.'##'));
 	return strtoupper($signature);
}


$iduser2 = isset($post['id']) ? $post['id'] : $post['iduser'];
$pass2 	 = isset($post['pass']) ? $post['pass'] : $post['passuser'];

$sig = sha1(md5($iduser2.$pass2.$orderid.$codes));
$paymentdate= $data->payment_date;
$order = new WC_Order($orderid);
if (isset($ordercc)){
	$ordercc = new WC_Order($orderidcc);

	if ($response['SIGNATURE'] == sigcc($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
		switch ($response['TXN_STATUS']) {
			case 'V':
				$ordercc->update_status('cancelled', __( 'Payment Void.', 'woocommerce' ));
				echo "Payment Void";
			break;
			
			case 'C':
				echo "Payment Success";
			break;

			default:
				echo "Something Wrong";
			break;
		}
	}
}

$response_date= date('Y-m-d H:i:s');

$status = $order->get_status();

if ($status != 'processing') {
	switch ($codes) {
		case '2':
			if ($sig == $data->signature) {
				
				$order->add_order_note(__('Pembayaran telah dilakukan melalui faspay dengan id '.$orderid. ' dan trxid '.$trxid .' pada tanggal '.$paymentdate.'.', 'woocommerce'));
				$order->update_status('processing', __( 'Payment Success.', 'woocommerce' ));
				wc_reduce_stock_levels($orderid);
				$woocommerce->cart->empty_cart();
				$updatefp = $wpdb->query("update ". $wpdb->prefix ."faspay_order SET status = '2', payment_reff = '$codes', date_payment = '$paymentdate' WHERE trx_id = '$trxid'");
				$xml ="<faspay>";
			   	$xml.="<response>Payment Notification</response>";
			   	$xml.="<trx_id>$trxid</trx_id>";
			   	$xml.="<merchant_id>$boi</merchant_id>";
			   	$xml.="<bill_no>$orderid</bill_no>";
			   	$xml.="<response_code>00</response_code>";
			   	$xml.="<response_desc>Sukses</response_desc>";
			   	$xml.="<response_date>$response_date</response_date>";
			   	$xml.="</faspay>";
				echo "$xml";
			}else{
				echo "Signature Not Match";
			}
		break;
		case '3':
			echo "Transaction Failed";
		break;
	}
}else{
	$xml ="<faspay>";
	$xml.="<response>Payment Notification</response>";
	$xml.="<trx_id>$trxid</trx_id>";
	$xml.="<merchant_id>$boi</merchant_id>";
	$xml.="<bill_no>$orderid</bill_no>";
	$xml.="<response_code>14</response_code>";
	$xml.="<response_desc>Transaction Already Paid</response_desc>";
	$xml.="<response_date>$response_date</response_date>";
	$xml.="</faspay>";
	
		echo "$xml";
}


