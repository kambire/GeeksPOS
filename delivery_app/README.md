# Geeks Delivery

App Flutter (Android) para los repartidores de GeeksPOS: pedidos asignados, estados
(en camino / en la puerta / entregado), GPS en vivo y rendición de caja. Habla con
`/delivery_api` del servidor GeeksPOS.

## Actualización automática

Al abrirse, la app consulta los Releases públicos de `kambire/GeeksPOS`
(`lib/services/update_service.dart`). Si hay un APK con build mayor al instalado,
muestra "Nueva versión disponible" y descarga el instalador.

Convención de los releases:

- tag `delivery-v<versión>` (por ejemplo `delivery-v1.1.0`)
- asset `geeks_delivery_<build>.apk` (`<build>` = versionCode de Android)

Los releases del sistema GeeksPOS (`update_<build>.zip`) no se mezclan con estos.

## Publicar una versión nueva

```bash
git tag -a delivery-v1.1.0 -m "Qué cambió en esta versión"
git push origin delivery-v1.1.0
```

El workflow `.github/workflows/delivery-app.yml` compila, firma y publica el APK.
El mensaje del tag se muestra a los repartidores como novedades.

## Firma (importante)

Todas las versiones deben firmarse con la misma clave; si cambia, Android no deja
instalar la actualización y hay que desinstalar la app.

- Local: `android/key.properties` + `android/app/upload-keystore.jks` (ignorados por git).
  **Guardá una copia de ambos fuera de esta PC**: si se pierden no se pueden publicar más actualizaciones.
- GitHub Actions: secrets `KEYSTORE_BASE64`, `KEYSTORE_PASSWORD`, `KEY_ALIAS`, `KEY_PASSWORD`.

Sin `key.properties`, `flutter build apk --release` firma con la clave debug (solo para pruebas).

## Compilar a mano

```bash
flutter build apk --release --build-name=1.1.0 --build-number=105 --dart-define=APP_BUILD=105 --dart-define=APP_VERSION=1.1.0
```

Sin `--dart-define=APP_BUILD` la app no busca actualizaciones (modo desarrollo).
