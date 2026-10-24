import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

nodes = {n['name']: n for n in wf['nodes']}

print(json.dumps(nodes.get('Memoria por cliente', {}).get('credentials'), indent=2))
print(json.dumps(nodes.get('Buscar ubicaciones', {}).get('credentials'), indent=2))
