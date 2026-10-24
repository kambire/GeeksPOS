import sqlite3

conn = sqlite3.connect('scripts/n8n.sqlite')
c = conn.cursor()

c.execute("SELECT id, name, type FROM credentials_entity")
for row in c.fetchall():
    print(row)
