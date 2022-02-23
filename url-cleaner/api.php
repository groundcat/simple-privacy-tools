<?php

// Turn off all error reporting
error_reporting(0);

// Functions
require_once ("functions.php");

// Get URL
$url = $_POST['url'];
$cleaned_url = clean_url($url);

// Encode JSON
$API_Obj = new stdClass();
$API_Obj->original_url = $url;
$API_Obj->cleaned_url = $cleaned_url;
$result = json_encode($API_Obj);
echo $result;

?>