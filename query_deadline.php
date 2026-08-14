<?php
// Configuración directa de DB
$db_config = array(
    'hostname' => 'localhost',
    'username' => 'baltc',
    'password' => 'Baltc2020@',
    'database' => 'baltc_db'
);

$conn = new mysqli(
    'localhost:3306',
    $db_config['username'],
    $db_config['password'],
    $db_config['database'],
    3306
);

if($conn->connect_error) {
    die('Error: ' . $conn->connect_error);
}

$query = "
SELECT
    m.id as partido_id,
    m.ronda,
    c.name as categoria,
    GROUP_CONCAT(DISTINCT CONCAT(p1.name, ' (', p1.email, ')') SEPARATOR ' / ') as pareja1,
    GROUP_CONCAT(DISTINCT CONCAT(p2.name, ' (', p2.email, ')') SEPARATOR ' / ') as pareja2,
    m.deadline
FROM matches m
JOIN category c ON c.id = m.category
JOIN reservations_partners rp1 ON rp1.reservation_id = m.jugador1_id
JOIN partners p1 ON p1.id = rp1.partner_id
JOIN reservations_partners rp2 ON rp2.reservation_id = m.jugador2_id
JOIN partners p2 ON p2.id = rp2.partner_id
WHERE m.deadline = '2026-08-18'
AND m.ganador_id IS NULL
AND m.fecha IS NULL
GROUP BY m.id, m.ronda, c.name, m.deadline
ORDER BY c.name ASC, m.ronda ASC
";

$result = $conn->query($query);

if($result === false) {
    die('Query error: ' . $conn->error);
}

echo "=== RECORDATORIOS DEADLINE 18/08/2026 ===\n\n";
echo "Total de partidos: " . $result->num_rows . "\n\n";

while($row = $result->fetch_assoc()) {
    echo "Partido ID: " . $row['partido_id'] . "\n";
    echo "Categoría: " . $row['categoria'] . " - " . $row['ronda'] . "\n";
    echo "Pareja 1: " . $row['pareja1'] . "\n";
    echo "Pareja 2: " . $row['pareja2'] . "\n";
    echo "Deadline: " . $row['deadline'] . "\n";
    echo "---------\n\n";
}

$conn->close();
?>
