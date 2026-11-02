<?php 

include(dirname(__FILE__)."/configuration.php");
include(dirname(__FILE__)."/funciones.php");

$fecha = date('Y-m-d');

//echo $fecha;

// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Falló la conexión: " . $conn->connect_error);
        }

        $sql = "SELECT id FROM cajas_diarias WHERE id_usuario = '".$_POST['codigo_cliente']."' AND fecha = '".$fecha."'";

        //echo $sql;

        if ($conn->query($sql) === TRUE) 
        {
            //echo "New record created successfully";
        } else {
            //echo "Error: " . $sql . "<br>" . $conn->error;
        }

      $datos = mysqli_query($conn,$sql);

      $cantidad_resultado = $datos->num_rows;

      if($cantidad_resultado != 0)
      {
        echo 1;
      }
      else
      {
        echo 0;
      }


 $conn->close();


?>