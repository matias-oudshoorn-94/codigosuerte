<?php


    //require("classes/PHPMailer/class.phpmailer.php");
        require ('classes/PHPMailer/PHPMailerAutoload.php'); 

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = "mail.casarom.com.ar"; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
        $mail->Username = "info@casarom.com.ar"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.
        $mail->Password = "SSkbYtf{d96="; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
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


        $mail->Subject = 'Sanitarios Casa Rom - Contacto web';

        $msg =  'Correo de prueba Casa Rom';

        $mail->CharSet = 'UTF-8';
        $mail->AddAddress('casarom.seo@gmail.com');
        $mail->From = "no-responder@casarom.com.ar";
        $mail->FromName = 'Sanitarios Casa Rom - Contacto web';

        $mail->Body = $msg;

        $exito = $mail->Send();


        if($exito)

        {

            echo "Enviado correctamente.";

        }

        else

        {

            echo "Hubo un problema. Contacta a un administrador.";

        }

?>



