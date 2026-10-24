const sqlite3 = require('/usr/local/lib/node_modules/n8n/node_modules/sqlite3');
const db = new sqlite3.Database('/home/node/.n8n/database.sqlite');

db.all("SELECT versionId, workflowId, createdAt FROM workflow_history WHERE workflowId = 'OlRZzvyTYUzRc6ec' ORDER BY createdAt DESC LIMIT 5", (err, rows) => {
  console.log('History rows:', rows);
});

db.all("SELECT * FROM workflow_published_version WHERE workflowId = 'OlRZzvyTYUzRc6ec'", (err, rows) => {
  console.log('Published version:', rows);
});

db.all("SELECT versionId FROM workflow_entity WHERE id = 'OlRZzvyTYUzRc6ec'", (err, rows) => {
  console.log('Workflow entity versionId:', rows);
});
