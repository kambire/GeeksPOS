const p = $('Parsear mensaje').first().json;
const ov = ($('Buscar overrides').first().json) || {};
const modo_operacion = ov.modo_operacion || 'produccion';
const NUMEROS_PRUEBA = ['595993241611', '595992641358', '595994103971', '59598187986', '595981867986', '595972867986'];
const esNumeroPrueba = NUMEROS_PRUEBA.includes(p.phone);
if (modo_operacion === 'desarrollo' && !esNumeroPrueba) {
  // En modo desarrollo solo responde al 595992641358
  return [];
}

const modo_chat = (($('Verificar chat_control').first().json) || {}).modo || 'bot';
if (modo_chat === 'humano') {
  // Chat pausado para atencion humana
  return [];
}

// patch9: el texto llega ya agrupado (todos los mensajes seguidos del cliente, en orden)
const ag = $('Agrupar mensajes').first().json || {};
let text = String(ag.texto_agrupado != null ? ag.texto_agrupado : (p.text || ''));

const g = (($('Buscar ubicaciones').first().json)||{}).ubicaciones_guardadas || '';
// Radio de delivery del día (el gerente lo puede bajar: 'radio a 4 km'); base 10 km
let radio_km = parseFloat(String(ov.radio_km || '').replace(',', '.'));
if (!(radio_km > 0)) radio_km = 10;
const mKm = text.match(/\[UBICACION_COMPARTIDA\][\s\S]*?Distancia aprox ([\d.]+) km/);
if (mKm && parseFloat(mKm[1]) > radio_km && text.indexOf('NO_ENTREGABLE') === -1) {
  text = text.replace(/Costo de envio: \d+ Gs\./, 'NO_ENTREGABLE: hoy el radio máximo de delivery es ' + radio_km + ' km, no se puede enviar a esa dirección (ofrecé retiro en el local).');
}
// Nunca mandar texto vacío al modelo (Gemini responde 400 y el cliente queda sin respuesta)
if (!String(text).trim()) text = (ag.hasAudio || p.hasAudio) ? '[AUDIO] El cliente envió un mensaje de audio o nota de voz.' : '[MENSAJE SIN TEXTO] El cliente envió un sticker o archivo sin texto.';
const no_disponibles = ov.no_disp || '';
const promos = ov.promos || '';
const delivery_cerrado = ov.deli_cerr === true;
const local_cerrado = ov.loc_cerr === true;
const tz = 'America/Asuncion';
const d = new Date();
const hora = parseInt(new Intl.DateTimeFormat('en-US',{timeZone:tz,hour:'2-digit',hour12:false,hourCycle:'h23'}).format(d),10);
const wd = new Intl.DateTimeFormat('en-US',{timeZone:tz,weekday:'short'}).format(d);
const diasMap = { Mon: 'Lunes', Tue: 'Martes', Wed: 'Miércoles', Thu: 'Jueves', Fri: 'Viernes', Sat: 'Sábado', Sun: 'Domingo' };
const dia_nombre = diasMap[wd] || wd;

const clavesPromo = { Mon: 'lunes', Tue: 'martes', Wed: 'miercoles', Thu: 'jueves' };
const clave_promo_dia = clavesPromo[wd] || '';

const promosFijasPorDia = {
  Mon: '1. Promo Hamburguesa: 2 Hamburguesas Bronx + 1 Porción de Papas Fritas por 50.000 Gs | 2. Promo Pizza: 2 Pizzas Medianas con Borde Mozzarella por 69.000 Gs',
  Tue: '1. Promo Hamburguesa: 2 Hamburguesas New York + 1 Porción de Papas Fritas por 50.000 Gs | 2. Promo Pizza: 2 Pizzas Medianas con Borde Pollo Catupiry por 79.000 Gs',
  Wed: '1. Promo Hamburguesa: 2 Hamburguesas Cheese + 1 Porción de Papas Fritas por 50.000 Gs | 2. Promo Pizza: 2 Pizzas Medianas con Borde Pepperoni por 79.000 Gs',
  Thu: '1. Promo 2x1: 2 Hamburguesas Times Square por 35.000 Gs (no incluye papas fritas) | 2. Promo Pizza: 2 Pizzas Medianas con Borde Bacon BBQ por 79.000 Gs'
};
const promo_del_dia = promosFijasPorDia[wd] || 'Ninguna promo diaria exclusiva hoy (disponibles los combos de todos los días)';
const promos_completas = promos ? (promo_del_dia + ' | ' + promos) : promo_del_dia;

const finde = ['Thu','Fri','Sat','Sun'].indexOf(wd) !== -1;
const cierre = finde ? 24 : 23;
const esMartes = (wd === 'Tue');  // los martes el local no abre (pedido del usuario 20/10/2026)
const enHorario = !esMartes && (hora >= 18 && hora < cierre);
const abierto = (enHorario && !local_cerrado) || p.esPrueba === true;
const horario = 'Lunes y Miercoles 18:00 a 23:00, Jueves a Domingo 18:00 a 00:00 (martes cerrado)';

const buJson = ($('Buscar ubicaciones').first().json) || {};
const per = buJson.pedido_entregado_reciente || null;
const tiene_encuesta_activa = !!(per && per.encuesta_enviada && per.rating_satisfaccion == null);
const id_pedido_encuesta = per ? per.id : null;
const pa = buJson.pedido_activo || null;
const comprobante_recibido = buJson.comprobante_reciente === true;
const comprobante_monto = Number(buJson.comprobante_monto) || 0;
const comprobante_motivo = comprobante_recibido ? '' : (buJson.comprobante_motivo || '');
const ultima_respuesta = buJson.ultima_respuesta || '';
const pedido_total = pa ? Number(pa.total) || 0 : 0;
const tiene_pedido_activo = !!pa;
const pedido_id = pa ? pa.id : null;
const pedido_num = pa ? (pa.numero_diario || pa.id) : null;
const pedido_estado = pa ? pa.estado : '';
const items_pedido = pa ? (pa.items || '-') : '';
const minutos_pedido = pa ? (Number(pa.minutos_pedido) || 0) : 0;
const minutos_en_camino = (pa && pa.minutos_salida != null) ? Number(pa.minutos_salida) : null;

let info_pedido_actual = 'No tiene pedidos activos en curso.';
if (pa) {
  if (pedido_estado === 'en_camino') {
    const minTxt = (minutos_en_camino != null) ? (minutos_en_camino + ' min') : 'recientemente';
    info_pedido_actual = 'El cliente tiene un pedido activo (#' + pedido_num + ') en estado \'en_camino\'. Productos: ' + items_pedido + '. Salió en camino hace: ' + minTxt + '. Total transcurrido desde que pidió: ' + minutos_pedido + ' min.';
  } else {
    info_pedido_actual = 'El cliente tiene un pedido activo (#' + pedido_num + ') en estado \'confirmado\' (en preparación / cocina). Productos: ' + items_pedido + '. Tiempo transcurrido desde que pidió: ' + minutos_pedido + ' min. (TODAVÍA NO SALIÓ en camino con el repartidor).';
  }
}

// El estado real va en el mensaje (el modelo a veces se guía por su memoria y no por el prompt)
if (!pa) text = '[ESTADO DEL SISTEMA (dato interno, no lo menciones): este cliente NO tiene ningún pedido registrado en cocina ahora. Un pedido SOLO queda registrado cuando generás el bloque <PEDIDO>; si en la conversación ya están productos, entrega y pago (y el comprobante si es transferencia) y el cliente confirmó, generalo ahora.]\n' + text;
if (pa) text = '[ESTADO ACTUAL DEL PEDIDO #' + pedido_num + ': ' + (pedido_estado === 'en_camino' ? ('YA SALIÓ, VA EN CAMINO con el repartidor' + (minutos_en_camino != null ? ' hace ' + minutos_en_camino + ' min' : '')) : ('EN PREPARACIÓN EN COCINA hace ' + minutos_pedido + ' min, todavía NO salió')) + '. Usá SOLO este estado si pregunta por su pedido.]\n' + text;
// === LOCAL CERRADO: la IA no conversa; mensaje fijo una sola vez (salvo postulaciones / CV) ===
const MARCA_CERRADO = '🤖 Hola, te escribe el asistente virtual (IA)';
const esPostulacion = ag.hasDoc === true || p.hasDoc === true || /(curr[ií]cul|\bcv\b|trabajo|empleo|postul|vacante|entrevista)/i.test(String(ag.texto_crudo || p.text || ''));
let cerradoFijo = false, mensaje_cerrado = '';
if (!abierto && !esPostulacion) {
  if (buJson.ultima_reciente === true && String(buJson.ultima_respuesta || '').startsWith(MARCA_CERRADO)) return [];
  cerradoFijo = true;
  mensaje_cerrado = MARCA_CERRADO + " de Benji's Lambaré.\n"
    + (esMartes ? 'Los *martes* el local está *cerrado* ⛔' : (local_cerrado && enHorario ? 'Por hoy el local ya *no está tomando pedidos* ⛔' : 'En este momento el local está *cerrado* ⛔')) + '\n'
    + '🕕 Horario: Lunes y Miércoles de 18:00 a 23:00, y Jueves a Domingo de 18:00 a 00:00 (los martes no abrimos).\n'
    + 'Escribinos dentro de ese horario y con gusto te tomamos el pedido 🙌';
}
return [{ json: Object.assign({}, p, { text, ubicaciones_guardadas: g, abierto, horario, no_disponibles, promos: promos_completas, promo_del_dia, dia_nombre, clave_promo_dia, delivery_cerrado, info_pedido_actual, tiene_pedido_activo, pedido_id, pedido_estado, items_pedido, minutos_pedido, minutos_en_camino, tiene_encuesta_activa, id_pedido_encuesta, radio_km, pedido_num, comprobante_recibido, comprobante_monto, comprobante_motivo, ultima_respuesta, pedido_total, cerradoFijo, mensaje_cerrado }) }];