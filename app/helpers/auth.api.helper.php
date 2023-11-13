<?php

require_once 'config.php';

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

class AuthHelper {
    function getAuthHeaders() {
        $header = "";
        if (isset($_SERVER['HTTP_AUTHORIZATION']))
            $header = $_SERVER['HTTP_AUTHORIZATION'];

        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
            $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];

        return $header;
    }

    function createToken($payload) {
        // Header
        $header = array(
            'alg' => 'HS256',
            'typ' => 'JWT'
        );

        $payload->exp = time() + JWT_EXP; // Tiempo de expiración

        // Se codifican header y payload en base64
        $header = base64url_encode(json_encode($header));
        $payload = base64url_encode(json_encode($payload));

        // Signature
        $signature = hash_hmac('SHA256', "$header.$payload", JWT_KEY, true);
        $signature = base64url_encode($signature);

        // Se genera el token y se devuelve
        $token = "$header.$payload.$signature";
        return $token;
    }

    function verify($token) {
        // Parsing del token para obtener header, payload y signature
        $token = explode(".", $token);
        $header = $token[0];
        $payload = $token[1];
        $signature = $token[2];

        // Se crea una nueva firma para verificar la firma del token
        $newSignature = hash_hmac('SHA256', "$header.$payload", JWT_KEY, true);
        $newSignature = base64url_encode($newSignature);

        if ($signature != $newSignature)
            return false;

        // Se decodifica el payload y se convierte en un objeto
        $payload = json_decode(base64_decode($payload));

        // Si el tiempo de expiración ya finalizó
        if ($payload->exp < time())
            return false;

        return $payload;
    }

    function currentUser() {
        $auth = $this->getAuthHeaders();
        $auth = explode(" ", $auth);

        if ($auth[0] != "Bearer")
            return false;

        return $this->verify($auth[1]);
    }
}
