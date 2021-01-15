<?php

class Crypto {
    // Encrypts the text using the secret key
    static function encryptText($text, $server_public_key)
    {
        openssl_public_encrypt($text, $encrypted_message, $server_public_key);

        return base64_encode($encrypted_message);
    }

    // Decrypts encrypted text (first parameter) using the secret key (second parameter)
    static function decryptText($encrypted_base64, $private_key)
    {
        $encrypted_string = base64_decode($encrypted_base64);
        openssl_private_decrypt($encrypted_string, $original_text, $private_key);

        return $original_text;
    }

    // Returns digital signature of the string
    static function getSignature($private_key, $algorithm, $string_to_sign) {

        $binary_signature = "";

        // Create signature on $data
        openssl_sign($string_to_sign, $binary_signature, $private_key, $algorithm);

        // Create base64 version of the signature
        $signature = base64_encode($binary_signature);
        
        return $signature;
    }

    // verifies the signature using the client's public key
    static function verifySignature($server_public_key, $algorithm, $signature, $string) {

        // Extract original binary signature 
        $binary_signature = base64_decode($signature);

        // Check signature
        $result = openssl_verify($string, $binary_signature, $server_public_key, $algorithm);

        return $result;
    }
}