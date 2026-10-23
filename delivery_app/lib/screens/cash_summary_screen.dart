import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/cash_summary.dart';
import '../services/api_service.dart';
import '../services/location_service.dart';

class CashSummaryScreen extends StatefulWidget {
  const CashSummaryScreen({super.key});

  @override
  State<CashSummaryScreen> createState() => _CashSummaryScreenState();
}

class _CashSummaryScreenState extends State<CashSummaryScreen> {
  CashSummary? _summary;
  bool _isLoading = true;
  final _currencyFmt = NumberFormat.currency(locale: 'es_PY', symbol: 'Gs. ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    _loadSummary();
  }

  Future<void> _loadSummary() async {
    setState(() => _isLoading = true);
    final data = await ApiService.getCashSummary();
    if (mounted) {
      setState(() {
        _summary = data;
        _isLoading = false;
      });
    }
  }

  void _shareSummaryViaWhatsApp() {
    if (_summary == null) return;
    final driver = ApiService.currentDriver;
    final now = DateFormat('dd/MM/yyyy HH:mm').format(DateTime.now());

    final text = StringBuffer();
    text.writeln('📋 *CIERRE DE CAJA / RENDICIÓN REPARTIDOR*');
    text.writeln('🛵 *Repartidor:* ${driver?.nombre ?? "Repartidor"} (Slot #${driver?.slot ?? 1})');
    text.writeln('🏪 *Empresa:* ${driver?.empresa ?? "GeeksPOS"}');
    text.writeln('📅 *Fecha y Hora:* $now');
    text.writeln('-----------------------------------');
    text.writeln('📦 *Pedidos Entregados:* ${_summary!.pedidosEntregados}');
    text.writeln('💵 *Efectivo a Entregar:* ${_currencyFmt.format(_summary!.totalEfectivo)}');
    text.writeln('📱 *Transferencias SIPAP:* ${_currencyFmt.format(_summary!.totalSipap)}');
    text.writeln('💳 *Cobros POS Tarjeta:* ${_currencyFmt.format(_summary!.totalPos)}');
    text.writeln('-----------------------------------');
    text.writeln('💰 *GRAN TOTAL RECAUDADO:* ${_currencyFmt.format(_summary!.totalGeneral)}');

    LocationService.openWhatsApp('', message: text.toString());
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9),
      appBar: AppBar(
        backgroundColor: const Color(0xFF0F172A),
        title: const Text(
          'Arqueo / Cierre del Repartidor',
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white, fontSize: 16),
        ),
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Colors.white70),
            tooltip: 'Actualizar',
            onPressed: _loadSummary,
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _summary == null
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Icon(Icons.error_outline, size: 60, color: Colors.grey),
                      const SizedBox(height: 12),
                      const Text('No se pudo cargar el resumen de caja'),
                      const SizedBox(height: 12),
                      ElevatedButton(
                        onPressed: _loadSummary,
                        child: const Text('Reintentar'),
                      ),
                    ],
                  ),
                )
              : SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      // Grand total hero card
                      Container(
                        padding: const EdgeInsets.all(24),
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(
                            colors: [Color(0xFF0F172A), Color(0xFF1E293B)],
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                          ),
                          borderRadius: BorderRadius.circular(20),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: 0.15),
                              blurRadius: 15,
                              offset: const Offset(0, 8),
                            ),
                          ],
                        ),
                        child: Column(
                          children: [
                            const Text(
                              'TOTAL EFECTIVO A RENDIR',
                              style: TextStyle(
                                color: Color(0xFF38BDF8),
                                fontSize: 13,
                                letterSpacing: 1.5,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 10),
                            Text(
                              _currencyFmt.format(_summary!.totalEfectivo),
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 32,
                                fontWeight: FontWeight.w900,
                              ),
                            ),
                            const SizedBox(height: 14),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                              decoration: BoxDecoration(
                                color: Colors.white.withValues(alpha: 0.1),
                                borderRadius: BorderRadius.circular(20),
                              ),
                              child: Text(
                                '${_summary!.pedidosEntregados} pedidos entregados hoy',
                                style: const TextStyle(color: Colors.white70, fontSize: 13),
                              ),
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 20),

                      // Breakdown Cards
                      _buildMethodCard(
                        title: 'Efectivo en Mano',
                        amount: _summary!.totalEfectivo,
                        icon: Icons.payments,
                        color: const Color(0xFFD97706),
                        subtitle: 'Monto físico recaudado a entregar al cajero',
                      ),
                      const SizedBox(height: 12),
                      _buildMethodCard(
                        title: 'Transferencias SIPAP',
                        amount: _summary!.totalSipap,
                        icon: Icons.verified_user,
                        color: const Color(0xFF059669),
                        subtitle: 'Confirmado por comprobante digital',
                      ),
                      const SizedBox(height: 12),
                      _buildMethodCard(
                        title: 'Cobros POS Tarjeta',
                        amount: _summary!.totalPos,
                        icon: Icons.credit_card,
                        color: const Color(0xFF2563EB),
                        subtitle: 'Cobrado con terminal de tarjeta',
                      ),

                      const SizedBox(height: 24),

                      // Share Button
                      ElevatedButton.icon(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF10B981),
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                          elevation: 4,
                        ),
                        onPressed: _shareSummaryViaWhatsApp,
                        icon: const Icon(Icons.share, size: 20),
                        label: const Text(
                          'COMPARTIR RESUMEN POR WHATSAPP',
                          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                        ),
                      ),
                    ],
                  ),
                ),
    );
  }

  Widget _buildMethodCard({
    required String title,
    required double amount,
    required IconData icon,
    required Color color,
    required String subtitle,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.03),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.12),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Color(0xFF0F172A)),
                ),
                const SizedBox(height: 2),
                Text(
                  subtitle,
                  style: const TextStyle(fontSize: 11, color: Colors.grey),
                ),
              ],
            ),
          ),
          Text(
            _currencyFmt.format(amount),
            style: TextStyle(fontWeight: FontWeight.w900, fontSize: 15, color: color),
          ),
        ],
      ),
    );
  }
}
