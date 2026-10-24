import sqlite3

conn = sqlite3.connect('scripts/n8n.sqlite')
c = conn.cursor()

c.execute("SELECT * FROM webhook_entity")
print("webhook_entity:", c.fetchall())

c.execute("SELECT * FROM workflow_publish_history")
print("workflow_publish_history:", c.fetchall())

c.execute("SELECT * FROM workflow_published_version")
print("workflow_published_version:", c.fetchall())
