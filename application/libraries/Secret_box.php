<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * GeeksIA - Cifrado en reposo de secretos por local (claves de IA, etc.)
 * AES-256-GCM con la clave maestra GEEKSIA_SECRET_KEY (fuera de la base y del repo).
 * El AAD ata cada ciphertext a su local: copiarlo a otro local no descifra.
 * Formato guardado: "v1:" . base64(iv[12] . tag[16] . ciphertext)
 */
class Secret_box
{
    const PREFIX = 'v1:';

    protected $key = null;

    public function __construct()
    {
        $CI =& get_instance();
        $CI->config->load('geeksia', true, true);
        $raw = base64_decode((string)$CI->config->item('geeksia_secret_key', 'geeksia'), true);
        if ($raw !== false && strlen($raw) === 32) {
            $this->key = $raw;
        }
    }

    public function is_ready()
    {
        return $this->key !== null;
    }

    public function is_encrypted($value)
    {
        return is_string($value) && strpos($value, self::PREFIX) === 0;
    }

    public function encrypt($plain, $aad)
    {
        $plain = (string)$plain;
        if ($plain === '') {
            return '';
        }
        if (!$this->is_ready()) {
            throw new RuntimeException('GEEKSIA_SECRET_KEY ausente o inválida: no se puede cifrar el secreto.');
        }
        $iv = random_bytes(12);
        $tag = '';
        $ct = openssl_encrypt($plain, 'aes-256-gcm', $this->key, OPENSSL_RAW_DATA, $iv, $tag, (string)$aad, 16);
        return self::PREFIX . base64_encode($iv . $tag . $ct);
    }

    /**
     * Devuelve el texto plano. Valores legacy sin prefijo (guardados antes del cifrado) se devuelven tal cual.
     */
    public function decrypt($stored, $aad)
    {
        $stored = (string)$stored;
        if ($stored === '') {
            return '';
        }
        if (!$this->is_encrypted($stored)) {
            return $stored;
        }
        if (!$this->is_ready()) {
            return '';
        }
        $bin = base64_decode(substr($stored, strlen(self::PREFIX)), true);
        if ($bin === false || strlen($bin) < 29) {
            return '';
        }
        $pt = openssl_decrypt(substr($bin, 28), 'aes-256-gcm', $this->key, OPENSSL_RAW_DATA, substr($bin, 0, 12), substr($bin, 12, 16), (string)$aad);
        return $pt === false ? '' : $pt;
    }

    /**
     * Últimos 4 caracteres para mostrar en la UI sin revelar el secreto.
     */
    public function hint($stored, $aad)
    {
        $plain = $this->decrypt($stored, $aad);
        return $plain === '' ? '' : substr($plain, -4);
    }
}
