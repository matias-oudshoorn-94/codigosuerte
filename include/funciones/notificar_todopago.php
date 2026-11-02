<?php



require ('PHPMailer/PHPMailerAutoload.php'); 

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


		$mail->Subject = 'PDF Color - ¡Finalizá tu compra!';

		$msg =  '<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
    <td align="center" valign="top" style="padding:20px 0 20px 0">
        <table bgcolor="#FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
            <!-- [ header starts here] -->
            <tr>
                <td valign="top"><a href="http://pdfcolor.com.ar"><img src="http://pdfcolor.com.ar/application/views/layouts/img/logo2.png" alt="http://pdfcolor.com.ar/application/views/layouts/img/logo2.png" style="margin-bottom:10px;max-width: 20%;" border="0"/></a></td>
            </tr>
            <!-- [ middle starts here] -->
            <tr>
                <td valign="top">
                    <h1 style="font-size:22px; font-weight:normal; line-height:22px; margin:0 0 11px 0;"">Hola, '.$_POST['nombre'].' '.$_POST['apellido'].'</h1>
                    <p style="font-size:12px; line-height:16px; margin:0;">
                        
                        <br>
          </p>
            </tr>
            <tr>
                <td>
                    <h2 style="font-size:18px; font-weight:normal; margin:0;">Finalizá tu compra ingresando en el siguiente link de PRISMA medios de pagos: '.$_POST['linktodopago'].'</small></h2>
                </td>
            </tr>
            <tr>
                <td>
                    
                    <br/>
                </td>
            </tr>
            <tr>
                <td valign="top">
                <p style="font-size:12px; line-height:16px; margin:0;color:red">
                        <b>IMPORTANTE:</b> Una vez que realizas el pago, en el plazo de 24/48hs te llegará un mail notificando el código de seguimiento de OCA para retirar en el correo. <br><br>

                    </p>
                </td>
            </tr>
            <tr>
                <td bgcolor="#EAEAEA" align="center" style="background:#EAEAEA; text-align:center;"><center><p style="font-size:12px; margin:0;">Gracias, <strong>PDFColor</strong></p></center></td>
            </tr>
        </table>
    </td>
</tr>
</table>
</div>
</body>
';



		$mail->CharSet = 'UTF-8';
        $mail->AddAddress($_POST['correo']);
        //$mail->AddBCC('matiasoudshoorn@axislab.com');
        //$mail->AddBCC('matiasoudshoorn@gmail.com');
        //$mail->AddBCC('casarom@hotmail.com');
		$mail->From = "no-responder@pdfcolor.com.ar";
		$mail->FromName = 'PDF Color - ¡Finalizá tu compra!';

		$mail->Body = $msg;

		$exito = $mail->Send();

		if($exito)

		{

			echo "Cliente notificado";

		}

		else

		{

			echo "Hubo un problema. Contacta a un administrador.";

		}


?>