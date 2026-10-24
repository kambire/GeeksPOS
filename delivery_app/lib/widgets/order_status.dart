import 'package:flutter/material.dart';

/// Estilo visual de cada estado de pedido, compartido por todas las pantallas.
class OrderStatusStyle {
  final Color color;
  final String label;
  final String longLabel;
  final IconData icon;

  /// Paso en el recorrido: 0 asignado, 1 en camino, 2 en la puerta, 3 entregado (-1 otro)
  final int step;

  const OrderStatusStyle({
    required this.color,
    required this.label,
    required this.longLabel,
    required this.icon,
    required this.step,
  });

  static const asignado = OrderStatusStyle(
    color: Color(0xFF2563EB),
    label: 'Por salir',
    longLabel: 'ASIGNADO',
    icon: Icons.inventory_2_outlined,
    step: 0,
  );
  static const enCamino = OrderStatusStyle(
    color: Color(0xFFD97706),
    label: 'En camino',
    longLabel: 'EN CAMINO AL CLIENTE',
    icon: Icons.moped,
    step: 1,
  );
  static const enPuerta = OrderStatusStyle(
    color: Color(0xFF7C3AED),
    label: 'En la puerta',
    longLabel: 'EN LA PUERTA DEL CLIENTE',
    icon: Icons.doorbell_outlined,
    step: 2,
  );
  static const entregado = OrderStatusStyle(
    color: Color(0xFF059669),
    label: 'Entregado',
    longLabel: 'ENTREGADO CON ÉXITO',
    icon: Icons.check_circle,
    step: 3,
  );

  static OrderStatusStyle of(String estado) {
    switch (estado) {
      case 'asignado':
        return asignado;
      case 'en_camino':
        return enCamino;
      case 'en_puerta':
        return enPuerta;
      case 'entregado':
        return entregado;
      default:
        return OrderStatusStyle(
          color: const Color(0xFF64748B),
          label: estado.isEmpty ? 'Sin estado' : estado[0].toUpperCase() + estado.substring(1),
          longLabel: estado.toUpperCase(),
          icon: Icons.block,
          step: -1,
        );
    }
  }
}

/// Chip con el estado del pedido (fondo lleno del color del estado).
class OrderStatusChip extends StatelessWidget {
  final String estado;

  const OrderStatusChip({super.key, required this.estado});

  @override
  Widget build(BuildContext context) {
    final s = OrderStatusStyle.of(estado);
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: s.color,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(s.icon, size: 14, color: Colors.white),
          const SizedBox(width: 5),
          Text(
            s.label,
            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w700, fontSize: 12),
          ),
        ],
      ),
    );
  }
}

/// Barra de progreso de 3 tramos: asignado -> en camino -> en la puerta.
class OrderProgressBar extends StatelessWidget {
  final String estado;

  const OrderProgressBar({super.key, required this.estado});

  @override
  Widget build(BuildContext context) {
    final s = OrderStatusStyle.of(estado);
    return Row(
      children: List.generate(3, (i) {
        final done = s.step >= i;
        return Expanded(
          child: Container(
            height: 5,
            margin: EdgeInsets.only(right: i < 2 ? 4 : 0),
            decoration: BoxDecoration(
              color: done ? s.color : const Color(0xFFE2E8F0),
              borderRadius: BorderRadius.circular(3),
            ),
          ),
        );
      }),
    );
  }
}
