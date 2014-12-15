<?php

if (!session_start())
{
	die('Nie można wystartowac sesji');
}

session_unset();

?>

<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

<?php

require 'queries.php';
require 'Database.php';
require 'CSVRecord.php';
require 'DiffProduct.php';
require 'DiffProductAttrib.php';

function PrintDiffRroducts($diffProducts)
{
	echo "<table border=1>\n";
	echo "<tr>\n";
	echo "<th>id_product</th>\n";
	echo "<th>reference</th>\n";
	echo "<th>Nazwa</th>\n";
	echo "<th>Ilość Przed</th>\n";
	echo "<th>Ilość Po</th>\n";
	echo "<th>Cena Przed</th>\n";
	echo "<th>Cena Po</th>\n";
	echo "</tr>\n";
	
	for ($i = 0; $i < count($diffProducts); $i++)
	{
		$dp = $diffProducts[$i];
		
		echo "<tr>\n";
		
		echo
			"<td>" . $dp->productId . "</td>" .
			"<td>" . $dp->reference . "</td>" .
			"<td>" . $dp->name . "</td>" .
			"<td>" . $dp->countBefore . "</td>" .
			"<td>" . $dp->countAfter . "</td>" .
			"<td>" . $dp->priceBefore . "</td>" .
			"<td>" . $dp->priceAfter . "</td>";
		
		echo "</tr>\n";
	}
	
	echo "</table>\n";
}

function PrintDiffRroductsAttribs($diffProducts)
{
	echo "<table border=1>\n";
	echo "<tr>\n";
	echo "<th>id_product_attribute</th>\n";
	echo "<th>id_product</th>\n";
	echo "<th>reference</th>\n";
	echo "<th>Nazwa</th>\n";
	echo "<th>Ilość Przed</th>\n";
	echo "<th>Ilość Po</th>\n";
	echo "<th>Cena bazowego produktu</th>\n";
	echo "<th>Cena atrybutu</th>\n";
	echo "<th>Cena ostateczna</th>\n";
	echo "</tr>\n";
	
	for ($i = 0; $i < count($diffProducts); $i++)
	{
		$dp = $diffProducts[$i];
		
		echo "<tr>\n";
		
		echo
		    "<td>" . $dp->productAttributeId . "</td>" .
			"<td>" . $dp->productId . "</td>" .
			"<td>" . $dp->reference . "</td>" .
			"<td>" . $dp->name . "</td>" .
			"<td>" . $dp->countBefore . "</td>" .
			"<td>" . $dp->countAfter . "</td>" .
			"<td>" . $dp->productPrice . "</td>" .
			"<td>" . $dp->priceBefore . "</td>" .
			"<td>" . $dp->priceAfter . "</td>";
		
		echo "</tr>\n";
	}
	
	echo "</table>\n";
}

function PrintErrors($csvRecords)
{
	echo "<table border=1>\n";
	echo "<tr>\n";
	echo "<th>reference</th>\n";
	echo "<th>problem</th>\n";
	echo "</tr>\n";
	
	for ($i = 0; $i < count($csvRecords); $i++)
	{
		$record = $csvRecords[$i];
		
		$errorMsg = "";
		
		if ($record->manyInstancesInProducts)
			$errorMsg = "Wiecej niz jedno wystapienie w produktach";
		else if ($record->manyInstancesInAttributes)
			$errorMsg = "Wiecej niz jedno wystapienie w atrybutach";
		//to jest poprawna sytuacja
		//else if (!$record->existsInProducts && !$record->existsInAttributes)
		//	$errorMsg = "Nie znaleziono ani w produktach ani w atrybutach";
		else
			continue;

		echo "<tr>\n";
		
		echo
		    "<td>" . $record->reference . "</td>" .
			"<td>" . $errorMsg . "</td>";
		
		echo "</tr>\n";
	}
	
	echo "</table>\n";
}

move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);

$con = ConnectToDatabase();
if ($con == NULL)
{
	die("Nie można połączyć się z bazą danych");
}

mysql_query("SET NAMES utf8");

//$scvRecords = CSVRecord::LoadRecordsFromFile("./wydruki.csv");
$scvRecords = CSVRecord::LoadRecordsFromFile("upload/" . $_FILES["file"]["name"]);
unlink("upload/" . $_FILES["file"]["name"]);

$diffProducts = array();
$diffProductIndex = 0;

for ($i = 0; $i < count($scvRecords); $i++)
{
	$query = sprintf($QUERY_PRODUCT_BY_REF_FROM_PRODUCTS, $scvRecords[$i]->reference);
	$res = mysql_query($query);
	
	if (mysql_num_rows($res) == 0)
		continue;
		
	else if (mysql_num_rows($res) > 1)
	{
		$scvRecords[$i]->manyInstancesInProducts = true;
		continue;
	}
	
	$scvRecords[$i]->existsInProducts = true;
	
	$record = mysql_fetch_object($res);
	
	$diffProducts[$diffProductIndex] = new DiffProduct(
		$record->id_product,
		$record->reference,
		$record->name,
		$record->quantity,
		$scvRecords[$i]->count,
		$record->price,
		$scvRecords[$i]->price);
		
	$diffProductIndex++;
}

$diffProductsAttribs = array();
$diffProductAttribsIndex = 0;

for ($i = 0; $i < count($scvRecords); $i++)
{
	$query = sprintf($QUERY_PRODUCT_BY_REF_FROM_PRODUCTS_ATTRIBS, $scvRecords[$i]->reference);
	$res = mysql_query($query);
	
	if (mysql_num_rows($res) == 0)
		continue;
		
	else if (mysql_num_rows($res) > 1)
	{
		$scvRecords[$i]->manyInstancesInAttributes = true;
		continue;
	}

	$scvRecords[$i]->existsInAttributes = true;
	
	$record = mysql_fetch_object($res);
	
	$diffProductsAttribs[$diffProductAttribsIndex] = new DiffProductAttrib(
		$record->id_product_attribute,
		$record->id_product,
		$record->product_price,
		$record->reference,
		$record->name,
		$record->quantity,
		$scvRecords[$i]->count,
		$record->price,
		$scvRecords[$i]->price);
		
	$diffProductAttribsIndex++;
}

echo "<div>Dane z tabeli st_product</div>\n";
PrintDiffRroducts($diffProducts);
echo "<br />\n";
echo "<div>Dane z tabeli st_product_attribute</div>\n";
PrintDiffRroductsAttribs($diffProductsAttribs);
echo "<br />\n";
echo "<div>Znalezione problemy</div>\n";
PrintErrors($scvRecords);


mysql_close($con);

$_SESSION['DiffProducts'] = $diffProducts;
$_SESSION['DiffProductsAttribs'] = $diffProductsAttribs;

?>

&nbsp;
<br />
&nbsp;
<br />
<form action="update_database.php" method="post" enctype="multipart/form-data">
	<input type="submit" name="submit" value="Zatwierdź">
</form>

</body>
