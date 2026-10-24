const sqlite3 = require('/usr/local/lib/node_modules/n8n/node_modules/sqlite3');
const db = new sqlite3.Database('/home/node/.n8n/database.sqlite');

db.all("SELECT id, status, mode, workflowId, startedAt, stoppedAt FROM execution_entity ORDER BY id DESC LIMIT 5", (err, rows) => {
  console.log('Executions:', rows);
  db.close();
});
