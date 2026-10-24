import sqlite3

conn = sqlite3.connect('scripts/n8n.sqlite')
c = conn.cursor()

c.execute("SELECT name FROM sqlite_master WHERE type='table'")
for row in c.fetchall():
    print(row[0])
