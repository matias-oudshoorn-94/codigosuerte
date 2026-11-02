<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>

    <title>Live Username Availability Checking using jQuery Ajax and PHP</title>

    <script type="text/javascript" src="jquery-1.3.2.js"></script>
    <link href="css.css" media="screen" rel="stylesheet" type="text/css"/>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#send').click(function () {

                $('#Info').html('<img src="loader.gif" alt="" />').fadeOut(1000);

                var username = $('input#username').val();
                //alert(username);	
                var dataString = 'username=' + username;

                $.ajax({
                    type: "POST",
                    url: "check_username_availablity.php",
                    data: dataString,
                    success: function (data) {
                        $('#Info').fadeIn(1000).html(data);
                        //alert(data);
                    }
                });
            });
        });
    </script>

</head>
<?php
include('dbcon.php'); ?>

<body>
<h1>Live Username Availability Checking using jQuery Ajax and PHP. </h1>

<div style="float:left; width:100%;">

</div>
</div>

<br clear="all"/>

<form name="customForm" id="customForm" method="post">

    <div class="both">
        <h4> Elige "jose" o "maria" siendo usuarios que ya existen en base de datos </h4><br clear="all"/><br
            clear="all"/>
        <br clear="all"/>
        <div>
            <label>Nombre de usuario</label>
            <input id="username" name="username" type="text" value=""/>
            <div id="Info"></div>

            <label>Contrase�a</label>
            <input id="password" name="password" type="password" value=""/><br/>
            <a id="send">Enviar</a>
        </div>

    </div>

    <br clear="all"/>

</form>

<br clear="all"/><br clear="all"/><br clear="all"/>
<br clear="all"/><br clear="all"/><br clear="all"/>
<br clear="all"/><br clear="all"/>
<br clear="all"/><br clear="all"/><br clear="all"/>
<br clear="all"/><br clear="all"/><br clear="all"/>
</body>
</html>