<?php
	function generateJwt ($headers, $payload, $secret) {
		$headersEncoded = base64urlEncode(json_encode($headers));
		$payloadEncoded = base64urlEncode(json_encode($payload));
		$signature = hash_hmac('SHA256', "$headersEncoded.$payloadEncoded", $secret, true);
		$signatureEncoded = base64urlEncode($signature);
		$jwt = "$headersEncoded.$payloadEncoded.$signatureEncoded";
		return $jwt;
	}

	function base64urlEncode($str) {
    	return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
	}
?>