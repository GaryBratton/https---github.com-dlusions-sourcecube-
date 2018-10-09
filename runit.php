<?php

$url = $_GET['url'];
$referrer = isset($_GET['referrer']) ? $_GET['referrer'] : "";
$query = isset($_GET['query']) ? $_GET['query'] :"";

// New Cookie file
$ckfile = tempnam("/tmp", "CURLCOOKIE");

// New Connection
$ch = curl_init();
curl_setopt($ch, CURLOPT_REFERER, $referrer);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);  
curl_setopt($ch, CURLOPT_COOKIESESSION, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "dengel:2vy3nSLp"); //Your credentials goes here
curl_setopt($ch, CURLOPT_URL, urldecode($url.$query));
curl_exec ($ch);
curl_close($ch);
