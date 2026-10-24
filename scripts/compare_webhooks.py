import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

for n in wf['nodes']:
    if n['name'] == 'Webhook WhatsApp':
        print("Webhook WhatsApp:", json.dumps(n, indent=2))
    if n['name'] == 'Webhook WebApp':
        print("Webhook WebApp:", json.dumps(n, indent=2))
