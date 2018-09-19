<?php //Función para cerrar la sesión en el CRM por solicitud del usuario

//cookie hecha por Jaime en el Index

			
setcookie("idUsuario","",time()-(3600),"/");
setcookie("pwd",      "",time()-(3600),"/");

unset($_COOKIE['idUsuario']);
unset($_COOKIE['pwd']);

echo '<script>window.location = "https://cideaa.com";</script>';
exit;

?>