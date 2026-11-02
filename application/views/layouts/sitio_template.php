
<?php

$base_url = base_url()."application/views/layouts";

//echo($this->session->userdata("user_data")[0]->caja_cerrada);

$servername = "localhost";
$username = "u245081402_codigosuerte";
$password = "hlD@Dh5No^5O";
$dbname = "u245081402_codigosuerte";

$conn = new mysqli($servername, $username, $password, $dbname);

mysqli_set_charset($conn, 'utf8');
// Check connection
if ($conn->connect_error)
{
    die("Falló la conexión: " . $conn->connect_error);
}

$sql456 = "SELECT caja_cerrada FROM usuarios WHERE idusuario = '".$this->session->userdata("user_data")[0]->idusuario."' ";


if ($conn->query($sql) === TRUE)
{
    //echo "New record created successfully";
}
else
{
    //echo "Error: " . $sql5 . "<br>" . $conn->error;
}

$resultado_caja = mysqli_query($conn,$sql456);


while ($fila = mysqli_fetch_array($resultado_caja))
{
    $caja_cerrada = $fila['caja_cerrada'];
}

//echo $caja_cerrada;



?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo $template['title'];?></title>
        <meta name="description" content="<?php echo $meta['description'];?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $base_url ?>/css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
        <link href="<?php echo $base_url ?>/css/themify-icons.css" rel="stylesheet" type="text/css" media="all" />
        <link href="<?php echo $base_url ?>/css/flexslider.css" rel="stylesheet" type="text/css" media="all" />
        <link href="<?php echo $base_url ?>/css/lightbox.min.css" rel="stylesheet" type="text/css" media="all" />
        <link href="<?php echo $base_url ?>/css/ytplayer.css" rel="stylesheet" type="text/css" media="all" />
        <link href="<?php echo $base_url ?>/css/theme.css" rel="stylesheet" type="text/css" media="all" />
        <link href="<?php echo $base_url ?>/css/custom.css" rel="stylesheet" type="text/css" media="all" />
        <link type="text/css" href="<?php echo $base_url ?>/css/uploader.css" rel="stylesheet" />
        <link  rel="icon"   href="<?php echo $base_url ?>/img/favicon.png" type="image/png" />



        <!-- OPEN GRAPH FACEBOOK -->

        <meta property="og:url" content="http://pdfcolor.com.ar" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="Imprenta PDF Color Mar del Plata" />
		<meta property="og:description" content="Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online." />
		<meta property="og:image" content="http://pdfcolor.com.ar/application/views/layouts/img/logo2.png" />


		<!-- CARD TWITTER -->

		<meta name="twitter:card" content="summary_large_image">
		<meta name="twitter:site" content="@pdfcolor">
		<meta name="twitter:title" content="Imprenta PDF Color Mar del Plata">
		<meta name="twitter:description" content="Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.">
		<meta name="twitter:image:src" content="http://pdfcolor.com.ar/application/views/layouts/img/logo2.png">


        <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">




        <link href='http://fonts.googleapis.com/css?family=Lato:300,400%7CRaleway:100,400,300,500,600,700%7COpen+Sans:400,500,600' rel='stylesheet' type='text/css'>

            <!-- Whatsapp logo flotante

            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <a href="https://api.whatsapp.com/send?phone=5492235395959" class="float" target="_blank">
        <i class="fa fa-whatsapp my-float"></i>
        </a>

        -->


    </head>


    <script>



    function buscar()
    {
      if(document.getElementById('texto_buscar').value == '')
      {
        alert("Por favor, ingrese el texto a buscar.");
      }
      else
      {
        //alert('<?php echo base_url()?>busqueda/'+document.getElementById('texto_buscar').value;);
        window.location='<?php echo base_url()?>busqueda/'+document.getElementById('texto_buscar').value;
      }
    }

    function buscar2()
    {
      if(document.getElementById('texto_buscar2').value == '')
      {
        alert("Por favor, ingrese el texto a buscar.");
      }
      else
      {
        //alert('<?php echo base_url()?>busqueda/'+document.getElementById('texto_buscar').value;);
        window.location='<?php echo base_url()?>busqueda/'+document.getElementById('texto_buscar2').value;
      }
    }

    function runScript(e)

    {

        if (event.which == 13 || event.keyCode == 13) {

            buscar_prod();

            return false;

        }

        return true;

    }



function agregarArt(idArticulo, cantidad)
{
	//alert (cantidad);

    if (cantidad == "" || cantidad < 1)
    {
        alert("La cantidad es incorrecta.");
        return false;
    }
    $.get("<?php echo base_url('sitio/getData') ?>", { idArt: idArticulo, quantity: cantidad }, function(data)
    {

        var json = JSON.parse(data);
        var html = "";
        var cantidad = 0;
        var total = 0;
        for (post in json)
        {
            //alert(json[post].quantity);
            //alert(json[post].precio);

            cantidad = cantidad+json[post].quantity*1;
            total += json[post].precio_final*json[post].quantity;

            html += /*'<p>'+json[post].nombre+'<br>'+json[post].precio+'</p>';*/

                    '<tr><td class="text-left"><a href="">'+json[post].nombre+'</a></td><td class="text-right">x '+json[post].quantity+'</td><td class="text-right">$'+json[post].precio_final+'</td><td class="text-right">$'+json[post].precio_final*cantidad+'</td><td class="text-right"><a onClick="javascript: quitarArt('+json[post].product+', 0, 0)" style="cursor: pointer;font-size: 20px;"><i class="ti-trash" style="vertical-align: middle;"></i></a><input type="number" name="quantity" id="'+json[post].product+'" value="'+json[post].quantity+'" size="1" class="form-control" style="display: initial;border: 1px solid #b9b9b9;width: 59px;height: 30px;padding-left: 10px;margin-left: 10px;font-weight: 500;margin-bottom: 0px;border-radius: 0;margin-right: 10px"/><a onClick="javascript: modificarArt('+json[post].product+', 0)" style="cursor: pointer;font-size: 20px;"><i class="ti-reload" style="vertical-align: middle;"></i></a></td></tr>';


        }



        total = total.toFixed(2);
        total = new Intl.NumberFormat('de-DE',{minimumFractionDigits: 2, maximunFractionDigits: 2}).format(total);
        $("#resultado").html('<table class="table table-hover"><thead><tr><th scope="col">Nombre</th><th scope="col" style="text-align: right;">Cantidad</th><th scope="col" style="text-align: right;">Precio</th><th scope="col" style="text-align: right;">Total</th><th scope="col" style="text-align: right;">Acciones</th></tr></thead><tbody>'+html+'</tbody></table>');
        $("#cart-total").html(cantidad+' item(s) -$'+total);
        $("#cart-total-bottom").html('<b>TOTAL:</b> $'+total);
        $("#cart-success"+idArticulo).html('<strong class="label label-success" id="agregado">¡AGREGADO!</strong>');
        $("#cart-success"+idArticulo).fadeIn( "slow", function() {
            // Animation complete.
        });
        /*$("#cart-success"+idArticulo).fadeOut( "slow", function() {
            // Animation complete.
        });*/

        if (total > 0)
        {
            $("#cart-buttons").html('<p class="text-right btn-block1"><a style="color:#FFF;background-color:#C5202B;" href="<?php echo base_url() ?>carro">Ver Carro</a>');
        }

    });
}//);


function modificarArt(idArticulo) {
    var input = document.getElementById('prod-' + idArticulo);
    if (!input) {
        alert("No se encontró el input de cantidad");
        return;
    }

    var cantidad = parseInt(input.value);
    if (!cantidad || cantidad < 1) {
        alert("La cantidad es incorrecta.");
        return;
    }

    $.get("<?php echo base_url('sitio/modificarData') ?>", { idArt: idArticulo, quantity: cantidad }, function(data) {
    var json = JSON.parse(data);
    if (!json || json.length == 0) {
        alert("No se pudo actualizar el carrito");
        return;
    }
        var json = JSON.parse(data);
        var html = "";
        var totalCantidad = 0;
        var totalPrecio = 0;

        json.forEach(function(post) {
            totalCantidad += post.quantity * 1;
            totalPrecio += post.precio_final * post.quantity;

            html += '<tr>' +
                        '<td class="text-left">' + post.nombre + '</td>' +
                        '<td class="text-right">x ' + post.quantity + '</td>' +
                        '<td class="text-right">$' + post.precio_final.toFixed(2) + '</td>' +
                        '<td class="text-right">$' + (post.precio_final * post.quantity).toFixed(2) + '</td>' +
                        '<td class="text-right">' +
                            '<a onclick="quitarArt(' + post.product + ', 0, 0)" style="cursor:pointer;font-size:20px;"><i class="ti-trash"></i></a>' +
                            '<input type="number" id="' + post.product + '" value="' + post.quantity + '" min="1" class="form-control" style="display:inline-block;width:59px;height:30px;margin:0 10px;" />' +
                            '<a onclick="modificarArt(' + post.product + ')" style="cursor:pointer;font-size:20px;"><i class="ti-reload"></i></a>' +
                        '</td>' +
                    '</tr>';
        });

        totalPrecio = new Intl.NumberFormat('de-DE',{minimumFractionDigits:2}).format(totalPrecio);

        $("#resultado").html('<table class="table table-hover"><thead><tr><th>Nombre</th><th style="text-align:right;">Cantidad</th><th style="text-align:right;">Precio</th><th style="text-align:right;">Total</th><th style="text-align:right;">Acciones</th></tr></thead><tbody>' + html + '</tbody></table>');

        $("#cart-total").html(totalCantidad + ' item(s) - $' + totalPrecio);
        $("#cart-total-bottom").html('<b>TOTAL:</b> $' + totalPrecio);

        if (totalCantidad > 0) {
            $("#cart-buttons").html('<p class="text-right btn-block1"><a style="color:#FFF;background-color:#C5202B;" href="<?php echo base_url() ?>carro">Ver Carro</a></p>');
        } else {
            $("#cart-buttons").html('');
        }
    });
}


function quitarArt(idArticulo, reloadPage, deleteAll)
{
    $.get("<?php echo base_url('sitio/quitarData') ?>", { idArt: idArticulo, delAll: deleteAll }, function(data)
    {
        var json = JSON.parse(data);
        var html = "";
        var cantidad = 0;
        var total = 0;
        for (post in json)
        {
            //alert(json[post].quantity);

            cantidad = cantidad+json[post].quantity*1;
            total += json[post].precio_final*json[post].quantity;

            html +=
                    '<tr><td class="text-left"><a href="">'+json[post].nombre+'</a></td><td class="text-right">x '+json[post].quantity+'</td><td class="text-right">$'+json[post].precio_final+'</td><td class="text-right">$'+json[post].precio_final*cantidad+'</td><td class="text-right"><a onClick="javascript: quitarArt('+json[post].product+', 0, 0)" style="cursor: pointer;font-size: 20px;"><i class="ti-trash" style="vertical-align: middle;"></i></a><input type="number" name="quantity" id="'+json[post].product+'" value="'+json[post].quantity+'" size="1" class="form-control" style="display: initial;border: 1px solid #b9b9b9;width: 59px;height: 30px;padding-left: 10px;margin-left: 10px;font-weight: 500;margin-bottom: 0px;border-radius: 0;margin-right: 10px"/><a onClick="javascript: modificarArt('+json[post].product+', 0)" style="cursor: pointer;font-size: 20px;"><i class="ti-reload" style="vertical-align: middle;"></i></a></td></tr>';

        }

        total = total.toFixed(2);
        total = new Intl.NumberFormat('de-DE',{minimumFractionDigits: 2, maximunFractionDigits: 2}).format(total);
        $("#resultado").html('<table class="table table-hover"><thead><tr><th scope="col">Nombre</th><th scope="col" style="text-align: right;">Cantidad</th><th scope="col" style="text-align: right;">Precio</th><th scope="col" style="text-align: right;">Total</th><th scope="col" style="text-align: right;">Acciones</th></tr></thead><tbody>'+html+'</tbody></table>');
        $("#cart-total").html(cantidad+' item(s) -$'+total);
        $("#cart-total-bottom").html('<b>TOTAL:</b> $'+total);

        if (total == 0)
        {
            $("#cart-buttons").html('');
        }

        if (reloadPage) window.location.reload();

    });
}//);







    </script>



<style>

.float{
    position:fixed;
    width:60px;
    height:60px;
    bottom:40px;
    right:40px;
    background-color:#25d366;
    color:#FFF;
    border-radius:50px;
    text-align:center;
  font-size:30px;
    box-shadow: 2px 2px 3px #999;
  z-index:100;
}

.my-float{
    margin-top:16px;
}

.link-list a:after
{
    display: none !important
}

.nav-bar ul li.active > a
{
	color: #47b475 !important;
}

</style>

    <body class="scroll-assist">
        <?php
        // ---------- Header ----------
        $hd_usuario = isset($this->session->userdata("user_data")[0]->idusuario) ? $this->session->userdata("user_data")[0] : null;
        $hd_pagina = $this->uri->segment(1);

        if ($hd_usuario)
        {
            $hd_id = (int)$hd_usuario->idusuario;
            $hd_datos = $this->db->select('usuario, nombre, apellido, caja_cerrada')->where('idusuario', $hd_id)->get('usuarios')->row();
            if (!$hd_datos) $hd_datos = $hd_usuario;

            $hd_hoy = $this->db->select('COUNT(*) AS ventas, COALESCE(SUM(total), 0) AS total', FALSE)
                ->where('cod_clie', $hd_id)->where('fecha', date('Y-m-d'))->get('pedidos')->row();

            $hd_nombre = trim($hd_datos->nombre.' '.$hd_datos->apellido, " .");
            $hd_palabras = preg_split('/\s+/', trim(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $hd_nombre)), -1, PREG_SPLIT_NO_EMPTY);
            $hd_iniciales = '';
            foreach (array_slice($hd_palabras, 0, 2) as $hd_palabra) $hd_iniciales .= substr($hd_palabra, 0, 1);
            if ($hd_iniciales === '') $hd_iniciales = substr($hd_datos->usuario, 0, 2);
            $hd_iniciales = strtoupper($hd_iniciales);
            $hd_caja_cerrada = isset($hd_datos->caja_cerrada) && $hd_datos->caja_cerrada == 1;

            $hd_links = array(
                array('url' => 'nueva_venta', 'texto' => 'Nueva venta', 'icono' => 'ti-plus',    'activo' => array('nueva_venta')),
                array('url' => 'pedidos',     'texto' => 'Ventas',      'icono' => 'ti-receipt', 'activo' => array('pedidos', 'pedido_detalles')),
                array('url' => 'cerrar_caja', 'texto' => 'Cerrar caja', 'icono' => 'ti-lock',    'activo' => array('cerrar_caja', 'caja_cerrada')),
            );
        }
        else
        {
            $hd_links = array(
                array('url' => '', 'texto' => 'Inicio', 'icono' => 'ti-home', 'activo' => array('', 'home')),
            );
        }
        ?>

        <style type="text/css">
        .hd {
            position: sticky; top: 0; z-index: 1000;
            background: #1f2329; color: #e7eaee; border-bottom: 1px solid rgba(255,255,255,.06);
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
        .hd * { box-sizing: border-box; }
        .hd a { text-decoration: none; }
        .hd button, .hd ul, .hd p { margin: 0; }
        .hd button { font-family: inherit; letter-spacing: 0; text-transform: none; line-height: 1.2; }
        .hd-barra { display: flex; align-items: center; gap: 24px; height: 64px; max-width: 1170px; margin: 0 auto; padding: 0 16px; }
        .hd-logo img { height: 40px; width: auto; display: block; }

        .hd-menu { display: flex; align-items: center; gap: 4px; list-style: none; padding: 0; }
        .hd-menu a {
            display: inline-flex; align-items: center; gap: 8px; height: 38px; padding: 0 14px; border-radius: 10px;
            color: #b9c0c8; font-size: 14px; font-weight: 600; transition: background .15s, color .15s;
        }
        .hd-menu a:hover { color: #fff; background: rgba(255,255,255,.06); }
        .hd-menu a.activo { color: #fff; background: rgba(71,180,117,.18); }
        .hd-menu a.activo i { color: #6fd39a; }
        .hd-menu i { font-size: 14px; }

        .hd-derecha { margin-left: auto; display: flex; align-items: center; gap: 12px; }
        .hd-hoy {
            display: inline-flex; align-items: center; gap: 10px; height: 38px; padding: 0 14px; border-radius: 10px;
            background: rgba(255,255,255,.05); color: #b9c0c8; font-size: 13px; white-space: nowrap;
        }
        .hd-hoy:hover { background: rgba(255,255,255,.09); color: #e7eaee; }
        .hd-hoy b { color: #fff; font-weight: 700; font-variant-numeric: tabular-nums; }
        .hd-punto { width: 8px; height: 8px; border-radius: 50%; background: #47b475; box-shadow: 0 0 0 3px rgba(71,180,117,.2); flex: none; }
        .hd-punto.cerrada { background: #e0a43a; box-shadow: 0 0 0 3px rgba(224,164,58,.2); }

        .hd-cuenta { position: relative; }
        .hd-cuenta-boton {
            display: flex; align-items: center; gap: 10px; height: 42px; padding: 0 8px 0 4px; border: 0; border-radius: 999px;
            background: transparent; color: #e7eaee; cursor: pointer;
        }
        .hd-cuenta-boton:hover, .hd-cuenta.abierta .hd-cuenta-boton { background: rgba(255,255,255,.06); }
        .hd .hd-avatar { width: 34px; height: 34px; border-radius: 50%; background: #47b475; color: #fff; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex: none; }
        .hd-cuenta-texto { text-align: left; line-height: 1.2; }
        .hd-cuenta-nombre { font-size: 13px; font-weight: 600; color: #fff; max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .hd-cuenta-sub { font-size: 11px; color: #9aa3ad; }
        .hd-cuenta-boton .ti-angle-down { font-size: 10px; color: #9aa3ad; }

        .hd-desplegable {
            position: absolute; right: 0; top: calc(100% + 8px); width: 260px; background: #fff; color: #1f2933; border-radius: 12px;
            box-shadow: 0 12px 32px rgba(16,24,40,.18); border: 1px solid #e3e8ee; overflow: hidden; display: none;
        }
        .hd-cuenta.abierta .hd-desplegable { display: block; }
        .hd-desplegable-cabecera { padding: 14px 16px; border-bottom: 1px solid #eef1f4; display: flex; gap: 12px; align-items: center; }
        .hd-desplegable-cabecera b { display: block; font-size: 14px; }
        .hd-desplegable-cabecera span { font-size: 12px; color: #6b7785; }
        .hd-desplegable-estado { padding: 10px 16px; font-size: 12px; color: #6b7785; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #eef1f4; background: #fafbfc; }
        .hd-desplegable a { display: flex; align-items: center; gap: 10px; padding: 11px 16px; font-size: 14px; color: #1f2933; font-weight: 500; }
        .hd-desplegable a:hover { background: #f5f7f9; color: #1f2933; }
        .hd-desplegable a i { color: #6b7785; width: 16px; }
        .hd-desplegable a.salir { color: #d64545; border-top: 1px solid #eef1f4; }
        .hd-desplegable a.salir i { color: #d64545; }

        .hd-ingresar { display: inline-flex; align-items: center; gap: 8px; height: 38px; padding: 0 16px; border-radius: 10px; background: #47b475; color: #fff !important; font-size: 14px; font-weight: 600; }
        .hd-ingresar:hover { background: #3a9862; }

        .hd-hamburguesa { display: none; width: 42px; height: 42px; border: 0; border-radius: 10px; background: rgba(255,255,255,.06); color: #fff; font-size: 18px; cursor: pointer; }
        .hd-movil { display: none; }

        @media (max-width: 991px) {
            .hd-barra { gap: 12px; }
            .hd-menu, .hd-hoy, .hd-cuenta, .hd-ingresar { display: none; }
            .hd-hamburguesa { display: inline-flex; align-items: center; justify-content: center; }
            .hd-movil { border-top: 1px solid rgba(255,255,255,.06); padding: 12px 16px 16px; }
            .hd.abierto .hd-movil { display: block; }
            .hd-movil-cuenta { display: flex; align-items: center; gap: 12px; padding: 4px 0 14px; }
            .hd-movil-cuenta b { display: block; color: #fff; font-size: 14px; }
            .hd-movil-cuenta span { color: #9aa3ad; font-size: 12px; }
            .hd-movil-hoy { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; background: rgba(255,255,255,.05); font-size: 13px; color: #b9c0c8; margin-bottom: 10px; }
            .hd-movil-hoy b { color: #fff; }
            .hd-movil a { display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 10px; color: #d4d9de; font-size: 15px; font-weight: 600; }
            .hd-movil a:hover, .hd-movil a.activo { background: rgba(255,255,255,.06); color: #fff; }
            .hd-movil a.activo i { color: #6fd39a; }
            .hd-movil a.salir { color: #f08b8b; margin-top: 6px; border-top: 1px solid rgba(255,255,255,.06); border-radius: 0 0 10px 10px; }
        }
        @media print { .hd { display: none !important; } }
        </style>

        <header class="hd" id="hd">
            <div class="hd-barra">
                <a class="hd-logo" href="<?php echo base_url() ?>"><img src="<?php echo $base_url ?>/img/logo2.png" alt="PDF Color"></a>

                <ul class="hd-menu">
                    <?php foreach ($hd_links as $hd_link) { ?>
                        <li><a href="<?php echo base_url().$hd_link['url'] ?>" class="<?php echo in_array((string)$hd_pagina, $hd_link['activo'], true) ? 'activo' : '' ?>"><i class="<?php echo $hd_link['icono'] ?>"></i><?php echo $hd_link['texto'] ?></a></li>
                    <?php } ?>
                </ul>

                <div class="hd-derecha">
                    <?php if ($hd_usuario) { ?>
                        <a class="hd-hoy" href="<?php echo base_url() ?>pedidos" title="<?php echo $hd_caja_cerrada ? 'Caja cerrada hoy' : 'Caja abierta' ?>">
                            <span class="hd-punto<?php echo $hd_caja_cerrada ? ' cerrada' : '' ?>"></span>
                            Hoy <b>$ <?php echo number_format($hd_hoy->total, 0, ',', '.') ?></b> · <?php echo (int)$hd_hoy->ventas ?> venta<?php echo $hd_hoy->ventas == 1 ? '' : 's' ?>
                        </a>

                        <div class="hd-cuenta" id="hd-cuenta">
                            <button type="button" class="hd-cuenta-boton" aria-haspopup="true" aria-expanded="false">
                                <span class="hd-avatar"><?php echo htmlspecialchars($hd_iniciales) ?></span>
                                <span class="hd-cuenta-texto">
                                    <span class="hd-cuenta-nombre" style="display: block;"><?php echo htmlspecialchars($hd_nombre !== '' ? $hd_nombre : $hd_datos->usuario) ?></span>
                                    <span class="hd-cuenta-sub">Sucursal <?php echo htmlspecialchars($hd_datos->usuario) ?></span>
                                </span>
                                <i class="ti-angle-down"></i>
                            </button>
                            <div class="hd-desplegable" role="menu">
                                <div class="hd-desplegable-cabecera">
                                    <span class="hd-avatar"><?php echo htmlspecialchars($hd_iniciales) ?></span>
                                    <div>
                                        <b><?php echo htmlspecialchars($hd_nombre !== '' ? $hd_nombre : $hd_datos->usuario) ?></b>
                                        <span>Sucursal <?php echo htmlspecialchars($hd_datos->usuario) ?></span>
                                    </div>
                                </div>
                                <div class="hd-desplegable-estado">
                                    <span class="hd-punto<?php echo $hd_caja_cerrada ? ' cerrada' : '' ?>"></span>
                                    <?php echo $hd_caja_cerrada ? 'La caja de hoy ya está cerrada' : 'Caja abierta' ?>
                                </div>
                                <a href="<?php echo base_url() ?>perfil" role="menuitem"><i class="ti-user"></i>Mis datos</a>
                                <a href="<?php echo base_url() ?>pedidos" role="menuitem"><i class="ti-receipt"></i>Ventas de hoy</a>
                                <a href="<?php echo base_url() ?>logout" class="salir" role="menuitem"><i class="ti-power-off"></i>Cerrar sesión</a>
                            </div>
                        </div>
                    <?php } else { ?>
                        <a class="hd-ingresar" href="<?php echo base_url() ?>login"><i class="ti-user"></i>Ingresar</a>
                    <?php } ?>

                    <button type="button" class="hd-hamburguesa" id="hd-hamburguesa" aria-label="Menú" aria-expanded="false"><i class="ti-menu"></i></button>
                </div>
            </div>

            <!-- Menú en celular -->
            <div class="hd-movil">
                <?php if ($hd_usuario) { ?>
                    <div class="hd-movil-cuenta">
                        <span class="hd-avatar"><?php echo htmlspecialchars($hd_iniciales) ?></span>
                        <div>
                            <b><?php echo htmlspecialchars($hd_nombre !== '' ? $hd_nombre : $hd_datos->usuario) ?></b>
                            <span>Sucursal <?php echo htmlspecialchars($hd_datos->usuario) ?></span>
                        </div>
                    </div>
                    <div class="hd-movil-hoy">
                        <span class="hd-punto<?php echo $hd_caja_cerrada ? ' cerrada' : '' ?>"></span>
                        <span>Hoy <b>$ <?php echo number_format($hd_hoy->total, 0, ',', '.') ?></b> · <?php echo (int)$hd_hoy->ventas ?> venta<?php echo $hd_hoy->ventas == 1 ? '' : 's' ?> · <?php echo $hd_caja_cerrada ? 'Caja cerrada' : 'Caja abierta' ?></span>
                    </div>
                <?php } ?>
                <?php foreach ($hd_links as $hd_link) { ?>
                    <a href="<?php echo base_url().$hd_link['url'] ?>" class="<?php echo in_array((string)$hd_pagina, $hd_link['activo'], true) ? 'activo' : '' ?>"><i class="<?php echo $hd_link['icono'] ?>"></i><?php echo $hd_link['texto'] ?></a>
                <?php } ?>
                <?php if ($hd_usuario) { ?>
                    <a href="<?php echo base_url() ?>perfil" class="<?php echo $hd_pagina == 'perfil' ? 'activo' : '' ?>"><i class="ti-user"></i>Mis datos</a>
                    <a href="<?php echo base_url() ?>logout" class="salir"><i class="ti-power-off"></i>Cerrar sesión</a>
                <?php } else { ?>
                    <a href="<?php echo base_url() ?>login"><i class="ti-user"></i>Ingresar</a>
                <?php } ?>
            </div>
        </header>

        <script type="text/javascript">
        (function () {
            var header = document.getElementById('hd');
            var hamburguesa = document.getElementById('hd-hamburguesa');
            hamburguesa.addEventListener('click', function () {
                var abierto = header.classList.toggle('abierto');
                hamburguesa.setAttribute('aria-expanded', abierto);
                hamburguesa.innerHTML = abierto ? '<i class="ti-close"></i>' : '<i class="ti-menu"></i>';
            });

            var cuenta = document.getElementById('hd-cuenta');
            if (!cuenta) return;
            var boton = cuenta.querySelector('.hd-cuenta-boton');
            boton.addEventListener('click', function (e) {
                e.stopPropagation();
                var abierta = cuenta.classList.toggle('abierta');
                boton.setAttribute('aria-expanded', abierta);
            });
            document.addEventListener('click', function (e) {
                if (!cuenta.contains(e.target)) { cuenta.classList.remove('abierta'); boton.setAttribute('aria-expanded', false); }
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') { cuenta.classList.remove('abierta'); boton.setAttribute('aria-expanded', false); }
            });
        })();
        </script>


        <div class="main-container">

        <?php echo $template['body'];?>

		</div>



            <footer class="footer-1 bg-dark">
                <div class="container">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <img alt="Logo" class="logo" style="max-height: 130px;" src="<?php echo $base_url ?>/img/logo2.png" />
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="widget">
                                <h6 class="title">SEGUINOS</h6>
                                <hr>
                                <ul class="link-list recent-posts">
                                    <li>
                                    <a href="https://www.instagram.com/impresospdfcolor/" target="_blank">
                                        <i class="ti-instagram"></i> /impresospdfcolor
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.facebook.com/PDFCOLORIMPRESOS/" target="_blank">
                                        <i class="ti-facebook"></i> /PDFCOLORIMPRESOS
                                    </a>
                                </li>
                                </ul>
                            </div>
                            <!--end of widget-->
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="widget">
                                <h6 class="title">SECCIONES</h6>
                                <hr>
                                <ul class="link-list recent-posts">
                                    <li>
                                        <a href="<?php echo base_url() ?>hacerPedido">Hacer pedido</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo base_url() ?>calculadorprecios">Calculador de precios</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo base_url() ?>pdfpoint">PDF Points</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo base_url() ?>revendedores">Quiero ser revendedor</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo base_url() ?>blog">Novedades</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo base_url() ?>imprenta_mar_del_plata">Contacto</a>
                                    </li>
                                </ul>
                            </div>
                            <!--end of widget-->
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="widget">
                                <h6 class="title">CONTÁCTENOS</h6>
                                <hr>
                                <ul class="link-list recent-posts">
                                    <li>
                                        <i class="ti-control-forward" style="margin-right: 10px;"></i> <a href="tel:2233021309">(223)3021309</a>
                                    </li>
                                    <li>
                                        <i class="ti-email" style="margin-right: 10px;"></i> <a href="#">info@pdfcolor.com.ar</a>
                                    </li>
                                    <li>
                                        <i class="ti-location-arrow" style="margin-right: 10px;"></i> <a href="#">Ayacucho 3592</a>
                                    </li>


                                </ul>
                            </div>
                            <!--end of widget-->
                        </div>
                    </div>
                    <!--end of row-->
                    <div class="row">
                        <div class="col-sm-12">
                        	<center>
                            	<span class="sub">&copy; 2021 PDF Color. Todos los derechos reservados. Desarrollado por <a href="#" style="color:#47b475">Matias Oudshoorn.</a></span>
                        	</center>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <script src="<?php echo $base_url ?>/js/jquery.js"></script>
        <script src="<?php echo $base_url ?>/js/Jqueryvalidation.js"></script>
        <script src="<?php echo $base_url ?>/js/jquery.min.js"></script>
        <script src="<?php echo $base_url ?>/js/bootstrap.min.js"></script>
        <script src="<?php echo $base_url ?>/js/flickr.js"></script>
        <script src="<?php echo $base_url ?>/js/flexslider.min.js"></script>
        <script src="<?php echo $base_url ?>/js/lightbox.min.js"></script>
        <script src="<?php echo $base_url ?>/js/masonry.min.js"></script>
        <script src="<?php echo $base_url ?>/js/twitterfetcher.min.js"></script>
        <script src="<?php echo $base_url ?>/js/spectragram.min.js"></script>
        <script src="<?php echo $base_url ?>/js/ytplayer.min.js"></script>
        <script src="<?php echo $base_url ?>/js/countdown.min.js"></script>
        <script src="<?php echo $base_url ?>/js/smooth-scroll.min.js"></script>
        <script src="<?php echo $base_url ?>/js/parallax.js"></script>
        <script src="<?php echo $base_url ?>/js/scripts.js"></script>
        <script src="<?php echo $base_url ?>/js/uploader.js"></script>

        <script type="text/javascript" charset="utf-8" language="javascript" src="<?php echo $base_url ?>/js/jquery.dataTables.js"></script>
        <script type="text/javascript" charset="utf-8" language="javascript" src="<?php echo $base_url ?>/js/DT_bootstrap.js"></script>

        <script type="text/javascript" src="<?php echo $base_url ?>/js/vpb_uploader.js"></script>



        <!-- Our main JS file -->
        <script src="<?php echo $base_url ?>/assets/js/script.js"></script>

    </body>
</html>





