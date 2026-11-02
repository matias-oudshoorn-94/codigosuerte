<?php
//error_reporting(E_ALL); //Para que no salte el error 500 
//ini_set('display_errors', 'On');

include_once('OCA/oca.php');

$testoca = new Oca('20-32923598-0', '372747');

if($_POST['orden'] != '' || $_POST['envio'] != '')
{
	echo 'El pedido ya fué generado en OCA';
}
else
{

	$xmlRetiro = '<?xml version="1.0" encoding="iso-8859-1" standalone="yes"?>

	<ROWS>

	       <cabecera ver="2.0" nrocuenta="169457/000"/>

	       <retiro calle="MDQ - CHAMPAGNAT" nro="2371 " cp="7600" localidad="MAR DEL PLATA" provincia="BUENOS AIRES" contacto="0223-4774004" email="info@pdfcolor.com.ar" solicitante="Guillermo Roncati"

	             observaciones="" centrocosto="1"

	       />

	       <envios>

	             <envio idoperativa="372747" nroremito="'.$_POST['dni'].'">

	                    <destinatario apellido="'.$_POST['apellido'].'" nombre="'.$_POST['nombre'].'" calle="'.$_POST['direccion'].'" nro="'.$_POST['altura'].'" piso="'.$_POST['piso'].'" depto="'.$_POST['depto'].'" cp="'.$_POST['codpostal'].'" localidad="'.$_POST['localidad'].'" provincia="'.$_POST['provincia'].'"

	                           telefono="'.$_POST['telefono'].'" email="'.$_POST['correo'].'" idci="0" celular="'.$_POST['dni'].'"

	                           />

	                    <paquetes>

	                           <paquete alto="50" ancho="50" largo="50" peso="2" valor="500" cant="1" />

	                    </paquetes>

	             </envio>

	       </envios>

	</ROWS>';


	$result = $testoca->ingresoOR('impresospdfcolor@gmail.com', '32923598Aa', $xmlRetiro, true);


	if (!$result)
	{
		echo "No se pudo generar el seguimiento";
	}
	else
	{
	 	$orden_retiro = $result['detalleIngresos'][0]['OrdenRetiro'];
	 	$numero_envio = $result['detalleIngresos'][0]['NumeroEnvio'];

	 
	 	// Create connection

	 	$servername = "localhost";
		$username = "c1381514_pdf";
		$password = "60naMOtegu";
		$dbname = "c1381514_pdf";


	        $conn = new mysqli($servername, $username, $password, $dbname);

	        mysqli_set_charset($conn, 'utf8');
	        // Check connection
	        if ($conn->connect_error) {
	            die("Falló la conexión: " . $conn->connect_error);
	        }

	        $sql = "UPDATE pedidos_fuera_mdp SET ordenenvio = '".$orden_retiro."', numeroretiro = '".$numero_envio."' where id = '".$_POST['id']."' "; 

	        if ($conn->query($sql) === TRUE) {
	            //echo "New record created successfully";
	        } else {
	            echo "Error: " . $sql . "<br>" . $conn->error;
	        }

	}

}
?>