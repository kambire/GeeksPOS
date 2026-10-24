import sys
import json
sys.stdout.reconfigure(encoding='utf-8')

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

nodes = {n['name']: n for n in wf['nodes']}

print("--- Nodes referencing 'Webhook WhatsApp' ---")
for n in wf['nodes']:
    s = json.dumps(n)
    if 'Webhook WhatsApp' in s and n['name'] != 'Webhook WhatsApp':
        print(f"- {n['name']} ({n['type']})")

print("\n--- Nodes referencing 'Parsear mensaje' ---")
for n in wf['nodes']:
    s = json.dumps(n)
    if 'Parsear mensaje' in s and n['name'] != 'Parsear mensaje':
        print(f"- {n['name']} ({n['type']})")
