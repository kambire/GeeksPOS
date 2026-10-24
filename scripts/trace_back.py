import json

with open('scripts/n8n_workflow_restaurante.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    wf = data[0] if isinstance(data, list) else data

nodes = {n['name']: n for n in wf['nodes']}
conns = wf['connections']

# Trace backwards from Agente IA to find the chain
parents = {}
for src, cdict in conns.items():
    for otype, groups in cdict.items():
        if otype == 'main':
            for group in groups:
                for c in group:
                    dest = c['node']
                    parents.setdefault(dest, []).append(src)

def trace_back(node, depth=0):
    indent = "  " * depth
    print(f"{indent}<- {node}")
    for p in parents.get(node, []):
        trace_back(p, depth+1)

print("Trace back from Agente IA:")
trace_back('Agente IA')
