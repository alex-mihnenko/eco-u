<?php

include("opencart_inc.php");


$link="https://eco-u.retailcrm.ru/api/v5/reference/statuses?apiKey=".$retail_key;
$res=crm_query($link);


foreach($res['statuses'] as $k=>$v){
 $STAT[$k]=$v['name'];
}

if($_GET['id']){
	$link="https://eco-u.retailcrm.ru/api/v5/orders/{$_GET['id']}?by=id&apiKey=".$retail_key;
	$res=crm_query($link);	
	$customer_id = 0;
	
	$customer_id = $res['order']['customer']['id'];
	
	
	$link="https://eco-u.retailcrm.ru/api/v5/customers/notes?filter[customerIds][]=".$customer_id."&apiKey=".$retail_key;			
//	$link="https://eco-u.retailcrm.ru/api/v5/customers/".$customer_id."?by=id&apiKey=".$retail_key;	
	$customer_notes=crm_query($link);	
	
	//echo "<pre>";
	//print_r($res['order']['customer']);
	//echo "</pre>";
	
	foreach ($customer_notes['notes'] as $key => $note ){
		$customer_notes['notes'][$key]['text'] = strip_tags($note['text'], '<br>');
	}
	
	//echo "<pre>";
	//print_r($customer_notes);
	//echo "</pre>";
	
	$customer_notes['cl_name'] = $res['order']['customer']['firstName'];
	
	$json = json_encode($customer_notes);
	echo $json;
	
}
