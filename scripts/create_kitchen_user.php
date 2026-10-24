<?php
// Script to create or reset the 'cocina' user in GeeksPOS
$host = 'geekspos_db';
$user = 'geekscom_nicole';
$pass = 'OCvacuCpWWhkwe7pFKMsAa9';
$db   = 'geekscom_nicole';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die("Connect Error: " . $mysqli->connect_error . "\n");
}

$username = 'cocina';
$raw_pass = 'cocina123';
$email    = 'cocina@geekspos.com';
$name     = 'Personal de Cocina (KDS)';

// Check if user exists
$res = $mysqli->query("SELECT id FROM geopos_users WHERE username = '$username'");
if ($row = $res->fetch_assoc()) {
    $uid = $row['id'];
    $salt = md5($uid);
    $hashed = hash('sha256', $salt . $raw_pass);
    $mysqli->query("UPDATE geopos_users SET pass = '$hashed', email = '$email', banned = 0, roleid = 2 WHERE id = $uid");
    echo "Usuario 'cocina' actualizado con exito (ID: $uid).\n";
} else {
    $mysqli->query("INSERT INTO geopos_users (email, pass, username, banned, date_created, roleid, loc, lang) 
                    VALUES ('$email', 'temp', '$username', 0, NOW(), 2, 0, 'spanish')");
    $uid = $mysqli->insert_id;
    $salt = md5($uid);
    $hashed = hash('sha256', $salt . $raw_pass);
    $mysqli->query("UPDATE geopos_users SET pass = '$hashed' WHERE id = $uid");

    // Also insert employee record
    $mysqli->query("INSERT INTO geopos_employees (id, username, name, address, city, phone, dept, picture, sign, joindate) 
                    VALUES ($uid, '$username', '$name', 'Cocina Central', 'Asuncion', '0981687986', 1, 'example.png', 'sign.png', NOW()) 
                    ON DUPLICATE KEY UPDATE name = '$name'");
    echo "Usuario 'cocina' creado con exito (ID: $uid).\n";
}

echo "Credenciales:\nUsuario: $username\nPassword: $raw_pass\n";
$mysqli->close();
