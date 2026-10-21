-- Campos de pedido en el POS: nombre libre, tipo de pedido y N de transaccion de tarjeta.
-- Aplicar una vez por instalacion.
ALTER TABLE geopos_invoices
  ADD COLUMN order_name VARCHAR(100) NULL AFTER refer,
  ADD COLUMN order_type VARCHAR(20) NULL AFTER order_name,
  ADD COLUMN card_txn  VARCHAR(50) NULL AFTER order_type;
