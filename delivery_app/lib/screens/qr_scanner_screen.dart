import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:mobile_scanner/mobile_scanner.dart';

class QrScannerScreen extends StatefulWidget {
  const QrScannerScreen({super.key});

  @override
  State<QrScannerScreen> createState() => _QrScannerScreenState();
}

class _QrScannerScreenState extends State<QrScannerScreen> {
  final MobileScannerController _controller = MobileScannerController(
    detectionSpeed: DetectionSpeed.normal,
    facing: CameraFacing.back,
  );
  bool _hasScanned = false;

  void _handleBarcode(BarcodeCapture capture) {
    if (_hasScanned) return;
    final List<Barcode> barcodes = capture.barcodes;
    for (final barcode in barcodes) {
      final rawValue = barcode.rawValue;
      if (rawValue != null && rawValue.trim().isNotEmpty) {
        _hasScanned = true;
        _processQrData(rawValue.trim());
        break;
      }
    }
  }

  void _processQrData(String rawData) {
    String? serverUrl;
    String? phone;
    String? pin;

    // 1. Try parsing JSON format
    try {
      final decoded = jsonDecode(rawData);
      if (decoded is Map) {
        serverUrl = decoded['url']?.toString();
        phone = decoded['phone']?.toString();
        pin = decoded['pin']?.toString();
      }
    } catch (_) {}

    // 2. Try parsing pipe-separated format: GEEKS_DELIVERY|url|phone|pin
    if (serverUrl == null && rawData.startsWith('GEEKS_DELIVERY|')) {
      final parts = rawData.split('|');
      if (parts.length >= 4) {
        serverUrl = parts[1];
        phone = parts[2];
        pin = parts[3];
      }
    }

    // 3. Fallback: if rawData is simply a URL with query params
    if (serverUrl == null && rawData.contains('delivery_api')) {
      try {
        final uri = Uri.parse(rawData);
        serverUrl = '${uri.scheme}://${uri.host}${uri.hasPort ? ":${uri.port}" : ""}';
        phone = uri.queryParameters['phone'];
        pin = uri.queryParameters['pin'];
      } catch (_) {}
    }

    if (phone != null || pin != null || serverUrl != null) {
      if (mounted) {
        Navigator.pop(context, {
          'url': serverUrl,
          'phone': phone,
          'pin': pin,
        });
      }
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Código QR no reconocido para Geeks Delivery')),
        );
      }
      Future.delayed(const Duration(seconds: 2), () {
        if (mounted) setState(() => _hasScanned = false);
      });
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        backgroundColor: const Color(0xFF0F172A),
        title: const Text(
          'Escanear QR de Vinculación',
          style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold),
        ),
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          IconButton(
            icon: const Icon(Icons.flash_on, color: Colors.white70),
            onPressed: () => _controller.toggleTorch(),
          ),
          IconButton(
            icon: const Icon(Icons.flip_camera_ios, color: Colors.white70),
            onPressed: () => _controller.switchCamera(),
          ),
        ],
      ),
      body: Stack(
        children: [
          MobileScanner(
            controller: _controller,
            onDetect: _handleBarcode,
            errorBuilder: (context, error) {
              return Center(
                child: Padding(
                  padding: const EdgeInsets.all(24.0),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Icon(Icons.camera_alt_outlined, color: Colors.amber, size: 50),
                      const SizedBox(height: 14),
                      const Text(
                        'No se pudo iniciar la cámara o el lector ML.',
                        textAlign: TextAlign.center,
                        style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        'Detalle: ${error.errorCode.name}',
                        style: const TextStyle(color: Colors.white54, fontSize: 12),
                      ),
                      const SizedBox(height: 20),
                      ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF2563EB),
                          foregroundColor: Colors.white,
                        ),
                        onPressed: () => Navigator.pop(context),
                        child: const Text('Volver e ingresar manualmente'),
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
          // Scanner overlay frame
          Center(
            child: Container(
              width: 260,
              height: 260,
              decoration: BoxDecoration(
                border: Border.all(color: const Color(0xFF38BDF8), width: 3),
                borderRadius: BorderRadius.circular(20),
              ),
            ),
          ),
          Positioned(
            bottom: 40,
            left: 20,
            right: 20,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: BoxDecoration(
                color: Colors.black87,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.white24),
              ),
              child: const Text(
                'Apunta la cámara al código QR de tu repartidor en la pantalla de GeeksPOS.',
                textAlign: TextAlign.center,
                style: TextStyle(color: Colors.white, fontSize: 13),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
