<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Pesmarica: GZT-project</title>
		<link rel="stylesheet" href="style.css">
		<link rel="stylesheet" href="jquery-ui-1.11/jquery-ui.css">
		<link href="multiselect/css/multi-select.css" media="screen" rel="stylesheet" type="text/css">
		<script src="jquery.js"></script>
		<script src="jquery-ui-1.11/jquery-ui.js"></script>
		<script src="jquery-colors.js"></script>
		<script src="multiselect/js/jquery.multi-select.js" type="text/javascript"></script>
		<script src="quicksearch/jquery.quicksearch.js" type="text/javascript"></script>
</head>

<?php
include_once 'config.php';
require_once 'functions.php';
session_start();

$page=stripslashes($_GET["p"]);

if ($page == "logout") {logout();}
//$_SESSION["user"]=test;

if (!checkCookie()){
	$username=stripslashes($_POST["username"]);
	$password=stripslashes($_POST["password"]);
	$cookie=$_POST["cookie"];
	if($username) { login($username,$password,$cookie); }
}

include("songs.php");

?>

