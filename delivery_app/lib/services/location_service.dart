import 'dart:async';
import 'package:geolocator/geolocator.dart';
import 'package:url_launcher/url_launcher.dart';
import 'api_service.dart';

class LocationService {
  static Timer? _trackingTimer;
  static bool _isTracking = false;

  static bool get isTracking => _isTracking;

  /// Check and request location permissions
  static Future<bool> handlePermission() async {
    bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      return false;
    }

    LocationPermission permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) {
        return false;
      }
    }

    if (permission == LocationPermission.deniedForever) {
      return false;
    }

    return true;
  }

  /// Start periodic GPS update every [intervalSeconds]
  static Future<void> startTracking({int intervalSeconds = 15}) async {
    if (_isTracking) return;

    final hasPerm = await handlePermission();
    if (!hasPerm) return;

    _isTracking = true;

    // Send initial immediate location
    _sendCurrentLocation();

    _trackingTimer?.cancel();
    _trackingTimer = Timer.periodic(
      Duration(seconds: intervalSeconds),
      (_) => _sendCurrentLocation(),
    );
  }

  /// Stop periodic GPS update
  static void stopTracking() {
    _trackingTimer?.cancel();
    _trackingTimer = null;
    _isTracking = false;
  }

  static Future<void> _sendCurrentLocation() async {
    try {
      final token = await ApiService.getToken();
      if (token == null) {
        stopTracking();
        return;
      }

      final position = await Geolocator.getCurrentPosition(
        locationSettings: const LocationSettings(
          accuracy: LocationAccuracy.high,
          timeLimit: Duration(seconds: 10),
        ),
      );

      await ApiService.updateGps(position.latitude, position.longitude);
    } catch (_) {
      // Ignored: transient network or GPS errors are expected during driving
    }
  }

  /// Launch Google Maps navigation
  static Future<bool> openGoogleMaps({
    double? lat,
    double? lng,
    String? address,
    String? customUrl,
  }) async {
    if (customUrl != null && customUrl.trim().isNotEmpty) {
      final uri = Uri.parse(customUrl);
      if (await canLaunchUrl(uri)) {
        return await launchUrl(uri, mode: LaunchMode.externalApplication);
      }
    }

    if (lat != null && lng != null && lat != 0 && lng != 0) {
      final googleMapsAppUrl = Uri.parse('google.navigation:q=$lat,$lng&mode=d');
      if (await canLaunchUrl(googleMapsAppUrl)) {
        return await launchUrl(googleMapsAppUrl, mode: LaunchMode.externalApplication);
      }
      final webUrl = Uri.parse('https://www.google.com/maps/dir/?api=1&destination=$lat,$lng');
      return await launchUrl(webUrl, mode: LaunchMode.externalApplication);
    }

    if (address != null && address.trim().isNotEmpty) {
      final encodedAddr = Uri.encodeComponent(address.trim());
      final webUrl = Uri.parse('https://www.google.com/maps/search/?api=1&query=$encodedAddr');
      return await launchUrl(webUrl, mode: LaunchMode.externalApplication);
    }

    return false;
  }

  /// Launch Waze navigation
  static Future<bool> openWaze({required double lat, required double lng}) async {
    final wazeAppUrl = Uri.parse('waze://?ll=$lat,$lng&navigate=yes');
    if (await canLaunchUrl(wazeAppUrl)) {
      return await launchUrl(wazeAppUrl, mode: LaunchMode.externalApplication);
    }
    final webUrl = Uri.parse('https://www.waze.com/ul?ll=$lat,$lng&navigate=yes');
    return await launchUrl(webUrl, mode: LaunchMode.externalApplication);
  }

  /// Launch phone call
  static Future<void> makePhoneCall(String phoneNumber) async {
    final cleanPhone = phoneNumber.replaceAll(RegExp(r'[^0-9+]'), '');
    final uri = Uri.parse('tel:$cleanPhone');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  /// Launch WhatsApp chat
  static Future<void> openWhatsApp(String phoneNumber, {String message = ''}) async {
    var cleanPhone = phoneNumber.replaceAll(RegExp(r'[^0-9]'), '');
    if (!cleanPhone.startsWith('595') && cleanPhone.startsWith('09')) {
      cleanPhone = '595${cleanPhone.substring(1)}';
    }
    final encodedMsg = Uri.encodeComponent(message);
    final uri = Uri.parse('https://wa.me/$cleanPhone?text=$encodedMsg');
    await launchUrl(uri, mode: LaunchMode.externalApplication);
  }
}
