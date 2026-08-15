<?php
// Script para buscar y actualizar deadline del partido
// Grupo B, 26/08 10:00, deadline 01/09

// Conexión directa a BD
$conn = new mysqli(
    'localhost',
    'baltc',
    'Baltc2020@',
    'baltc_db'
);

if($conn->connect_error) {
    die('❌ Error de conexión: ' . $conn->connect_error);
}

// Buscar el partido
$sql = "
    SELECT m.id, m.ronda, m.fecha, m.hora, m.deadline,
        (SELECT GROUP_CONCAT(p.name SEPARATOR ' / ') FROM reservations_partners rp JOIN partners p ON p.id = rp.partner_id WHERE rp.reservation_id = m.jugador1_id) as j1,
        (SELECT GROUP_CONCAT(p.name SEPARATOR ' / ') FROM reservations_partners rp JOIN partners p ON p.id = rp.partner_id WHERE rp.reservation_id = m.jugador2_id) as j2
    FROM matches m
    WHERE m.ronda = 'Grupo B'
    AND m.fecha = '2026-08-26'
    AND m.hora = '10:00'
    AND m.deadline = '2026-09-01'
    LIMIT 1
";

$result = $conn->query($sql);

if($result && $result->num_rows > 0) {
    $partido = $result->fetch_assoc();
    echo "✅ Partido encontrado:\n";
    echo "   ID: " . $partido['id'] . "\n";
    echo "   Ronda: " . $partido['ronda'] . "\n";
    echo "   Fecha/Hora: " . $partido['fecha'] . " " . $partido['hora'] . "\n";
    echo "   Deadline actual: " . $partido['deadline'] . "\n";
    echo "   J1: " . $partido['j1'] . "\n";
    echo "   J2: " . $partido['j2'] . "\n\n";

    // Actualizar deadline a 02/09
    $nuevo_deadline = '2026-09-02';
    $update_sql = "UPDATE matches SET deadline = ? WHERE id = ?";

    $stmt = $conn->prepare($update_sql);
    if($stmt) {
        $stmt->bind_param('si', $nuevo_deadline, $partido['id']);
        if($stmt->execute()) {
            echo "✅ Deadline actualizado a: " . $nuevo_deadline . "\n";
            echo "   Listo!\n";
        } else {
            echo "❌ Error al actualizar: " . $stmt->error . "\n";
        }
        $stmt->close();
    } else {
        echo "❌ Error en la query: " . $conn->error . "\n";
    }
} else {
    echo "❌ No se encontró el partido con esos datos\n";
    echo "   Buscando: Grupo B, 2026-08-26 10:00, deadline 2026-09-01\n";
}

$conn->close();
?>
