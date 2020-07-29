<?php
require_once '../../../wp-config.php';
require_once '../../../wp-settings.php';

function genKeyId($clearKey){
    return strtoupper(bin2hex(str2bin($clearKey)));
}

function genSignature($klikPayCode, $transactionDate, $transactionNo, $amount, $currency, $keyId){

    //Signature Step 1
    $tempKey1 = $klikPayCode . $transactionNo . $currency . $keyId;
    $hashKey1 = getHash($tempKey1);                   

    // Signature Step 2
   $expDate = explode("/",substr($transactionDate,0,10));
   $strDate = intval32bits($expDate[0] . $expDate[1] . $expDate[2]);
   $amt = intval32bits($amount);
   $tempKey2 = $strDate + $amt;
   $hashKey2 = getHash((string)$tempKey2);

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
if (strlen($keyId) == 32){
    $key = $keyId . substr($keyId,0,16);
}
else if (strlen($keyId) == 48){
    $key = $keyId;
    }
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
      $h = intval32bits(add31T($h) + ord($value{$i}));
    }
    return $h;
}

function add31T($value){
     $result = 0;
     for($i=1;$i <= 31;$i++){
      $result = intval32bits($result + $value);
    }

    return $result;
}


global $woocommerce;
global $wpdb;

$path_url = $_SERVER['REQUEST_URI'];
$path = explode('/', $path_url);
        
        isset($_GET['trx_id']) ? $transactionNo = $_GET['trx_id'] : $trx_id_get = null;
        isset($_GET['signature']) ? $signature_get = $_GET['signature'] :$signature_get = null;
        isset($_GET['authkey']) ? $authkey_get = $_GET['authkey'] : $authkey_get = null;
        $order_id       = get_orderid($transactionNo);
        $order          = new WC_Order((int)$order_id);
        $disc = $order->get_total_discount()*100.00;
        foreach ($order->get_items() as $item) {
            $billgross += $item[line_subtotal]*100.00-$disc;
        }
        $totalAmount    = $billgross/100;
        $transactionDate  = date('d/m/Y H:i:s', strtotime($order->order_date));
        $getdata        = $wpdb->get_results("select id,post_data,total_amount from ".$wpdb->prefix."faspay_post where order_id = '".$order_id."' order by id desc limit 1",ARRAY_A);
        $raw            = json_decode($getdata[0]['post_data']);
        // $totalAmount    = json_decode($getdata[0]['total_amount']);
        $klikPayCode    = $raw->klikpaycode;
        $clearKey       = $raw->clearkey;
        $currency       = 'IDR';
        $keyId        = genKeyId($clearKey);
        if ($signature_get == '') {
          $authKey = genAuthKey($klikPayCode, $transactionNo, $currency, $transactionDate,$keyId);
          if ($authKey == $authkey_get) {
            echo 1;
          }else{
            echo 0;
          }
        }else{
          $signature_bca = genSignature($klikPayCode,$transactionDate,$transactionNo,$totalAmount,$currency,$keyId);
          if ($signature_bca == $signature_get) {
            echo 1;
          }else{
            echo 0;
          }
        }