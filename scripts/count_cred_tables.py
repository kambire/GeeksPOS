import sqlite3

conn = sqlite3.connect('scripts/n8n.sqlite')
c = conn.cursor()

tables = [
    'credentials_entity', 'shared_credentials', 'dynamic_credential_resolver',
    'dynamic_credential_user_entry', 'dynamic_credential_entry', 'credential_dependency',
    'instance_credential_assignment', 'agent_credential_dependency'
]

for t in tables:
    c.execute(f"SELECT count(*) FROM {t}")
    print(f"{t}: {c.fetchone()[0]}")
