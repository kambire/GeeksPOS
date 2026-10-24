const sqlite3 = require('/usr/local/lib/node_modules/n8n/node_modules/sqlite3');
const db = new sqlite3.Database('/home/node/.n8n/database.sqlite');

db.serialize(() => {
  const vId = 'b71cdec1-8947-4f8d-9b07-d4d0af02a48b';
  const wfId = 'OlRZzvyTYUzRc6ec';

  db.run("DELETE FROM workflow_published_version WHERE workflowId = ?", [wfId], function(err) {
    if (err) console.error(err);
  });

  db.run("INSERT INTO workflow_published_version (workflowId, publishedVersionId) VALUES (?, ?)", [wfId, vId], function(err) {
    if (err) console.error('Error setting published version:', err);
    else console.log('workflow_published_version set successfully');
  });

  db.run("INSERT INTO workflow_publish_history (workflowId, versionId, event, userId) VALUES (?, ?, 'activated', '57b5e5c0-d0a1-49a9-9f8c-5f5b605e4d49')", [wfId, vId], function(err) {
    if (err) console.error('Error adding publish history:', err);
    else console.log('workflow_publish_history recorded successfully');
  });

  db.all("SELECT * FROM workflow_published_version", (err, rows) => {
    console.log('Published version now:', rows);
    db.close();
  });
});
