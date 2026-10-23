<?php
/**
 * GeeksPOS - Central API Configuration for Multi-System Integration
 * Configuración central para n8n, WhatsApp Bots, Ecommerce, Apps de Delivery y Puntos de Venta externos.
 */
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| 1. Dirección Base y Dominio Público del API
|--------------------------------------------------------------------------
| Define la URL pública que usarán los sistemas externos y los clientes
| de WhatsApp para conectarse a la API y abrir sus tickets térmicos.
|
| Si se deja vacío ('') o null, el sistema autodetectará la URL actual
| basándose en el host del servidor y base_url().
|
| Ejemplos:
| - 'https://pos.minegocio.com/' (Dominio con SSL)
| - 'http://192.168.1.100:8080/' (IP local de la red LAN)
| - 'https://subdominio.ngrok-free.app/' (Túnel Ngrok / Cloudflare)
*/
$config['api_public_base_url'] = getenv('API_BASE_URL') ?: '';

/*
|--------------------------------------------------------------------------
| 2. Versión e Identificador del Servidor API
|--------------------------------------------------------------------------
*/
$config['api_name'] = 'GeeksPOS Multi-System REST API Engine';
$config['api_version'] = '1.1';
$config['api_environment'] = getenv('ENVIRONMENT') ?: 'production';

/*
|--------------------------------------------------------------------------
| 3. Registro de Sistemas Externos Conectados (Multi-System Registry)
|--------------------------------------------------------------------------
| Aquí puedes registrar y configurar cada uno de los sistemas que se conectarán
| a GeeksPOS. Cada sistema puede tener:
| - 'name': Nombre legible del sistema.
| - 'key': Clave API específica (si se deja vacía, usa la clave global).
| - 'default_order_type': Tipo de pedido por defecto ('delivery', 'pickup', 'dinein').
| - 'auto_print': Si se debe disparar impresión térmica automática al recibir pedido.
| - 'paper_width': Ancho de papel térmico preferido (80 o 58).
| - 'webhook_url': URL del webhook del sistema externo (ej. n8n) para notificar
|                  cuando el estado del pedido cambia en el POS (ej. Listo / Enviado).
*/
$config['api_systems'] = [
    'n8n_whatsapp' => [
        'name'               => 'n8n WhatsApp Chatbot',
        'description'        => 'Automatización de pedidos, consultas y tickets vía WhatsApp',
        'key'                => '090665ea8d6294998e6829ad',
        'default_order_type' => 'delivery',
        'auto_print'         => true,
        'paper_width'        => 80,
        'webhook_url'        => '', // Ej: 'https://n8n.tuempresa.com/webhook/geekspos-order-status'
        'enabled'            => true
    ],

    'ecommerce' => [
        'name'               => 'Tienda Online / Ecommerce Web',
        'description'        => 'Plataforma web de compras y carrito online',
        'key'                => '', // Puede usar la misma clave o una dedicada
        'default_order_type' => 'delivery',
        'auto_print'         => true,
        'paper_width'        => 80,
        'webhook_url'        => '',
        'enabled'            => true
    ],

    'delivery_app' => [
        'name'               => 'App de Repartidores / Delivery',
        'description'        => 'Sistema de despacho, repartidores y geolocalización',
        'key'                => '',
        'default_order_type' => 'delivery',
        'auto_print'         => false,
        'paper_width'        => 80,
        'webhook_url'        => '',
        'enabled'            => true
    ],

    'remote_pos' => [
        'name'               => 'Terminales / Sucursales Remotas',
        'description'        => 'Puntos de venta remotos o tabletas de mozos',
        'key'                => '',
        'default_order_type' => 'dinein',
        'auto_print'         => true,
        'paper_width'        => 80,
        'webhook_url'        => '',
        'enabled'            => true
    ]
];

/*
|--------------------------------------------------------------------------
| 4. Valores por Defecto para Pedidos Creados por la API
|--------------------------------------------------------------------------
*/
$config['api_defaults'] = [
    // ID del empleado/cajero asignado a las ventas de la API (11 = admin/dueño)
    'employee_id'        => (int)(getenv('API_DEFAULT_EMPLOYEE_ID') ?: 11),

    // Sucursal / Depósito por defecto (0 = Central / Principal)
    'location_id'        => (int)(getenv('API_DEFAULT_LOC_ID') ?: 0),

    // Cliente por defecto en caso de no indicar cliente (1 = Walk-in Client)
    'customer_id'        => 1,

    // Método de pago por defecto si no se especifica ('Cash', 'Card Swipe', 'Bank', 'Transfer')
    'payment_method'     => 'Cash',

    // Estado inicial del pago ('due' = pendiente de cobro contra entrega, 'paid' = pagado)
    'payment_status'     => 'due',

    // Ancho de papel térmico predeterminado (80 = 80mm, 58 = 58mm)
    'thermal_paper_width'=> 80,

    // Forzar auto-impresión en el navegador al abrir el ticket
    'ticket_auto_print'  => true,

    // Moneda por defecto (0 = moneda base del sistema)
    'currency_id'        => 0
];

/*
|--------------------------------------------------------------------------
| 5. Opciones Permitidas y Validación
|--------------------------------------------------------------------------
*/
$config['api_allowed_order_types'] = [
    'delivery',
    'pickup',
    'dinein',
    'local',
    'pasa a buscar'
];

$config['api_allowed_statuses'] = [
    'paid',
    'due',
    'canceled',
    'partial'
];

/*
|--------------------------------------------------------------------------
| 6. CORS (Cross-Origin Resource Sharing)
|--------------------------------------------------------------------------
| Permite que frontends web (ej. aplicaciones React, Vue, portales n8n)
| realicen llamadas directas a la API desde otros orígenes o dominios.
*/
$config['api_cors'] = [
    'enabled'          => true,
    'allowed_origins'  => ['*'],
    'allowed_methods'  => ['GET', 'POST', 'PATCH', 'PUT', 'DELETE', 'OPTIONS'],
    'allowed_headers'  => [
        'Content-Type',
        'X-API-KEY',
        'Authorization',
        'X-SYSTEM',
        'Accept',
        'Origin',
        'X-Requested-With'
    ],
    'max_age'          => 86400
];

/*
|--------------------------------------------------------------------------
| 7. Webhook Notifier (Notificaciones salientes a n8n)
|--------------------------------------------------------------------------
| Si está habilitado, cuando un pedido cambie de estado (ej. a 'paid' o 'canceled')
| GeeksPOS enviará automáticamente un POST HTTP al webhook configurado en el
| sistema externo para que WhatsApp notifique al cliente en tiempo real.
*/
$config['api_webhooks'] = [
    'enabled' => true,
    'timeout' => 5 // segundos máximos de espera para no bloquear la petición
];
