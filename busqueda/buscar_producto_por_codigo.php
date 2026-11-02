<?php
include("../include/configuration.php"); // usa la misma conexión que los demás archivos

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falló la conexión: " . $conn->connect_error);
}

mysqli_set_charset($conn, 'utf8');

$codigo = isset($_POST['codigo']) ? trim($_POST['codigo']) : '';

if ($codigo == '') {
    echo '';
    exit;
}

$sql = "SELECT id, nombre, precio_final
        FROM productos
        WHERE codigo = '$codigo'
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo '';
}

$conn->close();
?>