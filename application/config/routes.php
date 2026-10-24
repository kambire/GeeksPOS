<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'user';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
/*
| -------------------------------------------------------------------------
| Sample REST API Routes
| -------------------------------------------------------------------------
*/
$route['api/example/users/(:num)'] = 'api/example/users/id/$1'; // Example 4
$route['api/example/users/(:num)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'api/example/users/id/$1/format/$3$4'; // Example 8
/*
| -------------------------------------------------------------------------
| GeeksPOS REST API Routes (n8n & WhatsApp Integration)
| -------------------------------------------------------------------------
*/
$route['api/ping'] = 'api/ping';
$route['api/config'] = 'api/config';
$route['api/info'] = 'api/config';
$route['api/products'] = 'api/products';
$route['api/products/(:num)'] = 'api/products/$1';
$route['api/customers'] = 'api/customers';
$route['api/customers/(:num)'] = 'api/customers/$1';
$route['api/orders'] = 'api/orders';
$route['api/orders/(:num)'] = 'api/orders/$1';
$route['api/orders/(:num)/status'] = 'api/order_status/$1';
$route['api/order_status'] = 'api/order_status';
$route['api/ticket/(:num)'] = 'api/ticket/$1';

// Rutas Facturación Electrónica SIFEN Paraguay
$route['sifen_config'] = 'sifen_config';
$route['sifen_config/(:any)'] = 'sifen_config/$1';

// Rutas de Seguimiento GPS en Vivo para Clientes
$route['tracking/live/(:any)'] = 'tracking/live/$1';
$route['tracking/(:any)'] = 'tracking/index/$1';

// Rutas WebApp Conversacional de Pedidos Estilo WhatsApp (GeeksIA + n8n)
$route['p'] = 'p/index';
$route['p/chat'] = 'p/chat';
$route['p/upload_comprobante'] = 'p/upload_comprobante';
$route['p/menu'] = 'p/menu';
$route['p/menu/(:num)'] = 'p/menu/$1';
$route['p/manifest'] = 'p/manifest';
$route['p/manifest/(:any)'] = 'p/manifest/$1';
$route['p/confirm_order'] = 'p/confirm_order';
$route['p/order_status/(:any)'] = 'p/order_status/$1';
$route['p/(:any)'] = 'p/index/$1';

// Soporte de Subdominio Exclusivo de Pedidos para Producción (ej: pedidos.tudominio.com o delivery.tudominio.com)
// Permite que la raíz del subdominio cargue directamente la WebApp de pedidos sin tener que escribir /p en la URL.
$gp_curr_host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
$gp_orders_sub = getenv('ORDERS_SUBDOMAIN'); // Opcional en variables de entorno

$is_orders_subdomain = false;
if ($gp_orders_sub && strpos($gp_curr_host, strtolower($gp_orders_sub)) !== false) {
    $is_orders_subdomain = true;
} elseif (
    strpos($gp_curr_host, 'pedidos.') === 0 ||
    strpos($gp_curr_host, 'delivery.') === 0 ||
    strpos($gp_curr_host, 'menu.') === 0 ||
    strpos($gp_curr_host, 'carta.') === 0 ||
    strpos($gp_curr_host, 'online.') === 0
) {
    $is_orders_subdomain = true;
}

if ($is_orders_subdomain) {
    $route['default_controller'] = 'p';
    $route['chat'] = 'p/chat';
    $route['upload_comprobante'] = 'p/upload_comprobante';
    $route['menu'] = 'p/menu';
    $route['menu/(:num)'] = 'p/menu/$1';
    $route['manifest'] = 'p/manifest';
    $route['manifest/(:any)'] = 'p/manifest/$1';
    $route['confirm_order'] = 'p/confirm_order';
    $route['order_status/(:any)'] = 'p/order_status/$1';
}

