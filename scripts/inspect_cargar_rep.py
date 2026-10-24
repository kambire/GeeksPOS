import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

nodes = {n['name']: n for n in wf['nodes']}

print("--- Cargar repartidores ---")
print(json.dumps(nodes.get('Cargar repartidores', {}).get('parameters'), indent=2))
print("--- Es ubicacion? ---")
print(json.dumps(nodes.get('Es ubicacion?', {}).get('parameters'), indent=2))
