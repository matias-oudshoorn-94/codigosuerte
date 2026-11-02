<?php


$email = $_POST['email'];
$repetir_email = $_POST['repetir_email'];

//echo ('Email: '.$email.'<br>Repetir email: '.$repetir_email);
print_r($_POST);

    if ($email == $repetir_email) 
    {
    	echo "true";

    } 
    else 
    {
    	echo "false";
    }

?>
