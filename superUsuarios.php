<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';

$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
$fecha = substr($ahora,0,10);

$jerarquia = permiteAcceso();
if($jerarquia < 65) salirJerarquia("nuevo.php");

if(isset($_GET['idUsuario']))
{
	$idUsr = $_GET['idUsuario'];
}
$nombre = "";
$listaNombres = array();
$cumpleCerca = false;
$tarjetaComprobada = false;

if(isset($_POST['registrar'])){
	$jerarquiaUsr = $_POST['jerarquia'];
	$nombreUsuario = validaCampoTXT('nombreUsuario',2,TRUE, $_POST, $errLista);
	$idUsrTemp = trim($_POST['idUsuario']); //Remover caracteres
	if($idUsrTemp != "") 
		$idUsr = $idUsrTemp;
	else
		$errLista .= "|Error ID vacio|";
	//Validar formulario
	$errLista = '';
	
	//jerarquia
	if(is_numeric($jerarquiaUsr)){
		if($jerarquiaUsr > $jerarquia)
		{
			$errLista .= "|No puedes registrar una jerarquia mayor a la tuya|"; 
		}elseif($jerarquiaUsr < 0){
			$errLista .= "|La jerarquia debe ser un valor positivo|"; 
		}
	}else{
		$errLista .= "|La jerarquia debe ser un valor numerico|"; 
	}
	if(noHayErrores($errLista))
	{
		$query = "";
		$modificado = "<br/>";
		
			$query = "UPDATE `0010_usuarios` SET `jerarquia`='".$jerarquiaUsr."', `nombre`='".$nombreUsuario."' WHERE `idUsuario` = '".$idUsr."'";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			
			$query = "SELECT `mail`, `nombre` FROM `0010_usuarios` WHERE `idUsuario` = '".$idUsr."'";
			$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			while($datoA=mysqli_fetch_assoc($resultadoSQL)){
				$mail = $datoA['mail'];
				$nombreUsuario = $datoA['nombre'];
			}
			$encriptado = encriptarNumero($idUsr);
			$mensajeMail="<h2>Hola ".($nombreUsuario)." </h2> <p>Bienvenido/a a CIdEAA</p> tu c&oacute;digo de activaci&oacute;n es <a href='https://www.cideaa.com/ERP/activar.php?user=".$mail."&code=".$encriptado . "'> $encriptado </a>, da clic o entra al siguente enlace y copia y p&eacute;galo<p> <p>www.cideaa.com/miembros/activar.php</p> <h3>para evitar que nuestros mensajes entren al buz&oacute;n de correo no deseado, guarda nuestro correo en tu libreta de direcci&oacute;nes atencion@cideaa.com</h3>";
			mandarMail($mensajeMail, $mail);
		
		mostrarModal("Registro exitoso".$modificado);
	}
}
if(isset($_POST['busca']) || $idUsr != "")
{
	
	$idUsrTemp = trim($_POST['idUsuario']); //Remover caracteres
	if($idUsrTemp != "") $idUsr = $idUsrTemp;
	$mail = $_POST['mail'];
	$nombreUsuario = $_POST['nombreUsuario'];
	$tarjetaBien = false;
	if(is_numeric($idUsr) && strlen($idUsr) > 0)
	{
		$tarjetaBien = true;
	}
	
	if(!$tarjetaBien && $mail != "")
	{
		$query = "SELECT `idUsuario` FROM `0010_usuarios` WHERE `mail` = '".$mail."'";
		$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		while($datoA=mysqli_fetch_assoc($resultadoSQL)){
			$idUsr = $datoA['idUsuario'];
			$tarjetaBien = true;
		}
	}
	
	if($tarjetaBien){
		$query = "SELECT `mail`, `nombre`, `tel`, `jerarquia`, `fechaNac`, `foto`, `activo`,`registro` FROM `0010_usuarios` WHERE `idUsuario` = '".$idUsr."'";
		$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		while($datoA=mysqli_fetch_assoc($resultadoSQL)){
			$mail = $datoA['mail'];
			$nombre = $datoA['nombre'];
			$tel = $datoA['tel'];
			$jerarquiaUsr = $datoA['jerarquia'];
			$fechaNac = $datoA['fechaNac'];
			$imagen = $datoA['foto'];
			$activo = $datoA['activo'];
			$fechaReg = $datoA['registro'];
			
			$tarjetaComprobada = true;
		}
		if($activo == 1)
		{
			$activo = "Activo";
			$color = "success";
		}else{
			$activo = "Inactivo";
			$color = "warning";
		}
		if($imagen == "") $imagen = "fotos/usrSinFoto.jpg";
		$nombreUsuario = $nombre;
		//CUMPLEANOS
		$mesDiaNac = substr($fechaNac,5,5);
		$yearNac = substr($fechaNac,0,4);
		$yearThis = substr($fecha,0,4);
		$anos = $yearThis - $yearNac;
		$cumple = substr($fecha,0,5).$mesDiaNac;
		if($cumple == $fecha)
		{
			$cumpleHoy = true;
		}
		else
		{
			$cumpleHoy = false;
			$cumpleUnix = strtotime($cumple);
			$hoyUnix = strtotime($fecha);
			if(($hoyUnix >= $cumpleUnix && ($hoyUnix - (7*24*60*60)) < $cumpleUnix) || ($hoyUnix <= $cumpleUnix && ($hoyUnix + (7*24*60*60)) > $cumpleUnix))
			{
				$cumpleCerca=true;
			}
		}
		
		//ANIVERSARIO DE SER MIEMBRO
		$mesDiaReg = substr($fechaReg,5,5);
		$yearReg = substr($fechaReg,0,4);
		$anosReg = $yearThis - $yearReg;
		$aniversario = substr($fecha,0,5).$mesDiaReg;
		if($aniversario == $fecha)
		{
			$aniversarioHoy = true;
		}
		else
		{
			$aniversarioHoy = false;
			$aniversarioUnix = strtotime($aniversario);
			$hoyUnix = strtotime($fecha);
			if(($hoyUnix >= $aniversarioUnix && ($hoyUnix - (7*24*60*60)) < $aniversarioUnix) || ($hoyUnix <= $aniversarioUnix && ($hoyUnix + (7*24*60*60)) > $aniversarioUnix))
			{
				$aniversarioCerca=true;
			}else{
				$aniversarioCerca=false;
			}
		}
	}elseif($nombreUsuario != ""){
		$query = "SELECT `idUsuario`, `user`, `nombre`, `mail` FROM `0010_usuarios` WHERE `nombre` LIKE '%".$nombreUsuario."%'";
		$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		while($datoA=mysqli_fetch_assoc($resultadoSQL)){
			$listaNombres[$datoA['idUsuario']] = $datoA['nombre'];
			$listaMails[$datoA['idUsuario']] = $datoA['mail'];
			$listaUser[$datoA['idUsuario']] = $datoA['user'];
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
        	<ul>
                <li>jerarquia 60 </li>
                <li>jerarquia 65 pueden registrar superUsuarios y modificar datos de usuarios</li>
                <li>jerarquia 70 </li>
                <li>jerarquia 75 pueden agregar m&oacute;dulos, misiones</li>
                <li>jerarquia 77 </li>
                <li>jerarquia 80 </li>
                <li>jerarquia 90 ----</li>
            </ul>
        	<form action="superUsuarios.php" method="post" name="acceso" target="_self" role="form" id="myForm">
				<?
				if($nombre != "") $idActivo = false; else $idActivo=true;
                echo campoTextoBasico("nombreUsuario","Nombre ",$nombreUsuario, "icon-search", false, "buscar por nombre");
                echo campoMail("mail","mail usuario",$mail, "icon-id-badge",false);
				echo campoTextoNumEnt("idUsuario","Buscar por ID",$idUsr, "icon-key",false,"","","",$idActivo)
                ?>
                
                <div class="clearfix"></div>
				<? if($nombre != ""){ ?>
                <div class="well">
                	<div class="media">
                    	
                        <div class="media-left media-middle datosAnuncio fotoAnuncio">
                            <a href="#">
                            	<img class="mediaAnuncio media-object" src="<? echo $imagen; ?>" alt="selfie" style="max-width: 100%;">
                            </a>
                        </div>
                        <div class="media-body" style="vertical-align:middle; ">
                            <h3 class="media-heading"><? echo $nombre; ?> <span class="label label-<? echo $color; ?>" style="font-size: 50%;"> <? echo $activo; ?></span></h3>
                            	<p>Tel: <? echo $tel; ?></p>
                                <p><a href="registro.php?idUsuario=<?php echo $idUsr; ?>" class="btn btn-primary btn-block btn-xs">Cambiar</a></p>
                            <? if($jerarquiaUsr < $jerarquia){ ?>
                            	<div style="margin-bottom: 6px;">
                                	<span class="label label-info row" style="font-size: 110%; display: block; margin:0px;">
                                    	<div class="col-xs-12 col-md-6 text-left" style="margin-bottom: 3px;">Usuario nivel <input type="number" style="min-width:55px; color:#000000; width: calc(100% - 100px);display: inline-block;" name="jerarquia" value="<? echo $jerarquiaUsr; ?>" class="form-control" /></div>
                                    </span>
                                </div>
                            <? 	}else{ ?>
                            	<div style="margin-bottom: 6px;">
                            		<span class="label label-default row" style="font-size: 110%; display: block; margin:0px;">
                                    	<div class="col-xs-12 col-md-6 text-left" style="margin-bottom: 3px;">Usuario nivel <input type="number" readonly style="min-width:55px; color:#000000; width: calc(100% - 100px);display: inline-block;" name="jerarquia" value="<? echo $jerarquiaUsr; ?>" class="form-control" /></div>
                                    </span>
                                </div>
                            <? 	} ?>
                            <? if($cumpleHoy){ ?> 
                            	<h2>Hoy es su cumplea&ntilde;os!</h2>
                                <p>Cumple <? echo $anos; ?> a&ntilde;os</p>
                            <? }elseif($cumpleCerca){ ?>
                            	<h4>Est&aacute; cerca su cumplea&ntilde;os!</h4>
                                <p>Cumple <? echo $anos; ?> a&ntilde;os el d&iacute;a <? echo fechaSola($cumple); ?></p>
                            <? } ?>
                            <? if($aniversarioHoy){ ?>
                            	<h2>Hoy es el aniversario de ser nuestro miembro!</h2>
                                <p>Cumple <? echo $anosReg; ?> a&ntilde;os</p>
                            <? }elseif($aniversarioCerca){ ?>
                            	<h4>Est&aacute; cerca el aniversario de ser miembro!</h4>
                                <p>Cumple <? echo $anosReg; ?> a&ntilde;os el d&iacute;a <? echo fechaSola($aniversario); ?></p>
                            <? } ?>
                            
                    	</div>
                	</div>
                </div>
                <? }elseif(count($listaNombres) != 0){ ?>
                <div class="panel panel-default">
                    <div class="panel-heading">	
                        <h3 class="panel-title">Nombres encontrados:</h3>
                    </div>
                    <div class="panel-body">
                    	<div class="well" style="background: #c2c2c2;">
                            <div class="table-responsive">
                                <table class="table table-striped table-condensed"> 
                                    <tr>
                                        <th scope="row" width="45px">ID</th>
                                        <th scope="row">Nombre</th>
                                        <th scope="row">usuario</th>
                                        <th scope="row">e-mail</th>
                                    </tr> 
                        <? foreach($listaNombres as $key => $unNombre){ ?>
                                	<tr id="asientos">
                                        <td style="vertical-align:middle;"><a href="superUsuarios.php?idUsuario=<?php echo $key; ?>" class="btn btn-primary btn-block btn-xs"><?php echo $key; ?></a></td>
                                        <td style="vertical-align:middle;"><span class="textos"><?php echo ucwords($unNombre); ?></span></td>
                                        <td style="vertical-align:middle;"><span class="textos"><?php echo ($listaUser[$key]); ?></span></td>
                                        <td style="vertical-align:middle;"><span class="textos"><?php echo ($listaMails[$key]); ?></span></td>
                                    </tr> 
						<?	} ?>
                            	</table>
                        	</div>
						</div>					 
					</div>
                </div>
             <?	} ?>
            	<div class="clearfix"></div>
                <? 
				echo botonSubmit("busca","Buscar", 'btn-primary');
				echo botonBasico("limpiar","Limpiar","superUsuarios.php", "btn-warning");
				if($nombre != "") echo botonSubmit("registrar","Registrar", 'btn-success'); ?>
            </form>
    	</section>
    	<?php include "parts/footer.html" ?>
	</div><!--cierre contenedor!-->
	<script>
		<?php if($mensajeModal) echo "mensajeModalA('".$mensaje."')"; ?>
	</script>
    
</body>
</html>
