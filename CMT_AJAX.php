<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');


$cors_list = "//www2.athoc.com
, https://www.athocdev.com
,  http://www.athocdev.com
,  http://dev.athocdev.com
,  http://athocdev.com
,  http://dev.athoc.com
, https://athoc.com
,  http://athoc.ossdev1.com
, https://blog.athoc.com
, https://healthcare.athoc.com
, https://commercial-industrial.athoc.com
, https://defense-military.athoc.com
, https://federal-government-agencies.athoc.com
, https://state-local.athoc.com
,  http://www2.athoc.com
,  http://bb.blackberry.com
, https://www.athocdev.com";

$cookiePath = ".athocdev.com";

date_default_timezone_set("America/New_York");

$log=date("Y-m-d h:i:sa");
//if(isset($_GET['u'])){
//	$log.=" url=".urlencode($_GET['u']);
//}
//if(isset($_GET['r'])){
//	$log.=" referer=".urlencode($_GET['r']);
//}

// Pardot source referer AJAX CORS code
if(isset($_GET['fetchReferCookies'])){
	ob_clean();
	$http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : $_SERVER['HTTP_HOST'];


	//$http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "none";
	if(stripos($cors_list,$http_origin)>-1){
		//$http_origin="http://athocdev.com";
		header("Access-Control-Allow-Origin: ".$http_origin);
	}
	header("Access-Control-Allow-Credentials: true");
	//header("Access-Control-Expose-Headers: testharness");
	header("Content-Type: text/html; charset=utf-8");
	$glue="";
	if(isset($_COOKIE['firstTouch'])){
		$log.=" firstTouch=".$_COOKIE['firstTouch'];
		echo "firstTouch=".$_COOKIE['firstTouch'];
		$glue=";";
	}
	if(isset($_COOKIE['lastTouch'])){
		$log.=" lastTouch=".$_COOKIE['lastTouch'];
		echo $glue."lastTouch=".$_COOKIE['lastTouch'];
		$glue=";";
		
	}

	error_log(print_r($log."\n", TRUE), 3, $_SERVER['DOCUMENT_ROOT'].'/plugins/system/CMT_Source_Cube/log.txt');
	ob_flush();
	die;
}
// Pardot source referer AJAX CORS code
if(isset($_GET['setReferCookie'])){
	ob_clean();
	$cooks = $_GET['setReferCookie'];
	$toks = explode("^",$cooks);  //url then ref setReferCookie=lastTouch^Google paid^https://www.spark.com^https://www.spark.com
	if($toks[1] == "delete"){
		setcookie($toks[0], urldecode($toks[1]), time() - 36000,"/",$cookiePath);
	}else{
		if($toks[0]=='firstTouch'){
			$timer =time()+36000;
		}else{ // last touch, add the url and ref to the cookie
			$timer =time()+3600;
			$toks[1] = isset($toks[2]) ? $toks[1]."^".$toks[2] : $toks[1];
			$toks[1] = isset($toks[3]) ? $toks[1]."^".$toks[3] : $toks[1];
		}
		$success = setcookie($toks[0],urldecode($toks[1]),$timer, "/",$cookiePath);
		$_COOKIE[$toks[0]]=urldecode($toks[1]);
	}
	echo $_COOKIE[$toks[0]];
	
	$log.=" ".$toks[0]."=".$toks[1];
	error_log(print_r($log."\n", TRUE), 3, $_SERVER['DOCUMENT_ROOT'].'/plugins/system/CMT_Source_Cube/log.txt');

	ob_flush();
	die;
}
