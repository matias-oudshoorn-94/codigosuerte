<?php 

include(dirname(__FILE__)."/configuration.php");
include(dirname(__FILE__)."/funciones.php");


$codigo = $_POST['impresora']

//echo $fecha;

// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Falló la conexión: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM detalles_impresoras WHERE id_impresora = '".$codigo."'";

        echo $sql;


        if ($conn->query($sql) === TRUE) {
            //echo "New record created successfully";
        } else {
            //echo "Error: " . $sql . "<br>" . $conn->error;
        }

      $datos = mysqli_query($conn,$sql);
      
      while ($fila = mysqli_fetch_array($datos))
      {
        echo 'Fecha: '.$fila['fecha'].'<br>';
        echo 'Impresiones agregadas: '.$fila['impresiones'].'<br>';
        
      }


 $conn->close();


?>