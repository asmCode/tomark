<?php

if (!session_start())
{
	die('Nie można wystartowac sesji');
}

session_unset();

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Tomark Shop Updater</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  
  <div class="container">
  
    <?php

	require 'queries.php';
	require 'Database.php';
	require 'CSVRecord.php';
	require 'DiffProduct.php';
	require 'DiffProductAttrib.php';

	function PrintDiffRroducts($diffProducts)
	{
		echo "<table class=\"table table-striped\">\n";
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
			if (!$dp->IsUpdateRequired())
				continue;
			
			// $tr_class = $dp->IsUpdateRequired() ? "info" : "";
			// echo "<tr class=\"". $tr_class ."\">\n";
			
			echo "<tr>\n";
			
			echo
				"<td>" . $dp->productId . "</td>" .
				"<td>" . $dp->reference . "</td>" .
				"<td>" . $dp->name . "</td>" .
				"<td>" . $dp->countBefore . "</td>" .
				"<td>" . $dp->countAfter . "</td>" .
				"<td>" . number_format((float)$dp->priceBefore, 2, ',', '') . " zł </td>" .
				"<td>" . number_format((float)$dp->priceAfter, 2, ',', '') . " zł </td>";
			echo "</tr>\n";
		}
		
		echo "</table>\n";
	}

	function PrintDiffRroductsAttribs($diffProducts)
	{
		echo "<table class=\"table table-striped\">\n";
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
			
			if (!$dp->IsUpdateRequired())
				continue;
			
			// $tr_class = $dp->IsUpdateRequired() ? "info" : "";
			// echo "<tr class=\"". $tr_class ."\">\n";
			
			echo "<tr>\n";
			
			echo
				"<td>" . $dp->productAttributeId . "</td>" .
				"<td>" . $dp->productId . "</td>" .
				"<td>" . $dp->reference . "</td>" .
				"<td>" . $dp->name . "</td>" .
				"<td>" . $dp->countBefore . "</td>" .
				"<td>" . $dp->countAfter . "</td>" .
				"<td>" . number_format((float)$dp->productPrice, 2, ',', '') . " zł </td>" .
				"<td>" . number_format((float)$dp->priceBefore, 2, ',', '') . " zł </td>" .
				"<td>" . number_format((float)$dp->priceAfter, 2, ',', '') . " zł </td>";
			
			echo "</tr>\n";
		}
		
		echo "</table>\n";
	}

	function PrintErrors($csvRecords)
	{
		echo "<table class=\"table table-striped\">\n";
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

	echo "<p>Dane z tabeli st_product</p>\n";
	PrintDiffRroducts($diffProducts);
	echo "<br />\n";
	echo "<p>Dane z tabeli st_product_attribute</p>\n";
	PrintDiffRroductsAttribs($diffProductsAttribs);
	echo "<br />\n";
	echo "<p>Znalezione problemy</p>\n";
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
	</div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>