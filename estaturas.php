<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';

$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
$fecha = substr($ahora,0,10);

$jerarquia = permiteAcceso();
if($jerarquia < 60) salirJerarquia("nuevo.php");

if(isset($_GET['idUsuario']))
{
	$idUsr = $_GET['idUsuario'];
}
$nombre = "";
$listaNombres = array();
$cumpleCerca = false;
$tarjetaComprobada = false;

if(isset($_POST['registrar'])){
	$errLista = '';
	$estatura = validaCampoTXT('estatura',1, FALSE, $_POST, $errLista);
	$peso = validaCampoTXT('peso',1, FALSE, $_POST, $errLista);
	$mision = validaCampoSelect('mision',$_POST,$errLista);
	$idUsrTemp = trim($_POST['idUsuario']); //Remover caracteres
	if($idUsrTemp != "") $idUsr = $idUsrTemp;
	else $errLista .= "|Error ID vacio|";
	
	$cambioMision = false;
	
	$lunH = $_POST['lunH'];
	$lunM = $_POST['lunM'];
	
	$marH = $_POST['marH'];
	$marM = $_POST['marM'];
	
	$mieH = $_POST['mieH'];
	$mieM = $_POST['mieM'];
	
	$jueH = $_POST['jueH'];
	$jueM = $_POST['jueM'];
	
	$vieH = $_POST['vieH'];
	$vieM = $_POST['vieM'];
	
	$sabH = $_POST['sabH'];
	$sabM = $_POST['sabM'];
	
	$domH = $_POST['domH'];
	$domM = $_POST['domM'];
	
	if(($lunH == "" && $lunM != "") || ($lunH != "" && $lunM == "")) $errLista .= "Falta un dato para el horario de Lunes";
	elseif($lunH != "" && $lunM != "")
	{
		$parteA = ",`lun`";
		$parteB = ",'".$lunH.":".$lunM."'";
		$cambioMision = true;
	}
	if(($marH == "" && $marM != "") || ($marH != "" && $marM == "")) $errLista .= "Falta un dato para el horario de Martes";
	elseif($marH != "" && $marM != "")
	{
		$parteA .= ",`mar`";
		$parteB .= ",'".$marH.":".$marM."'";
		$cambioMision = true;
	}
	if(($mienH == "" && $mieM != "") || ($mieH != "" && $mieM == "")) $errLista .= "Falta un dato para el horario de Mi&eacute;rcoles";
	elseif($mienH != "" && $mieM != "")
	{
		$parteA .= ",`mie`";
		$parteB .= ",'".$mieH.":".$mieM."'";
		$cambioMision = true;
	}
	if(($jueH == "" && $jueM != "") || ($jueH != "" && $jueM == "")) $errLista .= "Falta un dato para el horario de Jueves";
	elseif($jueH != "" && $jueM != "")
	{
		$parteA .= ",`jue`";
		$parteB .= ",'".$jueH.":".$jueM."'";
		$cambioMision = true;
	}
	if(($vieH == "" && $vieM != "") || ($vieH != "" && $vieM == "")) $errLista .= "Falta un dato para el horario de Viernes";
	elseif($vieH != "" && $vieM != "")
	{
		$parteA .= ",`vie`";
		$parteB .= ",'".$vieH.":".$vieM."'";
		$cambioMision = true;
	}
	
	if(($sabH == "" && $sabM != "") || ($sabH != "" && $sabM == "")) $errLista .= "Falta un dato para el horario de Sabado";
	elseif($sabH != "" && $sabM != "")
	{
		$parteA .= ",`sab`";
		$parteB .= ",'".$sabH.":".$sabM."'";
		$cambioMision = true;
	}
	
	if(($domH == "" && $domM != "") || ($domH != "" && $domM == "")) $errLista .= "Falta un dato para el horario de Sabado";
	elseif($domH != "" && $domM != "")
	{
		$parteA .= ",`dom`";
		$parteB .= ",'".$domH.":".$domM."'";
		$cambioMision = true;
	}
	
	$idCategoria="";
	$query ="SELECT `idCategoria` FROM `0050_categorias` WHERE `estaturaIni` < '".$estatura."' AND `estaturaFin` >= '".$estatura."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$idCategoria = $datoA['idCategoria'];
	}
	if($idCategoria == "")
	{
		$errLista .= "|No hay categor&iacute;a para esta estatura, pide a un administrador que la agregue para que puedas hacer este registro|";
	}
	
	$query = "SELECT `estatura`, `peso` FROM `0011_estaturas` WHERE  `idUsuario` = '".$idUsr."' ORDER BY `idEstatura` DESC LIMIT 1";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$estaturaBD = $datoA['estatura'];
		$pesoBD = $datoA['peso'];
	}
	if($estatura != $estaturaBD || $peso != $pesoBD)$cambioEstatura=true; else $cambioEstatura = false;
	
	$query = "SELECT `idMision` FROM `0012_usuarioMision` WHERE  `idUsuario` = '".$idUsr."' ORDER BY `idUsuarioMision` DESC LIMIT 1";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$misionBD = $datoA['idMision'];
	}
	if($mision != $misionBD) $cambioMision=true;
	
	
	if(noHayErrores($errLista))
	{
		if($cambioEstatura){
			$query = "UPDATE `0010_usuarios` SET `idCategoria`= '".$idCategoria."', `activo`= '1' WHERE `idUsuario` = '".$idUsr."'";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			
			$query = "INSERT INTO `0011_estaturas`(`idUsuario`, `fecha`, `estatura`, `peso`) VALUES ('".$idUsr."','".$fecha."','".$estatura."','".$peso."')";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		}
		if($cambioMision){
			
			
			$query = "INSERT INTO `0012_usuarioMision`(`idUsuario`, `fecha`, `idMision`".$parteA.") VALUES ('".$idUsr."','".$fecha."','".$mision."'".$parteB.")";
			$idFinal=insertSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			//echo $query;
			$query = "UPDATE `0010_usuarios` SET `idUsuarioMision`= '".$idFinal."', `activo`= '1' WHERE `idUsuario` = '".$idUsr."'";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		}
		
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
		
		
		$query = "SELECT `estatura`, `peso` FROM `0011_estaturas` WHERE `idUsuario` = '".$idUsr."' ORDER BY `idEstatura` DESC LIMIT 1 ";
		$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		while($datoA=mysqli_fetch_assoc($resultadoSQL)){
			$peso = $datoA['peso'];
			$estatura = $datoA['estatura'];
		}
		
		$query = "SELECT `idMision`, `lun`,`mar`,`mie`,`jue`,`vie`,`sab`,`dom` FROM `0012_usuarioMision` WHERE  `idUsuario` = '".$idUsr."' ORDER BY `idUsuarioMision` DESC LIMIT 1";
		$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		while($datoA=mysqli_fetch_assoc($resultadoSQL)){
			$mision = $datoA['idMision'];
			$lunH = substr($datoA['lun'],0,2);
			$lunM = substr($datoA['lun'],3,2);
			$marH = substr($datoA['mar'],0,2);
			$marM = substr($datoA['mar'],3,2);
			$mieH = substr($datoA['mie'],0,2);
			$mieM = substr($datoA['mie'],3,2);
			$jueH = substr($datoA['jue'],0,2);
			$jueM = substr($datoA['jue'],3,2);
			$vieH = substr($datoA['vie'],0,2);
			$vieM = substr($datoA['vie'],3,2);
			$sabH = substr($datoA['sab'],0,2);
			$sabM = substr($datoA['sab'],3,2);
			$domH = substr($datoA['dom'],0,2);
			$domM = substr($datoA['dom'],3,2);
		}
		
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
        	
        	<form method="post" name="acceso" target="_self" role="form" id="myForm">
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
                            <div>
                            	<img class="mediaAnuncio media-object" src="<? echo $imagen; ?>" alt="selfie" style="max-width: 100%;">
                            </div>
                        </div>
                        <div class="media-body" style="vertical-align:middle; ">
                        	<h3 class="media-heading "><? echo $nombre; ?> <span class="label label-<? echo $color; ?>" style="font-size: 50%;"> <? echo $activo; ?></span></h3>
                            
                            	<p>Tel: <? echo $tel; ?></p>
                                
                            	<div style="margin-bottom: 6px;">
                                <span class="label label-info row" style="font-size: 110%; display: block; margin:0px;">
                                    <div class="col-xs-12 col-md-6 text-left" style="margin-bottom: 3px;">Estatura: <input type="number" step=".01" class="form-control" style="min-width:55px; color:#000000; width: calc(100% - 73px);display: inline-block;" name="estatura" value="<? echo $estatura; ?>" /></div>
                                    <div class="col-xs-12 col-md-6 text-left" style="margin-bottom: 3px;">Peso: <input type="number" step=".01" class="form-control" style="min-width:55px; color:#000000; width: calc(100% - 46px);display: inline-block;" name="peso" value="<? echo $peso; ?>" /></div>
                                </span>
                                </div>
                                
                                <div style="margin-bottom: 10px;">
                                <span class="label label-info row" style="font-size: 110%; display: block; margin:0px;">
                                    <div class="col-xs-12 text-left" style="margin-bottom: 3px;">Misi&oacute;n: 
                                        <select name="mision" class="form-control" style="min-width:55px; color:#000000; width:calc(100% - 57px);display: inline-block;" required>
                                        	<? $misiones=misiones(); 
											if($mision == "")$sel=" selected"; else $sel=""; ?>
                                        	<option value=""<? echo $sel; ?> disabled>Misi&oacute;n</option>
                                            <? foreach($misiones as $key => $unaMision){ 
												if($mision == $key)$sel=" selected"; else $sel="";
												echo '<option value="'.$key.'"'.$sel.'>'.$unaMision.'</option>';
											} ?>
                                        </select>
                                        
                                    </div>
                                </span>
                                </div>
                                
                                <?
                                
								echo seleccionHoras('lun', 'Lun', $lunH, $lunM,false);
								echo seleccionHoras('mar', 'Mar', $marH, $marM,false);
								echo seleccionHoras('mie', 'Mie', $mieH, $mieM,false);
								echo seleccionHoras('jue', 'Jue', $jueH, $jueM,false);
								echo seleccionHoras('vie', 'Vie', $vieH, $vieM,false);
								echo seleccionHoras('sab', 'Sab', $sabH, $sabM,false);
								echo seleccionHoras('dom', 'Dom', $domH, $domM,false);
                                ?>
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
                                        <td style="vertical-align:middle;"><a href="estaturas.php?idUsuario=<?php echo $key; ?>" class="btn btn-primary btn-block btn-xs"><?php echo $key; ?></a></td>
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
                <br />
                <? 
				echo botonSubmit("busca","Buscar", 'btn-primary');
				echo botonBasico("limpiar","Limpiar","estaturas.php", "btn-warning");
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
