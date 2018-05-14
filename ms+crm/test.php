<?php
include("opencart_inc.php");
/*
for($i=479;$i<=656;$i++)
{
$link="https://eco-u.retailcrm.ru/api/v5/customers?page={$i}&apiKey=".$retail_key;
$res=crm_query($link);
print_r($res);

foreach ($res['customers'] as $r)
{
	if(isset($r['customFields']['moyskladexternalid']))
	{
		echo " ".$r['id']. " " ;
		
$link="https://eco-u.retailcrm.ru/api/v5/customers/{$r['id']}/edit?by=id&apiKey=".$retail_key;
$mass['customFields']=array('moyskladexternalid' => '');
$senddata['customer']=json_encode($mass);
$json=crm_query_send($link,$senddata);

	}
}

$fp = fopen("log2.txt", 'a+');
			        fwrite($fp, "\n ".$i."\n");
				fclose($fp);

}*/


/*
$link="https://eco-u.retailcrm.ru/api/v5/customers/14805/edit?by=id&apiKey=".$retail_key;
$mass['customFields']=array('moyskladexternalid' => '');
//print_r($mass);
$senddata['customer']=json_encode($mass);
//print_r($senddata);
$json=crm_query_send($link,$senddata);
print_r($json);*/

/*$num="12345";
$resx=mysql_query("select count(*) as kol from ms_test where numb='$num' ");
$mass=mysql_fetch_row($resx);
print_r($mass);
mysql_query("insert into   ms_test set numb='$num'");*/

/*$link3='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/?filter=name=16616C';
$json3 = ms_query($link3);
print_r($json3);*/


/*$link="https://eco-u.retailcrm.ru/api/v5/orders/16616?by=id&apiKey=".$retail_key;
	$res=crm_query($link);
	print_r($res);


$link="https://eco-u.retailcrm.ru/api/v5/orders/16667?by=id&apiKey=".$retail_key;
	$res=crm_query($link);
	print_r($res);*/




/*$link='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/?filter=name=IM20814';
$json = ms_query($link);
print_r($json);
$ms_lead_id=$json['rows'][0]['id'];
$POS=ms_query($json['rows'][0]['positions']['meta']['href']);
//print_r($POS);
foreach($POS['rows'] as $kp=>$vp){
					$vp['reserve']=0;
					//$vp['shipped']=0;
					$ms_data['positions'][]=$vp;	
				}
				
				$link='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/'.$ms_lead_id;
				$json = ms_query_send($link, $ms_data, 'PUT');
				print_r($json);*/







/*$link='https://online.moysklad.ru/api/remap/1.1/entity/customerorder?search=20580';
	$resx=ms_query($link);
	print_r($resx);
$num="20580";
foreach($resx['rows'] as $kms=>$vms){
		 if($vms['name']=="IM".$num) { //Заказы в retailCRM с префиксом и потому название отличается от id заказа
				 $VMS=$vms;
	}}
	print_r($VMS);*/


echo 'test';

$time=time();

$res_orders=mysql_query("select payment_method,customer_id,order_id, firstname,lastname,email,telephone,comment,total,order_status_id,date_added,shipping_code,shipping_postcode,shipping_city,
		shipping_country,shipping_address_1,shipping_address_2,delivery_time from oc_order where order_id='20895'");

$i=1;

while(list($payment_method,$customer_id,$order_id,$fname,$lname,$email,$phone,$comm,$total,$order_status_id,$date_added,$shipping_code,$shipping_postcode,$shipping_city,
		$shipping_country,$shipping_address_1,$shipping_address_2,$delivery_time)=mysql_fetch_row($res_orders)){
			
			
			
			
			
			
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
		//echo $weight_class_id." ".$weight;
		//2.Формируем вес
			if($weight=="0.00000000"){
			$weight_ignore=1;
		}
		else 
		{
			
			if($weight_class_id==8 || $weight_class_id==2 || $weight_class_id==1)
			{
				$weight_all=$weight_all+round(($quantity*$weight)); echo $quantity." ".$weight." gr<br>";
				
			}
			//Если литры или килограммы, то тоже самое но умножаем на 1000
			if($weight_class_id==7 || $weight_class_id==9)
			{
				$weight_all=$weight_all+(round(($quantity*$weight)*1000));echo $quantity." ".$weight." kg<br>";
				
			}
		}
		
		
		echo $weight_all;
		
		
		
		$extid=$msp_product_id;
		if($msv_povid) $extid.="#$msv_povid";

		if($quantity) $items_[$extid][]=array('quantity'=>(float)$quantity, 'amount'=>(float)$amount, 'initialPrice'=>(double)$price,
			'fasovka'=>$fasovka);			
	}
	
	
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

		$newprice=$sum/$quantity;		
		$items_new[]=array('offer'=>array('externalId'=>$ki),'quantity'=>(float)$quantity, 'initialPrice'=>(double)$newprice,
			'properties'=>$allfasovka);
		$total_new+=round($quantity*$newprice*100)/100;		
	}	
	//////////////////////////////////////////////////////////////////	
	
	
	
	
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		}
		
		
		print_r($items_new);
	



?>