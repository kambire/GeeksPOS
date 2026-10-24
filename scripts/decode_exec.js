const sqlite3 = require('/usr/local/lib/node_modules/n8n/node_modules/sqlite3');
const db = new sqlite3.Database('/home/node/.n8n/database.sqlite');

db.all("SELECT id, status FROM execution_entity WHERE status = 'error' ORDER BY id DESC LIMIT 1", (err, rows) => {
  if (rows && rows.length > 0) {
    const errId = rows[0].id;
    console.log('Error exec id:', errId);
    db.all("SELECT data FROM execution_data WHERE executionId = ?", [errId], (err2, dRows) => {
      const arr = JSON.parse(dRows[0].data);
      const seen = new Map();
      function resolve(val) {
        if (typeof val === 'string' && /^\d+$/.test(val)) {
          const idx = parseInt(val, 10);
          if (seen.has(idx)) return seen.get(idx);
          if (idx < arr.length) {
            seen.set(idx, '[Circular]');
            const r = resolve(arr[idx]);
            seen.set(idx, r);
            return r;
          }
        }
        if (Array.isArray(val)) return val.map(resolve);
        if (val && typeof val === 'object') {
          const res = {};
          for (const k of Object.keys(val)) res[k] = resolve(val[k]);
          return res;
        }
        return val;
      }
      const root = resolve(arr[0]);
      console.log('ResultData error:', root.resultData ? root.resultData.error : 'none');
      console.log('Last node executed:', root.resultData ? root.resultData.lastNodeExecuted : 'none');
      db.close();
    });
  } else {
    db.close();
  }
});
