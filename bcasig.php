<?php 
//FunctionKeyGeneratorBCA   
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