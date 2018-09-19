<?php 
include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';
if(permiteAcceso())
{
	$idUsuario = $_COOKIE['idERP'];
	$jerarquia = jerarquiaTarjeta($idUsuario);
}else{
	salirJerarquia("index.php");
}
$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));

$prefData = "";	
if(isset($_POST['preferencia'])) 
{
	$prefData = $_POST['preferencia'];
}elseif(isset($_GET['preferencia'])){
	$prefData = $_GET['preferencia'];
}

if(isset($_POST['registrar'])) 
{

	//Validar formulario
	$errLista = '';
	if($prefData == "")
	{
		$errLista .= "|Indica una preferencia|";
	}else{
		$registros = explode(",",$_POST['registros']);
		$campos = explode(",",$_POST['control']);
		foreach($campos as $key => $cadaCampo)
		{
			$datoTemp = trim($_POST[$cadaCampo]);
			//Si es telefono
			if($prefData == 3) $datoTemp = str_replace(array("(", ")", "-"), "", $datoTemp);
			if($datoTemp == "")
				$errLista .= "|" .$cadaCampo . " no tiene datos|";
			else
				$campo[$registros[$key]]=$datoTemp;
		}
	}
	
	if(noHayErrores($errLista)) //mostrar errores
	{	
		if($prefData==7){
			guardarFoto($_FILES["archivo"], "usuario", "usuarios", $tarjetaR);
		}elseif($prefData==1 || $prefData==2 || $prefData==3){
			$query = "UPDATE `_0010_usuario` SET ";
			foreach($campo as $key => $datoTemp)
			{
			 	$query .= "`".$key."`='".$datoTemp."',";
			}
			$query = substr($query,0,-1) . "WHERE `card`='".$tarjeta."'";
			ejecutarSQL($query, $user, $_SERVER['REQUEST_URI'], __LINE__);
		}
		mostrarModal("Registro Exitoso");
		$prefData = "";
	}
}
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
	<? include "parts/head.html"; ?>
    <script src="js/maskedinput.js" type="text/javascript"></script>   
</head>
<body>
	<?php include "parts/header.php" ?>
    <section class="container">
        <div class="page-header">
            <h1><? echo ucwords(nombreById($tarjeta)) ;?> <small><? echo userMailById($tarjeta); ?></small></h1>
        </div>
    </section>
<?php //CARGAR PREFERENCIAS DISPONIBLES EN FORMULARIO
/*
$query = "SELECT `cPreferencia`,`nombrePreferencia` FROM `_0012_preferencias` WHERE (`jerarquiaIni` <='".$jerarquia."' AND `jerarquiaFin` >= '".$jerarquia."') OR (`jerarquiaIni` IS NULL AND `jerarquiaFin` IS NULL)";
$resultadoSQL_lan = ejecutarSQL($query, $user, $_SERVER['REQUEST_URI'], __LINE__);
while($datoA=mysqli_fetch_assoc($resultadoSQL_lan)){
	$arregloPreferencia[$datoA['cPreferencia']]=ucwords($datoA['nombrePreferencia']);
}
*/
?>
    <section class="container">
    	<section class="main">
        	<form enctype="multipart/form-data" action="<?php echo dameURL(); ?>" method="post" name="preferenciasForm" target="_self" role="form" id="preferenciasForm">
                <h3 align="center"><?php echo ucwords($nombreSucursal); ?></h3>
                <h4 align="center"><?php echo $nombreCatTxt; ?></h4>
                
				<!-- PREFERENCIA -->
				<? echo campoSelectBasico("preferencia","Preferencias",$prefData,"icon-heart", $arregloPreferencia,true,true); ?>
				<div class="PRIMER_wrapper table-responsive">
<?
	switch($prefData){
		case 1: //Direccion
			$query = "SELECT `dir` FROM `_0010_usuario` WHERE `card` = '".$tarjeta."';";
			$resultadoSQL = ejecutarSQL($query, $user, $_SERVER['REQUEST_URI'], __LINE__);
			while($dato = mysqli_fetch_assoc($resultadoSQL))
			{
				$valor = str_replace("<br />","\r\n", $dato['dir']);
			}
			echo campoTextArea("direccion","e-mail",$valor, "icon-location2", true, "Tu direccion es importante para que podamos pasar por ti sin problemas", 6);
			echo '<input name="control" type="hidden" value="direccion"/>';
			echo '<input name="registros" type="hidden" value="dir"/>';
			break;
		case 2: //mail
			$query = "SELECT `user`, `pwd`, `nombre`, `card`, `tel`, `tipoTel`, `fechaNac`, `dir`, `registro`, `activo`, `imagen` FROM `_0010_usuario` WHERE `card` = '".$tarjeta."';";
			$resultadoSQL = ejecutarSQL($query, $user, $_SERVER['REQUEST_URI'], __LINE__);
			while($dato = mysqli_fetch_assoc($resultadoSQL))
			{
				$valor = $dato['user'];
			}
			echo campoMail("user","e-mail",$valor, "icon-user", true, "tu mail es tu usuario de acceso");
			echo '<input name="control" type="hidden" value="user"/>';
			echo '<input name="registros" type="hidden" value="user"/>';
			break;
		case 3: //telefono
			$tipoTelArray=array('Fijo', 'Cel', 'WhatsApp');
			$query = "SELECT `tel`, `tipoTel` FROM `_0010_usuario` WHERE `card` = '".$tarjeta."';";
			$resultadoSQL = ejecutarSQL($query, $user, $_SERVER['REQUEST_URI'], __LINE__);
			while($dato = mysqli_fetch_assoc($resultadoSQL))
			{
				$tipoTel = $dato['tipoTel'];
				$valor = $dato['tel'];
			}
			echo campoSelectConTel("tel", 'Tipo de tel', "N&uacute;mero",$tipoTel+0, $valor, 'icon-chevron-small-down', 'glyphicon glyphicon-earphone', $tipoTelArray);
			echo '<script> $("#telB").mask("(999)999-99-99"); </script>';
			echo '<input name="control" type="hidden" value="telA,telB"/>';
			echo '<input name="registros" type="hidden" value="tipoTel,tel"/>';
        	break;
	}
	
?>           
                </div>
                <div class="clearfix"></div>
                <? if($prefData != ""){ 
					//CANCELAR Y SUBMIT 
					$destino = "preferencias.php";
					echo "<br/><br/>";
					echo botonSubmit("registrar","Registrar", 'btn-primary');
					echo botonBasico("cancelar","Cancelar", $destino, "btn-info");
				
                
                } ?>
            </form>
        </section>
	</section>
	<?php include "footer.html" ?>
    <script>
		$(document).ready(function(){
			<? 
			//agregarCampoJS(20, "valor", "PRIMER", $HTML);
			?>
		});
		<? if($mensajeModal) echo "mensajeModalA('".$mensaje."')"; ?>
	</script>
</body>
</html>