import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

conns = wf['connections']
print("Parsear mensaje connections:", conns.get('Parsear mensaje'))
