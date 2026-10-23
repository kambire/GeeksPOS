<?php
/**
 * GeeksPOS - GeeksIA Industry Workflow Templates Model
 * Manages pre-configured workflows and specialized AI behaviors for different business verticals:
 * - Restaurantes & Gastronomía
 * - Ópticas & Salud Visual
 * - Tiendas de Ropa / Moda / Retail
 * - Barberías & Salones de Belleza
 * - Farmacias & Productos de Salud
 * - Supermercados & Bodegas
 * - Consultorios & Servicios Profesionales
 * - Talleres Mecánicos & Servicio Técnico
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Workflow_templates_model extends CI_Model
{
    private $templates = [];

    public function __construct()
    {
        parent::__construct();
        $this->_init_templates();
    }

    /**
     * Define all available industry workflow templates
     */
    private function _init_templates()
    {
        $this->templates = [
            'restaurante' => [
                'key'                   => 'restaurante',
                'name'                  => 'Restaurante & Gastronomía',
                'badge'                 => '🍔 Gastronomía',
                'badge_color'           => 'danger',
                'icon'                  => 'fa fa-cutlery',
                'short_desc'            => 'Delivery con turnos 1-10, comandas automáticas a cocina, retiro take-away y validación SIPAP.',
                'description'           => 'Diseñado para lomiterías, hamburgueserías, pizzerías, sushi y restaurantes. Coordina la toma de pedidos, cotización de delivery según distancia, comanda instantánea a cocina y turnos rotativos entre repartidores.',
                'features'              => [
                    'Asignación rotativa de repartidores (Turnos 1 al 10)',
                    'Comandas automáticas al WhatsApp de Cocina',
                    'Cálculo de tarifas de delivery por Km y radio máximo',
                    'Staff Silent (Caja y Cocina silenciados para la IA)',
                    'Validación de comprobantes de transferencia (SIPAP / QR)'
                ],
                'sample_queries'        => [
                    '¿Qué promo tienen para hoy?',
                    'Quiero 2 hamburguesas con papas para delivery.',
                    '¿Cuánto cuesta el envío hasta mi ubicación?',
                    'Ya transferí, aquí está mi comprobante.'
                ],
                'default_tono'          => 'Cálido, amigable, paraguayo informal con emojis moderados',
                'default_instrucciones' => 'Atender clientes que desean ordenar comida. Confirmar si el pedido es para delivery o retiro en el local. Si es delivery, solicitar ubicación GPS y calcular el costo de envío según distancia. Enviar comanda detallada a cocina y verificar el comprobante de pago.',
                'recommended_models'    => ['gemini-1.5-flash', 'gpt-4o-mini', 'qwen-turbo'],
                'base_workflow_name'    => 'Flujo GeeksIA - Restaurantes & Gastronomía'
            ],

            'optica' => [
                'key'                   => 'optica',
                'name'                  => 'Óptica & Salud Visual',
                'badge'                 => '👓 Óptica & Visión',
                'badge_color'           => 'info',
                'icon'                  => 'fa fa-eye',
                'short_desc'            => 'Consultas de recetas, catálogo de armazones, cristales de laboratorio y citas con optometrista.',
                'description'           => 'Especializado para ópticas y centros visuales. Asesora a pacientes sobre armazones de sol y de receta, tipos de cristales (antireflejo, filtro azul, fotocromáticos), seguimiento del estado de cristales en laboratorio y agendamiento de citas.',
                'features'              => [
                    'Recepción y lectura de recetas oftalmológicas (foto)',
                    'Catálogo interactivo de armazones por estilo y marca',
                    'Asesoramiento de cristales (Monofocales, Blue Light, Multifocales)',
                    'Consulta de estado de laboratorio (24-48 hs hábiles)',
                    'Agendamiento de consultas de refracción y control visual'
                ],
                'sample_queries'        => [
                    '¿Cuánto cuestan unos cristales con filtro azul?',
                    'Quiero agendar una consulta para graduación de vista.',
                    '¿Tienen armazones metálicos redondos?',
                    '¿Ya están listos los lentes que mandé a hacer?'
                ],
                'default_tono'          => 'Profesional, empático, claro, confiable y con enfoque en salud visual',
                'default_instrucciones' => 'Atender pacientes y clientes de la óptica. Preguntar cordialmente si cuentan con receta médica oftalmológica o desean agendar una consulta con el especialista. Asesorar sobre armazones (de sol o receta) y tipos de cristales (monofocales, bifocales, multifocales, filtro de luz azul para pantallas). Explicar que los trabajos de laboratorio tardan habitualmente entre 24 y 48 hs hábiles.',
                'recommended_models'    => ['gemini-1.5-flash', 'gpt-4o', 'claude-3-5-sonnet'],
                'base_workflow_name'    => 'Flujo GeeksIA - Ópticas & Salud Visual'
            ],

            'retail' => [
                'key'                   => 'retail',
                'name'                  => 'Tienda de Ropa & Moda (Retail)',
                'badge'                 => '👗 Moda & Calzado',
                'badge_color'           => 'success',
                'icon'                  => 'fa fa-shopping-bag',
                'short_desc'            => 'Guía de talles, colores, catálogo con fotos, políticas de cambios y envíos a domicilio.',
                'description'           => 'Ideal para boutiques, tiendas de indumentaria femenina/masculina, calzados y accesorios. Ayuda a elegir prendas, verifica talles y colores en stock, explica políticas de cambio y coordina despachos.',
                'features'              => [
                    'Asesoría de talles (S, M, L, XL / Calzados 35 al 45)',
                    'Confirmación de colores y stock en tiempo real',
                    'Envío de fotos de prendas y combos recomendados',
                    'Información de políticas de cambios (30 días con etiqueta)',
                    'Coordinación de envíos nacionales o retiro en sucursal'
                ],
                'sample_queries'        => [
                    '¿Tienen este vestido en talle M en color negro?',
                    '¿Cómo son las medidas del talle L?',
                    '¿Hacen envíos a Ciudad del Este o Encarnación?',
                    '¿Puedo cambiar una prenda si no me queda?'
                ],
                'default_tono'          => 'Cálido, moderno, entusiasta, asesor de moda y servicial',
                'default_instrucciones' => 'Asesorar a clientes sobre prendas de vestir, calzados y accesorios. Antes de confirmar una compra, confirmar el talle exacto (S, M, L, XL o número de calzado) y color preferido del cliente. Informar métodos de pago (transferencia, tarjeta, efectivo) y opciones de envío a domicilio o retiro en tienda.',
                'recommended_models'    => ['gemini-1.5-flash', 'gpt-4o-mini', 'deepseek-v3'],
                'base_workflow_name'    => 'Flujo GeeksIA - Tiendas de Ropa & Moda'
            ],

            'barberia' => [
                'key'                   => 'barberia',
                'name'                  => 'Barbería, Peluquería & Estética',
                'badge'                 => '💈 Barbería & Belleza',
                'badge_color'           => 'warning',
                'icon'                  => 'fa fa-scissors',
                'short_desc'            => 'Reserva de turnos por profesional, catálogo de servicios, recordatorios y cancelaciones.',
                'description'           => 'Perfecto para barberías masculinas, peluquerías unisex, salones de uñas y centros de estética. Automatiza la agenda de turnos, consulta disponibilidad de cada profesional y confirma citas sin perder clientes.',
                'features'              => [
                    'Selección de servicio (Corte, Barba, Tintura, Manicura, Spa)',
                    'Elección de barbero / estilista de preferencia',
                    'Asignación de fecha y hora disponible en agenda',
                    'Recordatorio automático previo a la cita',
                    'Gestión ágil de cancelaciones y reprogramaciones'
                ],
                'sample_queries'        => [
                    'Quiero cortarme el pelo hoy a la tarde con Dani.',
                    '¿Qué precio tiene el combo corte + barba?',
                    '¿Tienen lugar para mañana a las 11:00?',
                    'Necesito cancelar mi turno de las 16:00.'
                ],
                'default_tono'          => 'Buena onda, dinámico, amigable, respetuoso y directo',
                'default_instrucciones' => 'Gestionar reservas de turnos en el salón. Preguntar al cliente qué servicio desea realizarse, con qué profesional de su preferencia desea atenderse y qué día y horario le queda mejor. Confirmar el turno una vez verificada la disponibilidad en agenda.',
                'recommended_models'    => ['gemini-1.5-flash', 'gpt-4o-mini', 'groq-llama-3.3'],
                'base_workflow_name'    => 'Flujo GeeksIA - Barberías & Salones de Belleza'
            ],

            'farmacia' => [
                'key'                   => 'farmacia',
                'name'                  => 'Farmacia & Medicamentos',
                'badge'                 => '💊 Farmacia & Salud',
                'badge_color'           => 'danger',
                'icon'                  => 'fa fa-medkit',
                'short_desc'            => 'Consultas de fármacos, fotos de recetas médicas, alternativas genéricas y delivery urgente.',
                'description'           => 'Creado para farmacias y droguerías. Permite al cliente enviar fotos de recetas médicas, consultar disponibilidad de medicamentos de marca o genéricos con el mismo principio activo y coordinar envíos de urgencia.',
                'features'              => [
                    'Recepción de fotos de recetas médicas',
                    'Sugerencia de marcas alternativas y genéricos equivalentes',
                    'Advertencia de medicamentos bajo receta controlada',
                    'Delivery express prioritario para urgencias de salud',
                    'Horarios de guardia y farmacia 24hs'
                ],
                'sample_queries'        => [
                    '¿Tienen Amoxicilina 500mg? ¿Cuánto cuesta?',
                    'Te paso la foto de la receta de mi doctor.',
                    '¿Tienen alguna opción genérica más económica?',
                    '¿Llega rápido el delivery? Es urgente.'
                ],
                'default_tono'          => 'Formal, empático, respetuoso, riguroso y confidencial',
                'default_instrucciones' => 'Atender consultas de medicamentos y productos farmacéuticos. Solicitar foto clara de la receta para antibióticos o medicamentos bajo receta médica. Si el producto solicitado no tiene stock, sugerir alternativas con el mismo principio activo y miligramos previa aclaración al cliente. Priorizar pedidos urgentes.',
                'recommended_models'    => ['gemini-1.5-flash', 'gpt-4o', 'claude-3-5-sonnet'],
                'base_workflow_name'    => 'Flujo GeeksIA - Farmacias & Salud'
            ],

            'bodega' => [
                'key'                   => 'bodega',
                'name'                  => 'Supermercado, Minimarket & Bodega',
                'badge'                 => '🛒 Bodega & Market',
                'badge_color'           => 'primary',
                'icon'                  => 'fa fa-shopping-cart',
                'short_desc'            => 'Lista rápida de compras, bebidas frías, hielo, carbón, combos para asado y delivery rápido.',
                'description'           => 'Para minimarkets, bodegas de bebidas, estaciones de conveniencia y almacenes de barrio. Atiende pedidos rápidos tipo lista de compras, coordina despacho express y acepta pagos en efectivo o transferencia.',
                'features'              => [
                    'Toma ágil de pedidos por lista de texto o nota de voz',
                    'Combos de bebidas frías, packs y picadas para eventos',
                    'Delivery express en moto o móvil de carga',
                    'Monto mínimo de pedido y zonas de reparto',
                    'Cobro contra entrega (efectivo con cambio especificado o POS portátil)'
                ],
                'sample_queries'        => [
                    'Quiero un pack de cerveza bien fría, una bolsa de hielo y carbón.',
                    '¿Tienen carbón de 5kg y gaseosa de 2 litros?',
                    '¿Cuánto tarda el delivery a mi casa?',
                    'Pago con 100.000 Gs en efectivo, traé cambio por favor.'
                ],
                'default_tono'          => 'Rápido, dinámico, amigable, paraguayo coloquial y eficiente',
                'default_instrucciones' => 'Tomar pedidos de almacén y bodega rápidamente. Ayudar al cliente a listar sus productos (bebidas frías, hielo, carbón, carnes, víveres, snacks). Confirmar cantidades, forma de pago (si es en efectivo, preguntar con cuánto abonará para llevar el vuelto exacto) y dirección para despacho inmediato.',
                'recommended_models'    => ['gemini-1.5-flash', 'gpt-4o-mini', 'qwen-turbo'],
                'base_workflow_name'    => 'Flujo GeeksIA - Minimarket & Bodegas'
            ],

            'servicios' => [
                'key'                   => 'servicios',
                'name'                  => 'Servicios Profesionales & Consultorios',
                'badge'                 => '🏢 Consultorio & Servicios',
                'badge_color'           => 'dark',
                'icon'                  => 'fa fa-briefcase',
                'short_desc'            => 'Agendamiento de citas, presupuestos, recepción de consultas jurídicas, contables o médicas.',
                'description'           => 'Para estudios jurídicos, contables, consultorios médicos, odontología, psicólogos y agencias. Filtra la consulta del interesado, recopila datos clave y agenda reuniones presenciales o videollamadas.',
                'features'              => [
                    'Registro de datos de contacto y motivo de consulta',
                    'Agendamiento de citas con profesionales del equipo',
                    'Envío de información de requisitos, honorarios y documentos',
                    'Confirmación y recordatorio previo de la cita',
                    'Derivación al especialista adecuado según el tema'
                ],
                'sample_queries'        => [
                    'Necesito agendar una consulta contable para habilitación de RUC.',
                    '¿Qué horario de atención tiene la doctora?',
                    '¿Cuánto cuesta una consulta odontológica de diagnóstico?',
                    'Quiero coordinar una reunión presencial para la semana que viene.'
                ],
                'default_tono'          => 'Profesional, corporativo, formal, cordial y confiable',
                'default_instrucciones' => 'Recibir a personas interesadas en servicios profesionales o consultas de salud. Registrar sus datos de contacto, motivo principal de consulta y coordinar cita previa (presencial o videollamada) según los horarios disponibles del profesional.',
                'recommended_models'    => ['gemini-1.5-flash', 'gpt-4o', 'claude-3-5-sonnet'],
                'base_workflow_name'    => 'Flujo GeeksIA - Consultorios & Servicios'
            ],

            'taller' => [
                'key'                   => 'taller',
                'name'                  => 'Taller Mecánico & Servicio Técnico',
                'badge'                 => '🔧 Taller & Servicio Técnico',
                'badge_color'           => 'secondary',
                'icon'                  => 'fa fa-wrench',
                'short_desc'            => 'Consulta de estado de reparación por placa o código de orden, presupuestos y retiro.',
                'description'           => 'Para talleres automotrices, de motos, service de celulares, computadoras y electrodomésticos. Permite al cliente consultar el estado de su orden por chapa o número de ficha y notificar cuando el equipo está listo.',
                'features'              => [
                    'Búsqueda de estado de reparación por número de chapa o código de orden',
                    'Envío de presupuestos de repuestos y mano de obra para aprobación',
                    'Notificación automática de "Trabajo Finalizado - Listo para Retiro"',
                    'Información de garantías de servicio y formas de pago',
                    'Recepción de solicitudes de auxilio o grúa'
                ],
                'sample_queries'        => [
                    '¿En qué estado está mi auto con chapa ABC 123?',
                    '¿Cuánto sale el cambio de aceite y filtros?',
                    '¿Ya está lista mi notebook para pasar a buscarla?',
                    '¿Cuál fue el presupuesto final de la reparación?'
                ],
                'default_tono'          => 'Claro, responsable, directo, técnico accesible y confiable',
                'default_instrucciones' => 'Atender clientes del taller o servicio técnico. Solicitar número de orden de trabajo o número de chapa/código de equipo. Informar el estado actual del diagnóstico o reparación, detallar repuestos necesarios y presupuesto estimado, y notificar cuando esté listo para su retiro.',
                'recommended_models'    => ['gemini-1.5-flash', 'gpt-4o-mini', 'deepseek-v3'],
                'base_workflow_name'    => 'Flujo GeeksIA - Talleres & Servicio Técnico'
            ]
        ];
    }

    /**
     * Get all available templates
     */
    public function get_templates()
    {
        return $this->templates;
    }

    /**
     * Get specific template by key (defaults to 'restaurante')
     */
    public function get_template($key)
    {
        $k = strtolower(trim((string)$key));
        return $this->templates[$k] ?? $this->templates['restaurante'];
    }

    /**
     * Apply industry template to a location
     * Updates geopos_locations and geopos_whatsapp_config with template defaults
     */
    public function apply_template($loc_id, $template_key, $overwrite_prompts = false)
    {
        $loc_id = (int)$loc_id;
        $tpl = $this->get_template($template_key);
        $key = $tpl['key'];

        // 1. Update location table
        $this->db->where('id', $loc_id)->update('geopos_locations', [
            'tipo_flujo' => $key
        ]);

        // 2. Fetch current whatsapp config
        $wa_cfg = $this->db->where('loc_id', $loc_id)->get('geopos_whatsapp_config')->row_array();

        $update_data = [
            'tipo_flujo' => $key
        ];

        // Only overwrite tone and instructions if requested or if currently empty
        if ($overwrite_prompts || empty($wa_cfg['ia_tono'])) {
            $update_data['ia_tono'] = $tpl['default_tono'];
        }
        if ($overwrite_prompts || empty($wa_cfg['ia_instrucciones_extra'])) {
            $update_data['ia_instrucciones_extra'] = $tpl['default_instrucciones'];
        }

        if ($wa_cfg) {
            $this->db->where('loc_id', $loc_id)->update('geopos_whatsapp_config', $update_data);
        } else {
            $update_data['loc_id'] = $loc_id;
            $this->db->insert('geopos_whatsapp_config', $update_data);
        }

        return [
            'status'   => true,
            'template' => $tpl,
            'message'  => "¡Plantilla '{$tpl['name']}' aplicada con éxito al Local #{$loc_id}!"
        ];
    }
}
