<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

<div>Podaj plik CSV. Pierwszy wiersz pliku zostanie pominięty</div><br>

<form action="import.php" method="post" enctype="multipart/form-data">
	<label for="file">Plik CSV:</label>
	<input type="file" name="file" id="file"><br>
	<input type="submit" name="submit" value="Wyślij">
</form>

</body>

</html>
