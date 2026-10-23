<?php
/**
 * Migración de seguridad GeeksIA (idempotente). Ejecutar dentro del contenedor:
 *   docker compose exec app php db_migrations/2026-10-23_geeksia_security.php
 *
 * - geopos_restkeys: keys atadas a un local (loc_id + scope), guardadas como hash SHA-256, revocables.
 * - Revoca la clave maestra de Evolution como clave REST y registra la clave de plataforma de .env.
 * - geopos_deliveries: tokens de la app guardados como hash y con vencimiento.
 * - geopos_delivery_login_attempts: control de fuerza bruta del login de repartidores.
 * - Cifra con AES-256-GCM las claves de IA que todavía estén en texto plano.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__);
$env = [];
if (is_readable($root . '/.env')) {
    foreach (file($root . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
        list($k, $v) = explode('=', $line, 2);
        $env[trim($k)] = trim(trim($v), "\"'");
    }
}
$get = function ($k, $d = '') use ($env) {
    $v = getenv($k);
    return ($v !== false && $v !== '') ? $v : ($env[$k] ?? $d);
};

$db = new mysqli($get('DB_HOST', 'db'), $get('DB_USER'), $get('DB_PASS'), $get('DB_NAME'));
$db->set_charset('utf8mb4');
$q = function ($sql) use ($db) {
    if (!$db->query($sql)) {
        fwrite(STDERR, "ERROR: {$db->error}\n  SQL: {$sql}\n");
        exit(1);
    }
};
$col = function ($table, $column) use ($db) {
    $r = $db->query("SHOW COLUMNS FROM `{$table}` LIKE '" . $db->real_escape_string($column) . "'");
    return $r && $r->num_rows > 0;
};

echo "1) geopos_restkeys\n";
if (!$col('geopos_restkeys', 'loc_id'))     $q("ALTER TABLE geopos_restkeys ADD COLUMN loc_id INT NULL AFTER user_id");
if (!$col('geopos_restkeys', 'scope'))      $q("ALTER TABLE geopos_restkeys ADD COLUMN scope ENUM('tenant','platform') NOT NULL DEFAULT 'tenant' AFTER loc_id");
if (!$col('geopos_restkeys', 'key_hash'))   $q("ALTER TABLE geopos_restkeys ADD COLUMN key_hash CHAR(64) NULL AFTER `key`");
if (!$col('geopos_restkeys', 'key_hint'))   $q("ALTER TABLE geopos_restkeys ADD COLUMN key_hint VARCHAR(12) NULL AFTER key_hash");
if (!$col('geopos_restkeys', 'revoked_at')) $q("ALTER TABLE geopos_restkeys ADD COLUMN revoked_at DATETIME NULL");
$q("CREATE UNIQUE INDEX IF NOT EXISTS uk_key_hash ON geopos_restkeys (key_hash)");

// Hash de las keys existentes y vínculo con su local (por geopos_locations.api_key)
$q("UPDATE geopos_restkeys SET key_hash = SHA2(`key`, 256), key_hint = RIGHT(`key`, 4)
     WHERE `key` IS NOT NULL AND `key` <> '' AND key_hash IS NULL");
$q("UPDATE geopos_restkeys r JOIN geopos_locations l ON l.api_key = r.`key`
       SET r.loc_id = l.id, r.scope = 'tenant'
     WHERE r.`key` IS NOT NULL AND r.`key` <> ''");
$q("UPDATE geopos_restkeys SET scope = 'platform' WHERE loc_id IS NULL AND revoked_at IS NULL");

// La clave maestra de Evolution nunca debe servir como clave REST
$evo = $get('EVOLUTION_API_KEY');
if ($evo !== '') {
    $h = hash('sha256', $evo);
    $q("UPDATE geopos_restkeys SET revoked_at = NOW() WHERE key_hash = '{$h}' AND revoked_at IS NULL");
}

// Clave de plataforma (para n8n / integraciones internas) desde .env
$plat = $get('GEEKSPOS_PLATFORM_API_KEY');
if ($plat !== '') {
    $h = hash('sha256', $plat);
    $hint = $db->real_escape_string(substr($plat, -4));
    $exists = $db->query("SELECT id FROM geopos_restkeys WHERE key_hash = '{$h}'")->num_rows > 0;
    if (!$exists) {
        $q("INSERT INTO geopos_restkeys (user_id, loc_id, scope, `key`, key_hash, key_hint, level, ignore_limits, is_private_key, date_created)
            VALUES (0, NULL, 'platform', NULL, '{$h}', '{$hint}', 10, 0, 0, CURDATE())");
    }
}

// Keys de local y revocadas: sin copia en claro (la del local queda solo en geopos_locations.api_key para el panel).
// Las keys legacy de plataforma conservan `key` porque la impresión remota y el REST original de GeoPOS las leen así.
$q("UPDATE geopos_restkeys SET `key` = NULL WHERE key_hash IS NOT NULL AND (scope = 'tenant' OR revoked_at IS NOT NULL)");
$q("UPDATE geopos_restkeys SET ignore_limits = 0");

echo "2) geopos_deliveries (tokens de la app)\n";
if (!$col('geopos_deliveries', 'auth_token_expires')) {
    $q("ALTER TABLE geopos_deliveries ADD COLUMN auth_token_expires DATETIME NULL AFTER auth_token");
    // Tokens existentes: se guardan como hash y se respetan 30 días más (los repartidores no pierden la sesión)
    $q("UPDATE geopos_deliveries SET auth_token = SHA2(auth_token, 256), auth_token_expires = NOW() + INTERVAL 30 DAY
         WHERE auth_token IS NOT NULL AND auth_token <> ''");
}
$q("ALTER TABLE geopos_deliveries ALTER COLUMN pin DROP DEFAULT");

echo "3) geopos_delivery_login_attempts\n";
$q("CREATE TABLE IF NOT EXISTS geopos_delivery_login_attempts (
      id INT NOT NULL AUTO_INCREMENT,
      ip VARCHAR(45) NOT NULL DEFAULT '',
      phone_tail VARCHAR(12) NOT NULL DEFAULT '',
      success TINYINT(1) NOT NULL DEFAULT 0,
      created_at DATETIME NOT NULL,
      PRIMARY KEY (id),
      KEY idx_phone (phone_tail, created_at),
      KEY idx_ip (ip, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

echo "4) Cifrado de claves de IA en texto plano\n";
$master = base64_decode($get('GEEKSIA_SECRET_KEY'), true);
if ($master === false || strlen($master) !== 32) {
    echo "   (omitido: GEEKSIA_SECRET_KEY no está configurada)\n";
} else {
    $res = $db->query("SELECT loc_id, ia_api_key FROM geopos_whatsapp_config WHERE ia_api_key IS NOT NULL AND ia_api_key <> '' AND ia_api_key NOT LIKE 'v1:%'");
    $n = 0;
    while ($row = $res->fetch_assoc()) {
        $iv = random_bytes(12);
        $tag = '';
        $ct = openssl_encrypt($row['ia_api_key'], 'aes-256-gcm', $master, OPENSSL_RAW_DATA, $iv, $tag, 'loc:' . (int)$row['loc_id'] . ':ia_api_key', 16);
        $enc = $db->real_escape_string('v1:' . base64_encode($iv . $tag . $ct));
        $q("UPDATE geopos_whatsapp_config SET ia_api_key = '{$enc}' WHERE loc_id = " . (int)$row['loc_id']);
        $n++;
    }
    echo "   {$n} clave(s) cifrada(s)\n";
}

echo "Listo.\n";
