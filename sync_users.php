<?php
// Script simple de sincronización sin CodeIgniter

$host = 'localhost';
$user = 'baltc_db';
$pass = 'loze85saBO';
$db = 'baltc_torneo';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Obtener todos los usuarios
$sql = "SELECT id, name, dni, email, gender FROM users";
$result = $conn->query($sql);

$added = 0;
$skipped = 0;
$errors = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Verificar si ya existe en partners
        $check_sql = "SELECT id FROM partners WHERE dni = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $row['dni']);
        $stmt->execute();
        $check_result = $stmt->get_result();

        if ($check_result->num_rows == 0) {
            // Insertar en partners
            $insert_sql = "INSERT INTO partners (name, dni, email, gender) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $row['name'], $row['dni'], $row['email'], $row['gender']);

            if ($insert_stmt->execute()) {
                $added++;
            } else {
                $errors[] = "Error al insertar: " . $row['name'] . " - " . $insert_stmt->error;
            }
            $insert_stmt->close();
        } else {
            $skipped++;
        }
        $stmt->close();
    }
}

$conn->close();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Sincronización</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Sincronización de Users → Partners</h2>
    <p class="success"><strong>Agregados:</strong> <?php echo $added; ?></p>
    <p><strong>Ya existían:</strong> <?php echo $skipped; ?></p>

    <?php if (!empty($errors)): ?>
        <p class="error"><strong>Errores:</strong></p>
        <ul>
            <?php foreach($errors as $err): ?>
                <li><?php echo $err; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a href="javascript:history.back()">Volver</a></p>
</body>
</html>
