import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

nodes = {n['name']: n for n in wf['nodes']}

print("Es gerente?:", json.dumps(nodes.get('Es gerente?', {}).get('parameters'), indent=2))
print("Es intervencion?:", json.dumps(nodes.get('Es intervencion?', {}).get('parameters'), indent=2))
print("Es comando?:", json.dumps(nodes.get('Es comando?', {}).get('parameters'), indent=2))
print("Es mensaje?:", json.dumps(nodes.get('Es mensaje?', {}).get('parameters'), indent=2))
