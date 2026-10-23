class DeliveryDriver {
  final int id;
  final int slot;
  final String nombre;
  final String telefono;
  final bool activo;
  final bool ocupado;
  final int pedidosHoy;
  final int locId;
  final String empresa;
  final String token;

  DeliveryDriver({
    required this.id,
    required this.slot,
    required this.nombre,
    required this.telefono,
    required this.activo,
    required this.ocupado,
    required this.pedidosHoy,
    required this.locId,
    required this.empresa,
    required this.token,
  });

  factory DeliveryDriver.fromJson(Map<String, dynamic> json, String token) {
    return DeliveryDriver(
      id: int.tryParse(json['id']?.toString() ?? '0') ?? 0,
      slot: int.tryParse(json['slot']?.toString() ?? '0') ?? 0,
      nombre: json['nombre']?.toString() ?? 'Repartidor',
      telefono: json['telefono']?.toString() ?? '',
      activo: (json['activo'] == 1 || json['activo'] == true || json['activo'] == '1'),
      ocupado: (json['ocupado'] == 1 || json['ocupado'] == true || json['ocupado'] == '1'),
      pedidosHoy: int.tryParse(json['pedidos_hoy']?.toString() ?? '0') ?? 0,
      locId: int.tryParse(json['loc_id']?.toString() ?? '0') ?? 0,
      empresa: json['empresa']?.toString() ?? 'GeeksPOS',
      token: token,
    );
  }

  DeliveryDriver copyWith({
    bool? activo,
    bool? ocupado,
    int? pedidosHoy,
  }) {
    return DeliveryDriver(
      id: id,
      slot: slot,
      nombre: nombre,
      telefono: telefono,
      activo: activo ?? this.activo,
      ocupado: ocupado ?? this.ocupado,
      pedidosHoy: pedidosHoy ?? this.pedidosHoy,
      locId: locId,
      empresa: empresa,
      token: token,
    );
  }
}
