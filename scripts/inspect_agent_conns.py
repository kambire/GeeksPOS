import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

conns = wf['connections']

print("Connections TO 'Agente IA':")
for src, cdict in conns.items():
    for otype, groups in cdict.items():
        for group in groups:
            for c in group:
                if c['node'] == 'Agente IA':
                    print(f"- {src} [{otype}]")
