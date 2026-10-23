<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| GeeksIA - Integraciones (Evolution API, n8n) y secretos
| -------------------------------------------------------------------------
| Los secretos NUNCA van en el código. Se leen de variables de entorno
| (docker-compose las pasa desde .env) y, si el contenedor todavía no las
| tiene, del archivo .env en la raíz del proyecto (bloqueado por .htaccess
| y excluido de git). Ver .env.example.
*/

if (!function_exists('geeksia_env')) {
    function geeksia_env($name, $default = '')
    {
        $val = getenv($name);
        if ($val !== false && $val !== '') {
            return $val;
        }

        static $file_vars = null;
        if ($file_vars === null) {
            $file_vars = [];
            $path = FCPATH . '.env';
            if (is_readable($path)) {
                foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    $line = trim($line);
                    if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                        continue;
                    }
                    list($k, $v) = explode('=', $line, 2);
                    $file_vars[trim($k)] = trim(trim($v), "\"'");
                }
            }
        }
        return (isset($file_vars[$name]) && $file_vars[$name] !== '') ? $file_vars[$name] : $default;
    }
}

// Evolution API (WhatsApp)
$config['evolution_api_url'] = rtrim(geeksia_env('EVOLUTION_API_URL', 'http://evolution_api:8080'), '/');
$config['evolution_api_key'] = geeksia_env('EVOLUTION_API_KEY');

// n8n (motor de flujos, oculto bajo la marca GeeksIA)
$config['n8n_api_url']          = rtrim(geeksia_env('N8N_API_URL', 'http://n8n:5678/api/v1'), '/') . '/';
$config['n8n_api_key']          = geeksia_env('N8N_API_KEY');
$config['n8n_base_workflow_id'] = geeksia_env('N8N_BASE_WORKFLOW_ID', 'OlRZzvyTYUzRc6ec');

// Clave maestra (base64 de 32 bytes) para cifrar en reposo las claves de IA de cada local
$config['geeksia_secret_key'] = geeksia_env('GEEKSIA_SECRET_KEY');

// Las API keys de plataforma (scope=platform) solo se aceptan desde redes privadas
// (la red interna de Docker). Poner GEEKSPOS_PLATFORM_ALLOW_PUBLIC=1 para permitirlas desde internet.
$config['platform_key_allow_public'] = geeksia_env('GEEKSPOS_PLATFORM_ALLOW_PUBLIC', '0') === '1';
