<?php
// Init
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	include("../config.php");

	$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

	if ($db->connect_error) {
	    die("Connection failed to db: " . $db->connect_error);
	}

	$db->set_charset("utf8");

	define('AUTH_DATA', 'admin@mail195:b41fd841edc5');
	define('RETAILCRM_KEY', 'AuNf4IgJFHTmZQu7PwTKuPNQch5v03to');


	$config = json_decode(file_get_contents('retailcrm-processor-config.json'));


	$log = [];
// ---


// Check db customer
    $q = "SELECT * FROM `retailCRM_customers` WHERE `dublicates`>0;";
    
    $dbcustomer = $db->query($q);

    if ( $dbcustomer->num_rows > 0 ) {
    	echo '
    	<br>
    	<p>Всего записей '.$dbcustomer->num_rows.'</p>
    	<br>

    	<table border="1" cellpadding="10">
    		<thead>
    			<th>ID</th>
    			<th>Имя</th>
    			<th>Дата создания</th>
    			<th>Дублей</th>
    			<th>Ссылка</th>
    		</thead>

    		<tbody>
    	';

    	while ( $row = $dbcustomer->fetch_assoc() ) {
    		// ---
    			echo '
    				<tr>
    					<td>'.$row['id_internal'].'</td>
    					<td>'.$row['firstname'].'</td>
    					<td>'.$row['created'].'</td>
    					<td>'.($row['dublicates']-1).'</td>
    					<td><a href="https://eco-u.retailcrm.ru/customers/'.$row['id_internal'].'" target="_blank"> Редактировать</a></td>
    				</tr>
    			';
    		// ---
    	}

    	echo '
    		</tbody>
    	</table>
    	<br>
    	<hr>
    	';
    }
// ---



$res['log'] = json_encode($log);
$res['mess']='OK';
echo json_encode($res); exit;