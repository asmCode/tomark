<?php

require 'DiffProduct.php';
require 'DiffProductAttrib.php';

if (!session_start())
{
	die('Nie można wystartowac sesji');
}

require 'queries.php';
require 'Database.php';

$diffProducts = $_SESSION['DiffProducts'];
$diffProductsAttribs = $_SESSION['DiffProductsAttribs'];

if (!isset($diffProducts) || !isset($diffProductsAttribs))
{
	die('Brak parametrów sesji');
}

$con = ConnectToDatabase();
if ($con == NULL)
{
	die("Nie można połączyć się z bazą danych");
}

// Zaktualizuj cene i ilosc w tabeli st_products
for ($i = 0; $i < count($diffProducts); $i++)
{
	$record = $diffProducts[$i];
	
	if (!$record->IsUpdateRequired())
		continue;
	
	$query = sprintf($UPDATE_PRODUCTS, (int)$record->countAfter, $record->priceAfter, $record->productId);
	if (mysql_query($query) != TRUE)
		die("Błąd przy wykonywaniu polecenia: " . $query . ". Błąd: " . mysql_error() . "<br />");
	
	$query = sprintf($UPDATE_PRODUCTS_SHOP, $record->priceAfter, $record->productId);
	if (mysql_query($query) != TRUE)
		die("Błąd przy wykonywaniu polecenia: " . $query . ". Błąd: " . mysql_error() . "<br />");
	
	$query = sprintf($UPDATE_QUANTITY_IN_STOCK_AVAILABLE_FOR_PRODUCTS, $record->countAfter, $record->productId);
	if (mysql_query($query) != TRUE)
		die("Błąd przy wykonywaniu polecenia: " . $query . ". Błąd: " . mysql_error() . "<br />");
}

echo "Pomyślnie zaktualizowano cenę i ilość w tabeli st_product (" . count($diffProducts) . " rekordów)<br />";
echo "Pomyślnie zaktualizowano cenę w tabeli st_product_shop (" . count($diffProducts) . " rekordów)<br />";
echo "Pomyślnie zaktualizowano ilość w tabeli st_stock_available dla produktów (" . count($diffProducts) . " rekordów)<br />";

// Przenies cene z produktu do atrybutow, zaktualizuj cene i ilosc w tabeli st_product_attribute oraz wyzeruj cene w tabeli st_product
for ($i = 0; $i < count($diffProductsAttribs); $i++)
{
	$record = $diffProductsAttribs[$i];
	
	if (!$record->IsUpdateRequired())
		continue;
	
	mysql_query("START TRANSACTION");
	
	// niestety dla jednego id produktu moze wystapic kilkukrotne przeniesienie ceny - do poprawki (optymalizacja)
	$query = sprintf($MOVE_PRICE_PROM_PRODUCT_TO_ATTRIBS, $record->productId, $record->productId);
//	echo $query . "<br />";
	if (mysql_query($query) != TRUE)
	{
		mysql_query("ROLLBACK");
		die("Błąd przy wykonywaniu polecenia: " . $query . ". Błąd: " . mysql_error() . "<br />");
	}
	
	$query = sprintf($UPDATE_PRODUCTS_ATTRIBS, (int)$record->countAfter, $record->priceAfter, $record->productAttributeId);
	if (mysql_query($query) != TRUE)
	{
		mysql_query("ROLLBACK");
		die("Błąd przy wykonywaniu polecenia: " . $query . ". Błąd: " . mysql_error() . "<br />");
	}
	
	$query = sprintf($UPDATE_PRODUCTS_ATTRIBS_SHOP, $record->priceAfter, $record->productAttributeId);
	if (mysql_query($query) != TRUE)
	{
		mysql_query("ROLLBACK");
		die("Błąd przy wykonywaniu polecenia: " . $query . ". Błąd: " . mysql_error() . "<br />");
	}
	
	$query = sprintf($UPDATE_QUANTITY_IN_STOCK_AVAILABLE_FOR_PRODUCT_ATTRIBUTE, $record->countAfter, $record->productAttributeId);
	if (mysql_query($query) != TRUE)
	{
		mysql_query("ROLLBACK");
		die("Błąd przy wykonywaniu polecenia: " . $query . ". Błąd: " . mysql_error() . "<br />");
	}

	// niestety dla jednego id produktu moze wystapic kilkukrotne zerowanie - do poprawki (optymalizacja)
	$query = sprintf($SET_ZERO_PRICE_IN_PRODUCTS, $record->productId);
	if (mysql_query($query) != TRUE)
	{
		mysql_query("ROLLBACK");
		die("Błąd przy wykonywaniu polecenia: " . $query . ". Błąd: " . mysql_error() . "<br />");
	}

	mysql_query("COMMIT");
}

echo "Pomyślnie zaktualizowano cenę i ilość w tabeli st_product_attribute oraz pomyślnie wyzerowano ceny w odpowiednich produktach (" . count($diffProductsAttribs) . " rekordów)<br />";
echo "Pomyślnie zaktualizowano ilość w tabeli st_stock_available dla atrybutów (" . count($diffProductsAttribs) . " rekordów)<br />";

// Uaktualnij pole quantity w tabeli st_product na podstawie sumy pol quantity w odpowiednich rekordach z tabeli st_product_attribute
for ($i = 0; $i < count($diffProductsAttribs); $i++)
{
	$record = $diffProductsAttribs[$i];
	
	$query = sprintf($UPDATE_QUANTITY_IN_PRODUCTS, $record->productId, $record->productId);
	if (mysql_query($query) != TRUE)
		die("Błąd przy wykonywaniu polecenia: " . $query . ". Błąd: " . mysql_error() . "<br />");
	
	$query = sprintf($SELECT_QUANTITY_IN_STOCK_AVAILABLE_FOR_PRODUCT_WITH_ATTRIBUTES, $record->productId);
	$res = mysql_query($query);
	if ($res != TRUE)
		die("Błąd przy wykonywaniu polecenia: " . $query . ". Błąd: " . mysql_error() . "<br />");
	
	$num_records = mysql_num_rows($res);
	if ($num_records != 1)
		die("Niepoprawna ilosc (" . $num_records . ") atrybutow dla produktu: " . $record->productId . " Błąd: " . mysql_error() . "<br />");
	
	$record_quantity = mysql_fetch_object($res);
	
	$query = sprintf($FIX_QUANTITY_IN_STOCK_AVAILABLE_FOR_PRODUCT_WITH_ATTRIBUTES, $record_quantity->quantity, $record->productId);
	if (mysql_query($query) != TRUE)
		die("Błąd przy wykonywaniu polecenia: " . $query . ". Błąd: " . mysql_error() . "<br />");	
}

echo "Pomyślnie uaktualniono pole quantity w tabeli st_product na podstawie sumy pol quantity z tabeli st_product_attribute (" . count($diffProductsAttribs) . " rekordów)<br />";
echo "Pomyślnie uaktualniono pole quantity dla produktu w tabeli st_stock_available na podstawie sumy pol quantity dla atrybutow (" . count($diffProductsAttribs) . " rekordów)<br />";

echo "<br />";
echo "Zakończono pomyślnie!<br />";

mysql_close($con);
session_destroy();

?>
