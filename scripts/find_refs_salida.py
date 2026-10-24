with open('scripts/procesar_salida.js', 'r', encoding='utf-8') as f:
    code = f.read()

import re
matches = re.findall(r"\$\(['\"](.*?)['\"]\)", code)
print("Nodes referenced in Procesar salida:", set(matches))
