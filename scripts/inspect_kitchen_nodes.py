import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

nodes = {n['name']: n for n in wf['nodes']}

print("--- avisar_cocina ---")
print(json.dumps(nodes.get('avisar_cocina', {}).get('parameters'), indent=2))

print("--- Enviar a Norva ERP ---")
print(json.dumps(nodes.get('Enviar a Norva ERP', {}).get('parameters'), indent=2))
