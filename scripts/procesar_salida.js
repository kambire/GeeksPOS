const out = ($input.first().json.output) || '';
let reply = out, pedido = null, problema = '';
const mp = out.match(/<PEDIDO>([\s\S]*?)<\/PEDIDO>/);
if (mp) { try { pedido = JSON.parse(mp[1].trim()); } catch (e) { pedido = null; } reply = reply.replace(mp[0], '').trim(); }
const mq = out.match(/<PROBLEMA>([\s\S]*?)<\/PROBLEMA>/);
if (mq) { problema = mq[1].trim(); reply = reply.replace(mq[0], '').trim(); }
let ubic = null;
const mu = out.match(/<UBICACION>([\s\S]*?)<\/UBICACION>/);
if (mu) { try { ubic = JSON.parse(mu[1].trim()); } catch (e) { ubic = null; } reply = reply.replace(mu[0], '').trim(); }


let cv_data = null;
const mcv = out.match(/<CV>([\s\S]*?)<\/CV>/i);
if (mcv) {
  try { cv_data = JSON.parse(mcv[1].trim()); } catch (e) { cv_data = null; }
  reply = reply.replace(mcv[0], '').trim();
}

let alerta_delivery = null;
const mad = out.match(/<ALERTA_DELIVERY>([\s\S]*?)<\/ALERTA_DELIVERY>/);
if (mad) {
  try { alerta_delivery = JSON.parse(mad[1].trim()); } catch (e) { alerta_delivery = null; }
  reply = reply.replace(mad[0], '').trim();
}

let imagen_claves = [];
const imgMatches = [...out.matchAll(/<IMAGEN>([\s\S]*?)<\/IMAGEN>/gi)];
for (const m of imgMatches) {
  const k = m[1].toLowerCase().trim();
  if (k && !imagen_claves.includes(k)) {
    imagen_claves.push(k);
  }
}
const imagen_clave = imagen_claves[0] || '';
// Limpieza GLOBAL de cualquier etiqueta <IMAGEN> para que NUNCA aparezca texto de error en WhatsApp
reply = reply.replace(/<IMAGEN>[\s\S]*?<\/IMAGEN>/gi, '').trim();
// Normalizar saltos de línea para evitar espacios vacíos donde estaban las etiquetas
reply = reply.replace(/\n{3,}/g, '\n\n').trim();


let rating_data = null;
const mr = out.match(/<RATING>([\s\S]*?)<\/RATING>/i);
if (mr) {
  try { rating_data = JSON.parse(mr[1].trim()); } catch (e) { rating_data = null; }
  reply = reply.replace(mr[0], '').trim();
}

const replyIA = reply;  // patch25: lo que escribió la IA (si el código lo cambia, se corrige la memoria)
const p = $('Parsear mensaje').first().json;
let textoCliente = String(p.text || '');
try { const ag = $('Agrupar mensajes').first().json; if (ag && ag.texto_agrupado != null) textoCliente = String(ag.texto_agrupado); } catch (e) {}
const ultimo = (re) => { const ms = [...textoCliente.matchAll(re)]; return ms.length ? ms[ms.length - 1] : null; };
// Guardar la ubicación GPS que compartió el cliente (antes nunca se guardaba)
if (!ubic) {
  const ml = ultimo(/\[UBICACION_COMPARTIDA\] Ubicacion: (\S+?)\. Distancia aprox [\d.]+ km\. Costo de envio: (\d+) Gs/g);
  if (ml) ubic = { etiqueta: 'Última ubicación', link: ml[1], envio: Number(ml[2]) };
}
function ok(v){ return v != null && String(v).trim() !== '' && String(v).trim() !== '-'; }
// patch25: pedido pendiente (el código frenó la confirmación en un turno anterior). Se usa solo si el cliente
// confirma y la IA dice "confirmado" sin volver a armar el <PEDIDO> (si agrega algo, la IA arma un pedido nuevo).
let pendiente = null;
try { pendiente = $('Buscar ubicaciones').first().json.pedido_pendiente || null; } catch (e) {}
if (typeof pendiente === 'string') { try { pendiente = JSON.parse(pendiente); } catch (e) { pendiente = null; } }
const ctx25 = $('Contexto').first().json || {};
const txtConf25 = String(ctx25.text || p.text || '').split('\n').filter(l => !/^\[(ESTADO|CONTENIDO|UBICACION)/.test(l.trim())).join(' ').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
const confirma25 = /(\bsi+\b|confirm|\bdale\b|\bok\b|\bokey\b|\blisto\b|metele|correcto|perfecto|de una|\bclaro\b|\bva\b|exacto|asi (esta )?bien|todo bien|\bbueno\b|👍|✅)/i.test(txtConf25);
const iaDiceConfirmado = /PEDIDO CONFIRMADO|en marcha en (la )?cocina|pedido (ya )?(est[aá] |qued[oó] )?confirmado/i.test(reply);
let usoPendiente = false;
if (!pedido && pendiente && pendiente.items && confirma25 && iaDiceConfirmado && !ctx25.tiene_pedido_activo) {
  pedido = JSON.parse(JSON.stringify(pendiente));
  usoPendiente = true;
}
const esRetiro = !!pedido && /retiro|busc/i.test(String(pedido.tipo_entrega||''));
const ctx = $('Contexto').first().json || {};
const abierto = ctx.abierto !== false;
const deliveryCerrado = ctx.delivery_cerrado === true;
// === DOBLE VERIFICACIÓN DEL PEDIDO (por código, antes de mandarlo a caja) ===
const fmt = (n) => Number(n || 0).toLocaleString('es-PY') + ' Gs';
const verificacion = { avisos: [], correcciones: [], km: null };
// Pizzas mitad y mitad (patch23): cada mitad = mitad del precio de la pizza entera de ese sabor y tamaño
const SABOR_PIZZA = /(pollo\s*catupiry|bacon\s*bbq|bbq|pepperoni|mozzarella|muzzarella|napoletana|napolitana|catupiry)/g;
const NOMBRE_SABOR = { 'pollo catupiry': 'Pollo Catupiry', 'bacon bbq': 'Bacon BBQ', 'bbq': 'Bacon BBQ', 'pepperoni': 'Pepperoni',
  'mozzarella': 'Mozzarella', 'muzzarella': 'Mozzarella', 'napoletana': 'Napoletana', 'napolitana': 'Napoletana', 'catupiry': 'Catupiry' };
function mitadesDe(pieza) {
  const t = String(pieza).toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  if (!/\bmitad\b/.test(t) || /(4 sabores|cuatro sabores|gigante familiar|2 pizzas|dos pizzas|promo)/.test(t)) return null;
  const sab = (t.match(SABOR_PIZZA) || []).map(x => x.replace(/\s+/g, ' '));
  if (!sab.length) return null;
  const gig = /gigante/.test(t);
  return sab.slice(0, 2).map(f => ({ sabor: NOMBRE_SABOR[f] || f, precio: (/(pollo|bbq|pepperoni)/.test(f) ? (gig ? 100000 : 60000) : (gig ? 90000 : 50000)) / 2 }));
}
function lineaItem(x) {
  const it = String(x).trim();
  const mq = it.match(/^(\d+)\s*x?\s*/i);
  const m = mitadesDe(mq ? it.slice(mq[0].length) : it);
  if (!m || m.length < 2) return '• ' + it;
  const qty = mq ? Number(mq[1]) : 1;
  const tot = m.reduce((a, h) => a + h.precio, 0);
  return '• ' + it.replace(/\s*\([^)]*\)\s*$/, '') + ' (' + fmt(tot) + (qty > 1 ? ' c/u' : '') + ')\n'
    + m.map(h => '     ◦ Mitad ' + h.sabor + ': ' + fmt(h.precio)).join('\n');
}
function precioCarta(pieza) {
  const t = pieza.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  const s = t.replace(/\([^)]*\)/g, ' ');
  const mit = mitadesDe(pieza);
  if (mit) return mit.reduce((a, h) => a + h.precio, 0);
  if (/sampler/.test(t)) return 99000;
  if (/combo triple/.test(t)) return 99000;
  if (/combo doble/.test(t)) return 75000;
  if (/(gigante familiar|4 sabores|cuatro sabores)/.test(t)) return 89000;
  if (/(2 pizzas|dos pizzas|pizzas borde mediano|promo pizzas)/.test(t)) return 89000;
  if (/(promo|2x1|times square|corrida)/.test(s)) return null;           // promos del día: no se verifican
  if (/papa/.test(s) && !/(burger|hamburguesa|combo|desme)/.test(s)) {
    if (/(cheddar|bacon)/.test(s)) return 28000;
    if (/grande/.test(s)) return 16000;
    return 12000;
  }
  if (/(mozzarella|muzzarella|catupiry|napoletana|napolitana|pepperoni|bbq|pizza)/.test(s)) {
    const premium = /(pepperoni|bbq|pollo)/.test(s);
    if (!/(mozzarella|muzzarella|catupiry|napoletana|napolitana|pepperoni|bbq)/.test(s)) return null;
    if (/gigante/.test(t)) return premium ? 100000 : 90000;
    return premium ? 60000 : 50000;
  }
  if (/desme/.test(s)) return 45000;
  if (/strike/.test(s)) return 50000;
  if (/kids/.test(s)) return 35000;
  if (/(cheese|bronx|new york)/.test(s)) return 35000;
  if (/(suadero|alambre|campechano|taco)/.test(s)) return 50000;
  if (/(dorilocos|birriamen)/.test(s)) return 30000;
  if (/coca/.test(s)) return 15000;
  return null;
}
if (pedido) {
  pedido.nombre = ok(pedido.nombre) ? pedido.nombre : (p.name || 'Cliente');
  // 1) Productos + precios de carta
  const piezas = String(pedido.items || '').split(/\n|,(?![^(]*\))|;/).map(x => x.trim()).filter(Boolean);
  let subCarta = 0, todasConocidas = piezas.length > 0;
  for (const pz of piezas) {
    const mq = pz.match(/^(\d+)\s*x?\s*/i);
    const qty = mq ? Number(mq[1]) : 1;
    const pr = precioCarta(mq ? pz.slice(mq[0].length) : pz);
    if (pr == null) { todasConocidas = false; break; }
    subCarta += pr * qty;
  }
  if (todasConocidas && subCarta > 0 && Number(pedido.subtotal) !== subCarta) {
    verificacion.avisos.push('Subtotal de la IA ' + fmt(pedido.subtotal) + ' ≠ carta ' + fmt(subCarta) + ': revisar precios');
  }
  if (!(Number(pedido.subtotal) > 0) && todasConocidas && subCarta > 0) {
    pedido.subtotal = subCarta;
    verificacion.correcciones.push('subtotal tomado de la carta: ' + fmt(subCarta));
  }
  // 2) Ubicación y envío (por GPS)
  const guardadas = {};
  for (const m of String(ctx.ubicaciones_guardadas || '').matchAll(/^(.+?): (\S+) \(envio (\d+|\?) Gs\)$/gm)) guardadas[m[2]] = { etiqueta: m[1], envio: m[3] === '?' ? null : Number(m[3]) };
  if (ubic && ubic.link) guardadas[ubic.link] = { etiqueta: ubic.etiqueta, envio: Number(ubic.envio) };
  const kmM = ultimo(/Distancia aprox ([\d.]+) km/g);
  if (esRetiro) {
    if (Number(pedido.envio) !== 0) { verificacion.correcciones.push('retiro: envío ' + fmt(pedido.envio) + ' → 0 Gs'); pedido.envio = 0; }
  } else {
    let link = String(pedido.ubicacion || '').match(/https?:\/\/\S+/);
    link = link ? link[0].replace(/[).,]+$/, '') : '';
    if (!/maps\.google\.com\/\?q=-?\d|goo\.gl|maps\.app/.test(link)) {
      const ult = Object.keys(guardadas).find(k => /última ubicación/i.test(guardadas[k].etiqueta));
      if (ult) { link = ult; verificacion.avisos.push('Dirección tomada de la última ubicación que compartió el cliente: confirmar'); }
      else link = '';
    }
    pedido.ubicacion = link;   // sin link GPS -> el pedido no pasa y se le pide la ubicación
    if (link && guardadas[link] && guardadas[link].envio != null && Number(pedido.envio) !== guardadas[link].envio) {
      verificacion.correcciones.push('envío ' + fmt(pedido.envio) + ' → ' + fmt(guardadas[link].envio) + ' (según GPS)');
      pedido.envio = guardadas[link].envio;
    } else if (link && !guardadas[link]) {
      verificacion.avisos.push('Envío no verificado por GPS');
    }
    if (kmM) verificacion.km = kmM[1];
  }
  // 3) Total = subtotal + envío
  const totalOk = (Number(pedido.subtotal) || 0) + (Number(pedido.envio) || 0);
  if (totalOk > 0 && Number(pedido.total) !== totalOk) {
    verificacion.correcciones.push('total ' + fmt(pedido.total) + ' → ' + fmt(totalOk));
    pedido.total = totalOk;
  }
  pedido.items = piezas.join(', ');
}
const esTransfP = !!pedido && /transf/i.test(String(pedido.pago || ''));
const montoComprobanteOk = !!pedido && Number(ctx.comprobante_monto) > 0 && Number(ctx.comprobante_monto) === Number(pedido.total);
const faltaComprobante = esTransfP && !(ctx.comprobante_recibido === true && montoComprobanteOk);
const pedidoValido = !!pedido && abierto && ok(pedido.items) && ok(pedido.pago) && (Number(pedido.total) > 0) && (esRetiro || (ok(pedido.ubicacion) && !deliveryCerrado)) && !faltaComprobante;
// Si el modelo escribió "PEDIDO CONFIRMADO" pero el pedido no se puede registrar, NO engañar al cliente
// Pedido idéntico al que ya está en cocina (ej. el cliente dijo "gracias"): no reenviar el ticket
const norm = (t) => String(t || '').toLowerCase().replace(/\([^)]*\)/g, '').replace(/[^a-z0-9áéíóúñ]+/g, ' ').trim();
let pedidoDuplicado = false;
if (pedido && ctx.tiene_pedido_activo && Number(pedido.total) === Number(ctx.pedido_total) && norm(pedido.items) === norm(ctx.items_pedido)) {
  pedidoDuplicado = true;
  reply = '¡De nada! 🙌 Tu pedido #' + (ctx.pedido_num || '') + ' ya está registrado y en marcha 🧑‍🍳 Cualquier cosa nos escribís por acá.';
}
if ((mp || usoPendiente) && !pedidoValido && !pedidoDuplicado) {
  if (pedido && !ok(pedido.items)) {
    reply = '🍽️ Antes de confirmar, ¿me decís de nuevo qué productos querés pedir? Así lo mando bien a la cocina 🙏';
  } else if (!pedido || !(Number(pedido.total) > 0)) {
    reply = 'Estoy terminando de registrar tu pedido 🙌 Un encargado te lo confirma en instantes.';
    problema = (problema ? problema + ' | ' : '') + 'El bot NO pudo registrar el pedido automáticamente, revisar el chat y cargarlo a mano: ' + (mp ? mp[1].trim() : JSON.stringify(pedido || {})).slice(0, 400);
  } else if (!abierto) {
    reply = 'En este momento el local está cerrado ⛔ Atendemos Lunes y Miércoles de 18:00 a 23:00, y Jueves a Domingo de 18:00 a 00:00 (los martes no abrimos). ¡Te esperamos! 🙌';
  } else if (!esRetiro && deliveryCerrado) {
    reply = 'Hoy el delivery está cerrado 🛵❌ ¿Querés pasar a retirarlo por el local? 🏪 Avda. Cacique Lambaré c/ Cristóbal Colón';
  } else if (!ok(pedido.pago)) {
    reply = '💵 Para confirmar tu pedido, ¿cómo vas a abonar? *Efectivo*, *transferencia* o *QR / tarjeta*';
  } else if (!esRetiro && !ok(pedido.ubicacion)) {
    reply = '📍 Para confirmar el envío necesito tu ubicación: tocá el clip 📎 → *Ubicación* y compartila 🙏';
  } else if (ctx.comprobante_recibido === true && !montoComprobanteOk) {
    reply = '⚠️ El monto del comprobante (*' + fmt(ctx.comprobante_monto) + '*) no coincide con el total de tu pedido (*' + fmt(pedido.total) + '*).\n'
      + 'Por favor transferí el monto exacto a *MANHATTAN FOOD GROUP E.A.S.* (ALIAS RUC 80152605-1) y envianos el comprobante 🙏';
    problema = (problema ? problema + ' | ' : '') + 'Comprobante con monto distinto: ' + fmt(ctx.comprobante_monto) + ' vs total ' + fmt(pedido.total);
  } else if (ctx.comprobante_motivo) {
    reply = '⚠️ No pudimos validar tu comprobante: ' + ctx.comprobante_motivo + '.\n'
      + 'Por favor transferí a *MANHATTAN FOOD GROUP E.A.S.* (ALIAS RUC 80152605-1, ueno bank) el total de *' + fmt(pedido.total) + '* y envianos el comprobante 🙏';
  } else {
    reply = '📸 Para confirmar tu pedido por transferencia, envianos por acá la *foto o captura del comprobante* 🙏 Apenas lo recibamos lo marchamos a la cocina 🧑‍🍳';
  }
}
// Red de seguridad: el modelo dice que está confirmado pero no hay pedido registrado
if (!mp && !usoPendiente && !ctx.tiene_pedido_activo && /PEDIDO CONFIRMADO|en marcha en (la )?cocina|pedido (ya )?(est[aá] |qued[oó] )?confirmado/i.test(reply)) {
  reply = '¡Gracias! 🙌 Estoy registrando tu pedido, un encargado te lo confirma en instantes.';
  problema = (problema ? problema + ' | ' : '') + 'El bot le dijo al cliente que su pedido estaba confirmado pero NO se registró: revisar el chat y cargarlo a mano.';
}
// Nunca dejar al cliente sin respuesta: si el modelo falló o no dijo nada, pedir que repita y repetir el último mensaje
let esRespaldo = false;
if (!String(reply || '').trim() && imagen_claves.length === 0 && !cv_data) {
  esRespaldo = true;
  const ult = String(ctx.ultima_respuesta || '').trim();
  reply = 'Disculpá, no entendí bien tu mensaje 🙏 ¿Me lo podrías repetir o contarme qué necesitás?'
    + (ult ? '\n\nTe repito mi último mensaje 👇\n\n' + ult : '');
}
// === El pedido solo va a caja si el CLIENTE confirmó (o mandó el comprobante de la transferencia) ===
const textoConfirma = String(ctx.text || p.text || '').split('\n').filter(l => !/^\[(ESTADO|CONTENIDO|UBICACION)/.test(l.trim())).join(' ').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
const confirmaTexto = /(\bsi+\b|confirm|\bdale\b|\bok\b|\bokey\b|\blisto\b|metele|correcto|perfecto|de una|\bclaro\b|\bva\b|exacto|asi (esta )?bien|todo bien|\bbueno\b|👍|✅)/i.test(textoConfirma);
const confirmaComprobante = esTransfP && ctx.comprobante_recibido === true && p.hasImage === true;
const clienteConfirmo = confirmaTexto || confirmaComprobante;
if (pedidoValido && !pedidoDuplicado && !clienteConfirmo) {
  const lineasR = String(pedido.items).split(/,(?![^(]*\))/).map(lineaItem).join('\n');
  const pagoR = /transf/i.test(pedido.pago) ? 'Transferencia' : (/(tarj|qr|pos)/i.test(pedido.pago) ? 'QR / Tarjeta' : 'Efectivo');
  reply = '📋 *RESUMEN DE TU PEDIDO:*\n---\n' + lineasR + '\n\n'
    + '💰 *Subtotal:* ' + fmt(pedido.subtotal) + '\n'
    + (esRetiro ? '🏪 *Entrega:* Retiro en el local\n' : ('🛵 *Envío:* ' + fmt(pedido.envio) + '\n'))
    + '👉 *TOTAL:* ' + fmt(pedido.total) + '\n'
    + '💵 *Pago:* ' + pagoR + '\n---\n\n'
    + '¿Nos confirmás el pedido para marcharlo a la cocina? 🧑‍🍳';
}
// Resumen VERIFICADO para el cliente: es exactamente lo que va a caja
if (pedidoValido && !pedidoDuplicado && clienteConfirmo) {
  const lineas = String(pedido.items).split(/,(?![^(]*\))/).map(lineaItem).join('\n');
  const pagoTxt = /transf/i.test(pedido.pago) ? 'Transferencia ✅' : (/(tarj|qr|pos)/i.test(pedido.pago) ? 'QR / Tarjeta' : 'Efectivo');
  reply = reply.replace(/\n*¡Muchas gracias por elegir Benji's! ❤️\s*$/, '')
    + '\n\n✅ *Pedido verificado y enviado a caja:*\n' + lineas + '\n'
    + (esRetiro ? '🏪 Retiro en el local' : ('🛵 Delivery' + (verificacion.km ? ' (' + verificacion.km + ' km)' : '') + ' · envío ' + fmt(pedido.envio))) + '\n'
    + '💵 ' + pagoTxt + '\n'
    + '👉 *TOTAL: ' + fmt(pedido.total) + '*'
    + (verificacion.correcciones.some(c => /^total/.test(c)) ? ' _(total recalculado: subtotal + envío)_' : '') + '\n'
    + 'Si algo no está bien, avisanos ahora 🙏\n\n¡Muchas gracias por elegir Benji\'s! ❤️';
}
// patch25: pedido pendiente, memoria y aviso al encargado
const registrado25 = pedidoValido && !pedidoDuplicado && clienteConfirmo;
let pendiente_op = '', pendiente_json = '';
if (pedidoValido && !pedidoDuplicado && !clienteConfirmo) { pendiente_op = 'guardar'; pendiente_json = JSON.stringify(pedido); }
else if (registrado25 || pedidoDuplicado) pendiente_op = 'borrar';
const sinRegistrar = /NO se registr|NO pudo registrar/.test(problema);
if (sinRegistrar && pendiente_op === '') pendiente_op = 'escalado';
const normM = (t) => String(t || '').replace(/\s+/g, ' ').trim().slice(0, 80);
const memoria_ai = normM(reply) !== normM(replyIA) ? reply : '';
let aviso_encargado = '⚠️ RECLAMO - Cliente ' + p.phone + ' (' + p.name + '): ' + problema;
if (sinRegistrar) {
  const ref = pedido || pendiente || null;
  aviso_encargado = '🆘 *PEDIDO SIN REGISTRAR* — cargalo a mano\n'
    + '👤 Cliente: ' + (p.name || '-') + ' (' + p.phone + ')\n'
    + (ref && ref.items
      ? ('🍽️ ' + ref.items + '\n' + '👉 Total: ' + fmt(ref.total) + ' · 💵 ' + (ref.pago || '-') + '\n'
        + (/retiro|busc/i.test(String(ref.tipo_entrega || '')) ? '🏪 Retiro en el local\n' : ('📍 ' + (ref.ubicacion || 'sin ubicación') + '\n')))
      : '🍽️ (el bot no llegó a armar el detalle: revisá el chat)\n')
    + '💬 https://wa.me/' + p.phone + '\n'
    + '📝 ' + problema;
}
return [{ json: { pendiente_op, pendiente_json, memoria_ai, aviso_encargado, usoPendiente, reply, esRespaldo, verificacion, imagen_clave, imagen_claves, hasPedido: pedidoValido && !pedidoDuplicado && clienteConfirmo, pedido: pedido || {}, hasProblema: !!problema, problema, hasAlertaDelivery: !!alerta_delivery, alerta_delivery: alerta_delivery || {}, hasUbic: !!ubic, ubic: ubic || {}, phone: p.phone, name: p.name, hasCv: !!cv_data, cv: cv_data || {}, hasRating: !!rating_data, rating: rating_data ? (Number(rating_data.score) || 5) : null } }];