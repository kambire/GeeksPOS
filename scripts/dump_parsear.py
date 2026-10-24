import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

nodes = {n['name']: n for n in wf['nodes']}

with open('scripts/parsear_mensaje_code.js', 'w', encoding='utf-8') as out:
    out.write(nodes['Parsear mensaje'].get('parameters', {}).get('jsCode', ''))

print("Saved Parsear mensaje code to scripts/parsear_mensaje_code.js")
