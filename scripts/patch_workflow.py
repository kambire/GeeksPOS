import json
import uuid
import sys
sys.stdout.reconfigure(encoding='utf-8')

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    is_list = isinstance(data, list)
    wf = data[0] if is_list else data

nodes = {n['name']: n for n in wf['nodes']}
conns = wf['connections']

print(f"Current nodes: {len(wf['nodes'])}, connections: {len(conns)}")

# 1. Update Parsear mensaje jsCode to support WebApp
parsear_node = nodes.get('Parsear mensaje')
if not parsear_node:
    raise Exception("Node 'Parsear mensaje' not found")

js_code = parsear_node['parameters']['jsCode']

# We replace the initial webhook retrieval with dual check
old_start = "const wh = $('Webhook WhatsApp').first().json;\nconst body = wh.body || wh;"
new_start = """let wh = null;
let isWebApp = false;
try {
  const w = $('Webhook WebApp').first();
  if (w && w.json) {
    wh = w.json;
    isWebApp = true;
  }
} catch (e) {}

if (!isWebApp) {
  try {
    wh = $('Webhook WhatsApp').first().json;
  } catch (e) {}
}
const body = wh ? (wh.body || wh) : {};

if (isWebApp) {
  const rawPhone = String(body.phone || body.telefono || '').replace(/\\D/g, '');
  const phone = rawPhone || ('web_' + (body.session_id || 'guest'));
  const name = body.name || body.nombre || 'Cliente Web';
  let text = String(body.message || body.text || '').trim();
  
  if (body.location && body.location.lat != null) {
    const lat = Number(body.location.lat), lng = Number(body.location.lng);
    const link = 'https://maps.google.com/?q=' + lat + ',' + lng;
    const R = 6371, dLat = (lat - (-25.3411735)) * Math.PI / 180, dLng = (lng - (-57.6019842)) * Math.PI / 180;
    const a = Math.sin(dLat/2)**2 + Math.cos((-25.3411735)*Math.PI/180) * Math.cos(lat*Math.PI/180) * Math.sin(dLng/2)**2;
    let km = R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)) * 1.3;
    let envio = km <= 1 ? 5000 : (km <= 5 ? 10000 : (km <= 10 ? 15000 : null));
    if (envio === null) text += ' [UBICACION_COMPARTIDA] Ubicacion: ' + link + '. Distancia aprox ' + km.toFixed(1) + ' km. NO_ENTREGABLE: supera los 10 km, no se puede hacer delivery a esa direccion.';
    else text += ' [UBICACION_COMPARTIDA] Ubicacion: ' + link + '. Distancia aprox ' + km.toFixed(1) + ' km. Costo de envio: ' + envio + ' Gs.';
  }
  
  return [{
    json: {
      source: 'webapp',
      isWebApp: true,
      phone: phone,
      name: name,
      text: text,
      tipo: 'mensaje',
      esPrueba: false,
      rol: 'cliente',
      hasImage: false,
      hasAudio: false,
      hasDoc: false,
      msgId: 'web_' + Date.now()
    }
  }];
}"""

if old_start in js_code:
    js_code = js_code.replace(old_start, new_start, 1)
    parsear_node['parameters']['jsCode'] = js_code
    print("✓ Successfully patched Parsear mensaje with WebApp dual support")
else:
    print("! Warning: old_start not found in Parsear mensaje, checking if already patched...")

# 2. Add Webhook WebApp node if not exists
webhook_webapp_name = "Webhook WebApp"
if webhook_webapp_name in nodes:
    nodes[webhook_webapp_name]["webhookId"] = "restaurante-web"
if webhook_webapp_name not in nodes:
    webhook_webapp_node = {
        "parameters": {
            "httpMethod": "POST",
            "path": "restaurante-web",
            "responseMode": "responseNode",
            "options": {}
        },
        "id": "web-hook-webapp-0001",
        "name": webhook_webapp_name,
        "webhookId": "restaurante-web",
        "type": "n8n-nodes-base.webhook",
        "typeVersion": 2,
        "position": [
            360,
            1560
        ]
    }
    wf['nodes'].append(webhook_webapp_node)
    nodes[webhook_webapp_name] = webhook_webapp_node
    print("✓ Added Webhook WebApp node")

# Connect Webhook WebApp -> Parsear mensaje
if webhook_webapp_name not in conns:
    conns[webhook_webapp_name] = {
        "main": [
            [
                {
                    "node": "Parsear mensaje",
                    "type": "main",
                    "index": 0
                }
            ]
        ]
    }
    print("✓ Connected Webhook WebApp -> Parsear mensaje")

# 3. Add 'Es WebApp? (bypass)' node right after 'Texto del mensaje'
es_webapp_bypass_name = "Es WebApp? (bypass)"
if es_webapp_bypass_name not in nodes:
    es_webapp_bypass_node = {
        "parameters": {
            "conditions": {
                "options": {
                    "caseSensitive": True,
                    "leftValue": "",
                    "typeValidation": "loose",
                    "version": 2
                },
                "conditions": [
                    {
                        "id": "cond-webapp-bypass",
                        "leftValue": "={{ $('Parsear mensaje').first().json.isWebApp === true || $('Parsear mensaje').first().json.source === 'webapp' }}",
                        "rightValue": True,
                        "operator": {
                            "type": "boolean",
                            "operation": "true",
                            "singleValue": True
                        }
                    }
                ],
                "combinator": "and"
            },
            "options": {}
        },
        "id": "if-webapp-bypass-0001",
        "name": es_webapp_bypass_name,
        "type": "n8n-nodes-base.if",
        "typeVersion": 2,
        "position": [
            2400,
            1360
        ]
    }
    wf['nodes'].append(es_webapp_bypass_node)
    nodes[es_webapp_bypass_name] = es_webapp_bypass_node
    print("✓ Added Es WebApp? (bypass) node")

# Reconnect: Texto del mensaje -> Es WebApp? (bypass)
# True (output 0) -> Buscar ubicaciones (bypasses 8-sec wait)
# False (output 1) -> Guardar msg pendiente (existing WhatsApp flow)
conns["Texto del mensaje"] = {
    "main": [
        [
            {
                "node": es_webapp_bypass_name,
                "type": "main",
                "index": 0
            }
        ]
    ]
}

conns[es_webapp_bypass_name] = {
    "main": [
        [
            {
                "node": "Buscar ubicaciones",
                "type": "main",
                "index": 0
            }
        ],
        [
            {
                "node": "Guardar msg pendiente",
                "type": "main",
                "index": 0
            }
        ]
    ]
}
print("✓ Reconnected Texto del mensaje -> Es WebApp? (bypass) -> Buscar ubicaciones / Guardar msg pendiente")

# 4. Add 'Responder WebApp' (respondToWebhook) node
responder_webapp_name = "Responder WebApp"
if responder_webapp_name not in nodes:
    responder_webapp_node = {
        "parameters": {
            "respondWith": "json",
            "responseBody": "={\n  \"status\": \"success\",\n  \"reply\": {{ JSON.stringify($json.reply || '') }},\n  \"has_pedido\": {{ !!$json.hasPedido || !!$json.pedido }},\n  \"pedido\": {{ JSON.stringify($json.pedido || null) }},\n  \"ticket\": {{ JSON.stringify($json.ticket_caja || null) }},\n  \"phone\": {{ JSON.stringify($json.phone || '') }}\n}",
            "options": {
                "responseCode": 200,
                "responseHeaders": {
                    "entries": [
                        {
                            "name": "Content-Type",
                            "value": "application/json; charset=utf-8"
                        },
                        {
                            "name": "Access-Control-Allow-Origin",
                            "value": "*"
                        }
                    ]
                }
            }
        },
        "id": "respond-webapp-node-0001",
        "name": responder_webapp_name,
        "type": "n8n-nodes-base.respondToWebhook",
        "typeVersion": 1.1,
        "position": [
            7200,
            1600
        ]
    }
    wf['nodes'].append(responder_webapp_node)
    nodes[responder_webapp_name] = responder_webapp_node
    print("✓ Added Responder WebApp (respondToWebhook) node")

# 5. Add 'Es WebApp? (chat)' IF node in the chat branch
es_webapp_chat_name = "Es WebApp? (chat)"
if es_webapp_chat_name not in nodes:
    es_webapp_chat_node = {
        "parameters": {
            "conditions": {
                "options": {
                    "caseSensitive": True,
                    "leftValue": "",
                    "typeValidation": "loose",
                    "version": 2
                },
                "conditions": [
                    {
                        "id": "cond-webapp-chat",
                        "leftValue": "={{ $('Parsear mensaje').first().json.isWebApp === true || $('Parsear mensaje').first().json.source === 'webapp' }}",
                        "rightValue": True,
                        "operator": {
                            "type": "boolean",
                            "operation": "true",
                            "singleValue": True
                        }
                    }
                ],
                "combinator": "and"
            },
            "options": {}
        },
        "id": "if-webapp-chat-0001",
        "name": es_webapp_chat_name,
        "type": "n8n-nodes-base.if",
        "typeVersion": 2,
        "position": [
            6800,
            1600
        ]
    }
    wf['nodes'].append(es_webapp_chat_node)
    nodes[es_webapp_chat_name] = es_webapp_chat_node
    print("✓ Added Es WebApp? (chat) node")

# In Hay pedido?, False branch (index 1) goes to Es WebApp? (chat)
# Es WebApp? (chat): True -> Responder WebApp, False -> Responder (chat)
conns["Hay pedido?"]["main"][1] = [
    {
        "node": es_webapp_chat_name,
        "type": "main",
        "index": 0
    }
]

conns[es_webapp_chat_name] = {
    "main": [
        [
            {
                "node": responder_webapp_name,
                "type": "main",
                "index": 0
            }
        ],
        [
            {
                "node": "Responder (chat)",
                "type": "main",
                "index": 0
            }
        ]
    ]
}
print("✓ Reconnected Hay pedido? (false) -> Es WebApp? (chat) -> Responder WebApp / Responder (chat)")

# In the order branch (Construir ticket), add connection to Responder WebApp so if it was an order, WebApp also gets response!
# Also check if Construir ticket connects to Responder WebApp
ct_conns = conns.get("Construir ticket", {}).get("main", [])
if ct_conns:
    # Append Responder WebApp to group 0
    if not any(c['node'] == responder_webapp_name for c in ct_conns[0]):
        ct_conns[0].append({
            "node": responder_webapp_name,
            "type": "main",
            "index": 0
        })
        print("✓ Connected Construir ticket -> Responder WebApp for instant order confirmation")

# Save updated workflow
result = [wf] if is_list else wf
with open('scripts/n8n_workflow_restaurante.json', 'w', encoding='utf-8') as f:
    json.dump(result, f, indent=2, ensure_ascii=False)

print("Saved updated workflow to scripts/n8n_workflow_restaurante.json successfully!")
