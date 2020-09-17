<?php
  require_once '../../../wp-config.php';
  require_once '../../../wp-settings.php';
  include_once dirname(__FILE__) . '/guide.php';

  function dateIndonesian($date){
    $array_hari = array(1=>'Senin','Selasa','Rabu','Kamis','Jumat', 'Sabtu','Minggu');
    $array_bulan = array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember');
    
    $date  = strtotime($date);
    $hari  = $array_hari[date('N',$date)];
    $tanggal = date ('j', $date);
    $bulan = $array_bulan[date('n',$date)];
    $tahun = date('Y',$date);
    $formatTanggal = $hari.", ".$tanggal." ".$bulan." ".$tahun;
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
<style>
  body{
  margin: -150px;
  padding: 0;
  font-family: sans-serif;
  background: url(icon/background1.png);
  background-size: 100%;
  }
  .box{
    background: #F2F2F2;
    color: #5D5D5D;
    width: 480px;
    height: null;
    top: 30%;
    margin-left: auto;
    margin-right: auto;
    /*margin-top: 70px;*/
    margin-bottom: 70px;
    transform: translate(-50% -50%);
    box-sizing: border-box;
    position: relative;
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
    /*margin-top: 20%;*/
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
    /*width: 150px;*/
    /*height: 150px;*/
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
  .col{
    flex: 1;
    height: 10vh;
    position: relative;
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
  .VA{
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
<body style="background: url(<?= $img_back ?>);">
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
                    <div class="VA"><?php echo $trxid ?></div>
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
</body>
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
</html>