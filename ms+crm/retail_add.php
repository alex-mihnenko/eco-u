<?php

include("opencart_inc.php");

$link="https://eco-u.retailcrm.ru/api/v5/reference/statuses?apiKey=".$retail_key;
$res=crm_query($link);


foreach($res['statuses'] as $k=>$v){
 $STAT[$k]=$v['name'];
}

if($_GET['type']){

	//�������� �� retailCRM ������ �� ������
	$link="https://eco-u.retailcrm.ru/api/v5/orders/{$_GET['id']}?by=id&apiKey=".$retail_key;
	$res=crm_query($link);

	//��������� ��� �� ������ ������ � oc_order � ���� ���, �� ���������
	$rk=mysql_query("select order_id from oc_order where order_id='{$res['order']['externalId']}'");
	list($order_id)=mysql_fetch_row($rk);
	
	if(!$order_id) {
		$dateadd=date("Y-m-d H:i:s",time());
		mysql_query("insert into oc_order set firstname='{$res['order']['firstName']}' ,lastname='{$res['order']['lastName']}' ,
			email='{$res['order']['email']}' , telephone='{$res['order']['phone']}' ,store_url='http://eco-u.ru/',
			date_added='$dateadd' ,date_modified='$dateadd', store_name='������� ����������� ���-�������', 
			language_id	='1' ,currency_id = '1', currency_code='RUB'");
		$num=mysql_insert_id();
	}else{ exit(); }

	//��������� � CRM ������� ID ��� ������ = id ������ � oc_order
	$order=null;
	$order['externalId']=$num;
	$senddata['order']=json_encode($order);
	$link='https://eco-u.retailcrm.ru/api/v5/orders/'.$_GET['id'].'/edit?by=id&apiKey='.$retail_key;
	$json=crm_query_send($link,$senddata);
	
	//����� ���� �� ���������� ������
	$fp = fopen("retail_add.log", 'a+');
        fwrite($fp, "select order_id from   oc_order  where  order_id='{$res['order']['externalId']}'\n $link \n order_id:".
json_encode($res)."\n - $num - ".$res['order']['externalId']." - \n ".json_encode($json)."\n");
	fclose($fp);
	
	//�������� id �������, �������� ������������ �������� �� ��������
	$resj=mysql_query("select order_status_id from oc_order_status where  name='".$STAT[$res['order']['status']]."'");
	list($order_status_id)=mysql_fetch_row($resj);

/////��������� ���������� ����� ����. ����� �� �����???? ���������� $VMS ����� ����� �� ������������ ///
//	$num=$res['order']['externalId'];
	$VMS=null;
	$link='https://online.moysklad.ru/api/remap/1.1/entity/customerorder?search='.$num;
	$resx=ms_query($link);

	foreach($resx['rows'] as $kms=>$vms){
		 if($vms['name']==$num) {
				 $VMS=$vms;
	}}
/////�����. ��������� ���������� ����� ����. ����� �� �����???? ///
	

	//��������� ���� �� � order_products ������ ������ ������. ���� ���, �� ���������. ���� ����, �� ��������� ������ �� ���������� � ����
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



	//��������� ���� �� ������ � ���������� � ������. ���� ���, �� ��������� ������ � oc_customer
	$resx=mysql_query("select customer_id from oc_order where telephone='".$res['order']['phone']."'");
	list($cust_id)=mysql_fetch_row($resx);
	if(!$cust_id) {
		mysql_query("insert into oc_customer set firstname='{$res['order']['firstName']}' ,lastname='{$res['order']['lastName']}' ,
			email='{$res['order']['email']}' , telephone='{$res['order']['phone']}' ,
			date_added='$dateadd' ,	language_id	='1' ,customer_group_id = '1'");
		$cust_id=mysql_insert_id();

	}
	mysql_query("update  oc_order set customer_id='$cust_id',customer_group_id = '1',total='{$res['order']['totalSumm']}', order_status_id='$order_status_id' where  order_id='$num'");
	
	//��������� ������ �� ������ � oc_order_total
	mysql_query("insert into  oc_order_total set value='{$res['order']['totalSumm']}'  ,  order_id='$num', code='total', title='�����'");
	mysql_query("insert into  oc_order_total set value='{$res['order']['summ']}'  ,  order_id='$num' , code='sub_total', title='�����'");
	mysql_query("insert into oc_order_total set value='{$res['order']['cost']}'  ,  order_id='$num' , code='shipping' , title='�������� � ������������� ����������'");

	//����� ���� �� ������� ������.
	$fp = fopen("retail_upd.log", 'a+');
        fwrite($fp, "\nres 0:".json_encode($res));
	fclose($fp);

	//����� ��� ��� ����� �� �������
	$resx=mysql_query("select ms_id,demand_id from ms_leads where retailcrm_id='$num'");
	list($ms_lead_id,$ms_demand_id)=mysql_fetch_row($resx);

}


