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


		$mail->Subject = 'PDF Color - ¡Retire su pedido!';

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
            </tr>
            <tr>
                <td>
                    <h2 style="font-size:18px; font-weight:normal; margin:0;">¡Su pedido #'.$_POST['id_pedido'].' <small>(realizado el '.$_POST['fecha'].')</small> ya está listo para retirar!</h2>
                </td>
            </tr>
            <tr>
                <td>
                    <table cellspacing="0" cellpadding="0" border="0" width="650">
                        <thead>
                        <tr>
                            <th align="left" width="325" bgcolor="#EAEAEA" style="font-size:13px; padding:5px 9px 6px 9px; line-height:1em;">Información del pedido:</th>
                            <th width="10"></th>
                            <th align="left" width="325" bgcolor="#EAEAEA" style="font-size:13px; padding:5px 9px 6px 9px; line-height:1em;">Pasá a retirarlo por:</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td valign="top" style="font-size:12px; padding:7px 9px 9px 9px; border-left:1px solid #EAEAEA; border-bottom:1px solid #EAEAEA; border-right:1px solid #EAEAEA;">
                                Número de pedido: #'.$_POST['id_pedido'].'<br>
                                Fecha del pedido: '.$_POST['fecha'].'<br>
                                <hr style="height: 1px;background-color: #ccc;border: none;">
                                <font style="font-size:20px"><b>Total: $'.$_POST['total'].'</b></font>

                            </td>
                            <td>&nbsp;</td>
                            <td valign="top" style="font-size:12px; padding:7px 9px 9px 9px; border-left:1px solid #EAEAEA; border-bottom:1px solid #EAEAEA; border-right:1px solid #EAEAEA;">
                                Nombre: '.$_POST['pdfnombre'].'<br>
                                Dirección: '.$_POST['pdfdireccion'].'<br>
                                Teléfono: '.$_POST['pdftelefono'].'


                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <br/>
                    <br/>
                </td>
            </tr>
            <tr>
                <td valign="top">
                <p style="font-size:12px; line-height:16px; margin:0;color:red">
                        <b>IMPORTANTE:</b> Al momento de retirar el pedido deberá presentar el DNI.<br><br>

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
</body>';

		$mail->CharSet = 'UTF-8';
        $mail->AddAddress($_POST['correo']);
        //$mail->AddBCC('matiasoudshoorn@axislab.com');
        //$mail->AddBCC('matiasoudshoorn@gmail.com');
        //$mail->AddBCC('casarom@hotmail.com');
		$mail->From = "no-responder@pdfcolor.com.ar";
		$mail->FromName = 'PDF Color - ¡Retire su pedido!';

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