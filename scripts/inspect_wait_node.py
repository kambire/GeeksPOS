import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

nodes = {n['name']: n for n in wf['nodes']}

print("--- Esperar mas mensajes ---")
print(json.dumps(nodes.get('Esperar mas mensajes', {}).get('parameters'), indent=2))

# What connects to Esperar mas mensajes?
for src, cdict in wf['connections'].items():
    for otype, groups in cdict.items():
        for group in groups:
            for c in group:
                if c['node'] == 'Esperar mas mensajes':
                    print(f"Connects to Esperar mas mensajes: {src}")

# What does Esperar mas mensajes connect to?
print("Esperar mas mensajes connects to:", wf['connections'].get('Esperar mas mensajes'))
