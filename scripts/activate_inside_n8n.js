const sqlite3 = require('/usr/local/lib/node_modules/n8n/node_modules/sqlite3');
const db = new sqlite3.Database('/home/node/.n8n/database.sqlite');

db.serialize(() => {
  db.run("UPDATE workflow_entity SET active = 1 WHERE id = 'OlRZzvyTYUzRc6ec'", function(err) {
    if (err) console.error('Error updating workflow:', err);
    else console.log('Workflow set to active: 1, changes:', this.changes);
  });

  db.run("DELETE FROM webhook_entity WHERE workflowId = 'OlRZzvyTYUzRc6ec'", function(err) {
    if (err) console.error('Error clearing webhooks:', err);
    else console.log('Cleared old webhooks, changes:', this.changes);
  });

  const stmt = db.prepare("INSERT OR REPLACE INTO webhook_entity (workflowId, webhookPath, method, node) VALUES (?, ?, ?, ?)");
  stmt.run('OlRZzvyTYUzRc6ec', 'restaurante-wa', 'POST', 'Webhook WhatsApp');
  stmt.run('OlRZzvyTYUzRc6ec', 'qr-benjis', 'GET', '[QR] Webhook QR');
  stmt.run('OlRZzvyTYUzRc6ec', 'restaurante-web', 'POST', 'Webhook WebApp');
  stmt.finalize((err) => {
    if (err) console.error('Error inserting webhooks:', err);
    else console.log('Webhooks inserted successfully');
  });

  db.all("SELECT * FROM webhook_entity", (err, rows) => {
    if (err) console.error(err);
    else console.log('Current webhooks:', rows);
    db.close();
  });
});
