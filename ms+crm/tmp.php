<?php

Header("Content-Type: text/html;charset=UTF-8");

include("opencart_inc.php");

var_dump(json_decode("{\"errors\":[{\"error\":\"\u041e\u0448\u0438\u0431\u043a\u0430 \u0444\u043e\u0440\u043c\u0430\u0442\u0430: \u043d\u0435\u043f\u0440\u0430\u0432\u0438\u043b\u044c\u043d\u043e\u0435 \u0437\u043d\u0430\u0447\u0435\u043d\u0438\u0435 href \u0434\u043b\u044f meta \u043f\u043e\u043b\u044f 'customerOrder'\",\"code\":2013,\"moreInfo\":\"https:\/\/online.moysklad.ru\/api\/remap\/1.1\/doc#\u043e\u0431\u0440\u0430\u0431\u043e\u0442\u043a\u0430-\u043e\u0448\u0438\u0431\u043e\u043a-2013\",\"line\":1,\"column\":27}]}"));
exit();
$num=130;
$link='https://online.moysklad.ru/api/remap/1.1/entity/customerorder?search='.$num;
$res=ms_query($link);

foreach($res['rows'] as $k=>$v){
 if($v['name']==$num) var_dump($v);

}
