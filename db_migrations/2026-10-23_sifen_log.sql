-- Registro de cada llamada a los servicios web de SIFEN (recibe, lote, consultas, eventos).
-- El id se usa como dId (identificador de control de envío, autoincremental).
-- Aplicar una vez por instalación.
CREATE TABLE IF NOT EXISTS geopos_sifen_log (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  operacion VARCHAR(20) NOT NULL,
  ambiente VARCHAR(5) NOT NULL DEFAULT 'test',
  invoice_id INT NULL,
  cdc VARCHAR(44) NULL,
  referencia VARCHAR(100) NULL,
  estado VARCHAR(30) NULL,
  codigo VARCHAR(10) NULL,
  mensaje VARCHAR(500) NULL,
  protocolo VARCHAR(20) NULL,
  http_code SMALLINT NULL,
  request MEDIUMTEXT NULL,
  response MEDIUMTEXT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_cdc (cdc),
  KEY idx_invoice (invoice_id),
  KEY idx_operacion (operacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
