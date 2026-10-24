import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

print('Workflow Name:', wf.get('name'))
print('Total nodes:', len(wf.get('nodes', [])))

nodes_by_id = {n['id']: n for n in wf.get('nodes', [])}
nodes_by_name = {n['name']: n for n in wf.get('nodes', [])}

print("\n--- Key Nodes ---")
for n in wf.get('nodes', []):
    t = n.get('type')
    name = n.get('name')
    if any(k in t.lower() or k in name.lower() for k in ['webhook', 'agent', 'respond', 'openai', 'evolution', 'mensaje', 'filtro', 'normaliz']):
        print(f"[{n.get('type')}] \"{name}\" (id: {n.get('id')})")

print("\n--- Triggers & Endpoints ---")
for n in wf.get('nodes', []):
    if 'webhook' in n.get('type', '').lower():
        print("Webhook:", n['name'], n.get('parameters'))

print("\n--- Connections from Webhook ---")
for src, conns in wf.get('connections', {}).items():
    if 'webhook' in src.lower() or 'agent' in src.lower() or 'ia' in src.lower():
        print(f"From '{src}':")
        for output_type, conn_list in conns.items():
            print(f"   [{output_type}] -> {[c['node'] for group in conn_list for c in group]}")
