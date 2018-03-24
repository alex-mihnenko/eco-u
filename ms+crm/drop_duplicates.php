<?php

include("opencart_inc.php");

$duplicate_orders=mysql_query("SELECT DISTINCT p1.telephone AS phone, p1.order_id AS order_id, p1.date_added AS date_added, (SELECT name FROM oc_order_status os WHERE os.order_status_id = p1.order_status_id) AS status FROM oc_order p1, oc_order p2
WHERE p1.telephone = p2.telephone AND p1.total = p2.total AND p1.order_id <> p2.order_id AND DATEDIFF(p1.date_added, p2.date_added) >= -1 AND DATEDIFF(p1.date_added, p2.date_added) <= 1 GROUP BY p2.order_id ORDER BY phone ASC");

$i=0;
while(list($phone, $order_id, $date_added, $status)=mysql_fetch_row($duplicate_orders)) {
$i++;
    //echo $status;

    /*
   if($status=='Отменен' || $status=='Новый'){
       mysql_query("DELETE FROM oc_order WHERE order_id=".$order_id);
       mysql_query("DELETE FROM oc_order_product WHERE order_id".$order_id);
       mysql_query("DELETE FROM oc_order_total WHERE order_id=".$order_id);
   }
*/
}
echo $i;