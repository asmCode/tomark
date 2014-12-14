<?php

define('_DB_SERVER_', 'localhost');
define('_DB_NAME_', 'tomard_shippr');
define('_DB_USER_', 'tomard_tomship');
define('_DB_PREFIX_', 'st_');
define('_DB_PASSWD_', 'h6uQhn98%TGGES$H^w');
define('_DB_TYPE_', 'MySQL');

function ConnectToDatabase()
{
	$con = mysql_connect(_DB_SERVER_, _DB_USER_, _DB_PASSWD_);
	if (!$con)
	{
		die('Nie można się połaczyć z bazą danych: ' . mysql_error());
		return NULL;
	}
	else
	{
		echo "<div style='font-color:#00ff00'>Połączono z serwerem MySQL</div><br />";
	}

	if (!mysql_select_db( _DB_NAME_))
	{
		die('Nie można wybrać bazy danych: ' . mysql_error());
		return NULL;
	}
	else
	{
		echo "<p style='font-color:#00ff00'>Wybrano bazę " . _DB_NAME_ . "</p><br>";
	}

	mysql_query("SET NAMES utf8");
	
	return $con;
}

?>
