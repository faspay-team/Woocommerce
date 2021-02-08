<<<<<<< .mine
<<<<<<< HEAD
<?php
require_once '../../../wp-config.php';
require_once '../../../wp-settings.php';
global $woocommerce;
global $wpdb;
$ch 		='';
$order_id 	='';
$status   	= '';
$trxid 		= '';
$woocommerce->cart->empty_cart();
if (isset($_GET['bill_no']) || isset($_GET['status'])) {
	$order_id = $_GET['bill_no'];
	$status   = $_GET['status'];
}
if (isset($_GET['ch']) || isset($_GET['trxid'])) {
	$ch  = $_GET['ch'];
	$trxid = $_GET['trxid'];
}
if (isset($_GET['trx_id'])) {
	$trxidbca = $_GET['trx_id'];
}
// var_dump('<pre>',$trxidbca);exit();
$srv = get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/thanks.png';
$rawdata 	= file_get_contents("php://input");
if ($rawdata != null || $rawdata != '') {
	$data 		= explode("&",$rawdata);
	$response = array();
    foreach($data as $string){
        $body = explode("=",$string);
        $key = $body[0];
        $value = $body[1];
        $response[$key] = $value;
    }
	$rwdatetrx 	 = str_replace('%3A', ':', $response['TRANDATE']);
	$datetrx1 	= str_replace('+', ' ', $rwdatetrx);
	$datetrx 	= date('Y-m-d H:i:s',strtotime($datetrx1));
	$trxcc 		= $response['MERCHANT_TRANID'].date('ymd').$response['AUTH_ID']; //cc
    $query 		= $wpdb->get_results("select post_data from ".$wpdb->prefix."faspay_postdata where order_id = '".$response['MERCHANT_TRANID']."'",ARRAY_A);
    $query2 	= $wpdb->get_results("select post_data from ".$wpdb->prefix."faspay_post where order_id = '".$response['MERCHANT_TRANID']."'",ARRAY_A);
    $datacc		= str_replace ('\"','"', $query[0]['post_data']);
	$hapuscc 	= str_replace(array('{','}', '"'), '', $datacc);
	$balikcc 	= explode(',', $hapuscc);
	$envcc 		= $query2[0]['post_data'];

	foreach ($balikcc as $key => $value) {
	$pecahcc = explode(':', $value);
	if (in_array($pecahcc[0] , array('RETURN_URL','style_image_url','callback','transactionDate','bill_date','bill_expired'))) {
		if ($pecahcc[0]=='transactionDate' || $pecah[0]=='bill_date' || $pecah[0]=='bill_expired') {
			$postcc[$pecah[0]] = $pecahcc[1].":".$pecahcc[2].":".$pecahcc[3];	
		}else{
			$post[$pecahcc[0]] = $pecahcc[1].":".$pecahcc[2];
		}
	}else{
		$postcc[$pecahcc[0]]  = $pecahcc[1];
		}
	}
	$mid_mrc = $postcc['MERCHANTID'];
	$pass_mrc= $postcc['TXN_PASSWORD'];
    
    $order 	= new WC_Order($response['MERCHANT_TRANID']); 
}

function ceksig($mid_mrc,$pass_mrc,$merchant_trandid, $amount, $txn_status){
 	$signature = sha1(strtoupper('##'.$mid_mrc.'##'.$pass_mrc.'##'.$merchant_trandid.'##'.$amount.'##'.$txn_status.'##'));
 	return strtoupper($signature);
}

function requeryCapture($data,$server,$pass){
	$sigcapture	= sha1('##'.strtoupper($data["MERCHANTID"]).'##'.strtoupper($pass).'##'.$data["MERCHANT_TRANID"].'##'.$data["AMOUNT"].'##'.$data["TRANSACTIONID"].'##');
	$post = array(
		"PAYMENT_METHOD"		=> '1',
		"TRANSACTIONTYPE"		=> '2',
		"MERCHANTID"			=> $data["MERCHANTID"],
		"MERCHANT_TRANID"		=> $data["MERCHANT_TRANID"],
		"TRANSACTIONID"			=> $data["TRANSACTIONID"],
		"AMOUNT"				=> $data["AMOUNT"],
		"RESPONSE_TYPE"			=> '3',
		"SIGNATURE"				=> $sigcapture
	);
	$a	= inquiryCapture($post,$server);

	return $a;
}

function inquiryCapture($post,$server){
		$url 	= $server == "development" ? "https://fpgdev.faspay.co.id/payment" : 
									"https://fpg.faspay.co.id/payment";
		
		foreach($post as $key => $value){
			$post_items[] = $key . '=' . $value;
		}
		$postData = implode ('&', $post_items);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result	= curl_exec($ch);
		curl_close($ch);
		
		$lines	= explode(';',$result);
		$result = array();
		foreach($lines as $line){
			list($key,$value) = array_pad(explode('=', $line, 2), 2, null);
			$result[trim($key)] = trim($value);			
		}
		
		return $result;
	}

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

if ($status == '2') {
	switch ($ch) {
		case '405':
		$woocommerce->cart->empty_cart();
		$getid = $wpdb->get_results("select order_id,payment_reff from ".$wpdb->prefix."faspay_order where trx_id = '".$trxid."' limit 1",ARRAY_A);
		$order = new WC_Order((int)$getid[0]['order_id']);
		if ($order->status != 'completed') {
				$order->update_status('completed', __( 'Payment Success.', 'woocommerce' ));
				$order->add_order_note(__('Pembayaran telah dilakukan melalui faspay dengan id '.$order_id. ' dan trxid '.$trxid.'', 'woocommerce'));
		}
		$order->reduce_order_stock();
		if ($getid[0]['payment_reff'] == '2') {
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h3><center>Your order #<?php echo $getid[0]['order_id'] ?> has been succeed.</center></h3>
					<center><img src="<?php echo $srv ?>"></center>
				</div>
			</div>
			<br>
			<?php get_footer();
		}else{
			$order->add_order_note(__('Pembayaran tidak berhasil.', 'woocommerce'));
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h1><center>Failed</center></h1>
					<h3><center>Your order #<?php echo $getid[0]['order_id'] ?> has been cancelled.</center></h3>
				</div>
			</div>
			<br>
			<?php get_footer();
		}
			break;
		default:
							$order = new WC_Order((int)$order_id);
			if ($order->status != 'completed') {
				$order->update_status('completed', __( 'Payment Success.', 'woocommerce' ));
				$order->add_order_note(__('Pembayaran telah dilakukan melalui faspay dengan id '.$order_id. ' dan trxid '.$trxid.'', 'woocommerce'));
			}
			$woocommerce->cart->empty_cart();
			$updatefp = $wpdb->query("update ". $wpdb->prefix ."faspay_order SET status = '2', payment_reff = '2', date_payment = ".date('Y-m-d H:i:s')." WHERE trx_id = '$trxid'");
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h3><center>Your order #<?php echo $order_id ?> has been succeed.</center></h3>
					<center><img src="<?php echo $srv ?>"></center>
				</div>
			</div>
			<br>
			<?php get_footer();
			break;
	}
}elseif ($response['TXN_STATUS'] == 'A' && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','1')");
	// $woocommerce->cart->empty_cart();
	// $order->add_order_note(__('Pembayaran telah dilakukan melalui creditcard melalui Faspay dengan id '.$response['MERCHANT_TRANID']. '. Status: Pending('.$response['TXN_STATUS'].')', 'woocommerce'));
	// $order->update_status('pending', __( 'Payment Pending.', 'woocommerce' ));

	if ($response['TXN_STATUS']=='Yes') {
		$capture = requeryCapture($response,$envcc,$pass_mrc);
	}
	get_header(); 

	?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> is still on process, please contact your merchant for further assistance.</center></h3>
		</div>
	</div>
	<br>
	<?php get_footer();
}
elseif (($response['TXN_STATUS']=='C' || $response['TXN_STATUS']=='S') && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	if ($response['EXCEED_HIGH_RISK']=='No') {
		$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status,date_payment) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','2','".date('Y-m-d H:i:s')."')");
		$order->reduce_order_stock();
		$woocommerce->cart->empty_cart();
		$updatefp = $wpdb->query("update ". $wpdb->prefix ."faspay_postdata  ");
		if ($order->status != 'completed') {
			$order->update_status('completed', __( 'Payment Success.', 'woocommerce' ));
			$order->add_order_note(__('Pembayaran telah dilakukan melalui creditcard melalui Faspay dengan id '.$response['MERCHANT_TRANID']. '. Status: Success('.$response['TXN_STATUS'].')', 'woocommerce'));
		}
		get_header(); 
		?>
		<div class="primary">
			<div class="col-md-12">
				<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> has been succeed.</center></h3>
				<center><img src="<?php echo $srv ?>"></center>
			</div>
		</div>
		<br>
		<?php get_footer();
	}elseif ($response['EXCEED_HIGH_RISK']=='Yes') {
		$voidRes = autoThings($response['TRANSACTIONID'],(int)$response['MERCHANT_TRANID'],'V');
		if ($voidRes['TXN_STATUS'] == "V") {
			$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','3')");
			$order->update_status('cancelled', __( 'Payment Void', 'woocommerce' ));
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h1><center>Canceled</center></h1>
					<h3><center>Your payment for order #<?php echo $response['MERCHANT_TRANID'] ?> has been failed, please try again or contact your merchant if still facing same difficulties.</center></h3>
				</div>
			</div>
			<br>
			<?php get_footer();
		}
	}
}elseif ($response['TXN_STATUS']=="CF" && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','1')");
	get_header(); 
	?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> is still on process, please contact your merchant for further assistance.</center></h3>
		</div>
	</div>
	<br>
	<?php get_footer();
}elseif ($response['TXN_STATUS']=="P" && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','1')");
	get_header(); 

	?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> is still on process, please contact your merchant for further assistance.</center></h3>
		</div>
	</div>
	<br>
	<?php get_footer();
}elseif ($response['TXN_STATUS'] == 'F') {
	$woocommerce->cart->empty_cart();
	get_header(); ?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your payment for order #<?php echo $response['MERCHANT_TRANID'] ?> has been failed. Please order again.</center></h3>
		</div>
	</div>
	<br>
	<?php get_footer();
}
else{
	get_header(); ?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Failed</center></h1>
			<h3><center>Your payment for order #<?php echo $order_id ?> has been failed. Please order again.
		</div>
	</div>
	<br>
	<?php get_footer();
}
if ($response['TXN_STATUS']=="A") {
	if ($response['EXCEED_HIGH_RISK']=="No") {
		autoThings($response['TRANSACTIONID'],$response['MERCHANT_TRANID'],"A");
	}
}

if($trxidbca != null || $trxidbca != ''){
	$woocommerce->cart->empty_cart();
	get_header(); ?>
	<div class="primary">
		<div class="col-md-12">
		</div>
	</div>
	<br>
	<?php get_footer();
}
=======
<?php
require_once '../../../wp-config.php';
require_once '../../../wp-settings.php';
global $woocommerce;
global $wpdb;
$ch 		='';
$order_id 	='';
$status   	= '';
$trxid 		= '';
$woocommerce->cart->empty_cart();
if (isset($_GET['bill_no']) || isset($_GET['status'])) {
	$order_id = $_GET['bill_no'];
	$status   = $_GET['status'];
}
if (isset($_GET['ch']) || isset($_GET['trxid'])) {
	$ch  = $_GET['ch'];
	$trxid = $_GET['trxid'];
}
if (isset($_GET['trx_id'])) {
	$trxidbca = $_GET['trx_id'];
}
// var_dump('<pre>',$trxidbca);exit();
$srv = get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/thanks.png';
$rawdata 	= file_get_contents("php://input");
if ($rawdata != null || $rawdata != '') {
	$data 		= explode("&",$rawdata);
	$response = array();
    foreach($data as $string){
        $body = explode("=",$string);
        $key = $body[0];
        $value = $body[1];
        $response[$key] = $value;
    }
	$rwdatetrx 	 = str_replace('%3A', ':', $response['TRANDATE']);
	$datetrx1 	= str_replace('+', ' ', $rwdatetrx);
	$datetrx 	= date('Y-m-d H:i:s',strtotime($datetrx1));
	$trxcc 		= $response['MERCHANT_TRANID'].date('ymd').$response['AUTH_ID']; //cc
    $query 		= $wpdb->get_results("select post_data from ".$wpdb->prefix."faspay_postdata where order_id = '".$response['MERCHANT_TRANID']."'",ARRAY_A);
    $query2 	= $wpdb->get_results("select post_data from ".$wpdb->prefix."faspay_post where order_id = '".$response['MERCHANT_TRANID']."'",ARRAY_A);
    $datacc		= str_replace ('\"','"', $query[0]['post_data']);
	$hapuscc 	= str_replace(array('{','}', '"'), '', $datacc);
	$balikcc 	= explode(',', $hapuscc);
	$envcc 		= $query2[0]['post_data'];

	foreach ($balikcc as $key => $value) {
	$pecahcc = explode(':', $value);
	if (in_array($pecahcc[0] , array('RETURN_URL','style_image_url','callback','transactionDate','bill_date','bill_expired'))) {
		if ($pecahcc[0]=='transactionDate' || $pecah[0]=='bill_date' || $pecah[0]=='bill_expired') {
			$postcc[$pecah[0]] = $pecahcc[1].":".$pecahcc[2].":".$pecahcc[3];	
		}else{
			$post[$pecahcc[0]] = $pecahcc[1].":".$pecahcc[2];
		}
	}else{
		$postcc[$pecahcc[0]]  = $pecahcc[1];
		}
	}
	$mid_mrc = $postcc['MERCHANTID'];
	$pass_mrc= $postcc['TXN_PASSWORD'];
    
    $order 	= new WC_Order($response['MERCHANT_TRANID']); 
}

function ceksig($mid_mrc,$pass_mrc,$merchant_trandid, $amount, $txn_status){
 	$signature = sha1(strtoupper('##'.$mid_mrc.'##'.$pass_mrc.'##'.$merchant_trandid.'##'.$amount.'##'.$txn_status.'##'));
 	return strtoupper($signature);
}

function requeryCapture($data,$server,$pass){
	$sigcapture	= sha1('##'.strtoupper($data["MERCHANTID"]).'##'.strtoupper($pass).'##'.$data["MERCHANT_TRANID"].'##'.$data["AMOUNT"].'##'.$data["TRANSACTIONID"].'##');
	$post = array(
		"PAYMENT_METHOD"		=> '1',
		"TRANSACTIONTYPE"		=> '2',
		"MERCHANTID"			=> $data["MERCHANTID"],
		"MERCHANT_TRANID"		=> $data["MERCHANT_TRANID"],
		"TRANSACTIONID"			=> $data["TRANSACTIONID"],
		"AMOUNT"				=> $data["AMOUNT"],
		"RESPONSE_TYPE"			=> '3',
		"SIGNATURE"				=> $sigcapture
	);
	$a	= inquiryCapture($post,$server);

	return $a;
}

function inquiryCapture($post,$server){
		$url 	= $server == "development" ? "https://fpgdev.faspay.co.id/payment" : 
									"https://fpg.faspay.co.id/payment";
		
		foreach($post as $key => $value){
			$post_items[] = $key . '=' . $value;
		}
		$postData = implode ('&', $post_items);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result	= curl_exec($ch);
		curl_close($ch);
		
		$lines	= explode(';',$result);
		$result = array();
		foreach($lines as $line){
			list($key,$value) = array_pad(explode('=', $line, 2), 2, null);
			$result[trim($key)] = trim($value);			
		}
		
		return $result;
	}

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

if ($status == '2') {
	switch ($ch) {
		case '405':
		$woocommerce->cart->empty_cart();
		$getid = $wpdb->get_results("select order_id,payment_reff from ".$wpdb->prefix."faspay_order where trx_id = '".$trxid."' limit 1",ARRAY_A);
		$order = new WC_Order((int)$getid[0]['order_id']);
		if ($order->status != 'completed') {
				$order->update_status('completed', __( 'Payment Success.', 'woocommerce' ));
				$order->add_order_note(__('Pembayaran telah dilakukan melalui faspay dengan id '.$order_id. ' dan trxid '.$trxid.'', 'woocommerce'));
		}
		$order->reduce_order_stock();
		if ($getid[0]['payment_reff'] == '2') {
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h3><center>Your order #<?php echo $getid[0]['order_id'] ?> has been succeed.</center></h3>
					<center><img src="<?php echo $srv ?>"></center>
				</div>
			</div>
			<br>
			<?php get_footer();
		}else{
			$order->add_order_note(__('Pembayaran tidak berhasil.', 'woocommerce'));
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h1><center>Failed</center></h1>
					<h3><center>Your order #<?php echo $getid[0]['order_id'] ?> has been cancelled.</center></h3>
				</div>
			</div>
			<br>
			<?php get_footer();
		}
			break;
		default:
							$order = new WC_Order((int)$order_id);
			if ($order->status != 'completed') {
				$order->update_status('completed', __( 'Payment Success.', 'woocommerce' ));
				$order->add_order_note(__('Pembayaran telah dilakukan melalui faspay dengan id '.$order_id. ' dan trxid '.$trxid.'', 'woocommerce'));
			}
			$woocommerce->cart->empty_cart();
			$updatefp = $wpdb->query("update ". $wpdb->prefix ."faspay_order SET status = '2', payment_reff = '2', date_payment = ".date('Y-m-d H:i:s')." WHERE trx_id = '$trxid'");
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h3><center>Your order #<?php echo $order_id ?> has been succeed.</center></h3>
					<center><img src="<?php echo $srv ?>"></center>
				</div>
			</div>
			<br>
			<?php get_footer();
			break;
	}
}elseif ($response['TXN_STATUS'] == 'A' && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','1')");
	// $woocommerce->cart->empty_cart();
	// $order->add_order_note(__('Pembayaran telah dilakukan melalui creditcard melalui Faspay dengan id '.$response['MERCHANT_TRANID']. '. Status: Pending('.$response['TXN_STATUS'].')', 'woocommerce'));
	// $order->update_status('pending', __( 'Payment Pending.', 'woocommerce' ));

	if ($response['TXN_STATUS']=='Yes') {
		$capture = requeryCapture($response,$envcc,$pass_mrc);
	}
	get_header(); 

	?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> is still on process, please contact your merchant for further assistance.</center></h3>
		</div>
	</div>
	<br>
	<?php get_footer();
}
elseif (($response['TXN_STATUS']=='C' || $response['TXN_STATUS']=='S') && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	if ($response['EXCEED_HIGH_RISK']=='No') {
		$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status,date_payment) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','2','".date('Y-m-d H:i:s')."')");
		$order->reduce_order_stock();
		$woocommerce->cart->empty_cart();
		$updatefp = $wpdb->query("update ". $wpdb->prefix ."faspay_postdata  ");
		if ($order->status != 'completed') {
			$order->update_status('completed', __( 'Payment Success.', 'woocommerce' ));
			$order->add_order_note(__('Pembayaran telah dilakukan melalui creditcard melalui Faspay dengan id '.$response['MERCHANT_TRANID']. '. Status: Success('.$response['TXN_STATUS'].')', 'woocommerce'));
		}
		get_header(); 
		?>
		<div class="primary">
			<div class="col-md-12">
				<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> has been succeed.</center></h3>
				<center><img src="<?php echo $srv ?>"></center>
			</div>
		</div>
		<br>
		<?php get_footer();
	}elseif ($response['EXCEED_HIGH_RISK']=='Yes') {
		$voidRes = autoThings($response['TRANSACTIONID'],(int)$response['MERCHANT_TRANID'],'V');
		if ($voidRes['TXN_STATUS'] == "V") {
			$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','3')");
			$order->update_status('cancelled', __( 'Payment Void', 'woocommerce' ));
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h1><center>Canceled</center></h1>
					<h3><center>Your payment for order #<?php echo $response['MERCHANT_TRANID'] ?> has been failed, please try again or contact your merchant if still facing same difficulties.</center></h3>
				</div>
			</div>
			<br>
			<?php get_footer();
		}
	}
}elseif ($response['TXN_STATUS']=="CF" && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','1')");
	get_header(); 
	?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> is still on process, please contact your merchant for further assistance.</center></h3>
		</div>
	</div>
	<br>
	<?php get_footer();
}elseif ($response['TXN_STATUS']=="P" && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','1')");
	get_header(); 

	?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> is still on process, please contact your merchant for further assistance.</center></h3>
		</div>
	</div>
	<br>
	<?php get_footer();
}elseif ($response['TXN_STATUS'] == 'F') {
	$woocommerce->cart->empty_cart();
	get_header(); ?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your payment for order #<?php echo $response['MERCHANT_TRANID'] ?> has been failed. Please order again.</center></h3>
		</div>
	</div>
	<br>
	<?php get_footer();
}
else{
	get_header(); ?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Failed</center></h1>
			<h3><center>Your payment for order #<?php echo $order_id ?> has been failed. Please order again.
		</div>
	</div>
	<br>
	<?php get_footer();
}
if ($response['TXN_STATUS']=="A") {
	if ($response['EXCEED_HIGH_RISK']=="No") {
		autoThings($response['TRANSACTIONID'],$response['MERCHANT_TRANID'],"A");
	}
}

if($trxidbca != null || $trxidbca != ''){
	$woocommerce->cart->empty_cart();
	get_header(); ?>
	<div class="primary">
		<div class="col-md-12">
		</div>
	</div>
	<br>
	<?php get_footer();
}
||||||| .r40
<?php
require_once '../../../wp-config.php';
require_once '../../../wp-settings.php';
global $woocommerce;
global $wpdb;
$ch 		='';
$order_id 	='';
$status   	= '';
$trxid 		= '';
$woocommerce->cart->empty_cart();
if (isset($_GET['bill_no']) || isset($_GET['status'])) {
	$order_id = $_GET['bill_no'];
	$status   = $_GET['status'];
}
if (isset($_GET['ch']) || isset($_GET['trxid'])) {
	$ch  = $_GET['ch'];
	$trxid = $_GET['trxid'];
}
if (isset($_GET['trx_id'])) {
	$trxidbca = $_GET['trx_id'];
}
// var_dump('<pre>',$trxidbca);exit();
$srv = get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/thanks.png';
$rawdata 	= file_get_contents("php://input");
if ($rawdata != null || $rawdata != '') {
	$data 		= explode("&",$rawdata);
	$response = array();
    foreach($data as $string){
        $body = explode("=",$string);
        $key = $body[0];
        $value = $body[1];
        $response[$key] = $value;
    }
	$rwdatetrx 	 = str_replace('%3A', ':', $response['TRANDATE']);
	$datetrx1 	= str_replace('+', ' ', $rwdatetrx);
	$datetrx 	= date('Y-m-d H:i:s',strtotime($datetrx1));
	$trxcc 		= $response['MERCHANT_TRANID'].date('ymd').$response['AUTH_ID']; //cc
    $query 		= $wpdb->get_results("select post_data from ".$wpdb->prefix."faspay_postdata where order_id = '".$response['MERCHANT_TRANID']."'",ARRAY_A);
    $query2 	= $wpdb->get_results("select post_data from ".$wpdb->prefix."faspay_post where order_id = '".$response['MERCHANT_TRANID']."'",ARRAY_A);
    $datacc		= str_replace ('\"','"', $query[0]['post_data']);
	$hapuscc 	= str_replace(array('{','}', '"'), '', $datacc);
	$balikcc 	= explode(',', $hapuscc);
	$envcc 		= $query2[0]['post_data'];

	foreach ($balikcc as $key => $value) {
	$pecahcc = explode(':', $value);
	if (in_array($pecahcc[0] , array('RETURN_URL','style_image_url','callback','transactionDate','bill_date','bill_expired'))) {
		if ($pecahcc[0]=='transactionDate' || $pecah[0]=='bill_date' || $pecah[0]=='bill_expired') {
			$postcc[$pecah[0]] = $pecahcc[1].":".$pecahcc[2].":".$pecahcc[3];	
		}else{
			$post[$pecahcc[0]] = $pecahcc[1].":".$pecahcc[2];
		}
	}else{
		$postcc[$pecahcc[0]]  = $pecahcc[1];
		}
	}
	$mid_mrc = $postcc['MERCHANTID'];
	$pass_mrc= $postcc['TXN_PASSWORD'];
    
    $order 	= new WC_Order($response['MERCHANT_TRANID']); 
}

function ceksig($mid_mrc,$pass_mrc,$merchant_trandid, $amount, $txn_status){
 	$signature = sha1(strtoupper('##'.$mid_mrc.'##'.$pass_mrc.'##'.$merchant_trandid.'##'.$amount.'##'.$txn_status.'##'));
 	return strtoupper($signature);
}

function requeryCapture($data,$server,$pass){
	$sigcapture	= sha1('##'.strtoupper($data["MERCHANTID"]).'##'.strtoupper($pass).'##'.$data["MERCHANT_TRANID"].'##'.$data["AMOUNT"].'##'.$data["TRANSACTIONID"].'##');
	$post = array(
		"PAYMENT_METHOD"		=> '1',
		"TRANSACTIONTYPE"		=> '2',
		"MERCHANTID"			=> $data["MERCHANTID"],
		"MERCHANT_TRANID"		=> $data["MERCHANT_TRANID"],
		"TRANSACTIONID"			=> $data["TRANSACTIONID"],
		"AMOUNT"				=> $data["AMOUNT"],
		"RESPONSE_TYPE"			=> '3',
		"SIGNATURE"				=> $sigcapture
	);
	$a	= inquiryCapture($post,$server);

	return $a;
}

function inquiryCapture($post,$server){
		$url 	= $server == "development" ? "https://fpgdev.faspay.co.id/payment" : 
									"https://fpg.faspay.co.id/payment";
		
		foreach($post as $key => $value){
			$post_items[] = $key . '=' . $value;
		}
		$postData = implode ('&', $post_items);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result	= curl_exec($ch);
		curl_close($ch);
		
		$lines	= explode(';',$result);
		$result = array();
		foreach($lines as $line){
			list($key,$value) = array_pad(explode('=', $line, 2), 2, null);
			$result[trim($key)] = trim($value);			
		}
		
		return $result;
	}

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

if ($status == '2') {
	switch ($ch) {
		case '405':
		$woocommerce->cart->empty_cart();
		$getid = $wpdb->get_results("select order_id,payment_reff from ".$wpdb->prefix."faspay_order where trx_id = '".$trxid."' limit 1",ARRAY_A);
		$order = new WC_Order((int)$getid[0]['order_id']);
		if ($order->status != 'completed') {
				$order->update_status('completed', __( 'Payment Success.', 'woocommerce' ));
				$order->add_order_note(__('Pembayaran telah dilakukan melalui faspay dengan id '.$order_id. ' dan trxid '.$trxid.'', 'woocommerce'));
		}
		$order->reduce_order_stock();
		if ($getid[0]['payment_reff'] == '2') {
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h3><center>Your order #<?php echo $getid[0]['order_id'] ?> has been succeed.</center></h3>
					<center><img src="<?php echo $srv ?>"></center>
				</div>
			</div>
			<br>
			<?php get_footer();
		}else{
			$order->add_order_note(__('Pembayaran tidak berhasil.', 'woocommerce'));
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h1><center>Failed</center></h1>
					<h3><center>Your order #<?php echo $getid[0]['order_id'] ?> has been cancelled.</center></h3>
				</div>
			</div>
			<br>
			<?php get_footer();
		}
			break;
		default:
							$order = new WC_Order((int)$order_id);
			if ($order->status != 'completed') {
				$order->update_status('completed', __( 'Payment Success.', 'woocommerce' ));
				$order->add_order_note(__('Pembayaran telah dilakukan melalui faspay dengan id '.$order_id. ' dan trxid '.$trxid.'', 'woocommerce'));
			}
			$woocommerce->cart->empty_cart();
			$updatefp = $wpdb->query("update ". $wpdb->prefix ."faspay_order SET status = '2', payment_reff = '2', date_payment = ".date('Y-m-d H:i:s')." WHERE trx_id = '$trxid'");
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h3><center>Your order #<?php echo $order_id ?> has been succeed.</center></h3>
					<center><img src="<?php echo $srv ?>"></center>
				</div>
			</div>
			<br>
			<?php get_footer();
			break;
	}
}elseif ($response['TXN_STATUS'] == 'A' && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','1')");
	// $woocommerce->cart->empty_cart();
	// $order->add_order_note(__('Pembayaran telah dilakukan melalui creditcard melalui Faspay dengan id '.$response['MERCHANT_TRANID']. '. Status: Pending('.$response['TXN_STATUS'].')', 'woocommerce'));
	// $order->update_status('pending', __( 'Payment Pending.', 'woocommerce' ));

	if ($response['TXN_STATUS']=='Yes') {
		$capture = requeryCapture($response,$envcc,$pass_mrc);
	}
	get_header(); 

	?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> is still on process, please contact your merchant for further assistance.</center></h3>
		</div>
	</div>
	<br>
	<?php get_footer();
}
elseif (($response['TXN_STATUS']=='C' || $response['TXN_STATUS']=='S') && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	if ($response['EXCEED_HIGH_RISK']=='No') {
		$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status,date_payment) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','2','".date('Y-m-d H:i:s')."')");
		$order->reduce_order_stock();
		$woocommerce->cart->empty_cart();
		$updatefp = $wpdb->query("update ". $wpdb->prefix ."faspay_postdata  ");
		if ($order->status != 'completed') {
			$order->update_status('completed', __( 'Payment Success.', 'woocommerce' ));
			$order->add_order_note(__('Pembayaran telah dilakukan melalui creditcard melalui Faspay dengan id '.$response['MERCHANT_TRANID']. '. Status: Success('.$response['TXN_STATUS'].')', 'woocommerce'));
		}
		get_header(); 
		?>
		<div class="primary">
			<div class="col-md-12">
				<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> has been succeed.</center></h3>
				<center><img src="<?php echo $srv ?>"></center>
			</div>
		</div>
		<br>
		<?php get_footer();
	}elseif ($response['EXCEED_HIGH_RISK']=='Yes') {
		$voidRes = autoThings($response['TRANSACTIONID'],(int)$response['MERCHANT_TRANID'],'V');
		if ($voidRes['TXN_STATUS'] == "V") {
			$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','3')");
			$order->update_status('cancelled', __( 'Payment Void', 'woocommerce' ));
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h1><center>Canceled</center></h1>
					<h3><center>Your payment for order #<?php echo $response['MERCHANT_TRANID'] ?> has been failed, please try again or contact your merchant if still facing same difficulties.</center></h3>
				</div>
			</div>
			<br>
			<?php get_footer();
		}
	}
}elseif ($response['TXN_STATUS']=="CF" && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','1')");
	get_header(); 
	?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> is still on process, please contact your merchant for further assistance.</center></h3>
		</div>
	</div>
	<br>
	<?php get_footer();
}elseif ($response['TXN_STATUS']=="P" && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','1')");
	get_header(); 

	?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> is still on process, please contact your merchant for further assistance.</center></h3>
		</div>
	</div>
	<br>
	<?php get_footer();
}elseif ($response['TXN_STATUS'] == 'F') {
	$woocommerce->cart->empty_cart();
	get_header(); ?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your payment for order #<?php echo $response['MERCHANT_TRANID'] ?> has been failed. Please order again.</center></h3>
		</div>
	</div>
	<br>
	<?php get_footer();
}
else{
	get_header(); ?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Failed</center></h1>
			<h3><center>Your payment for order #<?php echo $order_id ?> has been failed. Please order again.
		</div>
	</div>
	<br>
	<?php get_footer();
}
if ($response['TXN_STATUS']=="A") {
	if ($response['EXCEED_HIGH_RISK']=="No") {
		autoThings($response['TRANSACTIONID'],$response['MERCHANT_TRANID'],"A");
	}
}

if($trxidbca != null || $trxidbca != ''){
	$woocommerce->cart->empty_cart();
	get_header(); ?>
	<div class="primary">
		<div class="col-md-12">
		</div>
	</div>
	<br>
	<?php get_footer();
}
=======
<?php
require_once '../../../wp-config.php';
require_once '../../../wp-settings.php';
global $woocommerce;
global $wpdb;
$ch 		='';
$order_id 	='';
$status   	= '';
$trxid 		= '';
$woocommerce->cart->empty_cart();
if (isset($_GET['bill_no']) || isset($_GET['status'])) {
	$order_id = $_GET['bill_no'];
	$status   = $_GET['status'];
}
if (isset($_GET['ch']) || isset($_GET['trxid'])) {
	$ch  = $_GET['ch'];
	$trxid = $_GET['trxid'];
}
if (isset($_GET['trx_id'])) {
	$trxidbca = $_GET['trx_id'];
}
$rawdata 	= file_get_contents("php://input");
if ($rawdata != null || $rawdata != '') {
	$data 		= explode("&",$rawdata);
	$response = array();
	foreach($data as $string){
		$body = explode("=",$string);
		$key = $body[0];
		$value = $body[1];
		$response[$key] = $value;
	}
	$rwdatetrx 	 = str_replace('%3A', ':', $response['TRANDATE']);
	$datetrx1 	= str_replace('+', ' ', $rwdatetrx);
	$datetrx 	= date('Y-m-d H:i:s',strtotime($datetrx1));
	$trxcc 		= $response['MERCHANT_TRANID'].date('ymd').$response['AUTH_ID']; //cc
	$query 		= $wpdb->get_results("select post_data from ".$wpdb->prefix."faspay_postdata where order_id = '".$response['MERCHANT_TRANID']."'",ARRAY_A);
	$query2 	= $wpdb->get_results("select post_data from ".$wpdb->prefix."faspay_post where order_id = '".$response['MERCHANT_TRANID']."'",ARRAY_A);
	$datacc		= str_replace ('\"','"', $query[0]['post_data']);
	$hapuscc 	= str_replace(array('{','}', '"'), '', $datacc);
	$balikcc 	= explode(',', $hapuscc);
	$envcc 		= $query2[0]['post_data'];
	foreach ($balikcc as $key => $value) {
		$pecahcc = explode(':', $value);
		if (in_array($pecahcc[0] , array('RETURN_URL','style_image_url','callback','transactionDate','bill_date','bill_expired'))) {
			if ($pecahcc[0]=='transactionDate' || $pecah[0]=='bill_date' || $pecah[0]=='bill_expired') {
				$postcc[$pecah[0]] = $pecahcc[1].":".$pecahcc[2].":".$pecahcc[3];	
			}else{
				$post[$pecahcc[0]] = $pecahcc[1].":".$pecahcc[2];
			}
		}else{
			$postcc[$pecahcc[0]]  = $pecahcc[1];
		}
	}
	$mid_mrc = $postcc['MERCHANTID'];
	$pass_mrc= $postcc['TXN_PASSWORD'];
	$order 	= new WC_Order($response['MERCHANT_TRANID']); 
}
function ceksig($mid_mrc,$pass_mrc,$merchant_trandid, $amount, $txn_status){
	$signature = sha1(strtoupper('##'.$mid_mrc.'##'.$pass_mrc.'##'.$merchant_trandid.'##'.$amount.'##'.$txn_status.'##'));
	return strtoupper($signature);
}
function requeryCapture($data,$server,$pass){
	$sigcapture	= sha1('##'.strtoupper($data["MERCHANTID"]).'##'.strtoupper($pass).'##'.$data["MERCHANT_TRANID"].'##'.$data["AMOUNT"].'##'.$data["TRANSACTIONID"].'##');
	$post = array(
		"PAYMENT_METHOD"		=> '1',
		"TRANSACTIONTYPE"		=> '2',
		"MERCHANTID"			=> $data["MERCHANTID"],
		"MERCHANT_TRANID"		=> $data["MERCHANT_TRANID"],
		"TRANSACTIONID"			=> $data["TRANSACTIONID"],
		"AMOUNT"				=> $data["AMOUNT"],
		"RESPONSE_TYPE"			=> '3',
		"SIGNATURE"				=> $sigcapture
	);
	$a	= inquiryCapture($post,$server);
	return $a;
}
function inquiryCapture($post,$server){
	$url 	= $server == "development" ? "https://fpgdev.faspay.co.id/payment/api" : 
	"https://fpg.faspay.co.id/payment/api";
	foreach($post as $key => $value){
		$post_items[] = $key . '=' . $value;
	}
	$postData = implode ('&', $post_items);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result	= curl_exec($ch);
	curl_close($ch);
	$lines	= explode(';',$result);
	$result = array();
	foreach($lines as $line){
		list($key,$value) = array_pad(explode('=', $line, 2), 2, null);
		$result[trim($key)] = trim($value);			
	}
	return $result;
}
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
if ($status == '2') {
	switch ($ch) {
		case '405':
		$woocommerce->cart->empty_cart();
		$getid = $wpdb->get_results("select order_id,payment_reff from ".$wpdb->prefix."faspay_order where trx_id = '".$trxid."' limit 1",ARRAY_A);
		$order = new WC_Order((int)$getid[0]['order_id']);
		if ($order->status != 'completed') {
			$order->update_status('completed', __( 'Payment Success.', 'woocommerce' ));
			$order->add_order_note(__('Pembayaran telah dilakukan melalui faspay dengan id '.$order_id. ' dan trxid '.$trxid.'', 'woocommerce'));
		}
		$order->reduce_order_stock();
		if ($getid[0]['payment_reff'] == '2') {
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h3><center>Your order #<?php echo $getid[0]['order_id'] ?> has been succeed.</center></h3>
					<center><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/payment-success.png' ?>"></center>
				</div>
			</div>
			<br>
			<?php get_footer();
		}else{
			$order->add_order_note(__('Pembayaran tidak berhasil.', 'woocommerce'));
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h1><center>Failed</center></h1>
					<h3><center>Your order #<?php echo $getid[0]['order_id'] ?> has been cancelled.</center></h3>
					<center><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/payment-danger.png' ?>"></center>
				</div>
			</div>
			<br>
			<?php get_footer();
		}
		break;
		default:
		$order = new WC_Order((int)$order_id);
		if ($order->status != 'processing') {
			$order->update_status('processing', __( 'Payment Success.', 'woocommerce' ));
			$order->add_order_note(__('Pembayaran telah dilakukan melalui faspay dengan id '.$order_id. ' dan trxid '.$trxid.'', 'woocommerce'));
		}
		$woocommerce->cart->empty_cart();
		$updatefp = $wpdb->query("update ". $wpdb->prefix ."faspay_order SET status = '2', payment_reff = '2', date_payment = ".date('Y-m-d H:i:s')." WHERE trx_id = '$trxid'");
		get_header(); ?>
		<div class="primary">
			<div class="col-md-12">
				<h3><center>Your order #<?php echo $order_id ?> has been succeed.</center></h3>
				<center><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/payment-success.png' ?>"></center>
			</div>
		</div>
		<br>
		<?php get_footer();
		break;
	}
}elseif ($response['TXN_STATUS'] == 'A' && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','1')");
	$payment = wc_get_payment_gateway_by_order( $order );
	$envcc = $payment->settings['environtcc'];
	$capture = requeryCapture($response,$envcc,$pass_mrc);
	$updatefp = $wpdb->query("update ". $wpdb->prefix ."faspay_postdata  ");
	if ($capture['TXN_STATUS'] == 'C') {
		$order->update_status('completed', __( 'Payment Success.', 'woocommerce' ));
		$order->add_order_note(__('Pembayaran telah dilakukan melalui creditcard melalui Faspay dengan id '.$response['MERCHANT_TRANID']. '. Status: Success('.$capture['TXN_STATUS'].')', 'woocommerce'));
		get_header(); 
		?>
		<div class="primary">
			<div class="col-md-12">
				<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> has been succeed.</center></h3>
				<center><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/payment-success.png' ?>"></center>
			</div>
		</div>
		<br>
		<?php get_footer();
	}elseif ($capture['TXN_STATUS'] == 'E') {
		get_header(); 
		?>
		<div class="primary">
			<div class="col-md-12">
				<h3><center>Your payment for order #<?php echo $response['MERCHANT_TRANID'] ?> has been failed. Please order again.</center></h3>
				<center><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/payment-danger.png' ?>"></center>
			</div>
		</div>
		<br>
		<?php get_footer();
	}else{
		get_header(); 
		?>
		<div class="primary">
			<div class="col-md-12">
				<h1><center>Info</center></h1>
				<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> is still on process, please contact your merchant for further assistance.</center></h3>
				<center><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/payment-process.png' ?>"></center>
			</div>
		</div>
		<br>
		<?php get_footer();
	}
}
elseif (($response['TXN_STATUS']=='C' || $response['TXN_STATUS']=='S') && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	if ($response['EXCEED_HIGH_RISK']=='No') {
		$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status,date_payment) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','2','".date('Y-m-d H:i:s')."')");
		$order->reduce_order_stock();
		$woocommerce->cart->empty_cart();
		$updatefp = $wpdb->query("update ". $wpdb->prefix ."faspay_postdata  ");
		if ($order->status != 'completed') {
			$order->update_status('completed', __( 'Payment Success.', 'woocommerce' ));
			$order->add_order_note(__('Pembayaran telah dilakukan melalui creditcard melalui Faspay dengan id '.$response['MERCHANT_TRANID']. '. Status: Success('.$response['TXN_STATUS'].')', 'woocommerce'));
		}
		get_header(); 
		?>
		<div class="primary">
			<div class="col-md-12">
				<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> has been succeed.</center></h3>
				<center><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/payment-success.png' ?>"></center>
			</div>
		</div>
		<br>
		<?php get_footer();
	}elseif ($response['EXCEED_HIGH_RISK']=='Yes') {
		$voidRes = autoThings($response['TRANSACTIONID'],(int)$response['MERCHANT_TRANID'],'V');
		if ($voidRes['TXN_STATUS'] == "V") {
			$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','3')");
			$order->update_status('cancelled', __( 'Payment Void', 'woocommerce' ));
			get_header(); ?>
			<div class="primary">
				<div class="col-md-12">
					<h1><center>Canceled</center></h1>
					<h3><center>Your payment for order #<?php echo $response['MERCHANT_TRANID'] ?> has been failed, please try again or contact your merchant if still facing same difficulties.</center></h3>
					<center><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/payment-danger.png' ?>"></center>
				</div>
			</div>
			<br>
			<?php get_footer();
		}
	}
}elseif ($response['TXN_STATUS']=="CF" && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','1')");
	get_header(); 
	?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> is still on process, please contact your merchant for further assistance.</center></h3>
			<center><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/payment-process.png' ?>"></center>
		</div>
	</div>
	<br>
	<?php get_footer();
}elseif ($response['TXN_STATUS']=="P" && $response['SIGNATURE'] == ceksig($mid_mrc,$pass_mrc,$response['MERCHANT_TRANID'], $response['AMOUNT'], $response['TXN_STATUS'])) {
	$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,trx_id_cc,order_id, date_trx, total_amount, channel, status) values ('".$trxcc."','".$response['TRANSACTIONID']."','".$response['MERCHANT_TRANID']."','".$datetrx."','".str_replace('.00', '', $response['AMOUNT'])."','500','1')");
	get_header(); 
	?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your order #<?php echo $response['MERCHANT_TRANID'] ?> is still on process, please contact your merchant for further assistance.</center></h3>
			<center><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/payment-process.png' ?>"></center>
		</div>
	</div>
	<br>
	<?php get_footer();
}elseif ($response['TXN_STATUS'] == 'F') {
	$woocommerce->cart->empty_cart();
	get_header(); ?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Info</center></h1>
			<h3><center>Your payment for order #<?php echo $response['MERCHANT_TRANID'] ?> has been failed. Please order again.</center></h3>
			<center><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/payment-process.png' ?>"></center>
		</div>
	</div>
	<br>
	<?php get_footer();
}
else{
	get_header(); ?>
	<div class="primary">
		<div class="col-md-12">
			<h1><center>Failed</center></h1>
			<h3><center>Your payment for order #<?php echo $order_id ?> has been failed. Please order again.</center></h3>
			<center><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/payment-danger.png' ?>"></center>
		</div>
	</div>
	<br>
	<?php get_footer();
}
if ($response['TXN_STATUS']=="A") {
	if ($response['EXCEED_HIGH_RISK']=="No") {
		autoThings($response['TRANSACTIONID'],$response['MERCHANT_TRANID'],"A");
	}
}
if($trxidbca != null || $trxidbca != ''){
	$woocommerce->cart->empty_cart();
	get_header(); ?>
	<div class="primary">
		<div class="col-md-12">
		</div>
	</div>
	<br>
	<?php get_footer();
}
>>>>>>> .r42
>>>>>>> abcc252f4192e153f4fa1de777e9b1a8abd69874
?>