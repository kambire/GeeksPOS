const wh = $('Webhook WhatsApp').first().json;
const body = wh.body || wh;
const data = body.data || {};
const key = data.key || {};
const remoteJid = key.remoteJid || '';
const fromMe = key.fromMe === true;
const isGroup = remoteJid.endsWith('@g.us');

// En grupos, remoteJid es el grupo; el remitente real es participant / participantAlt
let senderPhone = remoteJid.split('@')[0];
if (key.remoteJidAlt && key.remoteJidAlt.includes('@s.whatsapp.net')) {
  senderPhone = key.remoteJidAlt.split('@')[0];
}
let participantPhone = '';
if (isGroup) {
  if (key.participantAlt && key.participantAlt.includes('@s.whatsapp.net')) {
    participantPhone = key.participantAlt.split('@')[0];
  } else if (key.participant && key.participant.includes('@s.whatsapp.net')) {
    participantPhone = key.participant.split('@')[0];
  }
}

// phone será el remitente real de la persona que habla
const phone = (isGroup && participantPhone) ? participantPhone : senderPhone;

// LISTAS Y ROLES DE USUARIOS
const SUPER_ADMINS = ["595972867986"];
const GERENTES = ["595994103971", "595992641358", "595972867986"];
const INTERNAL = ["595994103971", "595992641358", "595972867986"];
// Repartidores: los fijos + los activos hoy en la base (incluye los de emergencia que agrega el gerente)
const DELIVERIES = ["595987257856", "595985735099"];
try { String($('Cargar repartidores').first().json.repartidores || '').split(',').map(x => x.trim()).filter(Boolean).forEach(x => { if (DELIVERIES.indexOf(x) === -1) DELIVERIES.push(x); }); } catch (e) {}
const DELIVERY = "0000";
// Cocina / caja: el bot no les contesta y escribirles desde el celular del local NO pausa la IA
const STAFF_SILENT = ["595993279548"];
const DELIVERY_GROUP = "120363428621650590@g.us";
const isStaffSilent = STAFF_SILENT.indexOf(phone) !== -1;
const isDeliveryGroup = isGroup && remoteJid === DELIVERY_GROUP;

const isSuperAdmin = SUPER_ADMINS.indexOf(phone) !== -1;
const isGerente = GERENTES.indexOf(phone) !== -1;
const isInternal = INTERNAL.indexOf(phone) !== -1;
const isDelivery = (phone === DELIVERY || DELIVERIES.indexOf(phone) !== -1);
// Números de prueba temporales (benjis_config 'prueba:<tel>' vigente): atendidos aunque esté cerrado, sus pedidos no salen a nadie
let esPrueba = false;
try { esPrueba = String($('Cargar repartidores').first().json.numeros_prueba || '').split(',').map(x => x.trim()).indexOf(phone) !== -1; } catch (e) {}
const rol = isSuperAdmin ? 'super_admin' : (isGerente ? 'gerente' : 'cliente');

const msg = data.message || {};

let tipo = 'ignorar', text = '', reactedId = '', emoji = '', hasImage = false, hasAudio = false, hasDoc = false, docName = '', docMime = '', comando = '', ocupadoVal = false, esComandoIntervencion = false;
let quotedPedidoId = '', quotedPhone = '', distanciaMetodo = '';
let quotedId = '', conCita = false, citaIdentifica = false;
const msgId = key.id || '';
if (msg.audioMessage) { hasAudio = true; if (!text) text = '[AUDIO] El cliente envió un mensaje de audio o nota de voz.'; }

// Reaccion (confirmacion de entrega)
const react = msg.reactionMessage;
const POS = ['✅','✔️','☑️','👍','🆗'];
if (react && react.key) {
  emoji = react.text || '';
  if (!fromMe && POS.includes(emoji) && (isDelivery || isInternal || isDeliveryGroup)) { tipo = 'comando'; comando = 'entregado'; reactedId = react.key.id || ''; conCita = true; }
} else {
  const docMsg = msg.documentMessage || (msg.documentWithCaptionMessage && msg.documentWithCaptionMessage.message && msg.documentWithCaptionMessage.message.documentMessage);
  if (docMsg) {
    hasDoc = true;
    docName = docMsg.fileName || docMsg.title || 'curriculum.pdf';
    docMime = docMsg.mimetype || 'application/pdf';
  }
  text = msg.conversation || (msg.extendedTextMessage && msg.extendedTextMessage.text) || (msg.imageMessage && msg.imageMessage.caption) || (docMsg && docMsg.caption) || '';
  if (msg.imageMessage) { hasImage = true; if (!text) text = '[IMAGEN] El cliente envio una imagen.'; }
  if (hasDoc) {
    if (!text) text = '[DOCUMENTO] El cliente adjuntó el archivo: ' + docName;
    else text = '[DOCUMENTO: ' + docName + '] ' + text;
  }
  if (hasAudio && !text) text = '[AUDIO] El cliente envió un mensaje de audio o nota de voz.';
  const loc = msg.locationMessage;
  if (loc && loc.degreesLatitude != null) {
    const lat = loc.degreesLatitude, lng = loc.degreesLongitude;
    const link = 'https://maps.google.com/?q=' + lat + ',' + lng;
    const R = 6371, dLat = (lat-(-25.3411735))*Math.PI/180, dLng = (lng-(-57.6019842))*Math.PI/180;
    const a = Math.sin(dLat/2)**2 + Math.cos((-25.3411735)*Math.PI/180)*Math.cos(lat*Math.PI/180)*Math.sin(dLng/2)**2;
    // Distancia por RUTA real (OSRM propio); si no responde, línea recta x 1.3 como antes
    let km = R*2*Math.atan2(Math.sqrt(a), Math.sqrt(1-a)) * 1.3;
    distanciaMetodo = 'estimada (linea recta x1.3)';
    try {
      const rt = $('Ruta OSRM').first().json;
      if (rt && rt.code === 'Ok' && rt.routes && rt.routes[0] && rt.routes[0].distance > 0) { km = rt.routes[0].distance / 1000; distanciaMetodo = 'ruta real por calles'; }
    } catch (e) {}
    let envio;
    if (km <= 1) envio = 5000; else if (km <= 5) envio = 10000; else if (km <= 10) envio = 15000; else envio = null;
    if (envio === null) text = '[UBICACION_COMPARTIDA] Ubicacion: ' + link + '. Distancia aprox ' + km.toFixed(1) + ' km. NO_ENTREGABLE: supera los 10 km, no se puede hacer delivery a esa direccion.';
    else text = '[UBICACION_COMPARTIDA] Ubicacion: ' + link + '. Distancia aprox ' + km.toFixed(1) + ' km. Costo de envio: ' + envio + ' Gs.';
  }
  text = (text || '').trim();
  const lower = text.toLowerCase();
  // Evolution manda la cita en data.contextInfo (mensaje tipo 'conversation')
  const ctx = (msg.extendedTextMessage && msg.extendedTextMessage.contextInfo) || (msg.imageMessage && msg.imageMessage.contextInfo) || data.contextInfo || {};
  quotedId = ctx.stanzaId || '';
  conCita = !!quotedId;
  const quotedMsg = ctx.quotedMessage || {};
  const quotedText = quotedMsg.conversation || (quotedMsg.extendedTextMessage && quotedMsg.extendedTextMessage.text) || '';

  // Extraer ID de pedido o teléfono si se citó un ticket
  if (quotedText) {
    const mPid = quotedText.match(/Pedido\s*#(\d+)/i);
    if (mPid) quotedPedidoId = mPid[1];
    const mTel = quotedText.match(/Tel:\s*(\+?[\d\s-]+)/i);
    if (mTel) quotedPhone = mTel[1].replace(/\D/g, '');
  }

  // Extraer teléfono o número de pedido explícito en el texto del comando
  const mExplicitTel = text.match(/\b(?:595\d{9}|09\d{8}|9\d{8})\b/);
  if (mExplicitTel) {
    let rawTel = mExplicitTel[0].replace(/\D/g, '');
    if (rawTel.startsWith('0')) rawTel = '595' + rawTel.substring(1);
    else if (rawTel.startsWith('9')) rawTel = '595' + rawTel;
    quotedPhone = rawTel;
  }
  const mExplicitPedido = text.match(/(?:pedido\s*#?|#)\s*(\d{1,5})\b/i) || text.match(/\b(\d{1,5})\s*(?:en camino|llegu[eé]|entregad[oa]|voy)\b/i) || text.match(/(?:en camino|llegu[eé]|entregad[oa]|voy)\s+(\d{1,5})\b/i);
  if (mExplicitPedido && !mExplicitTel) {
    quotedPedidoId = mExplicitPedido[1];
  }
  // Lo escrito (número de pedido o teléfono) manda sobre el ticket citado
  if (mExplicitPedido || mExplicitTel) quotedId = '';
  // La cita sirve solo si es un ticket del bot ("Pedido #N" / "Tel:") o si escribió el número
  citaIdentifica = !!(quotedPedidoId || quotedPhone);

  const cmdDetener = /(ia detente|ia pausa|detener ia|pausar bot|parar ia|callate ia|silencio ia|#pausa)/i.test(lower);
  const cmdContinuar = /(ia continua|ia activa|ia reanudar|activar bot|#bot|#activar)/i.test(lower);

  if (isStaffSilent) {
    tipo = 'ignorar';
  } else if (fromMe && !isGroup && phone && !isDelivery && !isInternal) {
    tipo = 'intervencion';
    comando = cmdContinuar ? 'bot' : 'humano';
    esComandoIntervencion = cmdContinuar || cmdDetener;
  } else if (!fromMe && cmdDetener) {
    tipo = 'intervencion';
    comando = 'humano';
    esComandoIntervencion = true;
  } else if (!fromMe && cmdContinuar) {
    tipo = 'intervencion';
    comando = 'bot';
    esComandoIntervencion = true;
  } else if (!fromMe && isGerente) {
    tipo = 'gerente';
  } else if (!fromMe && ((!isGroup && (isInternal || isDelivery)) || isDeliveryGroup)) {
    // patch22: COMANDOS ESTRICTOS. Solo es comando si el mensaje es corto (máx. 5 palabras sin contar #N / números),
    // no es pregunta y EMPIEZA con la palabra clave (se permiten antes "ya", "ok", "estoy", "voy"...).
    // La charla normal ("voy al baño", "voy llegando, hay que retirar algo?") ya no le manda avisos al cliente.
    const limpio = text.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
      .replace(/pedido\s*#?\s*\d+|#\s*\d+|@\d+|\d+/g, ' ').replace(/[^a-z\s]/g, ' ').replace(/\s+/g, ' ').trim();
    const palabras = limpio ? limpio.split(' ') : [];
    const RELLENO = /^(ya|ok|okey|oki|oka|dale|bueno|estoy|voy|ahora|recien)$/;
    let ini = 0;
    while (ini < palabras.length - 1 && RELLENO.test(palabras[ini])) ini++;
    const resto = palabras.slice(ini).join(' ');
    const esCorto = palabras.length > 0 && palabras.length <= 5 && text.indexOf('?') === -1;
    // "en camino al local" / "llegué al local" = el repartidor yendo al negocio, no al cliente
    const alLocal = /\b(local|negocio|restaurante)\b/.test(limpio);
    if (esCorto) {
      if (/^ocupad[oa]\b/.test(resto)) { comando = 'ocupado'; ocupadoVal = true; }
      else if (/^(disponible|libre)\b/.test(resto)) { comando = 'disponible'; ocupadoVal = false; }
      else if (!alLocal && /^(en camino|saliendo|sali|salgo|yendo)\b/.test(resto)) { comando = 'en_camino'; }
      else if (!alLocal && /^(llegue|llegamos|afuera|en la puerta|aca|aqui)\b/.test(resto)) { comando = 'llegue'; }
      // Con cita, "ok" / "👍" / "listo" son acuse de recibo: NUNCA cierran un pedido. Solo "entregado" / "entregué".
      else if (/^(entregad[oa]|entregue)\b/.test(resto)
               || (!conCita && isDelivery && !isGroup && /^listo( (sr|senor|jefe|ya|nomas|gracias))?$/.test(limpio))) { comando = 'entregado'; if (quotedId) reactedId = quotedId; }
    }
    // Citando algo que no es un ticket del bot (pedido manual, foto, charla) y sin número escrito: no se adivina el pedido
    if (conCita && !citaIdentifica && (comando === 'entregado' || comando === 'en_camino' || comando === 'llegue')) comando = '';
    tipo = comando ? 'comando' : 'ignorar';
  } else if ((text || hasAudio || hasImage || hasDoc) && !fromMe && !isGroup) {
    tipo = 'mensaje';
  }
}
const esEntregado = (comando === 'entregado');
const esDispo = (comando === 'ocupado' || comando === 'disponible');
const esAviso = (comando === 'en_camino' || comando === 'llegue');

// Si el cliente responde/cita una foto de promo o un mensaje nuestro, se lo decimos a la IA
if (tipo === 'mensaje') {
  const PROMOS = { pizza_4_sabores: 'Pizza Gigante Familiar 4 sabores (16 porciones) - 89.000 Gs', pizzas_borde_mediano: 'Promo 2 Pizzas Medianas Borde Relleno (1 Pollo Catupiry + 1 Pepperoni) - 89.000 Gs',
    combo_doble: 'Combo Doble (2 burgers dobles + papas cheddar y bacon) - 75.000 Gs', combo_triple: 'Combo Triple (2 burgers triples + papas cheddar y bacon) - 99.000 Gs',
    combo_sampler: 'Combo Sampler (1 Bronx + 1 New York + 1 Cheese + 2 papas) - 99.000 Gs', jueves: 'Promo de los Jueves (2x1 Times Square y pizza)', menu: 'Carta / menú completo' };
  let citada = '';
  try { citada = String($('Cargar repartidores').first().json.imagen_citada || ''); } catch (e) {}
  const ctxQ = (msg.extendedTextMessage && msg.extendedTextMessage.contextInfo) || (msg.imageMessage && msg.imageMessage.contextInfo) || data.contextInfo || {};
  const qm = ctxQ.quotedMessage || {};
  const qTexto = qm.conversation || (qm.extendedTextMessage && qm.extendedTextMessage.text) || '';
  if (citada) text = text + '\n[EL CLIENTE ESTÁ RESPONDIENDO A LA FOTO QUE LE ENVIAMOS: ' + (PROMOS[citada] || citada) + ']';
  else if (qm.productMessage && qm.productMessage.product) {
    const prod = qm.productMessage.product;
    const nFotos = Number(prod.productImageCount || 0);
    text = text + '\n[EL CLIENTE ESCRIBE DESDE EL CATÁLOGO DE WHATSAPP, SOBRE LA PUBLICACIÓN: "' + String(prod.title || 'sin título').slice(0, 120) + '"'
      + (prod.description ? ' — ' + String(prod.description).replace(/\s*\n\s*/g, ' ').slice(0, 200) : '')
      + (nFotos > 1 ? ' (tiene ' + nFotos + ' fotos/promos)' : '')
      + '. Si no sabés con certeza qué promo(s) de esa publicación quiere, NO adivines: preguntale.]';
  }
  else if (qm.imageMessage && String(qm.imageMessage.caption || '').trim()) text = text + '\n[EL CLIENTE ESTÁ RESPONDIENDO A LA FOTO QUE LE ENVIAMOS: ' + String(qm.imageMessage.caption).replace(/[*_~]/g, '').replace(/\s*\n\s*/g, ' · ').slice(0, 300) + ']';
  else if (qm.imageMessage) text = text + '\n[EL CLIENTE ESTÁ RESPONDIENDO A UNA FOTO de nuestras promos, pero no sabemos cuál: preguntale amablemente cuál de las promos es]';
  else if (qTexto) text = text + '\n[EL CLIENTE ESTÁ RESPONDIENDO A ESTE MENSAJE: "' + String(qTexto).slice(0, 300) + '"]';
}
const name = data.pushName || '';
return [{ json: { tipo, phone, senderPhone, isGroup, remoteJid, text, name, reactedId, emoji, fromMe, hasImage, hasAudio, hasDoc, docName, docMime, msgId, comando, esEntregado, esDispo, esAviso, ocupadoVal, esComandoIntervencion, quotedPedidoId, quotedPhone, quotedId, conCita, isSuperAdmin, rol, esPrueba, distanciaMetodo } }];
