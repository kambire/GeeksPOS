<?php
// Cache busting de assets (incrementar cuando cambian CSS/JS)
define('APPVER', '?v=8.2');

// Repositorio GitHub (owner/nombre) que sirve como servidor de actualizaciones.
// Los releases deben incluir un asset llamado update_<build>.zip que contenga:
//   - files.json      (lista de archivos: [{"path":"application/","file":"Foo.php"}, ...])
//   - update_build_<build>.sql  (migración de BD opcional)
define('UPDATE_REPO', 'kambire/GeeksPOS');
