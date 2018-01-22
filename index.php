<?php
// Version
define('VERSION', '2.3.0.2');
ini_set("error_reporting",E_ALL & ~E_NOTICE);
ini_set("display_errors",1);
ini_set("error_log","");
function errPrint(){
    print_r(error_get_last());
}
register_shutdown_function("errPrint");

if(in_array($_SERVER['REQUEST_URI'], Array('/', '/index.php'))) 
{
	$_REQUEST['_route_'] = 'eda/';
	$_GET['_route_'] = 'eda/';
}

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: install/index.php');
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('catalog');