<?php
$pdo = new PDO("mysql:host=geekspos_db;port=3306;dbname=geekscom_nicole", "geekscom_nicole", "OCvacuCpWWhkwe7pFKMsAa9");
$hash = password_hash('delivery123', PASSWORD_BCRYPT);
$stmt = $pdo->prepare("UPDATE geopos_deliveries SET password = ?");
$stmt->execute([$hash]);
echo "Successfully updated " . $stmt->rowCount() . " delivery rows with bcrypt password hash.\n";
