import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

nodes = {n['name']: n for n in wf['nodes']}
print("Tiene audio?:", json.dumps(nodes.get('Tiene audio?', {}).get('parameters'), indent=2))
print("Tiene imagen?:", json.dumps(nodes.get('Tiene imagen?', {}).get('parameters'), indent=2))
