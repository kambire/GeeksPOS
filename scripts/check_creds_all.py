import sqlite3

conn = sqlite3.connect('scripts/n8n.sqlite')
c = conn.cursor()
c.execute("SELECT id, name, type FROM credentials_entity")
print("scripts/n8n.sqlite credentials:", c.fetchall())

conn2 = sqlite3.connect('scripts/n8n_live.sqlite')
c2 = conn2.cursor()
c2.execute("SELECT id, name, type FROM credentials_entity")
print("scripts/n8n_live.sqlite credentials:", c2.fetchall())
