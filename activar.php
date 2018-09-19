<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';

if(isset($_GET['user'])){
	$mail = $_GET['user'];
	$query = "SELECT `nombre` FROM `0010_usuarios` WHERE `mail` = '".$mail."'";
	$resultadoSQL = ejecutarSQL($query, $mail, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$nombre = $datoA['nombre'];
	}
}

if(isset($_GET['code'])){
	$code = $_GET['code'];
}

if(isset($_POST['activar']))
{
	$mail = $_POST['mail'];
	$code = $_POST['code'];
	$usu = validaCampoTXT(($_POST['usuario']), 3);
	$pwdA 	= validaCampoTXT(($_POST['cont']), 4);
	
	
	$errLista = '';
	//usuario
	if ($usu === FALSE) {
		$usu = $_POST['usuario'];
		$errLista .= "|El usuario debe tener al menos 3 caracteres|"; 
	}
	
	//Contraseña
	if ($pwdA === FALSE) {
		$pwdA = $_POST['cont'];
		$errLista .= "|La contrase&ntilde;a debe tener al menos 4 caracteres|"; 
	}
	
	$idUsuario = desencriptarNumero($code);
	$query = "SELECT `mail` FROM `0010_usuarios` WHERE `idUsuario` = '".$idUsuario."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	$noResult = mysqli_num_rows($resultadoSQL);
	if ($noResult == 0){
		$errLista .= "|El n&uacute;mero no existe|"; 
	}else{
		$datoA= mysqli_fetch_assoc($resultadoSQL);
		$mailB = $datoA['mail'];
		echo var_dump($mailB);
		if($mail != $mailB)
		{
			$errLista .= "|C&oacute;digo inv&aacute;lido, no coincide con el e-mail|"; 
		}
	}
		
	//Verificación de $errLista por Errores o sino capturar
	if(noHayErrores($errLista)){
		//activar
		$query = "UPDATE `0010_usuarios` SET `activo`= '1',`user`='".$usu."', `pwd`='".$pwdA."' WHERE `idUsuario` = '".$idUsuario."'";
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		
		//entrar
		$tarjeta = validaUsuario($usu,$pwdA);
		if($tarjeta != '')
		{
			if($_POST['recordar'] == true)
			{
				setcookie("idUsuario",$idUsuario, time()+(30*24*60*60),"/"); //para un mes
				setcookie("pwd",      $pwdA,  time()+(30*24*60*60),"/"); //para un mes
			}else{
				setcookie("idUsuario",$idUsuario); //para una session
				setcookie("pwd",      $pwdA); //para una session
				setcookie("duracion",'neto');
			}
			$dir= "nuevo.php";
			header("Location: ".$dir);
			echo '<script>window.location = "'.$dir.'";</script>';
			exit;
		}else{
			mostrarModal("Error en el usuario");
		}
		
	}
}

?>
<!DOCTYPE HTML>
<html lang="es">
<head>
	<? include 'parts/head.html'; ?>  
</head>

<body>
	<div id="contenedor">
    <?php include "parts/header.php" ?>
    	<section class="container">
        	<h2 class="text-center">Hola de nuevo <? echo $nombre; ?></h2><br>
            <form enctype="multipart/form-data" action="activar.php" method="post" name="localizacion" target="_self" role="form" id="myForm">
                <? 
				echo campoMail("mail","mail de usuario",$mail,"icon-id-card-o"); //mail
				echo campoTextoBasico("code","C&oacute;digo de activaci&oacute;n",$code,"icon-key"); // codigo
				echo campoTextoBasico("usuario","Selecciona un usuario",$usu,"icon-id-card-o"); //mail
				?>
                <div class="clearfix"></div>
                <div class="form-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3" style="padding:0px;">
                    <label for="cont" class="control-label text-right control-label "> Nueva Contrase&ntilde;a</label>
                    <div class="input-group">
                        <span class="input-group-addon icon-key2" aria-hidden="true"></span>
                        <input type="password" class="form-control" value="<?php echo $pwdA; ?>" placeholder="Nueva Contrase&ntilde;a" id="cont" name="cont" min="6" onKeyUp="comprueba()" required >
                        <span class="input-group-addon icon-key2" aria-hidden="true"></span>
                        <input type="password" class="form-control" id="contB" name="contB" value="<?php echo $pwdA; ?>" placeholder="repetir contrase&ntilde;a" onKeyUp="comprueba()" required>
                    </div>    
                    <div class="help-block col-xs-6 col-sm-6"><? echo $minimoCar; ?></div>
                    <div id="contLabel" class="help-block label-warning col-xs-6 col-sm-6"></div>
            	</div>
                <div class="clearfix"></div>
                
                <?
				//CANCELAR Y SUBMIT 
				echo botonSubmit("activar","Activar", 'btn-success');
				?>
            	
        	</form>
             

    	</section>
    	<?php include "parts/footer.html" ?>
	</div><!--cierre contenedor!-->
	<script>
		function comprueba() {
			var tamMin = 6;
			var cadenaA = document.getElementById("cont").value;
			var cadenaB = document.getElementById("contB").value;
			if (cadenaB.length < tamMin && cadenaA.length < tamMin) {
				document.getElementById("contLabel").innerHTML = "Minimum of " + tamMin + " characters";
				errorB = true;
			}
			else if(cadenaA != cadenaB)
			{
				document.getElementById("contLabel").innerHTML = "Whoops, these do not match";
				errorB = true;
			}
			else
			{
				document.getElementById("contLabel").innerHTML = "";
				errorB = false;
			}
		}
		function checaSMS()
		{
			//SMS
			var tel = document.getElementById("telB").value
			var urlSMS = "https://platform.clickatell.com/messages/http/send?apiKey=W5zppQsfTAyXGvY23Jws1A==&to=52" + tel + "&content=activacion CIdEAA card = <? echo $codigo; ?>";
		
			console.log(urlSMS);
			/*
			$.get(urlSMS, function(responseText) {
				dato = (responseText);
				console.log(dato);
				if(dato != "")
				{
					
				}
			});
			*/
		}
		<?php if($mensajeModal) echo ("mensajeModalA('".$mensaje."');"); ?>
	</script>
    
</body>
</html>
