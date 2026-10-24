import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

nodes = {n['name']: n for n in wf['nodes']}
conns = wf['connections']

def trace(node_name, depth=0, visited=None):
    if visited is None: visited = set()
    if depth > 12 or node_name in visited: return
    visited.add(node_name)
    n = nodes.get(node_name, {})
    print('  '*depth + f"> {node_name} [{n.get('type')}]")
    targets = conns.get(node_name, {}).get('main', [])
    for group_idx, group in enumerate(targets):
        for c in group:
            trace(c['node'], depth+1, visited.copy())

print("--- Trace from Webhook WhatsApp ---")
trace('Webhook WhatsApp')

print("\n--- Trace from Agente IA ---")
trace('Agente IA')
