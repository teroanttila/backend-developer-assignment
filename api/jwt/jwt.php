<?php
	function base64UrlEncode($str) {
		return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
	}

	function validateJwt($jwt, $secret) {
		$tokenParts = explode('.', $jwt);
		$header = base64_decode($tokenParts[0]);
		$payload = base64_decode($tokenParts[1]);
		$signatureProvided = $tokenParts[2];
		$expiration = json_decode($payload)->exp;
		$isExpired = ($expiration - time()) < 0;
		$base64UrlHeader = base64UrlEncode($header);
		$base64UrlPayload = base64UrlEncode($payload);
		$signature = hash_hmac('SHA256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
		$base64UrlSignature = base64UrlEncode($signature);
		$isSignatureValid = ($base64UrlSignature === $signatureProvided);
		if ($isExpired || !$isSignatureValid) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function getAuthorizationHeader(){
		$headers = null;
		if (isset($_SERVER['Authorization'])) {
			$headers = trim($_SERVER["Authorization"]);
		} else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
			$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
		} else if (function_exists('apache_request_headers')) {
			$requestHeaders = apache_request_headers();
			$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
			if (isset($requestHeaders['Authorization'])) {
				$headers = trim($requestHeaders['Authorization']);
			}
		}
		return $headers;
	}

	function getToken() {
		$headers = getAuthorizationHeader();
		if (!empty($headers)) {
			if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
				return $matches[1];
			}
		}
		return null;
	}
?>