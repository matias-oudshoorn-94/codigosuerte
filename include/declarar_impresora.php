<?php 

include("configuration.php");
include("funciones.php");

$calculo =  $_POST['cantidad_total'] - $_POST['cantidad_impresiones_hoy'];

$fecha = date('Y-m-d');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

mysqli_set_charset($conn, 'utf8');
// Check connection
if ($conn->connect_error) 
{
   die("Falló la conexión: " . $conn->connect_error);
}

$sql = "INSERT INTO detalles_impresoras (id_impresora, fecha, impresiones) VALUES ('".$_POST['codigo_impresora']."', '".$fecha."' ,'".$calculo."') ";

//echo $sql;

if ($conn->query($sql) === TRUE)
{
    //echo "New record created successfully";
} 
else 
{
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$sql2 = "UPDATE impresoras SET copias = (copias+'".$calculo."') WHERE codigo =  '".$_POST['codigo_impresora']."'";

//echo $sql;

if ($conn->query($sql2) === TRUE)
{
    //echo "New record created successfully";
} 
else 
{
    echo "Error: " . $sql2 . "<br>" . $conn->error;
}


//$result = mysqli_query($conn,$sql);


echo "<i class='ti-check' style='margin-right:10px'></i>La impresora código: <b>".$_POST['codigo_impresora']."</b> se actualizó con <b>".$_POST['cantidad_total']."</b> impresiones.<br><div style='border: 2px solid #5dc55d;padding: 5px;margin-top: 10px;color: #5dc55d;font-size: 15px;text-align: center;'>Se agregaron: ".$calculo." impresiones del día de hoy</div>";

?>