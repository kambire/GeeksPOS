const sqlite3 = require('/usr/local/lib/node_modules/n8n/node_modules/sqlite3');
const db = new sqlite3.Database('/home/node/.n8n/database.sqlite');

db.all("SELECT id, status, startedAt, stoppedAt FROM execution_entity ORDER BY id DESC LIMIT 1", (err, rows) => {
  if (rows && rows.length > 0) {
    const lastId = rows[0].id;
    console.log('Last exec:', rows[0]);
    db.all("SELECT data FROM execution_data WHERE executionId = ?", [lastId], (err2, dRows) => {
      if (dRows && dRows.length > 0) {
        const raw = dRows[0].data;
        // In n8n execution data, let's search for "WrappedExecutionError" or "error" or lastNodeExecuted
        try {
          const parsed = JSON.parse(raw);
          const findErrors = (obj, path = '') => {
            if (!obj || typeof obj !== 'object') return;
            if (obj.message && obj.stack) {
              console.log('Found error at ' + path + ':', obj.message);
              console.log(obj.stack);
            }
            if (obj.lastNodeExecuted) {
              console.log('lastNodeExecuted:', obj.lastNodeExecuted);
            }
            for (const k of Object.keys(obj)) {
              if (path.split('.').length < 6) findErrors(obj[k], path + '.' + k);
            }
          };
          findErrors(parsed);
        } catch (e) {
          console.error(e);
        }
      }
      db.close();
    });
  } else {
    db.close();
  }
});
