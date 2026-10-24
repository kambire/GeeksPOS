const sqlite3 = require('/usr/local/lib/node_modules/n8n/node_modules/sqlite3');
const db = new sqlite3.Database('/home/node/.n8n/database.sqlite');

db.all("SELECT id, status, workflowId, startedAt, stoppedAt FROM execution_entity ORDER BY id DESC LIMIT 3", (err, rows) => {
  console.log('Executions:', rows);
  if (rows && rows.length > 0) {
    const lastId = rows[0].id;
    db.all("SELECT data FROM execution_data WHERE executionId = ?", [lastId], (err2, dataRows) => {
      if (dataRows && dataRows.length > 0) {
        try {
          const d = JSON.parse(dataRows[0].data);
          console.log('ResultData error:', d.resultData ? d.resultData.error : null);
          console.log('Last node executed:', d.resultData ? d.resultData.lastNodeExecuted : null);
        } catch (e) {
          console.log('Data raw:', dataRows[0].data.slice(0, 500));
        }
      }
      db.close();
    });
  } else {
    db.close();
  }
});
