-- Migration: Kitchen Display System (KDS) for GeeksPOS
-- Table: geopos_kitchen_orders

CREATE TABLE IF NOT EXISTS geopos_kitchen_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL,
    invoice_id INT NULL,
    delivery_order_id INT NULL,
    loc_id INT NOT NULL DEFAULT 1,
    order_type ENUM('local', 'takeaway', 'delivery', 'mesa') NOT NULL DEFAULT 'local',
    customer_name VARCHAR(150) NOT NULL DEFAULT 'Cliente Mostrador',
    customer_phone VARCHAR(30) NULL,
    table_number VARCHAR(50) NULL,
    delivery_slot INT NULL,
    delivery_name VARCHAR(100) NULL,
    notes TEXT NULL,
    items_json LONGTEXT NOT NULL,
    status ENUM('pendiente', 'en_preparacion', 'listo', 'entregado', 'cancelado') NOT NULL DEFAULT 'pendiente',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    started_at DATETIME NULL,
    ready_at DATETIME NULL,
    completed_at DATETIME NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status_loc (loc_id, status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed existing delivery orders into kitchen orders if not already present
INSERT INTO geopos_kitchen_orders 
(order_number, delivery_order_id, loc_id, order_type, customer_name, customer_phone, delivery_slot, delivery_name, notes, items_json, status, created_at)
SELECT 
    d.order_number,
    d.id,
    d.loc_id,
    'delivery',
    d.cliente_nombre,
    d.cliente_telefono,
    d.delivery_slot,
    d.delivery_nombre,
    d.notas_cocina,
    d.items_json,
    CASE 
        WHEN d.estado = 'en_camino' THEN 'listo'
        WHEN d.estado = 'entregado' THEN 'entregado'
        ELSE 'pendiente'
    END,
    d.created_at
FROM geopos_delivery_orders d
LEFT JOIN geopos_kitchen_orders k ON k.delivery_order_id = d.id
WHERE k.id IS NULL;

-- Insert two realistic sample dining/takeaway orders for kitchen testing
INSERT INTO geopos_kitchen_orders 
(order_number, loc_id, order_type, customer_name, table_number, notes, items_json, status, created_at)
VALUES 
(
    'M-04',
    1,
    'mesa',
    'Mesa 4 (Terraza)',
    '4',
    'Carne término medio. Sin pepinillos en una hamburguesa.',
    '[{"name":"Bronx Combo Doble","qty":2,"notes":"1 con papas rústicas, 1 con aros de cebolla"},{"name":"Birriamen Especial","qty":1,"notes":"Picante moderado"},{"name":"Coca-Cola 500ml","qty":2,"notes":"Bien fría"}]',
    'en_preparacion',
    DATE_SUB(NOW(), INTERVAL 8 MINUTE)
),
(
    'T-1025',
    1,
    'takeaway',
    'Santiago Rivas (Retiro)',
    NULL,
    'Cliente espera en recepción. Empacar salsas aparte.',
    '[{"name":"Cheese Combo Doble","qty":1,"notes":"Extra queso cheddar"},{"name":"Papas Rústicas Grandes","qty":1,"notes":"Con salsa especial Geeks"}]',
    'pendiente',
    DATE_SUB(NOW(), INTERVAL 3 MINUTE)
);
