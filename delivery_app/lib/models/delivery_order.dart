import 'dart:convert';

class OrderItem {
  final String name;
  final int qty;
  final double price;
  final double subtotal;

  OrderItem({
    required this.name,
    required this.qty,
    required this.price,
    required this.subtotal,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) {
    return OrderItem(
      name: json['name']?.toString() ?? json['product_name']?.toString() ?? 'Producto',
      qty: (json['qty'] is num)
          ? (json['qty'] as num).toInt()
          : (int.tryParse(json['qty']?.toString() ?? '1') ?? 1),
      price: (json['price'] is num)
          ? (json['price'] as num).toDouble()
          : (double.tryParse(json['price']?.toString() ?? '0') ?? 0.0),
      subtotal: (json['subtotal'] is num)
          ? (json['subtotal'] as num).toDouble()
          : (double.tryParse(json['subtotal']?.toString() ?? '0') ?? 0.0),
    );
  }
}

class DeliveryOrder {
  final int id;
  final int invoiceId;
  final String orderNumber;
  final int deliverySlot;
  String estado; // asignado, en_camino, en_puerta, entregado, cancelado
  final String clienteNombre;
  final String clienteTelefono;
  final String direccionEntrega;
  final String mapsLink;
  final double? latDestino;
  final double? lngDestino;
  final String metodoPago; // efectivo, sipap, transferencia, tarjeta, pos
  final double montoTotal;
  final double pagaCon;
  final double vuelto;
  final List<OrderItem> items;
  final String notasCocina;
  final String trackingHash;
  final String trackingUrl;
  final String createdAt;
  final String asignadoAt;

  DeliveryOrder({
    required this.id,
    required this.invoiceId,
    required this.orderNumber,
    required this.deliverySlot,
    required this.estado,
    required this.clienteNombre,
    required this.clienteTelefono,
    required this.direccionEntrega,
    required this.mapsLink,
    this.latDestino,
    this.lngDestino,
    required this.metodoPago,
    required this.montoTotal,
    required this.pagaCon,
    required this.vuelto,
    required this.items,
    required this.notasCocina,
    required this.trackingHash,
    required this.trackingUrl,
    required this.createdAt,
    required this.asignadoAt,
  });

  factory DeliveryOrder.fromJson(Map<String, dynamic> json) {
    // Parse items
    List<OrderItem> parsedItems = [];
    final rawItems = json['items_json'] ?? json['items'];
    if (rawItems != null) {
      if (rawItems is List) {
        parsedItems = rawItems
            .whereType<Map<String, dynamic>>()
            .map((item) => OrderItem.fromJson(item))
            .toList();
      } else if (rawItems is String && rawItems.trim().isNotEmpty) {
        try {
          final decoded = jsonDecode(rawItems);
          if (decoded is List) {
            parsedItems = decoded
                .whereType<Map<String, dynamic>>()
                .map((item) => OrderItem.fromJson(item))
                .toList();
          }
        } catch (_) {}
      }
    }

    return DeliveryOrder(
      id: int.tryParse(json['id']?.toString() ?? '0') ?? 0,
      invoiceId: int.tryParse(json['invoice_id']?.toString() ?? '0') ?? 0,
      orderNumber: json['order_number']?.toString() ?? '#0',
      deliverySlot: int.tryParse(json['delivery_slot']?.toString() ?? '0') ?? 0,
      estado: json['estado']?.toString() ?? 'asignado',
      clienteNombre: json['cliente_nombre']?.toString() ?? 'Cliente',
      clienteTelefono: json['cliente_telefono']?.toString() ?? '',
      direccionEntrega: json['direccion_entrega']?.toString() ?? '',
      mapsLink: json['maps_link']?.toString() ?? '',
      latDestino: double.tryParse(json['lat_destino']?.toString() ?? ''),
      lngDestino: double.tryParse(json['lng_destino']?.toString() ?? ''),
      metodoPago: json['metodo_pago']?.toString().toLowerCase() ?? 'efectivo',
      montoTotal: double.tryParse(json['monto_total']?.toString() ?? '0') ?? 0.0,
      pagaCon: double.tryParse(json['paga_con']?.toString() ?? '0') ?? 0.0,
      vuelto: double.tryParse(json['vuelto']?.toString() ?? '0') ?? 0.0,
      items: parsedItems,
      notasCocina: json['notas_cocina']?.toString() ?? '',
      trackingHash: json['tracking_hash']?.toString() ?? '',
      trackingUrl: json['tracking_url']?.toString() ?? '',
      createdAt: json['created_at']?.toString() ?? '',
      asignadoAt: json['asignado_at']?.toString() ?? '',
    );
  }

  bool get isEfectivo =>
      metodoPago.contains('efectivo') || metodoPago.contains('cash');
  bool get isSipap =>
      metodoPago.contains('sipap') || metodoPago.contains('transferencia');
  bool get isPos =>
      metodoPago.contains('pos') ||
      metodoPago.contains('tarjeta') ||
      metodoPago.contains('card');

  bool get isActive => estado != 'entregado' && estado != 'cancelado';
}
