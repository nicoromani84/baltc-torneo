<?php
// Conexión a la base de datos (usa credenciales del config)
require_once('application/config/database.php');

$db_config = $db['default'];
$conn = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$categorias = array('2da Caballeros', '3era Caballeros', '2da Damas', '3era Damas');
$resultado_final = array();

foreach($categorias as $cat_name) {
    $gender = (strpos($cat_name, 'Caballeros') !== false) ? 'M' : 'F';

    $sql = "
        SELECT DISTINCT
            CASE WHEN m.jugador1_id = m.ganador_id THEN m.jugador2_id ELSE m.jugador1_id END as perdedor_id,
            GROUP_CONCAT(p.name SEPARATOR ' / ') as nombre
        FROM matches m
        JOIN category c ON c.id = m.category
        LEFT JOIN reservations_partners rp ON rp.reservation_id =
            CASE WHEN m.jugador1_id = m.ganador_id THEN m.jugador2_id ELSE m.jugador1_id END
        LEFT JOIN partners p ON p.id = rp.partner_id
        WHERE c.name = '$cat_name'
        AND m.gender = '$gender'
        AND m.ganador_id IS NOT NULL
        AND m.jugador2_id IS NOT NULL
        GROUP BY perdedor_id
        ORDER BY nombre ASC
    ";

    $result = $conn->query($sql);
    if ($result) {
        $perdedores = array();
        while($row = $result->fetch_assoc()) {
            $perdedores[] = $row['nombre'];
        }
        $resultado_final[$cat_name] = $perdedores;
    } else {
        $resultado_final[$cat_name] = array('error' => $conn->error);
    }
}

$conn->close();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($resultado_final, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
