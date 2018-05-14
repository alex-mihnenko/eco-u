<?php

include("opencart_inc.php");

$order_nubmer=0;

if(is_numeric($_GET["n"])){
	$order_nubmer = $_GET["n"];
	echo"!!!".$order_nubmer;
}

$time=time();

$res_orders=mysql_query("select payment_method,customer_id,order_id, firstname,lastname,email,telephone,comment,total,order_status_id,date_added,shipping_code,shipping_postcode,shipping_city,
		shipping_country,shipping_address_1,shipping_address_2,delivery_time from oc_order where order_id = ".$order_nubmer." order by date_modified desc limit 0,20");

$i=1;

while(list($payment_method,$customer_id,$order_id,$fname,$lname,$email,$phone,$comm,$total,$order_status_id,$date_added,$shipping_code,$shipping_postcode,$shipping_city,
		$shipping_country,$shipping_address_1,$shipping_address_2,$delivery_time)=mysql_fetch_row($res_orders)){
	
	//Получаем правим е-мейл если он пустой, чтобы можно было загрузить заказ без ошибки
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
	
	echo "<b>ORDER #".$order_id."</b><br>";
		
	$res=mysql_query("select value from oc_order_total where code='shipping' and order_id='$order_id'");
	list($deliveryCost)=mysql_fetch_row($res);
	if(!isset($deliveryCost)) $deliveryCost=0;

	///CONTACTS
	
	$ms_last_tmp=DateTime::createFromFormat("Y-m-d H:i:s",$date_added);
	$ms_last= $ms_last_tmp->getTimestamp();

	$ms_last=$ms_last+3600;
	$date_added=date("Y-m-d H:i:s",$ms_last);
	$ms_data=array();


	$res=mysql_query("select ms_id from ms_contacts where shop_id='$phone'");
	list($ms_id)=mysql_fetch_row($res);

	$data['externalId']=$email;
	$data['email']=$email;
	$data['lastName']=$lname;
	$data['firstName']=$fname;
	$data['phone']=$phone;

	if(!$ms_id){
		$link='https://eco-u.retailcrm.ru/api/v5/customers/create?apiKey='.$retail_key;
		$senddata['customer']=json_encode($data);
		$res=crm_query_send($link,$senddata);
		mysql_query("insert into ms_contacts set ms_id='{$res['id']}', shop_id='$phone'");
		$cust_id=$res['id'];
	}else{
		$cust_id=$ms_id;
	//	$link='https://online.moysklad.ru/api/remap/1.1/entity/counterparty'.$ms_id;
	//	$result=ms_query_send($link,$ms_data,'POST');
	}
	
	$items_=$order=$items=$items_new=$data=null;
$weight_all=0;
			$weight_ignore=0;
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
	}
	
	//echo "WEIGHT = ".$weight_all." g<br>";
	
	//echo "<pre>";
	//echo var_dump($items_);
	//echo "</pre>";
	
	///////////// ЗАПОЛНЯЕМ МАССИВ ТОВАРОВ В ЗАКАЗЕ  ///////////
	$total_new=0;	
	foreach($items_ as $ki=>$vi){		
	 	
		$quantity=0;
		$sum=0;		
		$gnum=1;
		$allfasovka=null;
		
		foreach($vi as $ki2=>$vi2){
			$quantity=$quantity+$vi2['quantity'];
			$sum+=$vi2['quantity']*$vi2['initialPrice'];
			$allfasovka[]=array('name'=>'Фасовка '.$gnum,'value'=>$vi2['fasovka']."кг X ".$vi2['amount']);
			$gnum++;
		}	
		//echo "old = ".$newprice;
		$newprice=round($sum/$quantity);		
		$items_new[]=array('offer'=>array('externalId'=>$ki),'quantity'=>(float)$quantity, 'initialPrice'=>(double)$newprice,
			'properties'=>$allfasovka);
		$total_new+=$quantity*$newprice;
		//echo "new = ".$tolal_new."<br>";
		
	}	
	//////////////////////////////////////////////////////////////////	
	
	
//	$res=mysql_query("select ms_id from ms_leads where shop_id='$order_id'");
//	list($ms_lead_id)=mysql_fetch_row($res);

//	if(!$ms_lead_id){

		$link='https://eco-u.retailcrm.ru/api/v5/orders/create?apiKey='.$retail_key;
		$order['createdAt']=$date_added;
       	$order['items']=$items_new;
		$order['number']='IM'.$order_id;
		if($weight_ignore==0)
		{
			$order['weight']=$weight_all;
		}
//		$order['paymentType']=$PAY_ARRAY[$payment_method];
		$order['externalId']=$order_id;
		$order['lastName']=$lname;
//		$order['delivery']['time']['custom']=$dostime;
//		$order['delivery']['date']=$delivery_year."-".$delivery_month."-".$delivery_day;
//		$order['delivery']['time']['from']=$delivery_time_from.":00";
//		$order['delivery']['time']['to']=$delivery_time_to.":00";

//		$order['delivery']['address']['city']=$city;
//		$order['delivery']['address']['text']=$addr;


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
//			 	$disval=$tmpdiscval;
//				unset($discvalproc);
			}else 	unset( $discvalproc);
		}


	        if($discval!=0) $order['discountManualAmount']=(double)$discval;
	        if($discvalproc!=0) $order['discountManualPercent']=(double)$discvalproc;


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
	         if($delivery_code=="mkadout")
	         {
	         	$vr="mkad";
	         }
	          if($delivery_code=="free")
	         {
	         	$vr="flat";
	         }
	          if($delivery_code=="flat")
	         {
	         	$vr="flat-pay";
	         }
                 $order['delivery'] = array(
                    // 'code' => !empty($delivery_code) ? $delivery_code:0, //$settings['retailcrm_delivery'][$delivery_code] : '',
                     'code' => !empty($vr) ? $vr:0, //$settings['retailcrm_delivery'][$delivery_code] : '',
                    // 'service'=>array(
                    //'deliveryType' => !empty($vr) ? $vr:0,
//	                   
					//),
                     'cost' => (double)$deliveryCost,
                     'address' => array(
//	                    'index' => $shipping_postcode,
//	                    'city' => $shipping_city,
	                    'text' => $add_text
					)
				
				);				
				
				
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
		
		//echo "TYPE OF PAYMENT: ".$payment_method;

		//if($discvalproc) $total=$total*(100-$discvalproc)/100;

		$discount_real = 0;
		if($discvalproc) {
			$discount_real=(double)$total_new*$discvalproc/100;
//			$total_new=(double)$total_new*(100-$discvalproc)/100;
		}
		
		if($discval) {
			$discount_real=$discval;
			//$total_new=$total_new-$discval;
		}
		
		//$total_new = $total_new-$discount_real;
		
		//Посчитать сумму на основе позиций 
		//$order['items']=$items;
		
		echo "<pre>";
			//var_dump($order['items']);
		echo "</pre>";
		//echo "PARTS <BR>";
		/*
		$ss=0;
		foreach($order['items'] as $item){
			echo "PRICE = ".$item['initialPrice']*$item['quantity']."<br>";
			$ss=$ss+$item['initialPrice']*$item['quantity'];
		}
		
		echo "TOTAL_PRICE = ".$ss."<br>";
		*/
		//$total_new=round($total_new,2);
		$deliveryCost=round((double)$deliveryCost,0);
/*
		echo "TOTAL_NEW = ".$total_new."<br>";

		
		echo "DISCOUNT=".$discval."<br>";
		echo "DISCOUNT % =".(double)$discvalproc."<br>";
		echo "DISCOUNT REAL =".$discount_real."<br>";
		echo "DELIVERY=".$deliveryCost."<br>";
		
*/		
		//$total_pay=$total+$deliveryCost-$discval;

		//Новая сумма исключает глюки при пересчёте сложных цен
		$total_pay_new=$total_new+$deliveryCost-$discount_real;

		
//		echo "TOTAL_PAY=".$total_pay_new."<br>";		
		
		//$order['payments'][]=array('type'=>$pmethod,'amount'=>$total_pay_new);
		
		$order['payments'][]=array('externalId'=>$order_id, 'type'=>$pmethod,'amount'=>(double)$total_pay_new);
		
		$order['firstName']=$fname;
		$order['phone']=$phone;
		$order['email']=$email;

//		echo "TOTAL = ".$total;
		//$order['payments'][]=array('externalId'=>$order_id,'amount'=>$total,'type'=>'cash');

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
		
		if($order['status']!="lost-order"){					
			$json=crm_query_send($link,$senddata);
			if (!$json['success']) 
			{
				echo '<pre>';
					var_dump($json);
				echo '</pre>';
				
				echo "<span style='color:#ff000'>ERROR: ".$json['errorMsg']."</span><br><br>";
				if ($json['errorMsg']!='Order already exists.'){
					echo '<pre>';
					echo var_dump($order);
					echo '</pre>';
					foreach($order["items"] as $item){
					echo "quantity = ".$item["quantity"].", price = ".$item["initialPrice"]."<br>";
					}
					
				}
			}
			else {
				echo "<span style='color:#00ff00'>Order upload successfuly</span><br><br>";
			}
		}
		else {
			echo "lost-order. not load<br><br>";
		}
		
		
//	}

}

$link='https://eco-u.retailcrm.ru/api/v5/customers/?apiKey='.$retail_key;
$res=crm_query($link);

