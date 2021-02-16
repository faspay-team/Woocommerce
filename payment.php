<?php
require_once '../../../wp-config.php';
require_once '../../../wp-settings.php';
if(!class_exists('PHPMailer')){
	require_once dirname(__FILE__) .'/includes/PHPMailer/src/PHPMailer.php';	
}

if(!class_exists('SMTP')){
	require_once dirname(__FILE__) .'/includes/PHPMailer/src/SMTP.php';
}

include_once dirname(__FILE__) . '/guide.php';
function dateIndonesian($date){
	$array_hari = array(1=>'Senin','Selasa','Rabu','Kamis','Jumat', 'Sabtu','Minggu');
	$array_bulan = array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember');
	$date  = strtotime($date);
	$hari  = $array_hari[date('N',$date)];
	$tanggal = date ('j', $date);
	$bulan = $array_bulan[date('n',$date)];
	$tahun = date('Y',$date);
	$formatTanggal = $hari.", ".$tanggal." ".$bulan." ".$tahun."  ". date('H:i:s',$date);
	return $formatTanggal;
}
$orderid  = $_GET['order_id'];
$trxid    = $_GET['trx_id'];
$merchant   = $_GET['store'];
$mid    = $_GET['merchant'];
$ch     = $_GET['ch'];
$url    = get_site_url();
$img_back = get_bloginfo('wpurl')."/wp-content/plugins/woocommerce-gateway-faspay/includes/css/background1.png";
$img_footer = get_bloginfo('wpurl')."/wp-content/plugins/woocommerce-gateway-faspay/includes/css/faspay.jpg";
global $woocommerce;
global $product;
global $wpdb;
if (isset($orderid) && isset($trxid)) {
	$order = new WC_Order( $orderid );
	$woocommerce->cart->empty_cart();
	$srv      = get_bloginfo('wpurl');
	$expdate  = $wpdb->get_results("select date_expire from ".$wpdb->prefix."faspay_order where order_id = '".$orderid."' and trx_id = '".$trxid."'",ARRAY_A);
	$trxdate  = $wpdb->get_results("select date_trx from ".$wpdb->prefix."faspay_order where order_id = '".$orderid."' and trx_id = '".$trxid."'",ARRAY_A);
}else{
	throw new Exception('There is no transaction.');
}
$awal        = date_create(date('Y-m-d',strtotime($trxdate[0]['date_trx'])));
$akhir       = date_create(date('Y-m-d',strtotime($expdate[0]['date_expire'])));
$hr          =  date_diff($akhir,$awal);
$tanggal_ind = dateIndonesian(date('D, d M Y, H:i:s',strtotime($expdate[0]['date_expire'])));
?>
<?php

	date_default_timezone_set("Asia/Jakarta");
	$mail = new PHPMailer\PHPMailer\PHPMailer();   
	$mail->isSMTP();
// change this to 0 if the site is going live
	$mail->SMTPDebug = 0;
	$mail->Debugoutput = 'html';
 //use SMTP authentication
	$mail->SMTPAuth = true;
	$channel = get_name($ch);
	$trx_id  = $trxid;
	$trx_total  = $order->order_total;
	$customer_name  = $order->billing_first_name." ".$order->billing_last_name;
	$merchant_name  = $merchant;
	$date_expired  = $tanggal_ind;
	$to         = $order->billing_email;
  // $to         = "marcmitchellrb@gmail.com";
	$to_name    = $order->billing_first_name." ".$order->billing_last_name;
	$from_mail  = "faspayreport@faspay.co.id";
	$from_label = "Faspay Report";
	$subject    = "Pemberitahuan Transaksi Pembayaran - Payment Notice"." - ".$merchant_name;
	$message = "";
	$message.= "<title>".$channel." - ".$trx_id."</title>";
	$message.= '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">';
	$message.= "<div class='m-3 p-3'>";
	$message.= "<p>Dear ".ucwords($customer_name).",</p>";
	$message.= "<br>";
	$message.= "<p>Silahkan lakukan pembayaran pada metode pembayaran yang telah anda pilih</p>";
	$message.= "<br>";
	$message.= "<h1>".$channel."</h1>";
	$message.= "<h1><b>".$trx_id."</b></h1>";

	$message.= "<br>";
	$message.= "<table>";
	$message.= "<tr><td>Nama Toko</td><td> : </td><td>".$merchant_name."</td>";
	$message.= "<tr><td>Batas Pembayaran</td><td> : </td><td>".$date_expired."  ".date('H:i:s',strtotime($expdate[0]['date_expire']))."</td>";
	$message.= "<tr><td>Total Pembayaran</td><td> : </td><td>Rp. ".number_format($trx_total)."</td>";
	$message.= "</table>";
	$message.= "<br>";
	$message.= "<p>Silahkan cek tahapan dibawah untuk melihat tata cara pembayaran</p>";
	$message.= "<br>";
	$message.= "<hr>";
	$message.= "<br>";
	$message.= "<p>Dear ".ucwords($customer_name).",</p>";
	$message.= "<br>";
	$message.= "<p>Kindly make a payment using your chosen payment method</p>";
	$message.= "<br>";
	$message.= "<h1>".$channel."</h1>";
	$message.= "<h1><b>".$trx_id."</b></h1>";
	$message.= "<br>";
	$message.= "<table>";
	$message.= "<tr><td>Merchant Name</td><td> : </td><td>".$merchant_name."</td>";
	$message.= "<tr><td>Expired Payment</td><td> : </td><td>".$date_expired."  ".date('H:i:s',strtotime($expdate[0]['date_expire']))."</td>";
	$message.= "<tr><td>Total Payment</td><td> : </td><td>Rp. ".number_format($trx_total)."</td>";
	$message.= "</table>";
	$message.= "<br>";
	$message.= "<p>Please check the steps to look further how to do the payment</p>";
	$message.= "<br>";
	$message.= "</div>";

	$mail->Host = '';			//fill with your mail credential
	$mail->Port = '';			//fill with your mail credential
	$mail->SMTPSecure = '';		//fill with your mail credential
	$mail->Username = '';		//fill with your mail credential
	$mail->Password = "";		//fill with your mail credential

	$mail->setFrom($from_mail, $from_label);
	$mail->addAddress($to, $to_name);
	$mail->Subject = $subject;
	$mail->msgHTML($message);
	$mail->addAttachment('includes/assets/docs/guide/Guide - '.$ch.'.pdf', 'Payment Guide '.$channel.'.pdf');

	//uncomment code below to send email payment guidance
	// if (!$mail->send()) {
		// echo "We are extremely sorry to inform you that your message could not be delivered, please try again.";
	// }else{
		// echo "<center>We've sent you an email to avoid any anomaly</center>";
	// }

?>
<?php get_header(); ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

<style>
	body{
		margin: -30px;
		padding: 0;
		font-family: sans-serif;
		background: url(icon/background1.png);
		background-size: 100%;
		overflow-x: hidden;
	}
	.box{
		background: #F2F2F2;
		color: #5D5D5D;
		margin-top: 70px;
		margin-bottom: 70px;
		padding: 70px 30px;
	}
	.logo{
		width: 120px;
		display: block;
		margin-left: auto;
		margin-right: auto;
		margin-bottom: 2%;
	}
	.company{
		text-align: center;
		font-size: 16px;
		font-weight: bold;
		margin-bottom: -10%;
	}
	.header{
		padding: 0;
		text-align: center;
		transform: translate(0px, -30px);
		font-size: 20px;
		font-weight: bold;
	}
	.header p{
		color: #FC8410;
	}
	.payment{
		font-weight: bold;
	}
	.bank{

		margin-top: 5%;
		margin-bottom: 7%;
	}
	.list{
		list-style: none;
	}
	.content{
		font-weight: bold;
		border-top: 2px solid #FA8419;
	}
	.btn{
		background-color: #FC8410;
		padding: 10px 60px;
		font-size: 16px;
		font-weight: bold;
		font-family: sans-serif;
		cursor: pointer;
		margin-top: 7%;
		margin-bottom: 15%;
		margin-left: auto;
		margin-right: auto;
		display: block;
		color: #fff;
		border-radius: 20px;
		border-style: none;
		text-align: center;
		max-width: 100%;
	}
	.title{
		margin-bottom: 4%;
	}
	.time{
		color: #5D5D5D;
		width: 90%;
		display: flex;
		text-align: center;
		justify-content: left;
	}
	.time span{
		padding: 0 15px;
		margin-left: -15px;
		margin-top: 4%;
	}
	.time span div {
		font-size: 40px;
		color: #FA8419;
	}
	.pay{
		list-style: none;
		margin-top: 3px;
	}
	.VA,input#VA{
		font-weight: bold;
		text-align: right;
		margin-top: -8.5%;
	}
	.b{
		padding-top: 7%;
	}
	.c{
		margin-top: -3%;
	}
	.MerchantName{
		text-align: right;
		margin-top: -19.3%;
	}
	.d{
		margin-top: 20%;
	}
	.Price{
		text-align: right;
		margin-top: -8.5%;
	}
	.accordion {
		background-color: #eee;
		color: #444;
		cursor: pointer;
		padding: 18px;
		width: 100%;
		border: none;
		text-align: left;
		outline: none;
		font-size: 15px;
		transition: 0.4s;
		border-bottom: solid #FC8410 1px;
	}
	.active, .accordion:hover {
		background-color: #FC8410;
		color: white;
	}
	.panel {
		padding: 0 28px;
		background-color: none;
		/*max-height: 0;*/
		overflow: hidden;
		transition: max-height 0.2s ease-out;
	}
	.info{
		margin-top: 15px;
		margin-bottom: 15px;
	}
	.accordion:after {
		content: '\002B'; /* Unicode character for "plus" sign (+) */
		font-size: 13px;
		color: #000;
		float: right;
		margin-left: 5px;
	}
	.active:after {
		content: "\2212";
		color: #fff;
	}
	.default{
		width: 80px;
		height: 25px;
		overflow: hidden;
		position: relative;
		margin-top: 2%;
		margin-left: auto;
		margin-right: auto;
		display: block;
		margin-bottom: 3%;
	}
	.pwb{
		text-align: center;
		font-size: 10px;
		margin-top: 15%; 
	}
	.copyright{
		text-align: center;
		font-size: 10px;
		margin-bottom: -10%;
	}
	.qrcode{
		width: auto;
		height: 20%;
		position: relative;
		left: 50%;
		margin-left: -25%;
		margin-bottom: 20px;
	}
</style>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>
</head>
<body>
	<section class="col-md-12">
		<div class="row">
			<div class="col-lg-3 col-md-12 col-sm-12"></div>
			<div class="col-lg-6 col-md-12 col-sm-12">

				<div class="box"> 
					<div class="header">
						<p>Transaction Order Detail</p>
					</div>
					<div class="payment">Payment Via:</div>
					<img src="<?php get_pict($ch) ?>" class="bank">
					<br>
					<?php
					if ($ch == '711') {
						$imgurl = get_site_url().'/wp-content/plugins/woocommerce-gateway-faspay/includes/assets/qris/'.$trxid.'.png';            
						?>
						<img class="qrcode" src=<?= $imgurl ?>
						<?php
					}
					?>

					<li class="list a">
						<div class="title">VA Number / Kode Bayar</div>
						<div class="content a">
							<div class="VA" ><?php echo $trxid ?></div>
							<!-- copy state -->
							<!-- <div class="VA" ><input type="text" class="bg-transparent border-0" value="<?php echo $trxid ?>" id="VA"></div> -->
							<!-- <button class="btn btn-link text-muted" onclick="copytext()">copy</button> -->
						</div>
					</li>
					<li class="list b">
						<div class="title">Total Payment</div>
						<div class="content">
							<div class="Price">
								<?php 
								if ($totals = $order->get_order_item_totals()) {
									echo $totals['order_total']['value'];
								}?></p>
							</div>
						</div>  
					</li>
					<div class="row">
						<div class="col-md-12">
							<table class="table mgr-t-20">
								<tr class="border">
									<td class="custom_color_left" colspan="2">Expired: <?= $tanggal_ind ?></td>
								</tr>
							</table>
						</div>
					</div>
					<br>
					<?php get_guide($ch,$trxid,$merchant,$mid,$order->get_currency(),$order->get_total()); ?>
					<div class="footer">
						<p class="pwb">Powered by</p>
						<img src="<?= $img_footer ?>" class="default">
						<p class="copyright">All Rights Reserved © 2019 Faspay</p>
					</div>
				</div>
			</div>

			<div class="col-lg-6 col-md-4 col-sm-12"></div>
		</div>

	</section>
	<!-- </body> -->
	<style>
		.accordion {
			background-color: #eee;
			color: #444;
			cursor: pointer;
			padding: 18px;
			width: 100%;
			border: none;
			text-align: left;
			outline: none;
			font-size: 15px;
			transition: 0.4s;
		}
		.active, .accordion:hover {
			background-color: #ccc;
		}
		.panel {
			padding: 0 18px;
			display: none;
			background-color: white;
			overflow: hidden;
		}
	</style>
	<script>
		function copytext() {
			var copyText = document.getElementById("VA");
			copyText.select();
			copyText.setSelectionRange(0, 99999)
			document.execCommand("copy");
			alert("Copied to clipboard: " + copyText.value);
		}
	</script>
	<script>
		var acc = document.getElementsByClassName("accordion");
		var i;
		for (i = 0; i < acc.length; i++) {
			acc[i].addEventListener("click", function() {
				this.classList.toggle("active");
				var panel = this.nextElementSibling;
				if (panel.style.display === "block") {
					panel.style.display = "none";
				} else {
					panel.style.display = "block";
				}
			});
		}
	</script>
	<?php get_footer(); ?>
