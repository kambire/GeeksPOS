import 'dart:async';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/delivery_driver.dart';
import '../models/delivery_order.dart';
import '../services/alarm_service.dart';
import '../services/api_service.dart';
import '../services/location_service.dart';
import '../services/update_service.dart';
import '../widgets/order_status.dart';
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
  Set<int> _knownOrderIds = {};
  bool _initialLoadDone = false;
  bool _isLoading = false;
  Timer? _pollingTimer;

  // es_PY ubica el símbolo al final ("185.000 Gs."): sin espacio extra en el símbolo
  final _currencyFmt = NumberFormat.currency(locale: 'es_PY', symbol: 'Gs.', decimalDigits: 0);

  // Orden de urgencia de los grupos en la pestaña de activos
  static const _gruposActivos = ['en_puerta', 'en_camino', 'asignado'];

  // Minutos desde la asignación a partir de los cuales el pedido se marca como demorado
  static const _minutosDemora = 40;

  @override
  void initState() {
    super.initState();
    _driver = widget.driver;
    _tabController = TabController(length: 2, vsync: this);
    _loadOrders();

    // Auto-refresh orders every 8 seconds with order detection
    _pollingTimer = Timer.periodic(const Duration(seconds: 8), (_) {
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
        final currentActiveIds = active.map((o) => o.id).toSet();

        // Si ya se cargó previamente y aparecen IDs nuevos: ¡Disparar alarma y notificación!
        if (_initialLoadDone && active.isNotEmpty) {
          final newIds = currentActiveIds.difference(_knownOrderIds);
          if (newIds.isNotEmpty) {
            final newOrder = active.firstWhere((o) => newIds.contains(o.id));
            AlarmService.showNewOrderAlert(
              context,
              newOrder,
              onViewOrder: () => _openDetail(newOrder),
            );
          }
        }

        _knownOrderIds = currentActiveIds;
        _initialLoadDone = true;

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

  void _openDetail(DeliveryOrder order) {
    Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => OrderDetailScreen(order: order)),
    ).then((_) => _loadOrders(silent: true));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFE9EEF5),
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
            icon: const Icon(Icons.system_update_rounded, color: Color(0xFF38BDF8)),
            tooltip: 'Buscar Actualizaciones',
            onPressed: () => UpdateService.checkAndPrompt(context: context, manual: true),
          ),
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
                labelColor: Colors.white,
                unselectedLabelColor: Colors.white60,
                tabs: [
                  Tab(child: _tabLabel('ACTIVOS', _activeOrders.length, highlight: _activeOrders.isNotEmpty)),
                  Tab(child: _tabLabel('HISTORIAL HOY', _historyOrders.length)),
                ],
              ),
            ],
          ),
        ),
      ),
      body: Column(
        children: [
          if (_isLoading) const LinearProgressIndicator(minHeight: 2),
          Expanded(
            child: RefreshIndicator(
              onRefresh: () => _loadOrders(),
              child: TabBarView(
                controller: _tabController,
                children: [
                  _buildOrdersList(_activeOrders, isActiveTab: true),
                  _buildOrdersList(_historyOrders, isActiveTab: false),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _tabLabel(String text, int count, {bool highlight = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Text(text, style: const TextStyle(fontWeight: FontWeight.bold)),
        const SizedBox(width: 6),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
          decoration: BoxDecoration(
            color: highlight ? const Color(0xFF2563EB) : Colors.white24,
            borderRadius: BorderRadius.circular(10),
          ),
          child: Text(
            '$count',
            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.white),
          ),
        ),
      ],
    );
  }

  // ---------------------------------------------------------------------------
  // Listas
  // ---------------------------------------------------------------------------

  Widget _buildOrdersList(List<DeliveryOrder> orders, {required bool isActiveTab}) {
    if (_isLoading && orders.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    if (orders.isEmpty) {
      // Scrollable para que funcione el "deslizar para actualizar" aun sin pedidos
      return ListView(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.fromLTRB(32, 90, 32, 24),
        children: [
          Icon(
            isActiveTab ? Icons.moped : Icons.check_circle_outline,
            size: 72,
            color: Colors.grey.shade400,
          ),
          const SizedBox(height: 16),
          Text(
            isActiveTab ? 'No tienes pedidos asignados' : 'No hay pedidos completados hoy',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.grey.shade700),
          ),
          const SizedBox(height: 6),
          Text(
            isActiveTab
                ? 'Los nuevos pedidos aparecerán aquí automáticamente.'
                : 'Las entregas de hoy se mostrarán en esta lista.',
            style: TextStyle(fontSize: 13, color: Colors.grey.shade500),
            textAlign: TextAlign.center,
          ),
        ],
      );
    }

    final children = <Widget>[];
    if (isActiveTab) {
      // Agrupados por urgencia y, dentro de cada grupo, el más antiguo primero
      final ordenados = [...orders]..sort((a, b) => a.createdAt.compareTo(b.createdAt));
      for (final estado in _gruposActivos) {
        final grupo = ordenados.where((o) => o.estado == estado).toList();
        if (grupo.isEmpty) continue;
        children.add(_buildSectionHeader(OrderStatusStyle.of(estado), grupo.length));
        children.addAll(grupo.map(_buildOrderCard));
      }
      final otros = ordenados.where((o) => !_gruposActivos.contains(o.estado)).toList();
      if (otros.isNotEmpty) {
        children.add(_buildSectionHeader(OrderStatusStyle.of(''), otros.length, title: 'Otros'));
        children.addAll(otros.map(_buildOrderCard));
      }
    } else {
      children.add(_buildHistorySummary(orders));
      children.addAll(orders.map(_buildHistoryCard));
    }

    return ListView(
      physics: const AlwaysScrollableScrollPhysics(),
      padding: const EdgeInsets.fromLTRB(14, 6, 14, 28),
      children: children,
    );
  }

  Widget _buildSectionHeader(OrderStatusStyle s, int count, {String? title}) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(2, 14, 2, 10),
      child: Row(
        children: [
          Container(
            width: 10,
            height: 10,
            decoration: BoxDecoration(color: s.color, shape: BoxShape.circle),
          ),
          const SizedBox(width: 8),
          Text(
            (title ?? s.label).toUpperCase(),
            style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800, letterSpacing: 0.8, color: s.color),
          ),
          const SizedBox(width: 6),
          Text(
            '($count)',
            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Color(0xFF64748B)),
          ),
          const SizedBox(width: 10),
          Expanded(child: Container(height: 1, color: s.color.withValues(alpha: 0.25))),
        ],
      ),
    );
  }

  /// Contenedor de cada pedido: tarjeta blanca con borde, sombra y franja lateral del color del estado.
  Widget _cardFrame({required Color accent, required Widget child, VoidCallback? onTap, bool flat = false}) {
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFDDE3EC)),
        boxShadow: flat
            ? null
            : const [BoxShadow(color: Color(0x1A0F172A), blurRadius: 14, offset: Offset(0, 4))],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(15),
        child: Material(
          color: Colors.white,
          child: InkWell(
            onTap: onTap,
            child: IntrinsicHeight(
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Container(width: 6, color: accent),
                  Expanded(child: child),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  // ---------------------------------------------------------------------------
  // Tarjeta de pedido activo
  // ---------------------------------------------------------------------------

  String _hora(String fecha) => fecha.length >= 16 ? fecha.substring(11, 16) : fecha;

  /// Minutos desde la asignación (o creación) del pedido; null si la fecha no es válida.
  int? _minutosTranscurridos(DeliveryOrder o) {
    final raw = o.asignadoAt.isNotEmpty ? o.asignadoAt : o.createdAt;
    final base = DateTime.tryParse(raw.replaceFirst(' ', 'T'));
    if (base == null) return null;
    final min = DateTime.now().difference(base).inMinutes;
    return (min < 0 || min > 24 * 60) ? null : min;
  }

  String _textoTranscurrido(int min) {
    if (min < 1) return 'recién';
    if (min < 60) return 'hace $min min';
    return 'hace ${min ~/ 60} h ${min % 60} min';
  }

  Widget _buildOrderCard(DeliveryOrder order) {
    final s = OrderStatusStyle.of(order.estado);
    final minutos = _minutosTranscurridos(order);
    final demorado = minutos != null && minutos >= _minutosDemora;

    return _cardFrame(
      accent: s.color,
      onTap: () => _openDetail(order),
      child: Padding(
        padding: const EdgeInsets.fromLTRB(14, 14, 14, 14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Encabezado: número de pedido, hora y estado
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Pedido ${order.orderNumber}',
                        style: const TextStyle(fontSize: 19, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                      ),
                      const SizedBox(height: 2),
                      Row(
                        children: [
                          Icon(
                            demorado ? Icons.warning_amber_rounded : Icons.schedule,
                            size: 14,
                            color: demorado ? const Color(0xFFDC2626) : const Color(0xFF64748B),
                          ),
                          const SizedBox(width: 4),
                          Text(
                            '${_hora(order.createdAt)}${minutos != null ? ' · ${_textoTranscurrido(minutos)}' : ''}',
                            style: TextStyle(
                              fontSize: 12.5,
                              fontWeight: demorado ? FontWeight.w700 : FontWeight.w500,
                              color: demorado ? const Color(0xFFDC2626) : const Color(0xFF64748B),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                OrderStatusChip(estado: order.estado),
              ],
            ),
            const SizedBox(height: 12),
            OrderProgressBar(estado: order.estado),
            const SizedBox(height: 14),

            // Cliente + llamar / WhatsApp
            Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        order.clienteNombre,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(fontSize: 15.5, fontWeight: FontWeight.w700, color: Color(0xFF1E293B)),
                      ),
                      if (order.clienteTelefono.isNotEmpty)
                        Text(
                          order.clienteTelefono,
                          style: const TextStyle(fontSize: 12.5, color: Color(0xFF64748B)),
                        ),
                    ],
                  ),
                ),
                if (order.clienteTelefono.isNotEmpty) ...[
                  _circleAction(
                    Icons.phone,
                    const Color(0xFF2563EB),
                    'Llamar',
                    () => LocationService.makePhoneCall(order.clienteTelefono),
                  ),
                  const SizedBox(width: 8),
                  _circleAction(
                    Icons.chat,
                    const Color(0xFF16A34A),
                    'WhatsApp',
                    () => LocationService.openWhatsApp(
                      order.clienteTelefono,
                      message: '¡Hola ${order.clienteNombre}! Soy tu repartidor de ${order.orderNumber}.',
                    ),
                  ),
                ],
              ],
            ),
            const SizedBox(height: 12),

            // Dirección + navegar
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Padding(
                  padding: EdgeInsets.only(top: 1),
                  child: Icon(Icons.location_on, color: Color(0xFFDC2626), size: 19),
                ),
                const SizedBox(width: 6),
                Expanded(
                  child: Text(
                    order.direccionEntrega.isNotEmpty ? order.direccionEntrega : 'Sin dirección especificada',
                    maxLines: 3,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontSize: 13.5, height: 1.3, color: Color(0xFF334155)),
                  ),
                ),
                const SizedBox(width: 8),
                TextButton.icon(
                  style: TextButton.styleFrom(
                    backgroundColor: const Color(0xFF0F172A),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    minimumSize: Size.zero,
                    tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                  ),
                  onPressed: () {
                    LocationService.openGoogleMaps(
                      lat: order.latDestino,
                      lng: order.lngDestino,
                      address: order.direccionEntrega,
                      customUrl: order.mapsLink,
                    );
                  },
                  icon: const Icon(Icons.navigation, size: 16),
                  label: const Text('Ir', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700)),
                ),
              ],
            ),
            const SizedBox(height: 12),

            // Cobro
            _buildPaymentRow(order),

            if (order.items.isNotEmpty) ...[
              const SizedBox(height: 8),
              Text(
                '${order.items.fold<int>(0, (t, i) => t + i.qty)} producto(s) · tocá para ver el detalle',
                style: const TextStyle(fontSize: 12, color: Color(0xFF64748B)),
              ),
            ],

            if (order.notasCocina.isNotEmpty) ...[
              const SizedBox(height: 10),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                decoration: BoxDecoration(
                  color: const Color(0xFFFEF9C3),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Icon(Icons.sticky_note_2_outlined, color: Color(0xFFA16207), size: 16),
                    const SizedBox(width: 6),
                    Expanded(
                      child: Text(
                        order.notasCocina,
                        style: const TextStyle(fontSize: 12.5, color: Color(0xFF713F12), fontWeight: FontWeight.w500),
                      ),
                    ),
                  ],
                ),
              ),
            ],

            const SizedBox(height: 14),
            _buildActionButton(order),
          ],
        ),
      ),
    );
  }

  Widget _circleAction(IconData icon, Color color, String tooltip, VoidCallback onTap) {
    return Material(
      color: color.withValues(alpha: 0.12),
      shape: const CircleBorder(),
      child: IconButton(
        icon: Icon(icon, color: color, size: 20),
        tooltip: tooltip,
        visualDensity: VisualDensity.compact,
        onPressed: onTap,
      ),
    );
  }

  /// Cómo se cobra el pedido, con el monto destacado.
  Widget _buildPaymentRow(DeliveryOrder order) {
    final Color color;
    final Color bg;
    final IconData icon;
    final String titulo;
    String? detalle;

    if (order.isEfectivo) {
      color = const Color(0xFFB45309);
      bg = const Color(0xFFFFF7ED);
      icon = Icons.payments_outlined;
      titulo = 'Cobrar en efectivo';
      if (order.pagaCon > 0) {
        detalle = 'Paga con ${_currencyFmt.format(order.pagaCon)} · Vuelto ${_currencyFmt.format(order.vuelto)}';
      }
    } else if (order.isSipap) {
      color = const Color(0xFF047857);
      bg = const Color(0xFFECFDF5);
      icon = Icons.verified;
      titulo = 'Pagado por transferencia';
      detalle = 'No cobrar al cliente';
    } else {
      color = const Color(0xFF1D4ED8);
      bg = const Color(0xFFEFF6FF);
      icon = Icons.credit_card;
      titulo = 'Cobrar con POS (tarjeta)';
      detalle = 'Llevar el POS para pasar la tarjeta';
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(12)),
      child: Row(
        children: [
          Icon(icon, color: color, size: 22),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(titulo, style: TextStyle(fontSize: 12.5, fontWeight: FontWeight.w700, color: color)),
                if (detalle != null)
                  Text(detalle, style: TextStyle(fontSize: 11.5, color: color.withValues(alpha: 0.85))),
              ],
            ),
          ),
          const SizedBox(width: 8),
          Text(
            _currencyFmt.format(order.montoTotal),
            style: TextStyle(fontSize: 17, fontWeight: FontWeight.w900, color: color),
          ),
        ],
      ),
    );
  }

  /// Botón principal: avanza el pedido al siguiente estado (con el color de ese estado).
  Widget _buildActionButton(DeliveryOrder order) {
    final String next;
    final String label;
    final IconData icon;
    final Color color;

    switch (order.estado) {
      case 'asignado':
        next = 'en_camino';
        label = 'Salir hacia el cliente';
        icon = Icons.moped;
        color = OrderStatusStyle.enCamino.color;
        break;
      case 'en_camino':
        next = 'en_puerta';
        label = 'Llegué a la puerta (avisar)';
        icon = Icons.doorbell_outlined;
        color = OrderStatusStyle.enPuerta.color;
        break;
      case 'en_puerta':
        next = 'entregado';
        label = order.isEfectivo
            ? 'Cobré ${_currencyFmt.format(order.montoTotal)} y entregué'
            : 'Pedido entregado';
        icon = Icons.check_circle;
        color = OrderStatusStyle.entregado.color;
        break;
      default:
        return const SizedBox.shrink();
    }

    return SizedBox(
      width: double.infinity,
      height: 50,
      child: FilledButton.icon(
        style: FilledButton.styleFrom(
          backgroundColor: color,
          foregroundColor: Colors.white,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
        onPressed: _isLoading ? null : () => _updateOrderStatus(order, next),
        icon: Icon(icon),
        label: Text(label, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
      ),
    );
  }

  // ---------------------------------------------------------------------------
  // Historial
  // ---------------------------------------------------------------------------

  Widget _buildHistorySummary(List<DeliveryOrder> orders) {
    final entregados = orders.where((o) => o.estado == 'entregado').toList();
    final efectivo = entregados.where((o) => o.isEfectivo).fold<double>(0, (t, o) => t + o.montoTotal);

    return Container(
      margin: const EdgeInsets.only(top: 8, bottom: 14),
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        gradient: const LinearGradient(colors: [Color(0xFF0F172A), Color(0xFF1E3A8A)]),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Row(
        children: [
          Expanded(child: _statItem('${entregados.length}', 'Entregas hoy')),
          Container(width: 1, height: 34, color: Colors.white24),
          Expanded(child: _statItem(_currencyFmt.format(efectivo), 'Efectivo a rendir')),
        ],
      ),
    );
  }

  Widget _statItem(String value, String label) {
    return Column(
      children: [
        Text(value, style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w800)),
        const SizedBox(height: 2),
        Text(label, style: const TextStyle(color: Colors.white70, fontSize: 11.5)),
      ],
    );
  }

  /// Tarjeta compacta del historial (sin acciones).
  Widget _buildHistoryCard(DeliveryOrder order) {
    final s = OrderStatusStyle.of(order.estado);
    final pago = order.isEfectivo
        ? 'Efectivo'
        : order.isSipap
            ? 'Transferencia'
            : 'POS';

    return _cardFrame(
      accent: s.color,
      flat: true,
      onTap: () => _openDetail(order),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Text(
                        'Pedido ${order.orderNumber}',
                        style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                      ),
                      const SizedBox(width: 8),
                      Text(_hora(order.createdAt), style: const TextStyle(fontSize: 12, color: Color(0xFF64748B))),
                    ],
                  ),
                  const SizedBox(height: 3),
                  Text(
                    order.clienteNombre,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontSize: 13, color: Color(0xFF475569)),
                  ),
                  if (order.direccionEntrega.isNotEmpty)
                    Text(
                      order.direccionEntrega,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontSize: 12, color: Color(0xFF94A3B8)),
                    ),
                ],
              ),
            ),
            const SizedBox(width: 10),
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                OrderStatusChip(estado: order.estado),
                const SizedBox(height: 6),
                Text(
                  _currencyFmt.format(order.montoTotal),
                  style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                ),
                Text(pago, style: const TextStyle(fontSize: 11, color: Color(0xFF64748B))),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
