<?php
require_once '../../../wp-config.php';
require_once '../../../wp-settings.php';
include_once dirname(__FILE__) . '/guide.php';
global $woocommerce;
global $product;
global $wpdb;
$orderid	= $_GET['order_id'];
$channel	= $_GET['method'];
$env 		= $_GET['env'];
$merchant 	= $_GET['merchant'];
$param 		= $wpdb->get_results("select post_data from ".$wpdb->prefix."faspay_postdata where order_id = '".$orderid."' limit 1",ARRAY_A);
$data		= str_replace ('\"','"', $param[0]['post_data']);
$hapus 		= str_replace(array('{','}', '"'), '', $data);
$balik 		= explode(',', $hapus);
$url2 		= get_site_url();

if (isset($orderid)) {
	$order = new WC_Order( $orderid );
}else{
	throw new Exception('There is no transaction.');
}

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


function get_param1($array1){
	$keluar = ['klikPayCode','totalAmount','currency','payType','transactionDate','descp','miscFee','klikpaycode','clearkey','midfull','mid3bln','mid6bln','mid12bln','mid24bln','status3bln','status6bln','status12bln','status24bln','minprice3ins','minprice6ins','minprice12ins','minprice24ins','iduser','passuser','statusmix'];

	foreach ($keluar as $value) {
		unset($array1[$value]);
	}
	return $array1;
}

function get_param2($array1){
	$newarray = [];
	$newarray['klikPayCode'] = $array1['klikPayCode'];
	$newarray['totalAmount'] = $array1['totalAmount'];
	$newarray['currency'] = $array1['currency'];
	$newarray['transactionDate'] = $array1['transactionDate'];
	$newarray['descp'] = $array1['descp'];
	$newarray['miscFee'] = $array1['miscFee'];
	return $newarray;
}

function gen_sign($mid,$pass,$billno){
	$sign = sha1(md5($mid.$pass.$billno));
	return $sign;
}

switch ($channel) {
	case '500':
		if ($env != 'production') {
			$url = 'https://fpgdev.faspay.co.id/payment';
		}else{
			$url = 'https://fpg.faspay.co.id/payment';
		}
		$order->add_order_note(__('Pembayaran menggunakan creditcard melalui faspay dengan id '.$orderid.'.', 'woocommerce'));
		$order->reduce_order_stock();
		$woocommerce->cart->empty_cart();
		$string = '<form method="post" name="form" id="ccForm" action="'.$url.'">';
					if ($post != null) {
						foreach ($post as $name=>$value) {
							$string .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
						}
					}
					$string .= '</form>';
					$string .= '<script> document.getElementById("ccForm").submit()</script>';
		echo $string;
		break;
	case '405':
		$barang 	= [];
		$mid3bln 	= '';
		$mid6bln 	= '';
		$mid12bln	= '';
		$mid24bln	= '';
		$status_mix = $post['statusmix'];
		$status3bln = $post['status3bln'];
		$status6bln = $post['status6bln'];
		$status12bln= $post['status12bln'];
		$status24bln= $post['status24bln'];
		$minprice3bln = $post['minprice3ins'];
		$minprice6bln = $post['minprice6ins'];
		$minprice12bln= $post['minprice12ins'];
		$minprice24bln= $post['minprice24ins'];
		$miduser 	= $post['iduser'];
		$passuser 	= $post['passuser'];
		
		if (isset($_POST['submit'])) {
			if ($env != 'production') {
				$urlfaspay 	= 'https://dev.faspay.co.id/cvr/300011/10';
			}else{
				$urlfaspay 	= 'https://dev.faspay.co.id/cvr/300011/100';
			}
			$params1 	= get_param1($post);
			$paramsbca	= get_param2($post);
			$midfull 	= $post['midfull'];
			$mid3bln 	= $post['mid3bln'];
			$mid6bln 	= $post['mid6bln'];
			$mid12bln	= $post['mid12bln'];
			$mid24bln	= $post['mid24bln'];
			$sign 		= gen_sign($miduser,$passuser,$post['bill_no']);
			$countpay = '0';
			$count = '0';
			foreach ($_POST['payment_plan'] as $row => $act) {
				$payplan = $_POST['payment_plan'][$row];
				
				if ($payplan) {
					$countpay++;
					if ($payplan >= '2' && $payplan != '1') {
						$count++;
						$pay = '2';
					}
				}
			}
			//Installment
			if ($count == $countpay) {
			if ($pay == '2') {
				$paytype = '2';
				$params1['pay_type'] = '2';
				$counter = 0;
				foreach ($order->get_items() as $item) {
					$name[$counter] = $item['name'];
					$qty[$counter] = $item['qty'];
					$nominal[$counter] = $item['line_subtotal']*100.00;
					$counter++;
				}
				$counter2 = 0;
				foreach ($_POST['payment_plan'] as $row => $act) {
					$payplan = $_POST['payment_plan'][$row];

					$subd = [
						'product' => $name[$counter2],
						'qty' 	  => $qty[$counter2],
						'amount'  => $nominal[$counter2],
						'payment_plan'=> '02',
					];
					if ($payplan == '2') {
						$sub = array_merge($subd, ['tenor'=>'03','merchant_id'=>$mid3bln]);
					}elseif ($payplan == '3') {
						$sub = array_merge($subd, ['tenor'=>'06','merchant_id'=>$mid6bln]);
					}elseif ($payplan == '4') {
						$sub = array_merge($subd, ['tenor'=>'12','merchant_id'=>$mid12bln]);
					}else{
						$sub = array_merge($subd, ['tenor'=>'24','merchant_id'=>$mid24bln]);
					}
					$counter2++;
					array_push($barang, $sub);
				}
				if ($counter > 5) {
					$url=$woocommerce->cart->get_cart_url();
     				echo "<script>alert('Sorry, only 5 item for installment transaction ! Please reduce your item first');</script>";
     				echo "<script language='javascript'>window.location ='$url'</script>";
                    exit();
				}
			}
			$params1['item'] = $barang;
		}
		//End Installment
		// MIX Condition
		if (((int)$count < $countpay && $count != '') && $status_mix != 'disabled') {
			$paytype = '3';
			$params1['pay_type'] = '3';

			$counter = 0;

			foreach ($order->get_items() as $item) {
				$name[$counter] = $item['name'];
				$qty[$counter] = $item['qty'];
				$nominal[$counter] = $item['line_subtotal']*100.00;
				$counter++;
			}

			$counter=0;
			$countertiga=0;
			$counterenam=0;
			$counterduabelas=0;
			$counterduaempat=0;
			foreach ($_POST['payment_plan'] as $row => $act) {
				$payplan = $_POST['payment_plan'][$row];
				$subd = [
						'product' => $name[$counter],
						'qty' 	  => $qty[$counter],
						'amount'  => $nominal[$counter],
					];
			

				if ($payplan=='1') {
					$sub = array_merge($subd,['payment_plan'=>'01']);
				}else{
					$sub = array_merge($subd,['payment_plan'=>'02']);
				}
				if ($payplan == '1') {
					$sub = array_merge($subd,['tenor'=>'00','merchant_id'=>$midfull]);
				}elseif ($payplan=='2') {
					$sub = array_merge($subd,['tenor'=>'03','merchant_id'=>$mid3bln]);
					$countertiga++;
				}elseif ($payplan=='3') {
					$sub = array_merge($subd,['tenor'=>'06','merchant_id'=>$mid6bln]);
					$counterenam++;
				}elseif ($payplan=='4') {
					$sub = array_merge($subd,['tenor'=>'12','merchant_id'=>$mid12bln]);
					$counterduabelas++;
				}else{
					$sub = array_merge($subd,['tenor'=>'24','merchant_id'=>$mid24bln]);
					$counterduaempat++;
				}
				$counter++;
				array_push($barang, $sub);
			}
			$params1['item'] = $barang;
			$counter_count = $countertiga+$counterenam+$counterduabelas+$counterduaempat;
			// if ($status_mix == 'disabled') {
			// 	$url = get_bloginfo('wpurl')."/wp-content/plugins/woocommerce-gateway-faspay/paymentpage.php?order_id=".$_GET['order_id']."&merchant=".$_GET['merchant']."&method=".$_GET['method']."&env=".$_GET['env']."";
			// 	echo "<script>alert('Sorry, this transaction can not be mixed.');</script>";
			// 	echo "<script language='javascript'>window.location ='$url'</script>";
			// 	exit();
			// }
			if ($counter_count > 5) {
				$url=$woocommerce->cart->get_cart_url();
     			echo "<script>alert('Sorry, only 5 item for installment transaction ! Please reduce your item first');</script>";
     			echo "<script language='javascript'>window.location ='$url'</script>";
     			exit();
			}
		}
		//END MIX
		//Full Payment
		if ($countpay ='2' && $count == '0') {
			$paytype = '1';
			$params1['pay_type'] = '1';
			foreach ($order->get_items() as $item) {
				$sub = [
					'id'		=>$item['product_id'],
					'product'	=>$item['name'],
					'qty'		=>$item['qty'],
					'amount'	=>$item['line_subtotal']*100.00,
					'payment_plant'=>'01',
					'tenor'=>'00',
					'merchant_id'=>$midfull,
				];
			array_push($barang, $sub);
			}
		$params1['item'] = $barang;
		}
		//End Full Payment
		$headers = array('Content-Type' => 'application/json');
		//print_r($headers);exit;
		// Send this payload to faspay for processing
		$response = wp_remote_post($urlfaspay, array('method' => 'POST', 'body' => json_encode($params1), 'timeout' => 90, 'sslverify' => false, 'headers' => $headers,));
		// Retrieve the body's resopnse if no errors found
		//print_r($response);exit;
		$response_body = wp_remote_retrieve_body($response);
		$response_code = wp_remote_retrieve_response_code($response);
		if (is_wp_error($response)) {
			throw new Exception(__('We are currently experiencing problems. Trying to connect to this payment gateway. Sorry for the inconvenience.', 'faspay'));
		}

		if (empty($response_body)) {
			throw new Exception(__('Faspay\'s Response was empty.','faspay'));
		}
		
		}
		$resp = json_decode($response_body);
		
		if ($resp->response_code == '00') {
			
			$urlfbca = $resp->redirect_url;
			//print_r($urlfbca);exit;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $urlfbca);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);					
			
			$woocommerce->cart->empty_cart();
			$insert_trx2 = $wpdb->query("insert into ". $wpdb->prefix ."faspay_order (trx_id,order_id, date_trx, total_amount, channel, status) values ('".$resp->trx_id."','$orderid', '".$order->order_date."', '".intval($order->order_total)."', '".$channel."','1')");

			//header("Location:$urlfbca");

			echo $result;
			exit();
		}



		if ($resp->response_code == '00') {
			$disc = $order->get_total_discount()*100.00;
			foreach ($order->get_items() as $item) {
					$billgross += $item[line_subtotal]*100.00-$disc;
			}
			$klikPayCode 			= $paramsbca['klikPayCode'];
			$paytype 				= '0'.$paytype;
			$clearkey				= $post['clearkey'];
			$transactionNo 			= $resp->trx_id;
			$transactionDate		= date("d/m/Y H:i:s", strtotime($order->order_date));
			$totalAmount 			= $billgross/100;
			$currency 				= 'IDR';
			$srv 					= get_bloginfo('wpurl');
			$keyId 			= genKeyId($clearkey);
			$signature_bca 	= genSignature($klikPayCode, $transactionDate, $transactionNo, $totalAmount, $currency, $keyId);

			$post = array(
				"klikPayCode"  		=> $klikPayCode,
				"transactionDate"	=> date("d/m/Y H:i:s", strtotime($order->order_date)),
				"transactionNo"		=> $transactionNo,
				"currency"			=> $currency,
				"totalAmount"		=> $totalAmount.'.00',
				"payType"			=> $paytype,
				"signature"			=> $signature_bca,
				"descp"				=> $paramsbca['descp'],
				"callback"			=> "$srv"."/wp-content/plugins/woocommerce-gateway-faspay/thanks.php?trxid=".$resp->trx_id."&orderid=".$orderid."&ch=405&status=2",
				"miscFee"			=> $order->order_shipping,
			);

			$updatefp = $wpdb->query("update ". $wpdb->prefix ."faspay_postdata SET post_data2 = '".json_encode($post)."' WHERE order_id = '$orderid'");

			$string = '<form method="post" id="bcaForm" name="form" action="'.$urlbca.'">';
			if ($post != null) {
				foreach ($post as $name => $value) {
              		$string .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
            	}
			}
			$string .= '</form>';
			$string .= '<script> document.getElementById("bcaForm").submit();;</script>';
			echo $string;
			exit();
		}

		break;
	case '402':
		$sbbalik = str_replace('}', '', $balik);
		foreach ($sbbalik as $value) {
				$pecah = explode(':', $value);
				$post[$pecah[0]]  = $pecah[1];
			}
		if ($env != 'production') {
			$url = 'https://dev.faspay.co.id/permatanet/payment';
		}else{
			$url = 'https://web.faspay.co.id/permatanet/payment';
		}
		unset($post['iduser']);
		unset($post['passuser']);

		$woocommerce->cart->empty_cart();
		$string = '<form method="post" id="permataForm" name="form" action="'.$url.'">';
					if ($post != null) {
						foreach ($post as $name=>$value) {
							$string .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
						}
					}
		$string .= '</form>';
		$string .= '<script> document.getElementById("permataForm").submit();;</script>';
		echo $string;
		exit();
		break;
	default:
		# code...
		break;
}


?>

<?php get_header(); ?>

<div id="primary">
	<div id="content" role="main">
		<div class="row">
			<div class="large-12 colums"  style="border: double; border-color:#71235a; padding-left:20px; padding-right:20px; padding-top:15px; margin-bottom:20px">
				<h4>Order Details #<?php echo $orderid ?></h4>
				<img src="<?php get_pict($channel) ?>">
				<h4><?php echo $order->get_payment_method_title() ?></h4>
				<table>
				<thead>
					<th class="product-name" style="text-align: left;"><?php _e( 'Product', 'woothemes' ); ?></th>
					<th class="product-total" style="text-align: left;"><?php _e( 'Total', 'woothemes' ); ?></th>
					<?php if ($channel == '405') {
						echo "<th class='paytype' style='text-align: left;''>Payment Type</th>";
					} ?>
				</thead>
				<tbody>
					<?php 
					echo "<form id='' action='' method=post>";

					if (sizeof($order->get_items()>0)) {
						foreach ($order->get_items() as $item ) {
							$_product = wc_get_product( $item['variation_id'] ? $item['variation_id'] : $item['product_id'] );
							echo '
								<tr class = "' . esc_attr( apply_filters('woocommerce_order_table_item_class', 'order_table_item', $item, $order ) ) . '">
								<td class="product-name">';
							echo '<a href="'.get_permalink( $item['product_id'] ).'">' . $item['name'] . '</a> <strong class="product-quantity">&times; ' . $item['qty'] . '</strong>';
							echo '</td><td class="product-total">' . $order->get_formatted_line_subtotal( $item ) . '</td>';
							if ($channel == '405') {
								echo '<td clas="paytype"><select name=payment_plan[]>';
								echo '<option value="1">Full Payment</option>';
								if (($status3bln == 'active') && ($item['line_total'] >= $minprice3bln)) {
									echo '<option value="2">Cicilan 3 Bulan</option>';
								}
								if ($status6bln == 'active' && $item['line_total'] >= $minprice6bln) {
									echo '<option value="3">Cicilan 6 Bulan</option>';
								}
								if ($status12bln == 'active' && $item['line_total'] >= $minprice12bln) {
									echo '<option value="4">Cicilan 12 Bulan</option>';
								}
								if ($status24bln == 'active' && $item['line_total'] >= $minprice24bln) {
									echo '<option value="5">Cicilan 24 Bulan</option>';
								}
								echo '</select></td>';
							}
							echo '</tr>';
						}
					}
					?>
				</tbody>
				<tfoot>
					<?php 
						if ($totals = $order->get_order_item_totals()) {
							foreach ($totals as $total) {
								?> 
								<tr>
									<th style="text-align: right" scope="row"><?php echo $total['label']; ?> </th>
									<td><?php echo $total['value']; ?></td>
								</tr>
								<?php
							}
						}
					?>
				</tfoot>
			</table>
			</div>
		</div>
	</div>
<div class="center">
<?php 
if ($channel == '405') { ?>
	<input type='submit' class='button alt' name='submit' id='submit' value='Process Payment'>
<?php 
	echo "</form>";
}else{ ?>
	<button  class="btn btn-default" id="asd" onclick="document.form.submit();"> Process Payment</button>
<?php }
?>
</div>
</div>
<div>
<br>
</div>

<?php get_footer(); ?>