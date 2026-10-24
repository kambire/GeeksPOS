<?php
$pdo = new PDO("mysql:host=geekspos_db;port=3306;dbname=geekscom_nicole", "geekscom_nicole", "OCvacuCpWWhkwe7pFKMsAa9");

// Check if already inserted
$stmt = $pdo->prepare("SELECT COUNT(*) FROM geopos_delivery_orders WHERE order_number IN ('#2001', '#2002', '#2003')");
$stmt->execute();
if ($stmt->fetchColumn() > 0) {
    $pdo->exec("DELETE FROM geopos_delivery_orders WHERE order_number IN ('#2001', '#2002', '#2003')");
}

$orders = [
    [
        'loc_id'            => 2,
        'order_number'      => '#2001',
        'delivery_slot'     => 1,
        'delivery_nombre'   => 'Cesar Benitez',
        'delivery_telefono' => '595981687986',
        'estado'            => 'en_camino',
        'cliente_nombre'    => 'María Sol Gómez',
        'cliente_telefono'  => '0981687986',
        'direccion_entrega' => 'Av. Cacique Lambaré c/ Río Apa, Casa #412',
        'maps_link'         => 'https://maps.google.com/?q=-25.3456,-57.6045',
        'lat_destino'       => -25.3456,
        'lng_destino'       => -57.6045,
        'metodo_pago'       => 'efectivo',
        'monto_total'       => 75000.00,
        'paga_con'          => 100000.00,
        'vuelto'            => 25000.00,
        'items_json'        => json_encode([
            ['name' => 'Burger Doble Cheddar + Papas Fritas', 'qty' => 1, 'price' => 45000],
            ['name' => 'Pizza Muzzarella Mediana', 'qty' => 1, 'price' => 30000]
        ], JSON_UNESCAPED_UNICODE),
        'notas_cocina'      => 'Sin cebolla en la burger, entregar en portería.',
        'tracking_hash'     => md5('order_2001_' . time()),
        'assigned_at'       => date('Y-m-d H:i:s', strtotime('-15 minutes')),
        'picked_at'         => date('Y-m-d H:i:s', strtotime('-5 minutes')),
    ],
    [
        'loc_id'            => 2,
        'order_number'      => '#2002',
        'delivery_slot'     => 1,
        'delivery_nombre'   => 'Cesar Benitez',
        'delivery_telefono' => '595981687986',
        'estado'            => 'en_camino',
        'cliente_nombre'    => 'Juan Carlos Almada',
        'cliente_telefono'  => '0983112233',
        'direccion_entrega' => 'Barrio San Vicente, Asunción (cerca de la iglesia)',
        'maps_link'         => 'https://maps.google.com/?q=-25.3120,-57.6180',
        'lat_destino'       => -25.3120,
        'lng_destino'       => -57.6180,
        'metodo_pago'       => 'sipap',
        'monto_total'       => 110000.00,
        'paga_con'          => 110000.00,
        'vuelto'            => 0.00,
        'items_json'        => json_encode([
            ['name' => 'Combo Lomito Árabe Especial', 'qty' => 1, 'price' => 55000],
            ['name' => 'Picada Geeks para 2 personas', 'qty' => 1, 'price' => 55000]
        ], JSON_UNESCAPED_UNICODE),
        'notas_cocina'      => 'Timbre blanco, avisar por WhatsApp al llegar.',
        'tracking_hash'     => md5('order_2002_' . time()),
        'assigned_at'       => date('Y-m-d H:i:s', strtotime('-25 minutes')),
        'picked_at'         => date('Y-m-d H:i:s', strtotime('-10 minutes')),
    ],
    [
        'loc_id'            => 2,
        'order_number'      => '#2003',
        'delivery_slot'     => 1,
        'delivery_nombre'   => 'Cesar Benitez',
        'delivery_telefono' => '595981687986',
        'estado'            => 'asignado',
        'cliente_nombre'    => 'Diana Morales',
        'cliente_telefono'  => '0991445566',
        'direccion_entrega' => 'Av. Fernando de la Mora esq. Bartolomé de las Casas',
        'maps_link'         => 'https://maps.google.com/?q=-25.3150,-57.5950',
        'lat_destino'       => -25.3150,
        'lng_destino'       => -57.5950,
        'metodo_pago'       => 'tarjeta',
        'monto_total'       => 50000.00,
        'paga_con'          => 50000.00,
        'vuelto'            => 0.00,
        'items_json'        => json_encode([
            ['name' => 'Sandwiches de Lomito Completo', 'qty' => 2, 'price' => 50000]
        ], JSON_UNESCAPED_UNICODE),
        'notas_cocina'      => 'Llevar POS inalámbrico para tarjeta.',
        'tracking_hash'     => md5('order_2003_' . time()),
        'assigned_at'       => date('Y-m-d H:i:s', strtotime('-5 minutes')),
    ]
];

$sql = "INSERT INTO geopos_delivery_orders 
    (loc_id, order_number, delivery_slot, delivery_nombre, delivery_telefono, estado, cliente_nombre, cliente_telefono, direccion_entrega, maps_link, lat_destino, lng_destino, metodo_pago, monto_total, paga_con, vuelto, items_json, notas_cocina, tracking_hash, assigned_at, picked_at)
    VALUES (:loc_id, :order_number, :delivery_slot, :delivery_nombre, :delivery_telefono, :estado, :cliente_nombre, :cliente_telefono, :direccion_entrega, :maps_link, :lat_destino, :lng_destino, :metodo_pago, :monto_total, :paga_con, :vuelto, :items_json, :notas_cocina, :tracking_hash, :assigned_at, :picked_at)";

$stmt = $pdo->prepare($sql);
foreach ($orders as $o) {
    $stmt->execute([
        ':loc_id'            => $o['loc_id'],
        ':order_number'      => $o['order_number'],
        ':delivery_slot'     => $o['delivery_slot'],
        ':delivery_nombre'   => $o['delivery_nombre'],
        ':delivery_telefono' => $o['delivery_telefono'],
        ':estado'            => $o['estado'],
        ':cliente_nombre'    => $o['cliente_nombre'],
        ':cliente_telefono'  => $o['cliente_telefono'],
        ':direccion_entrega' => $o['direccion_entrega'],
        ':maps_link'         => $o['maps_link'],
        ':lat_destino'       => $o['lat_destino'],
        ':lng_destino'       => $o['lng_destino'],
        ':metodo_pago'       => $o['metodo_pago'],
        ':monto_total'       => $o['monto_total'],
        ':paga_con'          => $o['paga_con'],
        ':vuelto'            => $o['vuelto'],
        ':items_json'        => $o['items_json'],
        ':notas_cocina'      => $o['notas_cocina'],
        ':tracking_hash'     => $o['tracking_hash'],
        ':assigned_at'       => $o['assigned_at'],
        ':picked_at'         => $o['picked_at'] ?? null,
    ]);
}

// Actualizar repartidor como ocupado
$pdo->exec("UPDATE geopos_deliveries SET ocupado = 1 WHERE loc_id = 2 AND slot = 1");

echo "Test orders #2001, #2002, #2003 inserted successfully for loc_id=2 and slot=1 (Cesar Benitez).\n";
