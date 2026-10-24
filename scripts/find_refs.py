with open('scripts/contexto_code.js', 'r', encoding='utf-8') as f:
    code = f.read()

import re
matches = re.findall(r"\$\(['\"](.*?)['\"]\)", code)
print("Nodes referenced in Contexto:", set(matches))
