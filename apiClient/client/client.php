<?php
	define('JWT_SECRET', 'secret');  // JWT secret (täytyy olla sama API:ssa ja Clientissa)
	define('JWT_USER_NAME', 'user'); // JWT käyttäjänimi (voi olla tässä tapauksessa mikä vain)
	define('API_ENTRY_POINT', 'https://'.$_SERVER['SERVER_NAME'].'/api');

	function getEndPoints($query) {
		// Tässä end pointtien määritykset Clientille
		$endPoints = array();
		$movie = array();
		$movie['ep'] = '/getMovie';
		$movie['parameter'][0] = 'title';
		$movie['parameter'][1] = 'year';
		$movie['parameter'][2] = 'plot';
		$movie['value'][0] = $query['title'];
		$movie['value'][1] = $query['year'];
		$movie['value'][2] = $query['plot'];
		$endPoints[] = $movie;
		$book = array();
		$book['ep'] = '/getBook';
		$book['parameter'][0] = 'isbn';
		$book['parameter'][1] = 'jscmd';
		$book['value'][0] = $query['isbn'];
		$book['value'][1] = 'data';
		$endPoints[] = $book;
		//
		foreach ($endPoints as $index => $endPoint) {
			$endPointObjects[$index] = new Entity();
			$endPointArrayKeys = array_keys($endPoint);
			foreach ($endPointArrayKeys as $endPointArrayKey) {
				$endPointObjects[$index]->__set($endPointArrayKey, $endPoint[$endPointArrayKey]);
			}
		}
		return $endPointObjects;
	}

	function buildQuery($endPoints, $ep) {
		foreach ($endPoints as $endPoint) {
			if ($endPoint->__isset('ep') == 1) {
				if ($endPoint->__get('ep') == $ep){
					$endPointData = $endPoint->getData();
					if ($endPoint->__isset('parameter') == 1) {
						foreach ($endPoint->__get('parameter') as $index => $parameter) {
							if ($endPoint->__isset('value') == 1) {
								if (isset($endPoint->__get('value')[$index])) {
									$queryParams[$parameter] = $endPoint->__get('value')[$index];
								}
							}
						}
					}
				}
			} else {
				$queryParams = '';
			}
		}
		return $queryParams;
	}

	function getResults($queryParams, $chUrl, $jwt) {
		$headers = [];
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: Bearer '.$jwt;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $chUrl);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($queryParams));
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$serverOutput = curl_exec($ch);
		curl_close($ch);
		$results = json_decode($serverOutput, true);
		return $results;
	}

	function iterator($arr, $objName) {
		$i = 0;
		$resultObjects['resultObject_'.$objName] = new Entity();
		foreach ($arr as $key => $val){
			if (is_array($val)) {
				$resultObjects['resultObject_'.$objName]->__set($key, iterator($val, 'resultObject_'.$i.'_'.$key));
				$i++;
			} else {
				$resultObjects['resultObject_'.$objName]->__set($key, $val);
			}
		}
		return $resultObjects;
	}

	function printResults($resultObjects) {
		foreach ($resultObjects as $resultObject) {
			$resultObjectData =$resultObject->getData();
			$resultObjectArrayKeys = array_keys($resultObjectData);
			foreach ($resultObjectArrayKeys as $resultObjectArrayKey) {
				if (is_array($resultObject->__get($resultObjectArrayKey))) {	
					echo '
								<div class="panel panel-info">
									<div class="panel-heading">
										<h5>'.$resultObjectArrayKey.'</h5>
									</div>
									<div class="panel-body">';
					printResults($resultObject->__get($resultObjectArrayKey));
					echo '
									</div>
								</div>';
				} else {
					echo '
								<div class="form-group form-group-sm">
									<label class="control-label col-sm-2">'.$resultObjectArrayKey.'</label>
									<div class="col-sm-10">';
					if (strlen($resultObject->__get($resultObjectArrayKey)) > 200) {
						echo '
										<textarea class="form-control" rows="5" disabled>'.$resultObject->__get($resultObjectArrayKey).'</textarea>';
					} elseif (substr($resultObject->__get($resultObjectArrayKey), -4) == '.jpg') {
						echo '
										<img src="'.$resultObject->__get($resultObjectArrayKey).'" class="img-thumbnail" alt="'.$resultObject->__get($resultObjectArrayKey).'">';
					} elseif (substr($resultObject->__get($resultObjectArrayKey), 0, 4) == 'http') {
						echo '
										<a href="'.$resultObject->__get($resultObjectArrayKey).'" target="_blank">'.$resultObject->__get($resultObjectArrayKey).'</a>';
					} else {
						echo '
										<input class="form-control" type="text" value="'.$resultObject->__get($resultObjectArrayKey).'" disabled>';
					}
					echo '
									</div>
								</div>';
				}
			}
		}
	}
?>