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
		<p>Podaj plik CSV. Pierwszy wiersz pliku zostanie pominięty</p>

		<form action="import.php" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label for="file">Plik CSV:</label>
				<br>
				<!--
				<label class="btn btn-default">
					Przeglądaj <input type="file" name="file" id="file" hidden>
				</label>
				-->
				
				<!--
				<label class="btn btn-primary" for="file">
					<input id="file" type="file" name="file" style="display:none;">
					Przeglądaj
				</label>
				-->
				
				<label class="btn btn-primary" for="file">
					<input id="file" type="file" name="file" style="display:none" onchange="$('#upload-file-info').html(this.files[0].name)">
					Przeglądaj
				</label>
				<span id="upload-file-info"></span>
			</div>
			<div class="form-group">
				<button type="submit" name="submit" class="btn btn-default">Wyślij</button>
			</div>
		</form>	
	</div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>