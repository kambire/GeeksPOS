import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:url_launcher/url_launcher.dart';

/// Configuración para el sistema universal de actualizaciones automáticas desde GitHub Releases.
/// Puede ser utilizado en cualquier app Flutter (Delivery, POS, Mozo, Cocina KDS, etc.).
class AppUpdaterConfig {
  final String appName;
  final String githubRepo;
  final String githubTagPrefix;
  final RegExp? apkRegex;
  final int currentBuild;
  final String currentVersion;

  const AppUpdaterConfig({
    this.appName = 'Geeks Delivery',
    this.githubRepo = 'kambire/GeeksPOS',
    this.githubTagPrefix = 'delivery-v',
    this.apkRegex,
    this.currentBuild = const int.fromEnvironment('APP_BUILD', defaultValue: 1),
    this.currentVersion = const String.fromEnvironment('APP_VERSION', defaultValue: '1.0.0'),
  });

  /// Configuración predeterminada para la app de repartidores
  static AppUpdaterConfig deliveryDefault() {
    return AppUpdaterConfig(
      appName: 'Geeks Delivery',
      githubRepo: 'kambire/GeeksPOS',
      githubTagPrefix: 'delivery-v',
      apkRegex: RegExp(r'geeks_delivery.*\.apk$|delivery.*\.apk$', caseSensitive: false),
      currentBuild: const int.fromEnvironment('APP_BUILD', defaultValue: 1),
      currentVersion: const String.fromEnvironment('APP_VERSION', defaultValue: '1.0.0'),
    );
  }
}

/// Modelo de versión remota encontrada en GitHub Releases
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

  String get formattedSize {
    if (sizeBytes <= 0) return '';
    final mb = sizeBytes / (1024 * 1024);
    return '${mb.toStringAsFixed(1)} MB';
  }
}

/// Servicio Universal de Actualización de Aplicaciones desde GitHub Releases.
class UpdateService {
  static final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();
  static bool _checkedThisSession = false;
  static AppUpdaterConfig defaultConfig = AppUpdaterConfig.deliveryDefault();

  /// Compara dos strings de versión semántica (ej. "1.1.0" > "1.0.0")
  static bool isVersionHigher(String newVer, String curVer) {
    try {
      final cleanNew = newVer.replaceAll(RegExp(r'^[^\d]*'), '').trim();
      final cleanCur = curVer.replaceAll(RegExp(r'^[^\d]*'), '').trim();
      if (cleanNew.isEmpty || cleanCur.isEmpty) return false;

      final newParts = cleanNew.split('.').map((p) => int.tryParse(p) ?? 0).toList();
      final curParts = cleanCur.split('.').map((p) => int.tryParse(p) ?? 0).toList();

      final maxLen = newParts.length > curParts.length ? newParts.length : curParts.length;
      for (int i = 0; i < maxLen; i++) {
        final n = i < newParts.length ? newParts[i] : 0;
        final c = i < curParts.length ? curParts[i] : 0;
        if (n > c) return true;
        if (n < c) return false;
      }
      return false;
    } catch (_) {
      return false;
    }
  }

  /// Consulta la versión disponible exclusivamente desde GitHub Releases
  static Future<AppRelease?> fetchNewerRelease({AppUpdaterConfig? config}) async {
    final cfg = config ?? defaultConfig;
    try {
      final url = Uri.parse('https://api.github.com/repos/${cfg.githubRepo}/releases?per_page=15');
      final res = await http.get(
        url,
        headers: {
          'Accept': 'application/vnd.github+json',
          'User-Agent': 'GeeksAppUpdater-${cfg.appName}',
        },
      ).timeout(const Duration(seconds: 10));

      if (res.statusCode != 200) return null;

      final releases = jsonDecode(res.body);
      if (releases is! List || releases.isEmpty) return null;

      final pattern = cfg.apkRegex ?? RegExp(r'\.apk$', caseSensitive: false);
      final buildRegex = RegExp(r'_(\d+)\.apk$', caseSensitive: false);

      for (final r in releases) {
        if (r is! Map || r['draft'] == true || r['prerelease'] == true) continue;
        final tag = (r['tag_name'] ?? '').toString();
        if (!tag.startsWith(cfg.githubTagPrefix)) continue;

        final rawVersion = tag.substring(cfg.githubTagPrefix.length);
        final assets = r['assets'] as List? ?? [];

        for (final a in assets) {
          if (a is! Map) continue;
          final name = (a['name'] ?? '').toString();
          if (!pattern.hasMatch(name) && !name.toLowerCase().endsWith('.apk')) continue;

          int buildNumber = 0;
          final bm = buildRegex.firstMatch(name);
          if (bm != null) {
            buildNumber = int.tryParse(bm.group(1)!) ?? 0;
          }

          final downloadUrl = (a['browser_download_url'] ?? '').toString();
          if (!downloadUrl.startsWith('http')) continue;

          // Si el build number es superior, o si la versión semántica es superior
          final isNewer = (buildNumber > 0 && buildNumber > cfg.currentBuild) ||
              isVersionHigher(rawVersion, cfg.currentVersion);

          if (isNewer) {
            return AppRelease(
              build: buildNumber > 0 ? buildNumber : cfg.currentBuild + 1,
              version: rawVersion,
              apkUrl: downloadUrl,
              notes: (r['body'] ?? '').toString(),
              sizeBytes: (a['size'] as num?)?.toInt() ?? 0,
            );
          }
        }
      }
      return null;
    } catch (_) {
      return null;
    }
  }

  /// Muestra el diálogo moderno de actualización
  static Future<void> showUpdateDialog(
    BuildContext context,
    AppRelease release, {
    AppUpdaterConfig? config,
  }) async {
    final cfg = config ?? defaultConfig;
    final sizeText = release.formattedSize.isNotEmpty ? ' • ${release.formattedSize}' : '';

    await showDialog<void>(
      context: context,
      barrierDismissible: true,
      builder: (ctx) => AlertDialog(
        backgroundColor: const Color(0xFF1E293B),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        contentPadding: const EdgeInsets.fromLTRB(24, 20, 24, 16),
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF38BDF8), Color(0xFF2563EB)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(14),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF2563EB).withValues(alpha: 0.35),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: const Icon(Icons.system_update_rounded, color: Colors.white, size: 26),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Nueva Versión en GitHub',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                  Text(
                    cfg.appName,
                    style: const TextStyle(fontSize: 12, color: Colors.white60),
                  ),
                ],
              ),
            ),
          ],
        ),
        content: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 420, maxHeight: 400),
          child: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 6),
                // Comparación de versiones
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                  decoration: BoxDecoration(
                    color: const Color(0xFF0F172A),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: Colors.white.withValues(alpha: 0.08)),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('Instalada', style: TextStyle(fontSize: 10, color: Colors.white54)),
                          Text(
                            'v${cfg.currentVersion}',
                            style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: Colors.white70),
                          ),
                        ],
                      ),
                      const Icon(Icons.arrow_forward_rounded, color: Color(0xFF38BDF8), size: 18),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          const Text('Disponible', style: TextStyle(fontSize: 10, color: Color(0xFF38BDF8))),
                          Text(
                            'v${release.version}',
                            style: const TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.bold,
                              color: Color(0xFF38BDF8),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 10),

                // Badge de GitHub Releases y tamaño
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: const Color(0xFF2563EB).withValues(alpha: 0.2),
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        'GitHub Releases$sizeText',
                        style: const TextStyle(fontSize: 11, color: Color(0xFF38BDF8), fontWeight: FontWeight.w500),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 14),

                // Notas del release
                if (release.notes.trim().isNotEmpty) ...[
                  const Text(
                    'Novedades de la versión:',
                    style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.white),
                  ),
                  const SizedBox(height: 6),
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: const Color(0xFF0F172A),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Text(
                      release.notes.trim(),
                      style: const TextStyle(fontSize: 12, color: Colors.white70, height: 1.4),
                    ),
                  ),
                  const SizedBox(height: 14),
                ],

                const Text(
                  'Al tocar "Descargar e Instalar" se descargará el APK oficial desde GitHub. Una vez descargado, ábrelo para completar la actualización.',
                  style: TextStyle(fontSize: 11, color: Colors.white54, height: 1.3),
                ),
              ],
            ),
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('Más tarde', style: TextStyle(color: Colors.white60)),
          ),
          FilledButton.icon(
            style: FilledButton.styleFrom(
              backgroundColor: const Color(0xFF2563EB),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            ),
            icon: const Icon(Icons.download_rounded, size: 18),
            label: const Text('Descargar e Instalar'),
            onPressed: () async {
              Navigator.of(ctx).pop();
              try {
                final uri = Uri.parse(release.apkUrl);
                await launchUrl(uri, mode: LaunchMode.externalApplication);
              } catch (_) {
                if (context.mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('No se pudo abrir el enlace de GitHub')),
                  );
                }
              }
            },
          ),
        ],
      ),
    );
  }

  /// Verifica actualizaciones en GitHub Releases y muestra el diálogo si hay una versión nueva.
  static Future<void> checkAndPrompt({
    BuildContext? context,
    bool manual = false,
    AppUpdaterConfig? config,
  }) async {
    final cfg = config ?? defaultConfig;

    if (!manual) {
      if (_checkedThisSession) return;
      _checkedThisSession = true;
    }

    final targetContext = context ?? navigatorKey.currentContext;

    if (manual && targetContext != null && targetContext.mounted) {
      ScaffoldMessenger.of(targetContext).showSnackBar(
        SnackBar(
          content: Row(
            children: [
              const SizedBox(
                width: 16,
                height: 16,
                child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
              ),
              const SizedBox(width: 12),
              Text('Consultando GitHub Releases de ${cfg.appName}...'),
            ],
          ),
          duration: const Duration(seconds: 2),
          backgroundColor: const Color(0xFF1E293B),
        ),
      );
    }

    final release = await fetchNewerRelease(config: cfg);

    final validCtx = targetContext ?? navigatorKey.currentContext;
    if (validCtx == null || !validCtx.mounted) return;

    if (release != null) {
      await showUpdateDialog(validCtx, release, config: cfg);
    } else if (manual) {
      ScaffoldMessenger.of(validCtx).showSnackBar(
        SnackBar(
          content: Row(
            children: [
              const Icon(Icons.check_circle_rounded, color: Color(0xFF10B981), size: 20),
              const SizedBox(width: 10),
              Expanded(
                child: Text('Tu app está al día con GitHub (v${cfg.currentVersion}).'),
              ),
            ],
          ),
          backgroundColor: const Color(0xFF1E293B),
        ),
      );
    }
  }
}
