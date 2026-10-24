import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

nodes = {n['name']: n for n in wf['nodes']}

print("--- Responder (chat) ---")
print(json.dumps(nodes['Responder (chat)'].get('parameters'), indent=2))

print("\n--- Responder (pedido) ---")
print(json.dumps(nodes['Responder (pedido)'].get('parameters'), indent=2))
