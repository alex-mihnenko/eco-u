<?php

include("opencart_inc.php");

$link="https://eco-u.retailcrm.ru/api/v5/reference/statuses?apiKey=".$retail_key;
$res=crm_query($link);


foreach($res['statuses'] as $k=>$v){
 $STAT[$k]=$v['name'];
}

if($_GET['type']){

	//Получаем из retailCRM данные по заказу
	$link="https://eco-u.retailcrm.ru/api/v5/orders/{$_GET['id']}?by=id&apiKey=".$retail_key;
	$res=crm_query($link);

	//Проверяем нет ли такого заказа в oc_order и если нет, то добавляем
	$rk=mysql_query("select order_id from oc_order where order_id='{$res['order']['externalId']}'");
	list($order_id)=mysql_fetch_row($rk);
	
	if(!$order_id) {
		$dateadd=date("Y-m-d H:i:s",time());
		mysql_query("insert into oc_order set firstname='{$res['order']['firstName']}' ,lastname='{$res['order']['lastName']}' ,
			email='{$res['order']['email']}' , telephone='{$res['order']['phone']}' ,store_url='http://eco-u.ru/',
			date_added='$dateadd' ,date_modified='$dateadd', store_name='Магазин натуральных эко-товаров', 
			language_id	='1' ,currency_id = '1', currency_code='RUB'");
		$num=mysql_insert_id();
	}else{ exit(); }

	//Добавляем в CRM внешний ID для заказа = id заказа в oc_order
	$order=null;
	$order['externalId']=$num;
	$senddata['order']=json_encode($order);
	$link='https://eco-u.retailcrm.ru/api/v5/orders/'.$_GET['id'].'/edit?by=id&apiKey='.$retail_key;
	$json=crm_query_send($link,$senddata);
	
	//Пишем логи по добавлению заказа
	$fp = fopen("retail_add.log", 'a+');
        fwrite($fp, "select order_id from   oc_order  where  order_id='{$res['order']['externalId']}'\n $link \n order_id:".
json_encode($res)."\n - $num - ".$res['order']['externalId']." - \n ".json_encode($json)."\n");
	fclose($fp);
	
	//Уточняем id статуса, проверив соответствие статусов по названию
	$resj=mysql_query("select order_status_id from oc_order_status where  name='".$STAT[$res['order']['status']]."'");
	list($order_status_id)=mysql_fetch_row($resj);

/////Абсолютно непонятный кусок кода. Зачем он нужен???? Переменная $VMS далее нигде не используется ///
//	$num=$res['order']['externalId'];
	$VMS=null;
	$link='https://online.moysklad.ru/api/remap/1.1/entity/customerorder?search='.$num;
	$resx=ms_query($link);

	foreach($resx['rows'] as $kms=>$vms){
		 if($vms['name']==$num) {
				 $VMS=$vms;
	}}
/////КОНЕЦ. Абсолютно непонятный кусок кода. Зачем он нужен???? ///
	

	//Проверяем есть ли в order_products состав нашего заказа. Если нет, то добавляем. Если есть, то обновляем данные по количеству и цене
	$ndel=array();			
	foreach($res['order']['items'] as $kg=>$vg){
	        $resx=mysql_query("select order_product_id from oc_order_product where order_id='$num' and product_id='{$vg['offer']['externalId']}'");
		list($opid)=mysql_fetch_row($resx);

		$totalg=$vg['initialPrice']*$vg['quantity'];
		if(!$opid) {
			mysql_query("insert into   oc_order_product set order_id='$num', product_id='{$vg['offer']['externalId']}',name='{$vg['offer']['name']}',
				quantity='{$vg['quantity']}',price='{$vg['initialPrice']}',total ='$totalg'");
			$opid=mysql_insert_id();
		}
		else mysql_query("update   oc_order_product set quantity='{$vg['quantity']}',price='{$vg['initialPrice']}',total ='$totalg' where order_product_id='$opid'");
		$ndel[]=$opid;
	}    

        $resx=mysql_query("select order_product_id from oc_order_product where order_id='$num' ");
	while(list($opid)=mysql_fetch_row($resx)){
		 if(!in_array($opid,$ndel)) mysql_query("delete from  oc_order_product where  order_product_id='$opid'");
	}



	//Проверяем есть ли данные о покупателе в заказе. Если нет, то добавляем данные в oc_customer
	$resx=mysql_query("select customer_id from oc_order where telephone='".$res['order']['phone']."'");
	list($cust_id)=mysql_fetch_row($resx);
	if(!$cust_id) {
		mysql_query("insert into oc_customer set firstname='{$res['order']['firstName']}' ,lastname='{$res['order']['lastName']}' ,
			email='{$res['order']['email']}' , telephone='{$res['order']['phone']}' ,
			date_added='$dateadd' ,	language_id	='1' ,customer_group_id = '1'");
		$cust_id=mysql_insert_id();

	}
	mysql_query("update  oc_order set customer_id='$cust_id',customer_group_id = '1',total='{$res['order']['totalSumm']}', order_status_id='$order_status_id' where  order_id='$num'");
	
	//Добавляем данные по заказу в oc_order_total
	mysql_query("insert into  oc_order_total set value='{$res['order']['totalSumm']}'  ,  order_id='$num', code='total', title='Итого'");
	mysql_query("insert into  oc_order_total set value='{$res['order']['summ']}'  ,  order_id='$num' , code='sub_total', title='Сумма'");
	mysql_query("insert into oc_order_total set value='{$res['order']['cost']}'  ,  order_id='$num' , code='shipping' , title='Доставка с фиксированной стоимостью'");

	//Пишем логи по апдейту заказа.
	$fp = fopen("retail_upd.log", 'a+');
        fwrite($fp, "\nres 0:".json_encode($res));
	fclose($fp);

	//Зачем это код нужен не понятно
	$resx=mysql_query("select ms_id,demand_id from ms_leads where retailcrm_id='$num'");
	list($ms_lead_id,$ms_demand_id)=mysql_fetch_row($resx);

}


