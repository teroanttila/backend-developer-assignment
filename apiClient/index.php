<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/apiClient/class/objectClass.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/apiClient/client/client.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/apiClient/jwt/jwtGenerator.php');
	echo '
<!doctype html>
<html lang="fi">
	<head>
		<meta charset="utf-8">
		<title>API Client</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	</head>
	<body style="background: MidnightBlue; color: White;">
		<div class="container">
			<h1 class="text-center">API Client</h1>';
	switch ($_POST['submit']) {
		default:
			$queryHidden =  ' style="display: none;"';
			$movieActiveClass = ' class="active"';
			$movieActive = ' in active';
			$bookActiveClass = '';
			$bookActive = '';
			break;
		case 'movie':
			$queryHidden =  '';
			$movieActiveClass = ' class="active"';
			$movieActive = ' in active';
			$bookActiveClass = '';
			$bookActive = '';
			break;
		case 'book':
			$queryHidden =  '';
			$movieActiveClass = '';
			$movieActive = '';
			$bookActiveClass = ' class="active"';
			$bookActive = ' in active';
			break;
	}
	echo '
			<div class="row" id="queryRow"'.$queryHidden.'>	
				<div class="well well-sm" style="background: Azure;">
					<ul class="nav nav-tabs">
						<li'.$movieActiveClass.'><a data-toggle="tab" href="#movie"><span class="glyphicon glyphicon-film"></span> Hae elokuva</a></li>
						<li'.$bookActiveClass.'><a data-toggle="tab" href="#book"><span class="glyphicon glyphicon-book"></span> Hae kirja</a></li>
					</ul>
					<div class="tab-content">
						<div id="movie" class="tab-pane fade'.$movieActive.'">
							<div class="panel panel-primary" style="color: Black;">
								<div class="panel-heading">
									<h4>Hae elokuva</h4>
								</div>
								<div class="panel-body">
									<form action="." method="post">
										<div class="form-group form-group-sm">
											<label for="title">Nimi:</label>
											<input type="title" class="form-control" name="title" value="'.$_POST['title'].'">
										</div>
										<div class="form-group form-group-sm">
											<label for="year">Vuosi:</label>
											<input type="number" class="form-control" name="year" value="'.$_POST['year'].'">
										</div>';
	switch ($_POST['plot']) {
		default:
			$shortSelected = '';
			$fullSelected = '';
			break;
		case 'short':
			$shortSelected = ' selected';
			$fullSelected = '';
			break;
		case 'full':
			$shortSelected = '';
			$fullSelected = ' selected';
			break;
	}
	echo '
										<div class="form-group form-group-sm">
											<label for="year">Juonikuvaus:</label>
											<select class="form-control" name="plot">
												<option value="short"'.$shortSelected.'>Lyhyt</option>
												<option value="full"'.$fullSelected.'>Täydellinen</option>
											</select>
										</div>
										<input type="hidden" class="form-control" name="endPoint" value="/getMovie">
										<button type="submit" name="submit" value="movie" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Hae</button>
									</form>
								</div>
							</div>
						</div>
						<div id="book" class="tab-pane fade'.$bookActive.'">
							<div class="panel panel-primary" style="color: Black;">
								<div class="panel-heading">
									<h4>Hae kirja</h4>
								</div>
								<div class="panel-body">
									<form action="." method="post">
										<div class="form-group form-group-sm">
											<label for="isbn">ISBN:</label>
											<input type="isbn" class="form-control" name="isbn" value="'.$_POST['isbn'].'">
										</div>
										<input type="hidden" class="form-control" name="endPoint" value="/getBook">
										<button type="submit" name="submit" value="book" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Hae</button>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<script>
$(document).ready(function(){
	$("#queryRow").fadeIn();
});
			</script>';
if (isset($_POST['submit'])) {
	parse_str(file_get_contents("php://input", true), $query);
	echo '
			<div class="row" id="resultsRow" style="display: none;">
				<div class="well well-sm" style="background: Azure;">
					<div class="panel panel-primary" style="color: Black;">
						<div class="panel-heading">
							<h4>Tulokset</h4>
						</div>
						<div class="panel-body">
							<form class="form-horizontal">';
	printResults(iterator(getResults(buildQuery(getEndPoints($query), $query['endPoint']), API_ENTRY_POINT.$query['endPoint'], generateJwt(array('alg'=>'HS256','typ'=>'JWT'), array('username'=>JWT_USER_NAME, 'exp'=>(time() + 60)), JWT_SECRET)), 'result'));
	echo '
							</form>	
						</div>
					</div>
				</div>
			</div>
			<script>
$(document).ready(function(){
	$("#resultsRow").fadeIn();
});
			</script>';
}
echo '
		</div>
	</body>
</html>';
?>