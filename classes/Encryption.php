<?php
declare(strict_types=1);

namespace App;

class Encryption
{
    private string $key;

    public function __construct(?string $key = null)
    {
        $this->key = $key ?? APP_ENCRYPTION_KEY;
    }

    public function encrypt(string $plainText): string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
        $cipherText = openssl_encrypt($plainText, 'AES-256-CBC', hash('sha256', $this->key, true), OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $cipherText);
    }

    public function decrypt(string $cipherText): string
    {
        $raw = base64_decode($cipherText, true);
        if ($raw === false) {
            return '';
        }
        $ivLength = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr($raw, 0, $ivLength);
        $cipher = substr($raw, $ivLength);
        return openssl_decrypt($cipher, 'AES-256-CBC', hash('sha256', $this->key, true), OPENSSL_RAW_DATA, $iv) ?: '';
    }
}
