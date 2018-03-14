<?php
	if(!defined('ENV_DEBUG')) define('ENV_DEBUG', true);
	error_reporting(E_ALL & ~E_NOTICE);
	mb_internal_encoding('UTF-8');
    date_default_timezone_set('Europe/Kiev');
    // ini_set('display_errors', 'On');

	$_PAGE = [];
	$_CURL = [];

    require_once($_SERVER['DOCUMENT_ROOT'].'/classes/HTTPCache.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/classes/FPN.php');    
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DB.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Encoder.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Memcached.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Template.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/View.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Worker.php');
    if(isset(FPN::config()->user)) require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/handler.php');

	FPN::init();
?>