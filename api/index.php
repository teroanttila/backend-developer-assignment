<?php
	define('JWT_SECRET', 'secret'); // JWT secret (täytyy olla sama API:ssa ja Clientissa)
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: access');
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Allow-Credentials: true');
	header('Content-Type: application/json');
	require_once($_SERVER['DOCUMENT_ROOT'].'/api/class/objectClass.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/api/endPoints.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/api/jwt/jwt.php');

	function buildQuery($endPoints, $uri) {
		foreach ($endPoints as $endPoint) {
			if ($endPoint->__isset('uri') == 1) {
				if ($endPoint->__get('uri') == $uri){
					$endPointData = $endPoint->getData();
					if ($endPoint->__isset('parameter') == 1) {
						foreach ($endPoint->__get('parameter') as $index => $parameter) {
							if ($endPoint->__isset('value') == 1) {
								if (isset($endPoint->__get('value')[$index])) {
									$queryParams[$parameter] = $endPoint->__get('value')[$index];
								}
							}
						}
						$query =  $endPoint->__get('ep').'?'.http_build_query($queryParams);
					}
				}
			} else {
				$query = '';
			}
		}
		return $query;
	}

	function getResults($query) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$serverOutput = curl_exec($ch);
		curl_close($ch);
		return $serverOutput;
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if(validateJwt(getToken(), JWT_SECRET)) {
			echo getResults(buildQuery(getEndPoints(json_decode(file_get_contents("php://input", true))), rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/')));
		} else {
			echo json_encode(array('error' => 'JWT ei ole voimassa'));
		}	
	} else {
		echo json_encode(array('error' => 'Virheellinen kysely'));
	}
?>