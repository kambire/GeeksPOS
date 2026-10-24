import sqlite3

conn = sqlite3.connect('scripts/n8n.sqlite')
c = conn.cursor()

c.execute("SELECT id, name, type FROM credentials_entity")
for row in c.fetchall():
    print("Cred:", row)

c.execute("SELECT id, name, active FROM workflow_entity")
for row in c.fetchall():
    print("Workflow:", row)
