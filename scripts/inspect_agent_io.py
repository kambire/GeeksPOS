import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

nodes = {n['name']: n for n in wf['nodes']}

print("--- Agente IA parameters ---")
print(json.dumps(nodes['Agente IA'].get('parameters'), indent=2))

print("\n--- Parsear mensaje parameters ---")
print(nodes['Parsear mensaje'].get('parameters', {}).get('jsCode', '')[:500])

print("\n--- Procesar salida parameters ---")
print(nodes['Procesar salida'].get('parameters', {}).get('jsCode', '')[:500])

# Also check what directly connects to 'Agente IA'
for src, conns in wf['connections'].items():
    for out_type, groups in conns.items():
        for group in groups:
            for c in group:
                if c['node'] == 'Agente IA' and out_type == 'main':
                    print(f"Connects to Agente IA (main): {src}")
