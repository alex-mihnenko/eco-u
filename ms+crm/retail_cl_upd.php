<?php

include("opencart_inc.php");


if($_GET['type']){




	$link="https://eco-u.retailcrm.ru/api/v5/customers/{$_GET['id']}?by=id&apiKey=".$retail_key;

	$res=crm_query($link);


	$resj=mysql_query("select address_id from oc_customer where  telephone='{$res['customer']['phones'][0]['number']}'");
	while(list($addr_id)=mysql_fetch_row($resj)){
		mysql_query("update oc_address set address_1='{$res['customer']['address']['text']}'  where address_id='$addr_id'");	
	}

	mysql_query("update oc_customer set firstname='{$res['customer']['firstName']}', lastname='{$res['customer']['lastName']}'
				, email='{$res['customer']['email']}'  where telephone='{$res['customer']['phones'][0]['number']}' ");



			$fp = fopen("retail_cl_upd.log", 'a+');
 		        fwrite($fp, json_encode($res)."\n");
 		        fwrite($fp, "update oc_customer set  firstname='{$res['customer']['firstName']}', lastname='{$res['customer']['lastName']}'
				, email='{$res['customer']['email']}' where telephone='{$res['customer']['phones'][0]['number']}' \n");

			fclose($fp);








}