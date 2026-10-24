import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

nodes = {n['name']: n for n in wf['nodes']}

print("--- Texto del mensaje ---")
print(nodes.get('Texto del mensaje', {}).get('parameters', {}).get('jsCode', ''))
