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

        $sql = "SELECT idpedido FROM pedidos WHERE cod_clie = '".$_POST['codigo_cliente']."' AND fecha = '".$fecha."'";

        //echo $sql;


        if ($conn->query($sql) === TRUE) {
            //echo "New record created successfully";
        } else {
            //echo "Error: " . $sql . "<br>" . $conn->error;
        }

      $datos = mysqli_query($conn,$sql);

      $cantidad_impresiones = 0;
      
      while ($fila = mysqli_fetch_array($datos))
      {
      	
      	$sql2 = "SELECT SUM(cantidad) as impresiones FROM `pedidosdetalles` where idpedido = ".$fila['idpedido']." and (id_articu = 7 or id_articu = 8)";

      	if ($conn->query($sql2) === TRUE) {
            //echo "New record created successfully";
        } else {
            //echo "Error: " . $sql . "<br>" . $conn->error;
        }

      	$impresiones = mysqli_query($conn,$sql2);
      	$fila2 = mysqli_fetch_array($impresiones);
      	$cantidad_impresiones = $cantidad_impresiones + $fila2['impresiones'];
        
      }


 $conn->close();

 echo $cantidad_impresiones;

?>