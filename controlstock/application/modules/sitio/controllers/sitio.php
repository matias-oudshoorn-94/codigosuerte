<?php session_start(); defined('BASEPATH') OR exit('No direct script access allowed');

class Sitio extends MX_Controller
{
	var $data;
	var $meta;
	var $sql;

	function __construct()
	{
		parent::__construct();
		$this->template->set_layout('sitio_template');
		$this->template->enable_parser(FALSE);
		$this->load->library('form_validation');

	}


	function enviar_contacto()
	{

		//require("classes/PHPMailer/class.phpmailer.php");
		require ('classes/PHPMailer/PHPMailerAutoload.php'); 

		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->Host = "mail.pdfcolor.com.ar"; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
		$mail->Username = "no-responder@pdfcolor.com.ar"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.
		$mail->Password = "*Ybk0jp4sT"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465; // Puerto de conexión al servidor de envio.

		$mail->IsHTML(true); // El correo se envía como HTML

		$mail->SMTPOptions = array(
					    'ssl' => array(
					        'verify_peer' => false,
					        'verify_peer_name' => false,
					        'allow_self_signed' => true
					    )
					);


		$mail->Subject = 'PDF Color - Contacto web';
		$textarea = str_replace("\n", "<br>", $_POST['consulta']);

		$msg =  'Contacto recibido de:<br><br> Nombre: '.$_POST["nombre"].'<br>Email: '.$_POST["email"].'<br>Teléfono: '.$_POST["telefono"].'<br><br>Mensaje: <br>'.$textarea.'<br/>';

		$mail->CharSet = 'UTF-8';
        $mail->AddAddress('info@pdfcolor.com.ar');
        //$mail->AddBCC('matiasoudshoorn@axislab.com');
        //$mail->AddBCC('matiasoudshoorn@gmail.com');
        //$mail->AddBCC('casarom@hotmail.com');
		$mail->From = "no-responder@pdfcolor.com.ar";
		$mail->FromName = 'PDF Color - Contacto web';

		$mail->Body = $msg;

		if($variable != "")//Varibale para evitar robots
		{
			$mail->Subject = 'PDF Color - mensaje generado por Robot';
			$msg =  'Mensaje generado por robot.<br>Nombre robot: '.$_POST["nombre"].'';
			$mail->CharSet = 'UTF-8';
			$mail->AddAddress('matiasoudshoorn@axislab.com');
			//$mail->AddBCC('walteruribe@axislab.com');
			$mail->From = "no-responder@casarom.com.ar";
			$mail->FromName = 'PDF Color - Mensaje generado por Robot';
			$mail->Body = $msg;
			$mail->Send();
			exit();
		}

		if($_POST["nombre"] != '')//Para evitar que corran el "enviar_contacto" manualmente y lleguen correos vacios
		{
			$exito = $mail->Send();
		}

		if($exito)

		{

			$this->template->build('sitio/enviar_contacto', $this->data);

		}

		else

		{

			echo "Hubo un problema. Contacta a un administrador.";

		}


	}


	function login()

	{
		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->data['meta'] = $this->meta;

		$this->data['seccion'] = "ingresar";

		$this->template->title('Iniciar sesión | PDF Color');

		$this->template->build('sitio/login', $this->data);

	}

	function login_proc()

	{

		$this->load->model('admin/clientes_model','modelo',TRUE);

		$results = $this->modelo->login();



		if ($results['num_rows'] > 0)

		{

			$this->session->set_userdata("user_data", $results['rows']);
			

			//redirect();
			//redirect('sitio/login', 'refresh');
			$this->template->build('sitio/inicio', $this->data);

		}

		else

		{

			$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

			$this->data['meta'] = $this->meta;

			$this->data['msg'] = "ERROR. Nombre de usuario o contraseña incorrecto.";

			$this->template->title('Iniciar sesión | PDF Color');

			$this->template->build('sitio/home', $this->data);

		}

	}

	function registrarse()

	{
		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->data['meta'] = $this->meta;

		$this->data['seccion'] = "registrarse";

		$this->template->title('Registrarse | PDF Color');

		$this->template->build('sitio/registrarse', $this->data);

	}


	function registrarse_proc()

	{

		require ('classes/PHPMailer/PHPMailerAutoload.php'); 

		$mail = new PHPMailer();


		$this->load->model('admin/clientes_model','modelo',TRUE);


		$results = $this->modelo->registrar();


		if ($results['num_rows'] > 0)

		{

			$this->data['msg_usuario'] = "ERROR. Ya existe un usuario con ese teléfono.";

			$this->template->title('Registrarse | PDF Color');

			$this->template->build('sitio/registrarse', $this->data);

		}

		if ($results['num_rows'] == "pass")

		{

			$this->data['msg'] = "ERROR. Las contraseñas no coinciden.";

			$this->template->title('Registrarse | PDF Color');

			$this->template->build('sitio/registrarse', $this->data);

		}

		if (($results['num_rows'] == 0) && ($results['num_rows'] != "pass"))

		{

			$results = $this->modelo->insertar();


			//Luego tenemos que iniciar la validación por SMTP:

			$mail->IsSMTP();

			$mail->SMTPAuth = true;

			$mail->Host = "mail.pdfcolor.com.ar"; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com

			$mail->Username = "no-responder@pdfcolor.com.ar"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.

			$mail->Password = "*Ybk0jp4sT"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo

			$mail->SMTPSecure = 'ssl';

			$mail->Port = 465; // Puerto de conexión al servidor de envio.

			$mail->IsHTML(true); // El correo se envía como HTML

			$mail->SMTPOptions = array(
						    'ssl' => array(
					        'verify_peer' => false,
					        'verify_peer_name' => false,
					        'allow_self_signed' => true
					    )
					);

			$mail->CharSet = 'UTF-8';


			$mail->From = "no-responder@pdfcolor.com.ar";

			$mail->FromName = 'PDF Color - Solicitud de registro';

			$mail->Subject = 'Solicitud de registro';


			$msg = '

			<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
        <tr>
            <td align="center" valign="top" style="padding:20px 0 20px 0">
                <!-- [ header starts here] -->
                <table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
                    <tr>
                        <td valign="top">
                            <a href="http://pdfcolor.com.ar"><img src="http://pdfcolor.com.ar/application/views/layouts/img/logo2.png" alt="http://pdfcolor.com.ar/application/views/layouts/img/logo2.png" style="margin-bottom:10px;max-width: 20%;" border="0"/></a></td>
                    </tr>
                <!-- [ middle starts here] -->
                    <tr>
                        <td valign="top">
                            <h1 style="font-size:22px; font-weight:normal; line-height:22px; margin:0 0 11px 0;"">Estimado/a, '.$_POST['nombre'].' '.$_POST['apellido'].'</h1>
                            <p style="font-size:12px; line-height:16px; margin:0 0 16px 0;">Bienvenido/a a PDFColor. Para iniciar sesión en nuestra web, sólo tiene que hacer clic en <a href="http://pdfcolor.com.ar/login" style="color:#1E7EC8;">Iniciar sesión</a> e introducir su número de teléfono y contraseña.</p>
                            <p style="border:1px solid #E0E0E0; font-size:12px; line-height:16px; margin:0; padding:13px 18px; background:#f9f9f9;">
                                Use los siguientes datos para iniciar sesión:<br/><br>
                                <strong>Teléfono</strong>: '.$_POST['telefono'].'<br/>
                                <strong>Contraseña</strong>: '.$_POST['clave'].'<p>
                            <p style="font-size:12px; line-height:16px; margin:0 0 8px 0;">Cuando inicie sesión con su cuenta, podrá hacer lo siguiente:</p>
                            <ul style="font-size:12px; line-height:16px; margin:0 0 16px 0; padding:0;">
                                <li style="list-style:none inside; padding:0 0 0 10px;">&ndash; Realizar pedidos</li>
                                <li style="list-style:none inside; padding:0 0 0 10px;">&ndash; Comprobar el estado de los pedidos</li>
                                <li style="list-style:none inside; padding:0 0 0 10px;">&ndash; Ver pedidos anteriores</li>
                            </ul>
                            <p style="font-size:12px; line-height:16px; margin:0;">Si tiene alguna duda sobre su cuenta o alguna otra cuestión, no deje de ponerse en contacto con nosotros en <a href="mailto:info@pdfcolor.com.ar" style="color:#1E7EC8;">info@pdfcolor.com.ar</a> o por teléfono al (223)3021309</p>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#EAEAEA" align="center" style="background:#EAEAEA; text-align:center;"><center><p style="font-size:12px; margin:0;">Gracias de nuevo, <strong>PDFColor</strong></p></center></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>


			';



	        $mail->CharSet = 'UTF-8';

	        $mail->AddAddress($_POST['email']);

			$mail->AddBCC('matiasoudshoorn@axislab.com');


			$mail->Body = $msg;


			$exito = $mail->Send();


	        if($exito)


	        {

				$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

				$this->data['meta'] = $this->meta;

				$this->template->title('Registrarse | PDF Color');

				$this->template->build('sitio/registrado', $this->data);

	        }

	        else

	        {

	            echo "Se produjo un error durante el envío del correo.";

	        }



		}

	}



	function logout()

	{

		//Destruir variable de session de usuario

		$this->session->unset_userdata("user_data");

		//Destruir variable de session del carrito de compras

		$this->session->unset_userdata("cart_products");

		//redireccionar a la pagina en la que estaba actualmente

		redirect($this->input->get('last_url'));

	}



	function nueva_venta()
	{

		$this->load->model('admin/productos_model','modelo',TRUE);
		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		$sql['sort_order'] = "asc";
		$sql['sort_by'] = "nombre";

		$results = $this->modelo->getAll($sql);

		$this->data['rows_productos'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];


		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "pedido";


		$this->template->title('Nueva venta | PDF Color');
		$this->template->build('sitio/nueva_venta', $this->data);
	}



	
	function index()
	{
	
		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online. ";
		$this->meta['keywords'] = "";
		$this->data['seccion'] = "home";

			$this->template->title('PDF Color | Inicio');
			$this->template->build('sitio/inicio', $this->data);

			$this->template->title('PDF Color | Inicio');
			$this->template->build('sitio/login', $this->data);
			

	}


	function inicio()
	{
		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "contacto";

		$this->template->title('Inicio | PDF Color');
		$this->template->build('sitio/inicio', $this->data);
	}

	function revendedores()
	{
		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "revendedores";

		$this->template->title('Revendedores | PDF Color');
		$this->template->build('sitio/revendedores', $this->data);
	}


	function calculadorprecios()
	{
		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "calculador";

		$this->load->model('admin/precios_model','modelo',TRUE);
		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		$sql['sort_order'] = "asc";
		$sql['sort_by'] = "orden";

		$results = $this->modelo->getAll($sql);

		$this->data['rows_precios'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$this->template->title('Calculador de Precios | PDF Color');
		$this->template->build('sitio/calculadorprecios', $this->data);
	}


	function busqueda($nombre)
	{
		$nombre = str_replace("%20", " ", $nombre);

		$this->data['busqueda'] = $nombre;


		$this->load->model('admin/productos_model','modelo_busqueda',TRUE);
		$sql2['limit'] = NULL;
		$sql2['offset'] = NULL;
		$sql2['activo'] = 1;
		//Where para la búsqueda:
		$sql2['bnombre'] = $nombre;
		$sql2['sort_order'] = "asc";
		$sql2['sort_by'] = "orden";

		$results = $this->modelo_busqueda->getAll($sql2);
		$this->data['rows_productos'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$marca = $results['rows'][0]->nombre_marca;
		$categoria = $results['rows'][0]->categoria;


		$this->data['rows'] = $results['rows'];

		foreach($this->data['rows'] as $aux):

			$imagenPrincipal = $this->modelo_busqueda->getImagenPrincipal($aux->id);

			$aux->imagen = (isset($imagenPrincipal[0]->nombre))?$imagenPrincipal[0]->nombre:'no-thumbail.png';

		endforeach;

		$this->load->model('sitio/imagenes_model','modelo_imagenes',TRUE);
		$sql1['limit'] = NULL;
		$sql1['offset'] = NULL;
		$sql1['sort_order'] = "asc";
		$sql1['sort_by'] = "orden";

		$results = $this->modelo_imagenes->getAll($sql1);
		$this->data['row_imagenes'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$this->load->model('admin/marcas_model','modelo_marcas',TRUE);
		$sql3['limit'] = NULL;
		$sql3['offset'] = NULL;
		$sql3['sort_order'] = "asc";
		$sql3['sort_by'] = "orden";

		$results = $this->modelo_marcas->getAll($sql3);
		$this->data['rows_marcas'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$this->data['rows'] = $results['rows'];


		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "productos";


		$this->template->title('Busqueda | PDF Color');
		$this->template->build('sitio/busqueda', $this->data);
	}

	function blog()
	{
		$this->load->model('admin/blog_model','modelo',TRUE); 
		$sql['limit'] = NULL;
		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		$sql['sort_order'] = "asc";
		$sql['sort_by'] = "orden";

		$results = $this->modelo->getAll($sql);
		$this->data['row_blog'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "novedades";


		$this->template->title('Novedades | PDF Color');
		$this->template->build('sitio/blog', $this->data);
	}


	function vista_blog($nombre)
	{

		$this->load->model('admin/blog_model','modelo_blog',TRUE); 
		$sql2['limit'] = NULL;
		$sql2['limit'] = NULL;
		$sql2['offset'] = NULL;
		$sql2['sort_order'] = "asc";
		$sql2['sort_by'] = "orden";

		$resultados = $this->modelo_blog->getAll($sql2);
		$this->data['rows_blogs'] = $resultados['rows'];
		$this->data['nums_results'] = $resultados['num_rows'];
		

		$this->load->model('admin/blog_model','modelo',TRUE); 
		$sql['limit'] = NULL;
		$sql['slug'] = $nombre;

		$results = $this->modelo->getAll($sql);
		$this->data['row_blog'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];
		$this->data['seccion'] = "novedades";

		$titulo_seo = $results['rows'][0]->titulo_seo;
		$descripcion_seo = $results['rows'][0]->descripcion_seo;

		$this->meta['description'] = $descripcion_seo;

		$this->data['meta'] = $this->meta;


		$this->template->title( $titulo_seo.' | PDF Color');
		$this->template->build('sitio/vista_blog', $this->data);
	}



	function comprar_fuera_mdp()
	{

		$this->load->model('admin/provincia_model','modelo',TRUE);
		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		$sql['sort_order'] = "asc";
		$sql['sort_by'] = "orden";

		$results = $this->modelo->getAll($sql);

		$this->data['rows_provincias'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$this->load->model('admin/precios_model','modelo_precios',TRUE);
		$sql2['limit'] = NULL;
		$sql2['offset'] = NULL;
		$sql2['sort_order'] = "asc";
		$sql2['sort_by'] = "orden";

		$resultas = $this->modelo_precios->getAll($sql2);

		$this->data['rows_precios'] = $resultas['rows'];
		$this->data['num_results'] = $resultas['num_rows'];


		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "comprarfuera";


		$this->template->title('Comprar fuera de MDP | PDF Color');
		$this->template->build('sitio/comprar_fuera_mdp', $this->data);
	}

	function comprar_dentro_mdp()
	{

		$this->load->model('admin/provincia_model','modelo',TRUE);
		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		$sql['sort_order'] = "asc";
		$sql['sort_by'] = "orden";

		$results = $this->modelo->getAll($sql);

		$this->data['rows_provincias'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$this->load->model('admin/precios_model','modelo_precios',TRUE);
		$sql2['limit'] = NULL;
		$sql2['offset'] = NULL;
		$sql2['sort_order'] = "asc";
		$sql2['sort_by'] = "orden";

		$resultas = $this->modelo_precios->getAll($sql2);

		$this->data['rows_precios'] = $resultas['rows'];
		$this->data['num_results'] = $resultas['num_rows'];


		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "comprardentro";


		$this->template->title('Comprar dentro de MDP | PDF Color');
		$this->template->build('sitio/comprar_dentro_mdp', $this->data);
	}

	function mayorista()
	{

		$this->load->model('admin/precios_model','modelo_precios',TRUE); 
		$sql22['limit'] = NULL;
		$sql22['limit'] = NULL;
		$sql22['offset'] = NULL;
		$sql22['sort_order'] = "asc";
		$sql22['sort_by'] = "id";

		$resultadoss = $this->modelo_precios->getAll($sql22);
		$this->data['rows_precios'] = $resultadoss['rows'];
		$this->data['nums_results'] = $resultadoss['num_rows'];

		$this->load->model('admin/libro_model','modelo_libro',TRUE); 
		$sql2['limit'] = NULL;
		$sql2['limit'] = NULL;
		$sql2['offset'] = NULL;
		$sql2['sort_order'] = "asc";
		$sql2['sort_by'] = "nombre";

		$resultados = $this->modelo_libro->getAll($sql2);
		$this->data['rows_libros'] = $resultados['rows'];
		$this->data['nums_results'] = $resultados['num_rows'];

		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "mayorista";


		$this->template->title('Mayorista | PDF Color');
		$this->template->build('sitio/mayorista', $this->data);
	}


	function login_mayorista()

	{
		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->data['meta'] = $this->meta;

		$this->data['seccion'] = "ingresar";

		$this->template->title('Iniciar sesión | PDF Color');

		$this->template->build('sitio/login_mayorista', $this->data);

	}

	function login_proc_mayorista()

	{

		$this->load->model('admin/mayorista_model','modelo_mayorista',TRUE);

		$results = $this->modelo_mayorista->login();



		if ($results['num_rows'] > 0)

		{

			$this->session->set_userdata("mayorista", $results['rows']);
			

			//redirect();

			redirect('/mayorista', 'refresh');

		}

		else

		{

			$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

			$this->data['meta'] = $this->meta;

			$this->data['msg'] = "ERROR. Nombre de usuario o contraseña incorrecto.";

			$this->template->title('Iniciar sesión | PDF Color');

			$this->template->build('sitio/login_mayorista', $this->data);

		}

	}

	function logout_mayorista()

	{

		//Destruir variable de session de usuario

		$this->session->unset_userdata("mayorista");

		//Destruir variable de session del carrito de compras

		$this->session->unset_userdata("cart_products");

		//redireccionar a la pagina en la que estaba actualmente

		redirect($this->input->get('last_url'));

	}


	function getData()
	{
		//Carro
		if (isset($this->session->userdata("user_data")[0]->idusuario))
		{ 
			$this->load->model('admin/shopping_model','modelo_carro',TRUE); 

			$this->modelo_carro->add_item($this->session->userdata("session_id"), $_GET['idArt'], $_GET['quantity'], $this->session->userdata("user_data")[0]->idusuario); 
			
			
			$sql['limit'] = NULL;
			$sql['offset'] = NULL;
			/*$sql['where'] = "0";*/
			$sql['sort_order'] = "asc";
			$sql['sort_by'] = "productos.nombre";
			$sql['cod_clie'] = $this->session->userdata("user_data")[0]->idusuario;
			$results = $this->modelo_carro->getAll($sql);			
			//$this->data['rows_carro'] = $results['rows'];			
			echo json_encode($results['rows']);
		}		
	}
	
	function quitarData()
	{
		//Carro
		if (isset($this->session->userdata("user_data")[0]->idusuario))
		{ 
			$this->load->model('admin/shopping_model','modelo_carro',TRUE); 

			//error_log("--QUITAR--".$_GET['idArt']);
			if ($_GET['delAll'] == 1)
				$this->modelo_carro->clear_cart($this->session->userdata("user_data")[0]->idusuario); 			
			else
				$this->modelo_carro->delete_item($this->session->userdata("user_data")[0]->idusuario, $_GET['idArt']); 
			
			
			$sql['limit'] = NULL;
			$sql['offset'] = NULL;
			/*$sql['where'] = "0";*/
			$sql['sort_order'] = "asc";
			$sql['sort_by'] = "productos.nombre";
			$sql['cod_clie'] = $this->session->userdata("user_data")[0]->idusuario;
			$results = $this->modelo_carro->getAll($sql);			
			//$this->data['rows_carro'] = $results['rows'];			
			echo json_encode($results['rows']);
		}		
	}	
	

	function modificarData()
	{
		//Carro
		if (isset($this->session->userdata("user_data")[0]->idusuario))
		{ 
			$this->load->model('admin/shopping_model','modelo_carro',TRUE); 
			//error_log("--QUITAR--".$_GET['idArt']);
			$this->modelo_carro->modify_quantity($this->session->userdata("user_data")[0]->cod_clie, $_GET['idArt'], $_GET['quantity']); 			
		}		
	}	


	function enviar_pedido()
	{
				
		$this->load->model('admin/shopping_model','modelo_carro',TRUE); 

		$cliente = $this->session->userdata("user_data")[0]->idusuario;

		$modo_de_pago = $_SESSION['pago'];	

		$this->modelo_carro->send($cliente,$modo_de_pago);

		$this->modelo_carro->clear_cart($cliente);		
	
		$this->data['rows_carro'] = array();			
		
		$this->template->build('sitio/enviar_pedido', $this->data);		
	}


	function pedidos()
	{
				
		$this->load->model('admin/pedidos_model','modelo_pedidos',TRUE); 


		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		/*$sql['where'] = "0";*/
		$sql['sort_order'] = "asc";

		//$sql['sort_by'] = "productos.nombre";
		$sql['cod_clie'] = $this->session->userdata("user_data")[0]->idusuario;

		$sql['fecha'] = date('Y-m-d'); //Traigo los del dia actual. 

		$resultado = $this->modelo_pedidos->getAll($sql);			

		$this->data['rows_pedidos'] = $resultado['rows'];
		$this->data['num_results'] = $resultado['num_rows'];

		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "pedidos";

		$this->template->title('Ventas | PDF Color');		
		
		$this->template->build('sitio/pedidos', $this->data);		
	}

	function pedido_detalles($idpedido)
	{
				
		$this->load->model('admin/pedidosdetalles_model','modelo_detalles',TRUE); 


		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		/*$sql['where'] = "0";*/
		$sql['sort_order'] = "asc";
		//$sql['sort_by'] = "productos.nombre";
		$sql['id'] = $idpedido;

		$resultado = $this->modelo_detalles->getAll($sql);			

		$this->data['rows_detalle'] = $resultado['rows'];
		$this->data['num_results'] = $resultado['num_rows'];

		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "pedidos";

		$this->template->title('Detalles de ventas | PDF Color');	
		
		$this->template->build('sitio/pedido_detalles', $this->data);		
	}


	function perfil()

	{

		$this->load->model('admin/impresoras_model','modelo_impresoras',TRUE); 


		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		/*$sql['where'] = "0";*/
		$sql['sort_order'] = "asc";
		//$sql['sort_by'] = "productos.nombre";
		$sql['sub_id'] = $this->session->userdata("user_data")[0]->idusuario;
		$resultado = $this->modelo_impresoras->getAll($sql);			

		$this->data['rows_impresoras'] = $resultado['rows'];
		$this->data['num_results'] = $resultado['num_rows'];


		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->data['meta'] = $this->meta;

		$this->data['seccion'] = "perfil";

		$this->template->title('Perfil | PDF Color');

		$this->template->build('sitio/perfil', $this->data);

	}


	function cerrar_caja()

	{

		//IMPRESORAS:

		$this->load->model('admin/impresoras_model','modelo_impresoras',TRUE); 


		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		/*$sql['where'] = "0";*/
		$sql['sort_order'] = "asc";
		//$sql['sort_by'] = "productos.nombre";
		$sql['sub_id'] = $this->session->userdata("user_data")[0]->idusuario;
		$resultado = $this->modelo_impresoras->getAll($sql);			

		$this->data['rows_impresoras'] = $resultado['rows'];
		$this->data['num_results'] = $resultado['num_rows'];

		//PEDIDOS:

		$this->load->model('admin/pedidos_model','modelo_pedidos',TRUE); 


		$sql2['limit'] = NULL;
		$sql2['offset'] = NULL;
		/*$sql['where'] = "0";*/
		$sql2['sort_order'] = "asc";

		//$sql['sort_by'] = "productos.nombre";
		$sql2['cod_clie'] = $this->session->userdata("user_data")[0]->idusuario;

		$sql2['fecha'] = date('Y-m-d'); //Traigo los del dia actual. 

		$resultados= $this->modelo_pedidos->getAll($sql2);			

		$this->data['rows_pedidos'] = $resultados['rows'];
		$this->data['num_results'] = $resultados['num_rows'];


		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->data['meta'] = $this->meta;

		$this->data['seccion'] = "perfil";

		$this->template->title('Cerrar Caja | PDF Color');

		$this->template->build('sitio/cerrar_caja', $this->data);

	}


	function caja_cerrada()

	{

		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->data['meta'] = $this->meta;

		$this->data['seccion'] = "perfil";

		$this->template->title('Caja Cerrada | PDF Color');

		$this->template->build('sitio/caja_cerrada', $this->data);

	}





	function router()
	{
		$this->load->view('sitio/router');
	}

}