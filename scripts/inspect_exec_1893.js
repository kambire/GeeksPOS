const sqlite3 = require('/usr/local/lib/node_modules/n8n/node_modules/sqlite3');
const db = new sqlite3.Database('/home/node/.n8n/database.sqlite');

db.all("SELECT data FROM execution_data WHERE executionId = 1893", (err, rows) => {
  if (rows && rows.length > 0) {
    const d = JSON.parse(rows[0].data);
    console.log('Top keys:', Object.keys(d));
    if (d.resultData) {
      console.log('resultData keys:', Object.keys(d.resultData));
      console.dir(d.resultData, { depth: 3 });
    } else {
      console.dir(d, { depth: 3 });
    }
  }
  db.close();
});
