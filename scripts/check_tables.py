import sqlite3
import sys
sys.stdout.reconfigure(encoding='utf-8')

conn = sqlite3.connect('scripts/n8n.sqlite')
c = conn.cursor()

c.execute("SELECT name, sql FROM sqlite_master WHERE name LIKE '%publish%' OR name LIKE '%version%' OR name LIKE '%webhook%'")
for r in c.fetchall():
    print("Table:", r[0])
    print(r[1])
    print("-" * 50)
