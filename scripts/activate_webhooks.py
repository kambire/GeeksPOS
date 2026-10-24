import sqlite3

conn = sqlite3.connect('scripts/n8n_live.sqlite')
c = conn.cursor()

# Set active = 1 in workflow_entity
c.execute("UPDATE workflow_entity SET active = 1 WHERE id = 'OlRZzvyTYUzRc6ec'")

# Clear existing webhooks for this workflow
c.execute("DELETE FROM webhook_entity WHERE workflowId = 'OlRZzvyTYUzRc6ec'")

# Insert all 3 webhooks
webhooks = [
    ('OlRZzvyTYUzRc6ec', 'restaurante-wa', 'POST', 'Webhook WhatsApp', None, None),
    ('OlRZzvyTYUzRc6ec', 'qr-benjis', 'GET', '[QR] Webhook QR', None, None),
    ('OlRZzvyTYUzRc6ec', 'restaurante-web', 'POST', 'Webhook WebApp', None, None)
]

c.executemany("INSERT INTO webhook_entity (workflowId, webhookPath, method, node, webhookId, pathLength) VALUES (?, ?, ?, ?, ?, ?)", webhooks)

conn.commit()

c.execute("SELECT * FROM webhook_entity")
print("Webhooks registered in sqlite:")
for r in c.fetchall():
    print(r)

conn.close()
print("Updated n8n_live.sqlite successfully!")
