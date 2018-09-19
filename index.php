<?
include 'includes/inc.conexion.php';
include 'includes/inc.funciones.php';

$jerarquia = permiteAcceso(FALSE);
if($jerarquia !== FALSE)
{
	$destino = "nuevo.php";
	header("Location: ".$destino);
	echo '<script>window.location = "'.$destino.'";</script>';
	exit;
}

include 'includes/formsBase.php';

$ahora = gmdate("Y-m-d H:i",time() + ($timeZone*60*60));
$fecha = substr($ahora,0,10);



if(isset($_POST['olvidado']))
{
	$usuario = trim($_POST['usuario']);
	$errLista = '';
	
	//usuario-mail
	if ($usuario == "") {
		$errLista = mensajeErrValida("usuario", 1); 
	}
	$query = "SELECT `pwd`, mail FROM `0010_usuarios` WHERE `user` = '".$usuario."'";
	$resultadoSQL = ejecutarSQL($query, $usuario, $_SERVER['REQUEST_URI'], __LINE__);
	$pwd = "";
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$pwd=$datoA['pwd'];
		$mail=$datoA['mail'];
	}
	if ($pwd == "" || $mail == "") {
		$errLista = "|No se encontro a este usuario|"; 
	}
	//Verificación de $errLista por Errores o sino capturar
	if (noHayErrores($errLista))
	{ //No existieron errores, entrar
		$mensaje = '<h2>Tu Password para CIdEAA Miembros especiales es:</h2>';
		$mensaje .= '<p>'.$pwd.'</p>';
		
		mandarMail($mensaje, $mail);
		mostrarModal("Mail enviado");
	}
}
elseif(isset($_POST['entrar']))
{
	$usuario = trim($_POST['usuario']);
	$pwd 	= $_POST['cont'];
	
	$errLista = '';
	
	//usuario-mail
	if ($usuario == "") {
		$errLista .= mensajeErrValida("mail de usuario", 1); 
	}
	
	
	//Verificación de $errLista por Errores o sino capturar
	if(noHayErrores($errLista)){
		$tarjeta = validaUsuario($usuario,$pwd);
		
		if($tarjeta != ''){
			if($_POST['recordar'] == true)
			{
				setcookie("idPRO",$tarjeta, time()+(30*24*60*60),"/"); //para un mes
				setcookie("pwdPRO",      $pwd,      time()+(30*24*60*60),"/"); //para un mes
			}else{
				setcookie("idPRO",$tarjeta); //para una session
				setcookie("pwdPRO",      $pwd); //para una session
				setcookie("duracionPRO",'neto');
			}
			
			$dir="";
			if(isset($_GET['origen']))
			{
				$dir = $_GET['origen'];
				$dir=str_replace("|", "&", $dir);
			}
			
			if($dir=="")
			{
				$dir= $destino;
			}
			header("Location: ".$dir);
			echo '<script>window.location = "'.$dir.'";</script>';
			exit;
		}else{
			mostrarModal("Error en usuario o password");
		}
	}
}else{
	$usu 	= '';
	$pwd 	= NULL;
}

?>
<!DOCTYPE HTML>
<html lang="es">
<head>
	<? include "parts/head.html"; ?>
</head>

<body>
	<div id="contenedor">
    <?php include "parts/header.php" ?>
    	<section class="container">
        	<h2 class="text-center">Acceder a mi portal</h2><br>
            <form enctype="multipart/form-data" method="post" name="localizacion" target="_self" role="form" id="myForm">
                <? 
				
				echo campoTextoBasico("usuario","Usuario",$usuario, "icon-id-card-o");
				?>
                <div class="form-group">
                	<label for="cont" class="control-label sr-only col-xs-12 col-sm-12 col-md-3 col-lg-4"><?php echo $cont; ?></label>
                    <div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                        <span class="input-group-addon icon-key2" aria-hidden="true"></span>
                        <input type="password" class="form-control" value="<?php echo $pwd; ?>" placeholder="password" id="cont" name="cont" min="6">
                    </div>
            	</div>
                <div class="form-group">
                    <div class="checkbox checkbox-primary col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
                        <input type="checkbox" id="recordar" name="recordar" /><label for="recordar"> Recuerdame</label>
                    </div>
                </div>
                <div class="clearfix"></div>
                
				<?
				//CANCELAR Y SUBMIT 
				$destino = "registro.php";
				echo botonSubmit("entrar","Entrar", 'btn-success');
				echo botonBasico("nuevo","Soy nuevo", $destino, "btn-primary");
				?>
            	<div class="form-group">
                	<label for="olvidado" class="sr-only control-label col-xs-12 col-sm-12 col-md-3 col-lg-4">Mandar a mi mail mi password</label>
                    <div class="input-group col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
                        <input class="btn btn-default btn-block" type="submit" name="olvidado" id="olvidado" value="Olvid&eacute; mi password" />
                    </div>
            	</div>
            
        	</form>
             

    	</section>
    	<?php include "parts/footer.html" ?>
	</div><!--cierre contenedor!-->
	<script>
		
	</script>
    <?php
	if($mensajeModal)
	{
		echo "<script>mensajeModalA('".$mensaje."')</script>";
	}
	?>
</body>
</html>
