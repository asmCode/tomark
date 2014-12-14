<?php

require 'DatabaseDefs.php';

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
