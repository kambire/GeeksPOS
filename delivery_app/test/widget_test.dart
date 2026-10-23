import 'package:flutter_test/flutter_test.dart';
import 'package:geeks_delivery/main.dart';

void main() {
  testWidgets('App smoke test', (WidgetTester tester) async {
    await tester.pumpWidget(const GeeksDeliveryApp());
    expect(find.text('GEEKS DELIVERY'), findsOneWidget);
  });
}
