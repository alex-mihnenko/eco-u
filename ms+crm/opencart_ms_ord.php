<?php
// Init
	#error_reporting(E_ALL);
	#ini_set('display_errors', '1');

	include("opencart_inc.php");

	$time=time();

	$alertsList = ["mihnenko@gmail.com", "sales@eco-u.ru"];

	$log = "";
// ---

$res_orders=mysql_query("
	SELECT 
		payment_method, customer_id, order_id, firstname, lastname, email, telephone, comment, total, 
		order_status_id, date_added, shipping_code, shipping_postcode, shipping_city, shipping_country, 
		shipping_address_1, shipping_address_2, delivery_time 
	FROM oc_order WHERE customer_id>0 AND order_status_id>0 ORDER BY date_modified DESC LIMIT 0,20");

$i=1;

while(list($payment_method,$customer_id,$order_id,$fname,$lname,$email,$phone,$comm,$total,$order_status_id,$date_added,$shipping_code,$shipping_postcode,$shipping_city,
// ---

	$shipping_country,$shipping_address_1,$shipping_address_2,$delivery_time)=mysql_fetch_row($res_orders)){
	
	// Check email
	if($email == 'empty@localhost' || $email=='') {
		if ($phone!=''){
			$email = $phone.'@eco-u.ru';
		}else{
			$email = "customer_".$customer_id."@eco-u.ru";
		}
	}
	
	$res_roistat_id = "SELECT `order_roistat_visit_id` FROM `oc_order_roistat` WHERE `order_id`=".$order_id;
	$res=mysql_query($res_roistat_id);
	list($roistat_id)=mysql_fetch_row($res);
	if(!isset($roistat_id)) $roistat_id="";
	
	
	// Get delivery cost
		$res=mysql_query("select value from oc_order_total where code='shipping' and order_id='$order_id'");
		list($deliveryCost)=mysql_fetch_row($res);
		if(!isset($deliveryCost)) $deliveryCost=0;
	// ---

	// Customer
		$ms_last_tmp=DateTime::createFromFormat("Y-m-d H:i:s",$date_added);
		$ms_last= $ms_last_tmp->getTimestamp();

		$ms_last=$ms_last+3600;
		$date_added=date("Y-m-d H:i:s",$ms_last);
		$ms_data=array();


		$data['externalId']=$email;
		$data['email']=$email;
		$data['lastName']=$lname;
		$data['firstName']=$fname;
		$data['phone']=$phone;

		if ( $qCustomers = mysql_query("SELECT * FROM `retailCRM_customers` WHERE `email`='".$email."';") ) $nCustomers = mysql_num_rows($qCustomers);
		else $nCustomers = 0;

		if( $nCustomers==0 ){
			// ---
				$link='https://eco-u.retailcrm.ru/api/v5/customers/create?apiKey='.$retail_key;
				$senddata['customer']=json_encode($data);
				$res=crm_query_send($link,$senddata);

				$qInsert = mysql_query("
					INSERT INTO `retailCRM_customers` SET 
					`id_internal`='".(int)$res['id']."',
					`id_external`='".$email."',
					`firstname`='".$fname."',
					`email`='".$email."',
					`dublicates`=0,
					`created`=NOW()
				");

				$cust_id=$res['id'];
			// ---
		}
		else{
			$rowCustomer = mysql_fetch_assoc($qCustomers);
			$cust_id=$rowCustomer['id_internal'];
		}
	// ---
	
	$items_=$order=$items=$items_new=$data=null;
	$weight_all=0;
	$weight_ignore=0;

	// Get order options
		$resx=mysql_query("select MSP.ms_id,MSP.product_id,OOP.order_product_id,OOP.quantity,OOP.variant,OOP.amount,OOP.price from oc_order_product as OOP, ms_products as MSP where  MSP.product_id=OOP.product_id and OOP.order_id='$order_id'");
	
		while(list( $ms_pr_id,$msp_product_id,$opid,$quantity,$fasovka,$amount,$price)=mysql_fetch_row($resx)){

			$resx2=mysql_query("select MSV.ms_id,MSV.product_option_value_id  from oc_order_option as OOO, ms_variants as MSV where MSV.product_option_value_id=OOO.product_option_value_id and OOO.order_id='$order_id' 
				and OOO.order_product_id='$opid'");
			list( $ms_var_id, $msv_povid)=mysql_fetch_row($resx2);
			/*Подсчитываем общий вес всех товаров*/
			//1.Получем единицу измерения товара
			$resx3=mysql_query("select weight_class_id,weight from oc_product  where  product_id='".(int)$msp_product_id."'");
			list( $weight_class_id, $weight)=mysql_fetch_row($resx3);
			//echo $quantity." ".$weight."<br>";
			//2.Формируем вес
			
			//Если в МС не установлен вес для весовых товаров, то берём по умолчанию 1 кг
			if($weight=="0.00000000" && $weight_class_id==9){
				$weight="1";
			}
			
			//Если милилитры или граммы, то это и есть граммы или штуки
			if($weight=="0.00000000"){
				$weight_ignore=1;
			}
			else 
			{
				
				if($weight_class_id==8 || $weight_class_id==2 || $weight_class_id==1 || $weight_class_id==7)
				{
					$weight_all=$weight_all+round(($quantity*$weight));
					
				}
				//Если килограммы, то тоже самое но умножаем на 1000
				if($weight_class_id==9)
				{
					$weight_all=$weight_all+(round(($quantity*$weight)*1000));
					
				}
			}
			
			

			$extid=$msp_product_id;
			if($msv_povid) $extid.="#$msv_povid";

			if($quantity) $items_[$extid][]=array('quantity'=>(float)$quantity, 'amount'=>(float)$amount, 'initialPrice'=>(double)$price,
				'fasovka'=>$fasovka);	
		} // while
	// ...

	// Array of products
		$total_new=0;	

		foreach($items_ as $ki=>$vi){		
			// ---
		 	
				$quantity=0;
				$sum=0;		
				$gnum=1;
				$allfasovka=null;
				
				foreach($vi as $ki2=>$vi2){
					// ---
						$quantity=$quantity+$vi2['quantity'];
						$sum+=$vi2['quantity']*$vi2['initialPrice'];
						$allfasovka[]=array('name'=>'Фасовка '.$gnum,'value'=>$vi2['fasovka']."кг X ".$vi2['amount']);
						$gnum++;
					// ---
				}
				
				$newprice=round($sum/$quantity);		
				$items_new[]=array('offer'=>array('externalId'=>$ki),'quantity'=>(float)$quantity, 'initialPrice'=>(double)$newprice, 'properties'=>$allfasovka);
				
				// Sum total
				$total_new+=$quantity*round($newprice);

			// ---
		}	
	// ...

	// Discount
	    $discval=$discvalproc=0;
	    $resxxx=mysql_query("SELECT value from oc_order_total where order_id='".$order_id."' and code='coupon'");
	    
	    list($discval)=mysql_fetch_row($resxxx);
		
		if(!$discval){
		    	$resxxx=mysql_query("SELECT value from oc_order_total where order_id='".$order_id."' and code='discount'");
		        list($discval)=mysql_fetch_row($resxxx);
		}

	    $resxxx=mysql_query("SELECT value from oc_order_total where order_id='".$order_id."' and code='discount_percentage'");
	    list($discvalproc)=mysql_fetch_row($resxxx);
		
		if($discval&&$discvalproc){
			$tmpdiscval=$total_new*$discvalproc/100;
			if($discval<$tmpdiscval) {
				unset( $discval);
				//$disval=$tmpdiscval;
				//unset($discvalproc);
			}else 	unset( $discvalproc);
		}


        if($discval!=0) $order['discountManualAmount']=(double)$discval;
        if($discvalproc!=0) $order['discountManualPercent']=(double)$discvalproc;
    // ---
		
    // Shipping
		$add_text = "";
		if($shipping_postcode!=''){$add_text.=$shipping_postcode.", ";}
		if($shipping_country!=''){$add_text.=$shipping_country.", ";}
		if($shipping_city!=''){$add_text.=$shipping_city.", ";}
		if($shipping_address_1!=''){$add_text.=$shipping_address_1;}
		if($shipping_address_2!=''){$add_text.=", ".$shipping_address_2;}

			
		$tmp=explode(".",$shipping_code);
		$delivery_code = $tmp[0];
		$order['shipmentStore']='eco-u';
		$vr=$delivery_code;

		if($delivery_code=="mkadout") {
			$vr="mkad";
		}
		if($delivery_code=="free") {
			$vr="flat";
		}
		if($delivery_code=="flat") {
			$vr="flat-pay";
		}


		// Get delivery net cost
			$deliveryNetCost = 0;

			if($delivery_code != "free") {
				// ---
					if ( $qShippingNetCost = mysql_query("SELECT * FROM `oc_setting` WHERE `code`='".$delivery_code."';") ) $nShippingNetCost = mysql_num_rows($qShippingNetCost);
					else $nShippingNetCost = 0;


					if( $nShippingNetCost>0 ){
						// ---
							$mainCost = 0;
							$netCost = 0;

							while ($shippingNetCostRow = mysql_fetch_assoc($qShippingNetCost)) {
								
								if( $shippingNetCostRow['key'] == $delivery_code.'_cost' ) { $mainCost = $shippingNetCostRow['value']; }
								if( $shippingNetCostRow['key'] == $delivery_code.'_netcost' ) { $netCost = $shippingNetCostRow['value']; }
							
							}

							$deliveryNetCost = $deliveryCost + ($mainCost-$netCost);
						// ---
					}
				// ---
			}
		// ---


        $order['delivery'] = array(
			'code' => !empty($vr) ? $vr:0,
			'cost' => (double)$deliveryCost,
			'netCost' => (double)$deliveryNetCost,
			'address' => array('text' => $add_text)
		);
    // ---

    // Init
		$link='https://eco-u.retailcrm.ru/api/v5/orders/create?apiKey='.$retail_key;
		$order['createdAt']=$date_added;
	   	$order['items']=$items_new;
		$order['number']='IM'.$order_id;
		if($weight_ignore==0) { $order['weight']=$weight_all; }
		
		$order['externalId']=$order_id;
		$order['lastName']=$lname;
	// ---
				
				
	if($cust_id) $order['customer']['id']=$cust_id;
	$order['customerComment']=$comm;

	$tmpd=explode(" ",$delivery_time);

	$tmpd3=explode(".",$tmpd[0]);
	$tmpd2=explode("-",$tmpd[1]);

	if(count($tmpd)>0){
		if($tmpd3[0]) $order['delivery']['date']=$tmpd3[2]."-".$tmpd3[1]."-".$tmpd3[0];
		if($tmpd2[0]) $order['delivery']['time']['from']=$tmpd2[0];
		if($tmpd2[1]) $order['delivery']['time']['to']=$tmpd2[1];
	}

	if($payment_method=='Банковской картой на сайте') $pmethod='e-money';
	else $pmethod='cash';

	
	$deliveryCost=round((double)$deliveryCost,0);


	//Новая сумма исключает глюки при пересчёте сложных цен
	$discount_real = 0;
		
	if($discvalproc) { $discount_real=(double)$total_new*$discvalproc/100; }
	if($discval) { $discount_real=$discval; }
	$discount_real = round($discount_real, 2);


	$total_pay_new=round($total_new+$deliveryCost, 2) - $discount_real;

	
	if($pmethod=='e-money' && $order_status_id==20) {
		$order['payments'][]=array('externalId'=>$order_id, 'type'=>$pmethod,'amount'=>(double)$total_pay_new, 'paidAt' => $date_added, 'status'=>'paid');
		$order['status']="new";
	}
	else {
		$order['payments'][]=array('externalId'=>$order_id, 'type'=>$pmethod,'amount'=>(double)$total_pay_new, 'paidAt' => $date_added, 'status'=>'not-paid');
	}

	$order['firstName']=$fname;
	$order['phone']=$phone;
	$order['email']=$email;

	$order['orderMethod']="shopping-cart";		
	
	if($order_status_id==0){
		$order['orderMethod']="missed-order";
		$order['status']="lost-order";
	}
	
	//Передаём ROISTAT_ID
	if($roistat_id!=""){
		$order['customFields']['roistat']=$roistat_id;
	}
	
		
   	$senddata['order']=json_encode($order);
	//header('Content-Type: application/json');
	echo $senddata['order'];


	if($order['status']!="lost-order"){

		$json=crm_query_send($link,$senddata);

		if (!$json['success']) {
			echo "<br><span style='color:#ff0000'>ERROR: ".$json['errorMsg']."</span><br><br>";
			
			if ($json['errorMsg']!='Order already exists.'){
				// Check log
					if ( $qLogs = mysql_query("SELECT * FROM `retailCRM_errors` WHERE `id_order`=".$order_id.";") ) $nLogs = mysql_num_rows($qLogs);
					else $nLogs = 0;

					if( $nLogs==0 ){
						// ---
							$qInsert = mysql_query("
								INSERT INTO `retailCRM_errors` SET 
								`id_order`='".$order_id."',
								`id_externalid`='IM".$order_id."',
								`message`='".$json['errorMsg']."'
							");

							$log .= "ID заказа: ".$order_id." <span style='color:#ff0000'>ERROR: ".$json['errorMsg']."</span><br>";
							
							foreach($order["items"] as $item){
								echo "quantity = ".$item["quantity"].", price = ".$item["initialPrice"]."<br>";
							}
						// ---
					}
				// ---
			}
		}
		else {
			echo "<br><span style='color:#00ff00'>Order upload successfuly</span><br><br>";
		}
	}
	else {
		echo "lost-order. not load<br><br>";
	}

//---
}

// Send log
	if( $log != ""){
		// ---
			$subject = "Ошибка отправки заказа(ов) в RetailCRM";
		    $message = "<b>Лог ошибок:</b><br><br>".$log;

		    $headers = "From: noreoly@eco-u.ru\r\n";
		    $headers .= "Reply-To: noreoly@eco-u.ru\r\n";
		    $headers .= "MIME-Version: 1.0\r\n";
		    $headers .= "Content-Type: text/html; charset=utf-8\r\n";

			foreach ($alertsList as $key => $to) {
		        // Semd email
		        if (mail($to, $subject, $message, $headers)) {
		            $mess .= 'Send to client '.$to;
		        } else {
		            $mess .= 'Do not send to client '.$to;
		        }
			}
		// ---
	}
// ---


$link='https://eco-u.retailcrm.ru/api/v5/customers/?apiKey='.$retail_key;
$res=crm_query($link);