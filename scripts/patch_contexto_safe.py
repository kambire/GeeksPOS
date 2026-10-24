import json
import sys
sys.stdout.reconfigure(encoding='utf-8')

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    is_list = isinstance(data, list)
    wf = data[0] if is_list else data

nodes = {n['name']: n for n in wf['nodes']}

# 1. Update Contexto
ctx = nodes.get('Contexto')
if ctx:
    code = ctx['parameters']['jsCode']
    old_ag = "const ag = $('Agrupar mensajes').first().json || {};"
    new_ag = """let ag = {};
try {
  if ($('Agrupar mensajes').isExecuted) {
    ag = $('Agrupar mensajes').first().json || {};
  }
} catch (e) {}"""
    if old_ag in code:
        ctx['parameters']['jsCode'] = code.replace(old_ag, new_ag, 1)
        print("✓ Contexto patched with safe isExecuted check for Agrupar mensajes")
    else:
        print("! Warning: old_ag not found in Contexto")

# 2. Update Procesar salida
ps = nodes.get('Procesar salida')
if ps:
    code = ps['parameters']['jsCode']
    old_ps_ag = "try { const ag = $('Agrupar mensajes').first().json; if (ag && ag.texto_agrupado != null) textoCliente = String(ag.texto_agrupado); } catch (e) {}"
    new_ps_ag = """try {
  if ($('Agrupar mensajes').isExecuted) {
    const ag = $('Agrupar mensajes').first().json;
    if (ag && ag.texto_agrupado != null) textoCliente = String(ag.texto_agrupado);
  }
} catch (e) {}"""
    if old_ps_ag in code:
        ps['parameters']['jsCode'] = code.replace(old_ps_ag, new_ps_ag, 1)
        print("✓ Procesar salida patched with safe isExecuted check for Agrupar mensajes")
    else:
        print("! Warning: old_ps_ag not found in Procesar salida")

# Save
result = [wf] if is_list else wf
with open('scripts/n8n_workflow_restaurante.json', 'w', encoding='utf-8') as f:
    json.dump(result, f, indent=2, ensure_ascii=False)

print("Saved updated workflow to scripts/n8n_workflow_restaurante.json")
