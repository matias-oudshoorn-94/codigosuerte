<?php

include(dirname(__FILE__)."/configuration.php");
include(dirname(__FILE__)."/funciones.php");

$codigo = $_POST['impresora'];

// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Falló la conexión: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM detalles_impresoras WHERE id_impresora = '".$codigo."'";

        
        if ($conn->query($sql) === TRUE) {
            //echo "New record created successfully";
        } else {
            //echo "Error: " . $sql . "<br>" . $conn->error;
        }

      $datos = mysqli_query($conn,$sql);

      echo '<table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">Fecha</th>
                  <th scope="col">Impresiones agregadas</th>
                </tr>
              </thead>';
      
      while ($fila = mysqli_fetch_array($datos))
      {
        echo '
              <tbody>
                <tr>
                  <td>'.$fila['fecha'].'</td>
                  <td>'.$fila['impresiones'].'</td>
                </tr>
              </tbody>';
      }

      echo '</table>';


 $conn->close();

?> 