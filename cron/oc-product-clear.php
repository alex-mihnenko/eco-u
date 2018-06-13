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

	$log = [];
// ---

// Processing
	// Clear new product status 
	$q = "UPDATE `".DB_PREFIX."product` p SET p.new = 0 WHERE p.date_new < NOW() - INTERVAL 7 DAY AND p.new = 1;";
	$result = $db->query($q);

	if ($db->query($q) === TRUE) {
	    $log[] = 'Success clearing new product status';
	} else {
	    $log[] = 'Error clearing new product status: ' . $db->error;
	}
// ---


$res['log'] = json_encode($log);
$res['mess']='OK';
echo json_encode($res); exit;