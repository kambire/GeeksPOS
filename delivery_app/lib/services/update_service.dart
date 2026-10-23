import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:url_launcher/url_launcher.dart';

/// Actualización automática desde GitHub Releases.
///
/// Convención (la cumple el workflow .github/workflows/delivery-app.yml):
/// - tag `delivery-v<versión>` (ej. delivery-v1.2.0)
/// - asset `geeks_delivery_<build>.apk`, donde <build> es el versionCode de Android.
///
/// El build instalado llega por `--dart-define=APP_BUILD=<build>` al compilar.
/// En compilaciones locales sin ese valor (0) no se busca actualización.
class UpdateService {
  static const String _repo = 'kambire/GeeksPOS';
  static const int currentBuild = int.fromEnvironment('APP_BUILD', defaultValue: 0);
  static const String currentVersion = String.fromEnvironment('APP_VERSION', defaultValue: 'dev');

  static final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();
  static bool _checkedThisSession = false;

  /// Busca la última versión publicada. Devuelve null si no hay una más nueva o si falla la red.
  static Future<AppRelease?> fetchNewerRelease() async {
    if (currentBuild <= 0) return null;
    try {
      final res = await http.get(
        Uri.parse('https://api.github.com/repos/$_repo/releases?per_page=20'),
        headers: {'Accept': 'application/vnd.github+json', 'User-Agent': 'GeeksDelivery'},
      ).timeout(const Duration(seconds: 12));
      if (res.statusCode != 200) return null;

      final releases = jsonDecode(res.body);
      if (releases is! List) return null;

      AppRelease? best;
      final apkName = RegExp(r'^geeks_delivery_(\d+)\.apk$');
      for (final r in releases) {
        if (r is! Map || r['draft'] == true || r['prerelease'] == true) continue;
        final tag = (r['tag_name'] ?? '').toString();
        if (!tag.startsWith('delivery-v')) continue;
        for (final a in (r['assets'] as List? ?? [])) {
          final m = apkName.firstMatch((a['name'] ?? '').toString());
          if (m == null) continue;
          final build = int.parse(m.group(1)!);
          if (build > currentBuild && (best == null || build > best.build)) {
            best = AppRelease(
              build: build,
              version: tag.substring('delivery-v'.length),
              apkUrl: (a['browser_download_url'] ?? '').toString(),
              notes: (r['body'] ?? '').toString(),
              sizeBytes: (a['size'] as num?)?.toInt() ?? 0,
            );
          }
        }
      }
      return (best != null && best.apkUrl.startsWith('https://')) ? best : null;
    } catch (_) {
      return null;
    }
  }

  /// Revisa una vez por sesión y, si hay versión nueva, ofrece descargarla.
  static Future<void> checkAndPrompt() async {
    if (_checkedThisSession) return;
    _checkedThisSession = true;

    final release = await fetchNewerRelease();
    final context = navigatorKey.currentContext;
    if (release == null || context == null || !context.mounted) return;

    final sizeMb = release.sizeBytes > 0 ? ' (${(release.sizeBytes / 1048576).toStringAsFixed(0)} MB)' : '';
    await showDialog<void>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Nueva versión disponible'),
        content: SingleChildScrollView(
          child: Text(
            'Geeks Delivery ${release.version}$sizeMb ya está disponible.\n'
            'Tenés la versión $currentVersion.\n\n'
            '${release.notes.trim().isEmpty ? '' : '${release.notes.trim()}\n\n'}'
            'Al tocar "Actualizar" se descarga el instalador. Abrilo desde las notificaciones y confirmá la instalación.',
          ),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.of(ctx).pop(), child: const Text('Más tarde')),
          FilledButton(
            onPressed: () async {
              Navigator.of(ctx).pop();
              await launchUrl(Uri.parse(release.apkUrl), mode: LaunchMode.externalApplication);
            },
            child: const Text('Actualizar'),
          ),
        ],
      ),
    );
  }
}

class AppRelease {
  final int build;
  final String version;
  final String apkUrl;
  final String notes;
  final int sizeBytes;

  const AppRelease({
    required this.build,
    required this.version,
    required this.apkUrl,
    required this.notes,
    required this.sizeBytes,
  });
}
