<?php

include("opencart_inc.php");

$order_nubmer=21059;

if(is_numeric($_GET["n"])){
	$order_nubmer = $_GET["n"];
	echo"!!!".$order_nubmer;
}

$time=time();

$res_orders=mysql_query("select payment_method,customer_id,order_id, firstname,lastname,email,telephone,comment,total,order_status_id,date_added,shipping_code,shipping_postcode,shipping_city,
		shipping_country,shipping_address_1,shipping_address_2,delivery_time from oc_order where order_id = ".$order_nubmer." order by date_modified desc limit 0,1");

$i=1;

while(list($payment_method,$customer_id,$order_id,$fname,$lname,$email,$phone,$comm,$total,$order_status_id,$date_added,$shipping_code,$shipping_postcode,$shipping_city,
		$shipping_country,$shipping_address_1,$shipping_address_2,$delivery_time)=mysql_fetch_row($res_orders)){
	
	echo "<b>ORDER #".$order_id."</b><br>";
		
	$payment = array();
	$payment['order']['externalId'] = $order_nubmer;
	$payment['status'] = 'paid';
	$payment['paidAt'] = $date_added;	
	$payment['amount'] = $total;
	$payment['type']='e-money';
	$site = 'eco-u-ru';	

	$senddata = array();
	$senddata['site'] = $site;
	$senddata['payment'] = json_encode($payment); 
	
	$link='https://eco-u.retailcrm.ru/api/v5/orders/payments/create?apiKey='.$retail_key;		
	echo "<pre>";
	echo var_dump($senddata);
	echo "</pre>";

	$json=crm_query_send($link,$senddata);
	echo $json;

	echo '<pre>';
		var_dump($json);
	echo '</pre>';	

}
