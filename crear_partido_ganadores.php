<?php
// Script para crear partido de Cuartos de Final en 3era Caballeros
// Ganadores: Frenkel/Lupis vs Hermida/Granillo

$conn = new mysqli('localhost', 'baltc', 'Baltc2020@', 'baltc_db');
if($conn->connect_error) {
    die('❌ Error de conexión: ' . $conn->connect_error);
}

// Buscar Frenkel/Lupis
$sql1 = "
    SELECT DISTINCT r.id, GROUP_CONCAT(p.name SEPARATOR ' / ') as nombres
    FROM reservations r
    JOIN reservations_partners rp ON r.id = rp.reservation_id
    JOIN partners p ON p.id = rp.partner_id
    WHERE r.category_id = 3
    AND r.reservation_type = 'doubles'
    GROUP BY r.id
    HAVING (nombres LIKE '%frenkel%' AND nombres LIKE '%lupis%')
    OR (nombres LIKE '%lupis%' AND nombres LIKE '%frenkel%')
    LIMIT 1
";

$result1 = $conn->query($sql1);
if($result1 && $result1->num_rows > 0) {
    $pareja1 = $result1->fetch_assoc();
    echo "✅ Pareja 1 encontrada: ID=" . $pareja1['id'] . " - " . $pareja1['nombres'] . "\n";
} else {
    echo "❌ No se encontró Frenkel/Lupis en 3era Caballeros\n";
    $conn->close();
    exit;
}

// Buscar Hermida/Granillo
$sql2 = "
    SELECT DISTINCT r.id, GROUP_CONCAT(p.name SEPARATOR ' / ') as nombres
    FROM reservations r
    JOIN reservations_partners rp ON r.id = rp.reservation_id
    JOIN partners p ON p.id = rp.partner_id
    WHERE r.category_id = 3
    AND r.reservation_type = 'doubles'
    GROUP BY r.id
    HAVING (nombres LIKE '%hermida%' AND nombres LIKE '%granillo%')
    OR (nombres LIKE '%granillo%' AND nombres LIKE '%hermida%')
    LIMIT 1
";

$result2 = $conn->query($sql2);
if($result2 && $result2->num_rows > 0) {
    $pareja2 = $result2->fetch_assoc();
    echo "✅ Pareja 2 encontrada: ID=" . $pareja2['id'] . " - " . $pareja2['nombres'] . "\n";
} else {
    echo "❌ No se encontró Hermida/Granillo en 3era Caballeros\n";
    $conn->close();
    exit;
}

// Crear el partido
$insert_sql = "
    INSERT INTO matches (category_id, jugador1_id, jugador2_id, ronda)
    VALUES (3, ?, ?, 'Cuartos de Final')
";

$stmt = $conn->prepare($insert_sql);
if($stmt) {
    $stmt->bind_param('ii', $pareja1['id'], $pareja2['id']);
    if($stmt->execute()) {
        $partido_id = $stmt->insert_id;
        echo "\n✅ Partido creado exitosamente!\n";
        echo "   ID Partido: " . $partido_id . "\n";
        echo "   Ronda: Cuartos de Final\n";
        echo "   Categoría: 3era Caballeros (id=3)\n";
        echo "   Pareja 1 (ID=" . $pareja1['id'] . "): " . $pareja1['nombres'] . "\n";
        echo "   Pareja 2 (ID=" . $pareja2['id'] . "): " . $pareja2['nombres'] . "\n";
    } else {
        echo "❌ Error al crear el partido: " . $stmt->error . "\n";
    }
    $stmt->close();
} else {
    echo "❌ Error en la query: " . $conn->error . "\n";
}

$conn->close();
?>
