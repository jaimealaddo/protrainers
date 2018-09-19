<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';

$jerarquia = permiteAcceso(false);


$ahora = gmdate("Y-m-d H:i",time() + ($timeZone*60*60));
$fecha = "1930-01-01";

if($jerarquia > 65 && $_GET['idUsuario'])
{
	$idAct = $_GET['idUsuario'];
}

if(isset($_GET['desde']))
{
	$procede = $_GET['desde'];
}

if(isset($_POST['registrar']))
{
	//cargar variables
	$nombre	= validaCampoTXT(($_POST['nombre']), 4);
	$tipoTel = $_POST['telA'];
	$telTemp = validaCampoTelefono($_POST['telB']); 
	$mail = validEmail($_POST['mail']); //true or false
	$fecha = $_POST['fecha'];
	$idAct = $_POST['idAct'];
	//Validar formulario
	$errLista = '';
	
	//nombre
	if ($nombre === FALSE) {
		$nombre = $_POST['nombre'];
		$errLista .= "|El nombre debe tener al menos 4 caracteres|"; 
	}
	
	//telefono
	if ($telTemp == "") {
		$errLista .= "|El telefono ".$telTemp. " tiene un error|"; 
		$telData = $telTemp;
	}elseif($idAct==""){
		$telData = $telTemp;
		$query = "SELECT `tel` FROM `0010_usuarios` WHERE `tel` = '".$telData."'";
		$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		$noResult = mysqli_num_rows($resultadoSQL);
		if ($noResult != 0){
			$errLista .= "|Tu telefono ya esta registrado, pide ayuda al administrador|"; 
		}
	}else{
		$telData = $telTemp;
	}
	
	//mail
	if ($mail === FALSE){
		$errLista .= "|el e-mail tiene un error|"; 
		$mail = $_POST['mail'];
	}else{
		$mail = $_POST['mail'];
		$query = "SELECT `mail` FROM `0010_usuarios` WHERE `mail` = '".$mail."'";
		$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		$noResult = mysqli_num_rows($resultadoSQL);
		if ($noResult != 0){
			$errLista .= "|Tu e-mail ya esta registrado, pide ayuda al administrador|"; 
		}
	}
		
	//fecha 
	if ($fecha == "" || $fecha == "1930-01-01") {
		$errLista .= "|Indica tu fecha de nacimiento|"; 
		$fecha = "1930-01-01";
	}
	
	if($idAct!="")
	{
		$idRegistro = $idAct;
	}
	
	//Verificar la foto
	if($idAct != "") $errLista .= verificarFoto($_FILES["archivo"]);
	
	if(noHayErrores($errLista))
	{
		//include_once '../miembros - Copy/includes/Imagen.php';
		if($idAct==""){
			$query = "INSERT INTO `0010_usuarios` (`mail`, `nombre`, `tel`, `fechaNac`, `jerarquia`) ".
				"VALUES ('".$mail."','".$nombre."','".$telData."','".$fecha."','1')";
			$idUsuario = insertSQL($query, $mail, $_SERVER['REQUEST_URI'], __LINE__);
		}else{
			$idUsuario = $idAct;
			$query = "UPDATE `0010_usuarios` SET `nombre`='".$nombre."', `tel`='".$telData."', `fechaNac`='".$fecha."', `mail`='".$mail."' WHERE `idUsuario`='".$idUsuario."'";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		}
		
		//guardar foto
		$imagen = guardarFotoSimple($_FILES["archivo"], "usuarios", "usr_".$idUsuario);
		$query = "UPDATE `0010_usuarios` SET `foto` = '" .$imagen . "' WHERE `idUsuario` = '" . $idUsuario . "'";
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		
		//mandar mail
		$encriptado = encriptarNumero($idUsuario);
		$mensajeMail="<h2>Hola ".($nombre)." </h2> <p>Bienvenido/a a CIdEAA</p> tu c&oacute;digo de activaci&oacute;n es <a href='https://www.cideaa.com/protrainers/activar.php?user=".$mail."&code=".$encriptado . "'> $encriptado </a>, da clic o entra al siguente enlace y copia y p&eacute;galo<p> <p>www.cideaa.com/protrainers/activar.php</p> <h3>para evitar que nuestros mensajes entren al buz&oacute;n de correo no deseado, guarda nuestro correo en tu libreta de direcci&oacute;nes atencion@cideaa.com</h3>";
		
		mandarMail($mensajeMail, $mail);
		
		mostrarModal("Registro exitoso, busca tu codigo de activaci&oacute;n en tu mail <br/>(posiblemente en la carpeta de correo no deseado)");
		
		
		//$tarjetaR = "";
		//$nombre	= '';
		//$tipoTel = '';
		//$telData = '';
		//$mail 	= '';
		//$direccion =  '';
		//$idRegistro = "";
		//$pwd = "";
		//$pwdB = "";
	}
}
elseif($idAct != "")
{
	$query = "SELECT `nombre`, `mail`, `tel`, `fechaNac` FROM `0010_usuarios` WHERE `idUsuario` = '".$idAct."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$nombre=$datoA['nombre'];
		$mail=$datoA['mail'];
		$fecha = $datoA['fechaNac'];
		$tel = $datoA['tel'];
	}	
	
}

?>
<!DOCTYPE HTML>
<html lang="es">
<head>
	<? include 'parts/head.html'; ?>  
    <script src="js/maskedinput.js" type="text/javascript"></script>
</head>

<body>
	<div id="contenedor">
    <?php include "parts/header.php"; ?>
	
    	<section class="container">
        	<h2 class="text-center">Registro</h2><br>
            <form enctype="multipart/form-data" method="post" name="localizacion" target="_self" role="form" id="myForm">
				<input type="hidden" value="<? echo $idAct; ?>" name="idAct"/>
				<?
                echo campoTextoBasico("nombre","Tu nombre completo",$nombre,"icon-id-card-o"); // nombre
                echo campoMail('mail','e-mail',$mail,'');
				$tipoTelArray=array(1 => 'Cel', 2 => 'WhatsApp');
				echo campoSelectConTel("tel", 'Tipo de tel', "N&uacute;mero", $tipoTel, $telData, 'icon-chevron-small-down', 'glyphicon glyphicon-earphone', $tipoTelArray);
				?>
                <p class="text-center">Fecha de Nacimeinto</p>
				<div class="form-group">
					<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
						<span class="input-group-addon icon-calendar" aria-hidden="true"></span>
						<input class="form-control" type="hidden" required autocomplete="off" placeholder="fecha de nacimiento"  id="datetimepicker12" name="fecha"/>
					</div>
                    <p class="help-block col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">Fecha de nacimiento</p>
				</div>
                <div class="clearfix"></div>
                <p class="text-center" id="textMail">Selecciona una selfie, solo dejaremos pasar a la persona que aparece en esta foto.</p>
                <?
				echo campoFoto('archivo',"Selfie");
				//CANCELAR Y SUBMIT 
				$destino = "formulario.php";
				echo botonBasico("cancelar","Cancelar", $destino, "btn-danger");
				if(!($idRegistro > 0))	
					echo botonSubmit("registrar","Registrar", 'btn-success');
				else 
					echo botonSubmit("registrar","Actualizar", 'btn-warning');
				?>
            
            
        	</form>
             

    	</section>
    	<?php include "parts/footer.html" ?>
	</div><!--cierre contenedor!-->
	<script>
		$(function () {
			$('#datetimepicker12').datetimepicker({
				inline: true,
				locale: 'es',
				format:'YYYY-MM-DD',
				viewMode: 'decades',
				date:"<? echo $fecha; ?>",
				maxDate:"<? echo $ahora; ?>"
			});
		});
	<?php if($mensajeModal)echo " mensajeModalA('".$mensaje."')"; ?>
	</script>
</body>
</html>
