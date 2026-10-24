import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl/intl.dart';
import '../models/delivery_order.dart';
import '../services/api_service.dart';
import '../services/location_service.dart';
import '../widgets/order_status.dart';

class OrderDetailScreen extends StatefulWidget {
  final DeliveryOrder order;

  const OrderDetailScreen({super.key, required this.order});

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  late DeliveryOrder _order;
  bool _isLoading = false;
  final _currencyFmt = NumberFormat.currency(locale: 'es_PY', symbol: 'Gs. ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    _order = widget.order;
  }

  Future<void> _updateStatus(String newStatus) async {
    if (newStatus == 'entregado') {
      final confirm = await showDialog<bool>(
        context: context,
        builder: (ctx) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          title: const Text('Confirmar Entrega'),
          content: Text(
            _order.isEfectivo
                ? '¿Confirmas que cobraste ${_currencyFmt.format(_order.montoTotal)} y entregaste el pedido ${_order.orderNumber}?'
                : '¿Confirmas la entrega final del pedido ${_order.orderNumber}?',
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(ctx, false),
              child: const Text('Cancelar'),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF10B981)),
              onPressed: () => Navigator.pop(ctx, true),
              child: const Text('Sí, Entregado', style: TextStyle(color: Colors.white)),
            ),
          ],
        ),
      );
      if (confirm != true) return;
    }

    setState(() => _isLoading = true);
    final res = await ApiService.updateOrderStatus(_order.id, newStatus);
    setState(() => _isLoading = false);

    if (res['success'] == true && mounted) {
      setState(() {
        _order.estado = newStatus;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message'] ?? 'Estado actualizado'),
          backgroundColor: const Color(0xFF10B981),
        ),
      );
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message'] ?? 'Error al actualizar'),
          backgroundColor: Colors.redAccent,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final status = OrderStatusStyle.of(_order.estado);
    final statusColor = status.color;
    final statusLabel = status.longLabel;

    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9),
      appBar: AppBar(
        backgroundColor: const Color(0xFF0F172A),
        title: Text(
          'Pedido ${_order.orderNumber}',
          style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white),
        ),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Status Banner
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: BoxDecoration(
                color: statusColor,
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(
                    color: statusColor.withValues(alpha: 0.3),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Row(
                children: [
                  const Icon(Icons.info_outline, color: Colors.white),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'ESTADO: $statusLabel',
                          style: const TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                            fontSize: 14,
                          ),
                        ),
                        if (_order.createdAt.isNotEmpty)
                          Text(
                            'Creado: ${_order.createdAt}',
                            style: const TextStyle(color: Colors.white70, fontSize: 11),
                          ),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 16),

            // Client Info Card
            _buildCard(
              title: 'Datos del Cliente',
              icon: Icons.person_outline,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    _order.clienteNombre,
                    style: const TextStyle(fontSize: 17, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    _order.clienteTelefono.isNotEmpty ? _order.clienteTelefono : 'Sin teléfono',
                    style: const TextStyle(fontSize: 14, color: Colors.black54),
                  ),
                  const SizedBox(height: 14),
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton.icon(
                          style: OutlinedButton.styleFrom(
                            side: const BorderSide(color: Color(0xFF2563EB)),
                            padding: const EdgeInsets.symmetric(vertical: 10),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                          ),
                          onPressed: () => LocationService.makePhoneCall(_order.clienteTelefono),
                          icon: const Icon(Icons.phone, color: Color(0xFF2563EB), size: 18),
                          label: const Text('Llamar', style: TextStyle(color: Color(0xFF2563EB))),
                        ),
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: ElevatedButton.icon(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF10B981),
                            padding: const EdgeInsets.symmetric(vertical: 10),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                          ),
                          onPressed: () => LocationService.openWhatsApp(
                            _order.clienteTelefono,
                            message: '¡Hola ${_order.clienteNombre}! Soy tu repartidor de Geeks Delivery con tu pedido ${_order.orderNumber}.',
                          ),
                          icon: const Icon(Icons.chat, color: Colors.white, size: 18),
                          label: const Text('WhatsApp', style: TextStyle(color: Colors.white)),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            const SizedBox(height: 16),

            // Delivery Location & Navigation
            _buildCard(
              title: 'Dirección de Entrega',
              icon: Icons.location_on_outlined,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    _order.direccionEntrega.isNotEmpty ? _order.direccionEntrega : 'Sin dirección especificada',
                    style: const TextStyle(fontSize: 14, color: Colors.black87),
                  ),
                  const SizedBox(height: 14),
                  Row(
                    children: [
                      Expanded(
                        child: ElevatedButton.icon(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF0F172A),
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                          ),
                          onPressed: () {
                            LocationService.openGoogleMaps(
                              lat: _order.latDestino,
                              lng: _order.lngDestino,
                              address: _order.direccionEntrega,
                              customUrl: _order.mapsLink,
                            );
                          },
                          icon: const Icon(Icons.map, color: Colors.white, size: 18),
                          label: const Text('Google Maps', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                        ),
                      ),
                      if (_order.latDestino != null && _order.lngDestino != null) ...[
                        const SizedBox(width: 10),
                        Expanded(
                          child: OutlinedButton.icon(
                            style: OutlinedButton.styleFrom(
                              side: const BorderSide(color: Color(0xFF00D2D3)),
                              padding: const EdgeInsets.symmetric(vertical: 12),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                            ),
                            onPressed: () {
                              LocationService.openWaze(
                                lat: _order.latDestino!,
                                lng: _order.lngDestino!,
                              );
                            },
                            icon: const Icon(Icons.navigation, color: Color(0xFF009688), size: 18),
                            label: const Text('Waze', style: TextStyle(color: Color(0xFF009688), fontWeight: FontWeight.bold)),
                          ),
                        ),
                      ],
                    ],
                  ),
                  if (_order.trackingUrl.isNotEmpty) ...[
                    const SizedBox(height: 12),
                    InkWell(
                      onTap: () {
                        Clipboard.setData(ClipboardData(text: _order.trackingUrl));
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Enlace de rastreo copiado al portapapeles')),
                        );
                      },
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                        decoration: BoxDecoration(
                          color: const Color(0xFFEFF6FF),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: const Color(0xFFBFDBFE)),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.share_location, color: Color(0xFF2563EB), size: 18),
                            const SizedBox(width: 8),
                            const Expanded(
                              child: Text(
                                'Enlace de rastreo en vivo para el cliente',
                                style: TextStyle(fontSize: 12, color: Color(0xFF1E40AF), fontWeight: FontWeight.w500),
                              ),
                            ),
                            const Icon(Icons.copy, color: Color(0xFF2563EB), size: 16),
                          ],
                        ),
                      ),
                    ),
                  ],
                ],
              ),
            ),

            const SizedBox(height: 16),

            // Products list
            _buildCard(
              title: 'Detalle del Pedido',
              icon: Icons.receipt_long,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (_order.items.isEmpty)
                    const Text('Sin detalle de productos', style: TextStyle(color: Colors.black54, fontSize: 13))
                  else
                    ..._order.items.map((item) => Padding(
                          padding: const EdgeInsets.symmetric(vertical: 6),
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFE2E8F0),
                                  borderRadius: BorderRadius.circular(6),
                                ),
                                child: Text(
                                  '${item.qty}x',
                                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                                ),
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: Text(
                                  item.name,
                                  style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500),
                                ),
                              ),
                              Text(
                                _currencyFmt.format(item.subtotal > 0 ? item.subtotal : item.price * item.qty),
                                style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
                              ),
                            ],
                          ),
                        )),
                  if (_order.notasCocina.isNotEmpty) ...[
                    const Divider(height: 20),
                    Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFEF3C7),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.speaker_notes, color: Color(0xFFB45309), size: 18),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              'Nota: ${_order.notasCocina}',
                              style: const TextStyle(color: Color(0xFF92400E), fontSize: 13, fontWeight: FontWeight.w500),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ],
              ),
            ),

            const SizedBox(height: 16),

            // Payment Breakdown Card
            _buildCard(
              title: 'Pago y Cobranza',
              icon: Icons.payments,
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Método de Pago:', style: TextStyle(color: Colors.black54, fontSize: 14)),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: _order.isEfectivo
                              ? const Color(0xFFFEF3C7)
                              : _order.isSipap
                                  ? const Color(0xFFD1FAE5)
                                  : const Color(0xFFDBEAFE),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          _order.metodoPago.toUpperCase(),
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 12,
                            color: _order.isEfectivo
                                ? const Color(0xFF92400E)
                                : _order.isSipap
                                    ? const Color(0xFF065F46)
                                    : const Color(0xFF1E40AF),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const Divider(height: 16),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Total a Cobrar:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                      Text(
                        _currencyFmt.format(_order.montoTotal),
                        style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 18, color: Color(0xFF0F172A)),
                      ),
                    ],
                  ),
                  if (_order.isEfectivo && _order.pagaCon > 0) ...[
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('El cliente paga con:', style: TextStyle(color: Colors.black54, fontSize: 13)),
                        Text(_currencyFmt.format(_order.pagaCon), style: const TextStyle(fontSize: 14)),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFFFBEB),
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: const Color(0xFFFDE68A)),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('VUELTO A ENTREGAR:', style: TextStyle(fontWeight: FontWeight.bold, color: Color(0xFFB45309))),
                          Text(
                            _currencyFmt.format(_order.vuelto),
                            style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 15, color: Color(0xFFB45309)),
                          ),
                        ],
                      ),
                    ),
                  ],
                ],
              ),
            ),

            const SizedBox(height: 24),

            // Status Progression Buttons
            if (_order.estado == 'asignado')
              ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF2563EB),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                ),
                onPressed: _isLoading ? null : () => _updateStatus('en_camino'),
                icon: const Icon(Icons.moped),
                label: const Text('SALIR HACIA DESTINO (EN CAMINO)', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
              )
            else if (_order.estado == 'en_camino')
              ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF7C3AED),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                ),
                onPressed: _isLoading ? null : () => _updateStatus('en_puerta'),
                icon: const Icon(Icons.doorbell),
                label: const Text('¡LLEGUÉ A LA PUERTA! (AVISAR)', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
              )
            else if (_order.estado == 'en_puerta')
              ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF10B981),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                ),
                onPressed: _isLoading ? null : () => _updateStatus('entregado'),
                icon: const Icon(Icons.check_circle),
                label: const Text('COBRAR Y FINALIZAR ENTREGA', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
              ),

            const SizedBox(height: 30),
          ],
        ),
      ),
    );
  }

  Widget _buildCard({
    required String title,
    required IconData icon,
    required Widget child,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, size: 20, color: const Color(0xFF2563EB)),
              const SizedBox(width: 8),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF64748B),
                  letterSpacing: 0.5,
                ),
              ),
            ],
          ),
          const Divider(height: 20),
          child,
        ],
      ),
    );
  }
}
