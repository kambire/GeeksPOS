<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * GeeksPOS - Guardias de seguridad globales (hook post_controller_constructor)
 *
 * 1. Protección CSRF por origen (OWASP "Verifying Origin With Standard Headers"):
 *    un POST/PUT/DELETE desde un navegador en otro sitio se rechaza. No requiere tokens,
 *    así que no rompe los formularios legacy de GeoPOS que no los envían.
 * 2. Módulos de plataforma: solo el administrador de plataforma (roleid 5 y loc 0)
 *    puede usarlos; los administradores de cada local no.
 */
class Security_guard
{
    // Clientes que no usan cookies de sesión (se autentican con API key o token) o reciben callbacks de terceros
    protected $origin_exempt = ['api', 'delivery_api', 'tracking', 'rest', 'billing', 'paymentgateways', 'cronjob'];

    // Controladores que administran toda la plataforma (afectan a todos los locales)
    protected $platform_only = ['saas', 'restapi', 'locations'];

    public function check()
    {
        $CI =& get_instance();
        $class = strtolower($CI->router->fetch_class());

        $this->check_origin($CI, $class);
        $this->check_platform($CI, $class);
    }

    protected function check_origin($CI, $class)
    {
        $method = strtoupper((string)$CI->input->server('REQUEST_METHOD'));
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true) || in_array($class, $this->origin_exempt, true)) {
            return;
        }

        $source = (string)$CI->input->server('HTTP_ORIGIN');
        if ($source === '' || $source === 'null') {
            $source = (string)$CI->input->server('HTTP_REFERER');
        }
        if ($source === '') {
            return; // cliente sin cabeceras de origen (no es un navegador en otro sitio)
        }

        $source_host = strtolower((string)parse_url($source, PHP_URL_HOST));
        if ($source_host !== '' && in_array($source_host, $this->allowed_hosts($CI), true)) {
            return;
        }

        log_message('error', 'Security_guard: POST bloqueado por origen cruzado (' . $source_host . ') hacia ' . $class);
        set_status_header(403);
        exit('Solicitud bloqueada: el formulario no se envió desde ' . htmlspecialchars((string)$CI->input->server('HTTP_HOST'), ENT_QUOTES) . '. Recargá la página e intentá de nuevo.');
    }

    protected function allowed_hosts($CI)
    {
        $hosts = [];
        foreach ([$CI->input->server('HTTP_HOST'), $CI->input->server('HTTP_X_FORWARDED_HOST')] as $h) {
            if ($h) {
                // "host:puerto" o lista separada por comas en X-Forwarded-Host
                foreach (explode(',', $h) as $one) {
                    $one = trim($one);
                    $parsed = parse_url('http://' . $one, PHP_URL_HOST);
                    if ($parsed) $hosts[] = strtolower($parsed);
                }
            }
        }
        $base = parse_url((string)$CI->config->item('base_url'), PHP_URL_HOST);
        if ($base) $hosts[] = strtolower($base);
        return array_values(array_unique($hosts));
    }

    protected function check_platform($CI, $class)
    {
        if (!in_array($class, $this->platform_only, true) || !isset($CI->aauth) || !$CI->aauth->is_loggedin()) {
            return;
        }
        // Volver de "entrar como local" está permitido (Saas::revert lo valida con la sesión)
        if ($class === 'saas' && $CI->router->fetch_method() === 'revert') {
            return;
        }
        $u = $CI->aauth->get_user();
        if (!((int)$u->roleid === 5 && (int)$u->loc === 0)) {
            set_status_header(403);
            exit('<h3>Acceso restringido: esta sección es solo para la administración de la plataforma.</h3>');
        }
    }
}
