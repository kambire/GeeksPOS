class CashSummary {
  final int pedidosEntregados;
  final double totalEfectivo;
  final double totalSipap;
  final double totalPos;
  final double totalGeneral;

  CashSummary({
    required this.pedidosEntregados,
    required this.totalEfectivo,
    required this.totalSipap,
    required this.totalPos,
    required this.totalGeneral,
  });

  factory CashSummary.fromJson(Map<String, dynamic> json) {
    return CashSummary(
      pedidosEntregados: int.tryParse(json['pedidos_entregados']?.toString() ?? '0') ?? 0,
      totalEfectivo: double.tryParse(json['total_efectivo']?.toString() ?? '0') ?? 0.0,
      totalSipap: double.tryParse(json['total_sipap']?.toString() ?? '0') ?? 0.0,
      totalPos: double.tryParse(json['total_pos']?.toString() ?? '0') ?? 0.0,
      totalGeneral: double.tryParse(json['total_general']?.toString() ?? '0') ?? 0.0,
    );
  }
}
