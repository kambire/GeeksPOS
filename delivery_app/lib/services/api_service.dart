import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/delivery_driver.dart';
import '../models/delivery_order.dart';
import '../models/cash_summary.dart';

class ApiService {
  static const String _defaultServerUrl = 'https://pos.geeks.com.py';
  static const String _keyServerUrl = 'geeks_server_url';
  static const String _keyToken = 'geeks_auth_token';
  static const String _keyDriverJson = 'geeks_driver_json';

  static String? _cachedServerUrl;
  static String? _cachedToken;
  static DeliveryDriver? _currentDriver;

  static DeliveryDriver? get currentDriver => _currentDriver;

  static Future<String> getServerUrl() async {
    if (_cachedServerUrl != null && _cachedServerUrl!.isNotEmpty) {
      return _cachedServerUrl!;
    }
    final prefs = await SharedPreferences.getInstance();
    final stored = prefs.getString(_keyServerUrl);
    // Si no hay, o venía de túneles temporales de prueba o localhost, migrar al servidor oficial de producción
    if (stored == null ||
        stored.isEmpty ||
        stored.contains('10.0.2.2') ||
        stored.contains('localhost') ||
        stored.contains('trycloudflare.com') ||
        stored.contains('post.geeks.com.py') ||
        stored.contains('compatible-hood-profiles-savannah') ||
        stored.contains('wait-dee-level-signals')) {
      _cachedServerUrl = _defaultServerUrl;
      await prefs.setString(_keyServerUrl, _defaultServerUrl);
    } else {
      _cachedServerUrl = stored;
    }
    return _cachedServerUrl!;
  }

  static Future<void> setServerUrl(String url) async {
    var cleanUrl = url.trim();
    if (cleanUrl.endsWith('/')) {
      cleanUrl = cleanUrl.substring(0, cleanUrl.length - 1);
    }
    // Auto-corregir typo común post -> pos
    if (cleanUrl.contains('post.geeks.com.py')) {
      cleanUrl = cleanUrl.replaceAll('post.geeks.com.py', 'pos.geeks.com.py');
    }
    _cachedServerUrl = cleanUrl;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_keyServerUrl, cleanUrl);
  }

  static Future<String?> getToken() async {
    if (_cachedToken != null) return _cachedToken;
    final prefs = await SharedPreferences.getInstance();
    _cachedToken = prefs.getString(_keyToken);
    return _cachedToken;
  }

  static Future<DeliveryDriver?> getSavedDriver() async {
    if (_currentDriver != null) return _currentDriver;
    final prefs = await SharedPreferences.getInstance();
    final jsonStr = prefs.getString(_keyDriverJson);
    final token = prefs.getString(_keyToken);
    if (jsonStr != null && token != null) {
      try {
        final data = jsonDecode(jsonStr);
        _currentDriver = DeliveryDriver.fromJson(data, token);
        return _currentDriver;
      } catch (_) {}
    }
    return null;
  }

  static Future<Map<String, dynamic>> login(String identifier, String password) async {
    try {
      final baseUrl = await getServerUrl();
      final uri = Uri.parse('$baseUrl/delivery_api/login');

      final response = await http
          .post(
            uri,
            headers: {'Content-Type': 'application/json'},
            body: jsonEncode({
              'username': identifier.trim(),
              'email': identifier.trim(),
              'phone': identifier.trim(),
              'password': password.trim(),
              'pin': password.trim(),
            }),
          )
          .timeout(const Duration(seconds: 15));

      final data = jsonDecode(response.body);

      final isSuccess = (data['status'] == true ||
          data['status'] == 'success' ||
          data['success'] == true);

      if (response.statusCode == 200 && isSuccess) {
        final token = data['token'] as String;
        final driver = DeliveryDriver.fromJson(data['driver'], token);

        _cachedToken = token;
        _currentDriver = driver;

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString(_keyToken, token);
        await prefs.setString(_keyDriverJson, jsonEncode(data['driver']));

        return {'success': true, 'driver': driver, 'message': data['message']};
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Credenciales incorrectas'
        };
      }
    } catch (e) {
      return {
        'success': false,
        'message': 'Error de conexión: Verifica la IP/URL del servidor ($e)'
      };
    }
  }

  static Future<void> logout() async {
    _cachedToken = null;
    _currentDriver = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_keyToken);
    await prefs.remove(_keyDriverJson);
  }

  static Future<Map<String, String>> _authHeaders() async {
    final token = await getToken();
    return {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer $token',
    };
  }

  static Future<List<DeliveryOrder>> getOrders({String status = 'activos'}) async {
    try {
      final baseUrl = await getServerUrl();
      final path = (status == 'historial')
          ? '/delivery_api/orders_history'
          : '/delivery_api/orders';
      final uri = Uri.parse('$baseUrl$path');
      final headers = await _authHeaders();

      final response = await http
          .get(uri, headers: headers)
          .timeout(const Duration(seconds: 15));

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final isSuccess = (data['status'] == true || data['status'] == 'success' || data['success'] == true);
        if (isSuccess && data['orders'] != null) {
          final List list = data['orders'];
          return list.map((item) => DeliveryOrder.fromJson(item)).toList();
        }
      }
      return [];
    } catch (_) {
      return [];
    }
  }

  static Future<Map<String, dynamic>> updateOrderStatus(
    int orderId,
    String newStatus, {
    String nota = '',
  }) async {
    try {
      final baseUrl = await getServerUrl();
      final uri = Uri.parse('$baseUrl/delivery_api/update_order_status');
      final headers = await _authHeaders();

      final response = await http
          .post(
            uri,
            headers: headers,
            body: jsonEncode({
              'order_id': orderId,
              'estado': newStatus,
              'nota': nota,
            }),
          )
          .timeout(const Duration(seconds: 15));

      final data = jsonDecode(response.body);
      final isSuccess = (data['status'] == true || data['status'] == 'success' || data['success'] == true);
      if (response.statusCode == 200 && isSuccess) {
        return {'success': true, 'message': data['message']};
      }
      return {
        'success': false,
        'message': data['message'] ?? 'Error al actualizar estado'
      };
    } catch (e) {
      return {'success': false, 'message': 'Error de conexión: $e'};
    }
  }

  static Future<bool> toggleAvailability(bool activo) async {
    try {
      final baseUrl = await getServerUrl();
      final uri = Uri.parse('$baseUrl/delivery_api/toggle_availability');
      final headers = await _authHeaders();

      final response = await http
          .post(
            uri,
            headers: headers,
            body: jsonEncode({'activo': activo ? 1 : 0}),
          )
          .timeout(const Duration(seconds: 10));

      final data = jsonDecode(response.body);
      final isSuccess = (data['status'] == true || data['status'] == 'success' || data['success'] == true);
      if (response.statusCode == 200 && isSuccess) {
        if (_currentDriver != null) {
          _currentDriver = _currentDriver!.copyWith(activo: activo);
        }
        return true;
      }
      return false;
    } catch (_) {
      return false;
    }
  }

  static Future<bool> updateGps(double lat, double lng) async {
    try {
      final baseUrl = await getServerUrl();
      final uri = Uri.parse('$baseUrl/delivery_api/update_gps');
      final headers = await _authHeaders();

      final response = await http
          .post(
            uri,
            headers: headers,
            body: jsonEncode({'lat': lat, 'lng': lng}),
          )
          .timeout(const Duration(seconds: 8));

      return response.statusCode == 200;
    } catch (_) {
      return false;
    }
  }

  static Future<CashSummary?> getCashSummary() async {
    try {
      final baseUrl = await getServerUrl();
      final uri = Uri.parse('$baseUrl/delivery_api/cash_summary');
      final headers = await _authHeaders();

      final response = await http
          .get(uri, headers: headers)
          .timeout(const Duration(seconds: 15));

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final isSuccess = (data['status'] == true || data['status'] == 'success' || data['success'] == true);
        if (isSuccess && data['summary'] != null) {
          return CashSummary.fromJson(data['summary']);
        }
      }
      return null;
    } catch (_) {
      return null;
    }
  }
}
