<?php 
	global $db;
	try
	{
		$host = "mysql:host=localhost;dbname=select2";
		$username = "root";
		$password = "";
		$db = new PDO($host, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"));
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(Exception $e)
	{
		print("Veritabanı Bağlantısı Hatası: " . $e->getMessage());
		die();
	}
?>