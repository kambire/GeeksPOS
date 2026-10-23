import 'dart:async';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/delivery_driver.dart';
import '../models/delivery_order.dart';
import '../services/api_service.dart';
import '../services/location_service.dart';
import 'login_screen.dart';
import 'order_detail_screen.dart';
import 'cash_summary_screen.dart';

class HomeScreen extends StatefulWidget {
  final DeliveryDriver driver;

  const HomeScreen({super.key, required this.driver});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> with SingleTickerProviderStateMixin {
  late DeliveryDriver _driver;
  late TabController _tabController;
  List<DeliveryOrder> _activeOrders = [];
  List<DeliveryOrder> _historyOrders = [];
  bool _isLoading = false;
  Timer? _pollingTimer;

  final _currencyFmt = NumberFormat.currency(locale: 'es_PY', symbol: 'Gs. ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    _driver = widget.driver;
    _tabController = TabController(length: 2, vsync: this);
    _loadOrders();

    // Auto-refresh orders every 12 seconds
    _pollingTimer = Timer.periodic(const Duration(seconds: 12), (_) {
      _loadOrders(silent: true);
    });

    if (_driver.activo) {
      LocationService.startTracking();
    }
  }

  @override
  void dispose() {
    _pollingTimer?.cancel();
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadOrders({bool silent = false}) async {
    if (!silent) {
      setState(() => _isLoading = true);
    }

    try {
      final active = await ApiService.getOrders(status: 'activos');
      final history = await ApiService.getOrders(status: 'historial');

      if (mounted) {
        setState(() {
          _activeOrders = active;
          _historyOrders = history;
          _isLoading = false;
        });
      }
    } catch (_) {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  Future<void> _toggleAvailability(bool val) async {
    final success = await ApiService.toggleAvailability(val);
    if (success && mounted) {
      setState(() {
        _driver = _driver.copyWith(activo: val);
      });
      if (val) {
        LocationService.startTracking();
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('🟢 Estás ACTIVO y compartiendo GPS en tiempo real'),
            backgroundColor: Color(0xFF10B981),
          ),
        );
      } else {
        LocationService.stopTracking();
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('⛔ Turno en PAUSA. GPS detenido'),
            backgroundColor: Color(0xFFEF4444),
          ),
        );
      }
    }
  }

  Future<void> _updateOrderStatus(DeliveryOrder order, String newStatus) async {
    // Show confirmation modal for important steps
    if (newStatus == 'entregado') {
      final confirm = await showDialog<bool>(
        context: context,
        builder: (ctx) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          title: const Text('Confirmar Entrega'),
          content: Text(
            order.isEfectivo
                ? '¿Confirmas que cobraste ${_currencyFmt.format(order.montoTotal)} y entregaste el pedido ${order.orderNumber}?'
                : '¿Confirmas la entrega final del pedido ${order.orderNumber}?',
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
    final res = await ApiService.updateOrderStatus(order.id, newStatus);
    setState(() => _isLoading = false);

    if (res['success'] == true && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message'] ?? 'Estado actualizado'),
          backgroundColor: const Color(0xFF10B981),
        ),
      );
      _loadOrders();
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message'] ?? 'Error al actualizar'),
          backgroundColor: Colors.redAccent,
        ),
      );
    }
  }

  Future<void> _logout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('Cerrar Turno'),
        content: const Text('¿Deseas cerrar tu sesión y pausar el rastreo GPS?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('Cancelar'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.redAccent),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Cerrar Turno', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );

    if (confirm == true) {
      LocationService.stopTracking();
      await ApiService.logout();
      if (mounted) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (_) => const LoginScreen()),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9),
      appBar: AppBar(
        backgroundColor: const Color(0xFF0F172A),
        elevation: 2,
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
              decoration: BoxDecoration(
                color: const Color(0xFF2563EB),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                'SLOT #${_driver.slot}',
                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.white),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    _driver.nombre,
                    style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: Colors.white),
                    overflow: TextOverflow.ellipsis,
                  ),
                  Text(
                    _driver.empresa,
                    style: const TextStyle(fontSize: 11, color: Colors.white70),
                  ),
                ],
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.account_balance_wallet, color: Color(0xFF38BDF8)),
            tooltip: 'Arqueo de Caja',
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const CashSummaryScreen()),
              );
            },
          ),
          IconButton(
            icon: const Icon(Icons.power_settings_new, color: Colors.redAccent),
            tooltip: 'Cerrar Turno',
            onPressed: _logout,
          ),
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(105),
          child: Column(
            children: [
              // Availability Switch Card
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                decoration: BoxDecoration(
                  color: _driver.activo ? const Color(0xFF064E3B) : const Color(0xFF7F1D1D),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(
                    color: _driver.activo ? const Color(0xFF10B981) : Colors.redAccent,
                    width: 1,
                  ),
                ),
                child: Row(
                  children: [
                    Icon(
                      _driver.activo ? Icons.radio_button_checked : Icons.pause_circle_outline,
                      color: _driver.activo ? const Color(0xFF34D399) : Colors.redAccent,
                      size: 20,
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            _driver.activo ? 'DISPONIBLE PARA PEDIDOS' : 'EN PAUSA (NO RECIBIR PEDIDOS)',
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          Text(
                            _driver.activo ? 'GPS en tiempo real activado' : 'GPS en reposo',
                            style: const TextStyle(color: Colors.white70, fontSize: 10),
                          ),
                        ],
                      ),
                    ),
                    Switch(
                      value: _driver.activo,
                      activeThumbColor: const Color(0xFF34D399),
                      activeTrackColor: const Color(0xFF059669),
                      inactiveThumbColor: Colors.white70,
                      inactiveTrackColor: Colors.white24,
                      onChanged: _toggleAvailability,
                    ),
                  ],
                ),
              ),
              TabBar(
                controller: _tabController,
                indicatorColor: const Color(0xFF38BDF8),
                indicatorWeight: 3,
                tabs: [
                  Tab(
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Text('ACTIVOS', style: TextStyle(fontWeight: FontWeight.bold)),
                        const SizedBox(width: 6),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
                          decoration: BoxDecoration(
                            color: _activeOrders.isNotEmpty ? const Color(0xFF2563EB) : Colors.white24,
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Text(
                            '${_activeOrders.length}',
                            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.white),
                          ),
                        ),
                      ],
                    ),
                  ),
                  Tab(
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Text('HISTORIAL HOY', style: TextStyle(fontWeight: FontWeight.bold)),
                        const SizedBox(width: 6),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
                          decoration: BoxDecoration(
                            color: Colors.white24,
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Text(
                            '${_historyOrders.length}',
                            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.white),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
      body: RefreshIndicator(
        onRefresh: () => _loadOrders(),
        child: TabBarView(
          controller: _tabController,
          children: [
            _buildOrdersList(_activeOrders, isActiveTab: true),
            _buildOrdersList(_historyOrders, isActiveTab: false),
          ],
        ),
      ),
    );
  }

  Widget _buildOrdersList(List<DeliveryOrder> orders, {required bool isActiveTab}) {
    if (_isLoading && orders.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    if (orders.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              isActiveTab ? Icons.moped : Icons.check_circle_outline,
              size: 70,
              color: Colors.grey.shade400,
            ),
            const SizedBox(height: 16),
            Text(
              isActiveTab ? 'No tienes pedidos asignados' : 'No hay pedidos completados hoy',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.grey.shade700),
            ),
            const SizedBox(height: 6),
            Text(
              isActiveTab ? 'Los nuevos pedidos aparecerán aquí automáticamente.' : 'Las entregas de hoy se mostrarán en esta lista.',
              style: TextStyle(fontSize: 13, color: Colors.grey.shade500),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(12),
      itemCount: orders.length,
      itemBuilder: (ctx, index) {
        final order = orders[index];
        return _buildOrderCard(order, isActiveTab: isActiveTab);
      },
    );
  }

  Widget _buildOrderCard(DeliveryOrder order, {required bool isActiveTab}) {
    Color badgeColor;
    String badgeText;

    switch (order.estado) {
      case 'asignado':
        badgeColor = const Color(0xFF2563EB);
        badgeText = 'ASIGNADO';
        break;
      case 'en_camino':
        badgeColor = const Color(0xFFD97706);
        badgeText = 'EN CAMINO';
        break;
      case 'en_puerta':
        badgeColor = const Color(0xFF7C3AED);
        badgeText = 'EN LA PUERTA';
        break;
      case 'entregado':
        badgeColor = const Color(0xFF10B981);
        badgeText = 'ENTREGADO';
        break;
      default:
        badgeColor = Colors.grey;
        badgeText = order.estado.toUpperCase();
    }

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      elevation: 2,
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => OrderDetailScreen(order: order)),
          ).then((_) => _loadOrders(silent: true));
        },
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header: Order Number + Time + Status Badge
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: const Color(0xFF0F172A),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          order.orderNumber,
                          style: const TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                            fontSize: 14,
                          ),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Text(
                        order.createdAt.length >= 16 ? order.createdAt.substring(11, 16) : order.createdAt,
                        style: TextStyle(color: Colors.grey.shade600, fontSize: 12),
                      ),
                    ],
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: badgeColor.withValues(alpha: 0.12),
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: badgeColor, width: 1),
                    ),
                    child: Text(
                      badgeText,
                      style: TextStyle(color: badgeColor, fontWeight: FontWeight.bold, fontSize: 11),
                    ),
                  ),
                ],
              ),
              const Divider(height: 20),

              // Cliente + Phone Buttons
              Row(
                children: [
                  const CircleAvatar(
                    radius: 18,
                    backgroundColor: Color(0xFFE2E8F0),
                    child: Icon(Icons.person, color: Color(0xFF475569), size: 20),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          order.clienteNombre,
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                        ),
                        Text(
                          order.clienteTelefono,
                          style: TextStyle(color: Colors.grey.shade600, fontSize: 12),
                        ),
                      ],
                    ),
                  ),
                  if (order.clienteTelefono.isNotEmpty) ...[
                    IconButton(
                      icon: const Icon(Icons.phone, color: Color(0xFF2563EB)),
                      onPressed: () => LocationService.makePhoneCall(order.clienteTelefono),
                    ),
                    IconButton(
                      icon: const Icon(Icons.chat, color: Color(0xFF10B981)),
                      onPressed: () => LocationService.openWhatsApp(
                        order.clienteTelefono,
                        message: '¡Hola ${order.clienteNombre}! Soy tu repartidor de ${order.orderNumber}.',
                      ),
                    ),
                  ],
                ],
              ),

              const SizedBox(height: 10),

              // Dirección + Botón Maps
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: const Color(0xFFF8FAFC),
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.location_on, color: Colors.redAccent, size: 20),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        order.direccionEntrega.isNotEmpty ? order.direccionEntrega : 'Sin dirección especificada',
                        style: const TextStyle(fontSize: 13, color: Colors.black87),
                      ),
                    ),
                    ElevatedButton.icon(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF0F172A),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                        minimumSize: Size.zero,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                      ),
                      onPressed: () {
                        LocationService.openGoogleMaps(
                          lat: order.latDestino,
                          lng: order.lngDestino,
                          address: order.direccionEntrega,
                          customUrl: order.mapsLink,
                        );
                      },
                      icon: const Icon(Icons.navigation, size: 14),
                      label: const Text('Maps', style: TextStyle(fontSize: 11)),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 10),

              // Metodo de pago & Montos
              _buildPaymentCard(order),

              if (order.notasCocina.isNotEmpty) ...[
                const SizedBox(height: 8),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                  decoration: BoxDecoration(
                    color: const Color(0xFFFEF3C7),
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: const Color(0xFFFCD34D)),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.speaker_notes, color: Color(0xFFB45309), size: 16),
                      const SizedBox(width: 6),
                      Expanded(
                        child: Text(
                          'Nota: ${order.notasCocina}',
                          style: const TextStyle(fontSize: 12, color: Color(0xFF92400E), fontWeight: FontWeight.w500),
                        ),
                      ),
                    ],
                  ),
                ),
              ],

              // Main Status Progression Action Button (only in active orders)
              if (isActiveTab) ...[
                const SizedBox(height: 14),
                _buildActionButton(order),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildPaymentCard(DeliveryOrder order) {
    if (order.isEfectivo) {
      return Container(
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(
          color: const Color(0xFFFFFBEB),
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: const Color(0xFFFDE68A)),
        ),
        child: Row(
          children: [
            const Icon(Icons.payments_outlined, color: Color(0xFFD97706), size: 24),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('COBRAR EFECTIVO:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF92400E))),
                      Text(_currencyFmt.format(order.montoTotal), style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 14, color: Color(0xFFB45309))),
                    ],
                  ),
                  if (order.pagaCon > 0)
                    Text(
                      'Paga con: ${_currencyFmt.format(order.pagaCon)} | VUELTO: ${_currencyFmt.format(order.vuelto)}',
                      style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFFB45309)),
                    ),
                ],
              ),
            ),
          ],
        ),
      );
    } else if (order.isSipap) {
      return Container(
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(
          color: const Color(0xFFECFDF5),
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: const Color(0xFFA7F3D0)),
        ),
        child: Row(
          children: [
            const Icon(Icons.verified, color: Color(0xFF059669), size: 24),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('SIPAP / TRANSFERENCIA:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF065F46))),
                      Text(_currencyFmt.format(order.montoTotal), style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 14, color: Color(0xFF047857))),
                    ],
                  ),
                  const Text('¡PAGO CONFIRMADO! NO COBRAR AL CLIENTE', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF059669))),
                ],
              ),
            ),
          ],
        ),
      );
    } else {
      return Container(
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(
          color: const Color(0xFFEFF6FF),
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: const Color(0xFFBFDBFE)),
        ),
        child: Row(
          children: [
            const Icon(Icons.credit_card, color: Color(0xFF2563EB), size: 24),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('COBRAR CON POS (TARJETA):', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF1E40AF))),
                      Text(_currencyFmt.format(order.montoTotal), style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 14, color: Color(0xFF1D4ED8))),
                    ],
                  ),
                  const Text('Llevar dispositivo POS para pasar tarjeta', style: TextStyle(fontSize: 10, color: Color(0xFF3B82F6))),
                ],
              ),
            ),
          ],
        ),
      );
    }
  }

  Widget _buildActionButton(DeliveryOrder order) {
    if (order.estado == 'asignado') {
      return SizedBox(
        width: double.infinity,
        child: ElevatedButton.icon(
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xFF2563EB),
            foregroundColor: Colors.white,
            padding: const EdgeInsets.symmetric(vertical: 12),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          ),
          onPressed: () => _updateOrderStatus(order, 'en_camino'),
          icon: const Icon(Icons.moped),
          label: const Text('SALIR HACIA DESTINO (EN CAMINO)', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
        ),
      );
    } else if (order.estado == 'en_camino') {
      return SizedBox(
        width: double.infinity,
        child: ElevatedButton.icon(
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xFF7C3AED),
            foregroundColor: Colors.white,
            padding: const EdgeInsets.symmetric(vertical: 12),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          ),
          onPressed: () => _updateOrderStatus(order, 'en_puerta'),
          icon: const Icon(Icons.doorbell),
          label: const Text('¡LLEGUÉ A LA PUERTA! (AVISAR)', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
        ),
      );
    } else if (order.estado == 'en_puerta') {
      return SizedBox(
        width: double.infinity,
        child: ElevatedButton.icon(
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xFF10B981),
            foregroundColor: Colors.white,
            padding: const EdgeInsets.symmetric(vertical: 12),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          ),
          onPressed: () => _updateOrderStatus(order, 'entregado'),
          icon: const Icon(Icons.check_circle),
          label: Text(
            order.isEfectivo ? 'COBRAR ${_currencyFmt.format(order.montoTotal)} Y ENTREGAR' : 'ENTREGAR PEDIDO COMPLETADO',
            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
          ),
        ),
      );
    }

    return const SizedBox.shrink();
  }
}
