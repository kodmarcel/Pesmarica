<?php
include'config.php';
require 'password.php';
session_start();



function dbconnect()
{
    global $config;
	$link = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PWD'],$config['DB_NAME']);
	$link->query("SET NAMES utf8");
	if (mysqli_connect_errno())
	{
		echo "Failed to connect to mysql: " . mysqli_connect_error();
	}
    return $link;
}

function login($username,$password,$cookie) {
	global $loginErr;
	$link = dbconnect();
	$query = "SELECT * FROM users WHERE user = '".$username."'";
	$result =  mysqli_query($link,$query);
	$idstring =  mysqli_fetch_array($result,MYSQLI_ASSOC);
	if (!$idstring) { 
		$loginErr="Username not recognized.";
		die ("<meta http-equiv='refresh' content='0; URL=index.php'>"); 
	}
	if (!password_verify($password, $idstring["pass"])){ 
		$loginErr="Wrong password.";
		die ("<meta http-equiv='refresh' content='0; URL=index.php'>");
	} 
	else
	{
		$_SESSION["user"] = $username;
		$_SESSION["uid"] = $idstring["id"];
		$_SESSION["gid"] = $idstring["gid"];
		$_SESSION["lang"] = $idstring["language"];
		$_SESSION["snd"] = $idstring["snd"];
		if($cookie=="1"){
			$selector = base64_encode(openssl_random_pseudo_bytes(30));
			$validator = base64_encode(openssl_random_pseudo_bytes(30));
			//Write $validator and $selector along with username to DB
			$link = dbconnect();
			$query = 'INSERT INTO `cookies` (`selector`, `validator`, `uid`) VALUES ("'.$selector.'","'.$validator.'",'.$_SESSION["uid"].')';
			mysqli_query($link,$query);
						
			$cookie_name = "auth";
			$cookie_value["sel"] = $selector;
			$cookie_value["val"] = password_hash($validator, PASSWORD_DEFAULT);
			setcookie($cookie_name, json_encode($cookie_value), time() + (86400 * 10), "/"); // 86400 = 1 day
		}
	}
}

function checkCookie(){
	//get username from DB based on $selector
	if(!isset($_COOKIE["auth"])) {
		return 0;
		die();
	} 
	$data = json_decode($_COOKIE["auth"],true);	
	$selector = $data["sel"];
	$validator = $data["val"];
	
	$link = dbconnect();
	$query = "SELECT `validator`, `uid`  FROM cookies WHERE selector = '".$selector."'";
	
	$result = mysqli_query($link,$query);
	$array =  mysqli_fetch_array($result,MYSQLI_ASSOC);
	if (!$array) { 
		return 0;
		die();
	}
	$DBvalidator = $array["validator"];
	$uid = $array["uid"];
	$link = dbconnect();
	$query = "SELECT * FROM users WHERE id = '".$uid."'";
	$result =  mysqli_query($link,$query);
	$idstring =  mysqli_fetch_array($result,MYSQLI_ASSOC);
	if(password_verify($DBvalidator, $validator)){
		$_SESSION["uid"] = $uid;
		$_SESSION["gid"] = $idstring["gid"];
		$_SESSION["user"] = $idstring["user"];
		//echo "<script type='text/javascript'>alert(".$idstring['user'].");</script>";
		$_SESSION["lang"] = $idstring["language"];
		//echo "<script type='text/javascript'>alert(".$idstring['language'].");</script>";
		if (!isset($_SESSION['lang'])){ $_SESSION['lang']="en_US"; };
		return 1;
	}
	else{
		return 0;
	}
}

function logout(){
	if(isset($_COOKIE["auth"])) {
		$data = json_decode($_COOKIE["auth"],true);	
		$link = dbconnect();
		$query = "DELETE FROM cookies WHERE uid = '".$_SESSION["uid"]."' AND selector = '".$data["sel"]."'";
		mysqli_query($link,$query);
		setcookie($cookie_name, FALSE, 1, "/");
	}	
	session_unset(); 
	session_destroy(); 
	ob_end_flush(); 
	header("Location: index.php"); 
}

?>
