<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';
$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
$primerCarga=true;
$jerarquia = permiteAcceso();
if($jerarquia < 75 || !isset($_GET['idMision'])) salirJerarquia("nuevo.php");
$idMision = $_GET['idMision'];

$idModuloAlterno=array();

$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
if(isset($_GET['id']))
{
	$idActivo = $_GET['id'];
}
if(isset($_POST['borrar']))
{
	$idActivo=$_POST['id'];
	if($idActivo != "")
	{
		$query = "DELETE FROM `0041_misionesModulos` WHERE `idMisionActividad`= '".$idActivo."'";
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		$idActivo ="";
		$modulo="";
		
		mostrarModal("Borrado");
	}
	
}elseif(isset($_POST['registrar']))
{	
	$errLista = '';
	$noModulos = 0;
	$idModuloAlterno=$_REQUEST['moduloA'];
	foreach($idModuloAlterno as $key => $unModulo)
	{
		if($unModulo == "")$errLista .= "|El modulo ". ($key+1) ." no puede estar vac&iacute;o|";
	}
	$noModulos=count($idModuloAlterno);
	if($noModulos < 1) $errLista .= "|registra al menos un m&oacute;dulo|";
	
	$despuesDe=$_POST['despuesDe'];
	$idActivo=$_POST['id'];
	
	
	//$errLista .= "PAUSA";
	//Verificación de $errLista por Errores o sino capturar
	if(noHayErrores($errLista))
	{
		$arreglo=array();
		if($idActivo == "")
		{
			if($despuesDe!="A-L-L" && $despuesDe != "")
			{
				$query="SELECT `orden` FROM `0041_misionesModulos` WHERE `idMisionActividad` = '".$despuesDe."'";
				$resultadoSQL = ejecutarSQL($query, $tarjeta, $_SERVER['REQUEST_URI'], __LINE__);
				while($datoA=mysqli_fetch_assoc($resultadoSQL)){
					$orden = $datoA['orden'];
				}
				$query = "UPDATE `0041_misionesModulos` SET `orden`=(`orden` + 1) WHERE `idMision`= '".$idMision."' AND `orden`>='".($orden + 1)."'";
				ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				$query="INSERT INTO `0041_misionesModulos`(`idMision`, `idModulo`, `orden`) VALUES ('".$idMision."','".$idModuloAlterno[0]."','".($orden + 1)."')";
				$idActivo = insertSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			}else{
				$query = "UPDATE `0041_misionesModulos` SET `orden`=(`orden` + 1) WHERE `idMision`= '".$idMision."'";
				ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				$query="INSERT INTO `0041_misionesModulos`(`idMision`, `idModulo`, `orden`) VALUES ('".$idMision."','".$idModuloAlterno[0]."','1')";
				$idActivo = insertSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				
			}
		}else{
			if($despuesDe!="A-L-L" && $despuesDe != "")
			{
				$query="SELECT `orden` FROM `0041_misionesModulos` WHERE `idMisionActividad` = '".$despuesDe."'";
				$resultadoSQL = ejecutarSQL($query, $tarjeta, $_SERVER['REQUEST_URI'], __LINE__);
				while($datoA=mysqli_fetch_assoc($resultadoSQL)){
					$orden = $datoA['orden'];
				}
				$query="UPDATE `0041_misionesModulos` SET `orden`=(`orden` + 1) WHERE `idMision`= '".$idMision."' AND `orden`>='".($orden + 1)."'";
				ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				$query="UPDATE `0041_misionesModulos` SET `idModulo`='".$idModuloAlterno[0]."', `idMision`='".$idMision."', `orden`='".($orden + 1)."' WHERE `idMisionActividad` = '".$idActivo."'";
				ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				
			}else{
				$query = "UPDATE `0041_misionesModulos` SET `orden`=(`orden` + 1) WHERE `idMision`= '".$idMision."'";
				ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				$query="UPDATE `0041_misionesModulos` SET `idMision`='".$idMision."', `idModulo`='".$idModuloAlterno[0]."', `orden`='1' WHERE `idMisionActividad` = '".$idActivo."'";
				ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			}
		}
		$query = "DELETE FROM `0042_modulosAlternos` WHERE `idMisionModulo` = '".$idActivo."'";
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		if($noModulos>1)
		{
			$query = "";
			for($i=1;$i<$noModulos;$i++)
			{
				$query = "('".$idActivo."','".$idModuloAlterno[$i]."'),";
			}
			$query = substr($query,0,-1);
			$query = "INSERT INTO `0042_modulosAlternos`(`idMisionModulo`, `idModulo`) VALUES ".$query;
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		}
		
		
		$idActivo ="";
		$idModuloAlterno=array();
		
		mostrarModal("Registro exitoso");
	}
}elseif($idActivo != ""){
	
	$query = "SELECT `idMision`, `idModulo`, `orden` FROM `0041_misionesModulos` WHERE `idMisionActividad`= '".$idActivo."'";
	$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoB=mysqli_fetch_assoc($resultadoSQL_B))
	{
		$idModuloAlterno[0]=$datoB['idModulo'];
		if($idMision!=$datoB['idMision'])echo "ERROR, la mision no coincide";
		$orden = $datoB['orden'];
	}
	$query = "SELECT `idModulo` FROM `0042_modulosAlternos` WHERE `idMisionModulo` ='".$idActivo."'";
	$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoB=mysqli_fetch_assoc($resultadoSQL_B))
	{
		$idModuloAlterno[]=$datoB['idModulo'];
	}
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
        	<h2 class="text-center">Asignar m&oacute;dulos a la misi&oacute;n:<br/><b><? echo nombreMision($idMision); ?></b></h2>
            <p>Asignar los m&oacute;dulos que componen la misi&oacute;n</p>
            <form method="post" name="localizacion" target="_self" role="form" id="myForm">
				<input type="hidden" name="id" value="<? echo $idActivo; ?>"/>
				<div class="PRIMER_wrapper" id="PRIMER_wrapper" style="margin-bottom:12px;">
					<?
					$i=0;
					$listaModulos=modulos();
					do{
						echo campoSelectMultiple("modulo","M&oacute;dulo",$idModuloAlterno[$i], "icon-sitemap", $listaModulos, true, ($i+1));
						$i++;
					}while($i<count($idModuloAlterno));
                    ?>
                </div>
                <?
				$orden = "";
				if($idActivo != "")
				{
					$query="SELECT `orden` FROM `0041_misionesModulos` WHERE `idMisionActividad` = '".$idActivo."'";
					$resultadoSQL = ejecutarSQL($query, $tarjeta, $_SERVER['REQUEST_URI'], __LINE__);
					while($datoA=mysqli_fetch_assoc($resultadoSQL)){
						$orden = $datoA['orden'];
					}
				}
				if($orden != "")
				{
					$query="SELECT `idMisionActividad` FROM `0041_misionesModulos` WHERE `idMision` = '".$idMision."' AND `orden` < '".$orden."' ORDER BY `orden` DESC LIMIT 1";
					$resultadoSQL = ejecutarSQL($query, $tarjeta, $_SERVER['REQUEST_URI'], __LINE__);
					while($datoA=mysqli_fetch_assoc($resultadoSQL)){
						$despuesDe = $datoA['idMisionActividad'];
					}
				}else{
					$query="SELECT `idMisionActividad` FROM `0041_misionesModulos` WHERE `idMision` = '".$idMision."' ORDER BY `orden` DESC LIMIT 1";
					$resultadoSQL = ejecutarSQL($query, $tarjeta, $_SERVER['REQUEST_URI'], __LINE__);
					while($datoA=mysqli_fetch_assoc($resultadoSQL)){
						$despuesDe = $datoA['idMisionActividad'];
					}
				}
				
				if($despuesDe == "") $despuesDe="A-L-L";
				
				echo campoSelectBasico("despuesDe","Desp&uacute;es de:",$despuesDe, "icon-sort-numeric-asc", modulosPorMision($idMision),true,false,'','AL INICIO');
				
				
				//CANCELAR Y SUBMIT 
				$destino = "misiones.php?id=".$idMision;
				if($idActivo == "")
					echo botonSubmit("registrar","Registrar", 'btn-primary');
				else{
					echo botonSubmit("registrar","Actualizar", 'btn-warning');
					echo botonSubmit("borrar","Borrar", 'btn-danger');
				}
					
				
				echo botonBasico("cancelar","Cancelar", $destino, "btn-info");
				?>
        	</form>
            
            <div class="well" style="background: #c2c2c2;">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed">                        
                        <tr>
                            <th col colspan="3" style="vertical-align:middle; background:#DB0003"><span class="textos">M&oacute;dulos de la misi&oacute;n</span></th>
                        </tr>
                        <tr>
                            <th scope="row" width="40px"style="background:#C0D3E9">Orden</th>
                            <th scope="row" style="background:#C0D3E9">M&oacute;dulo</th>
                            <th scope="row" style="background:#C0D3E9">Alternos</th>
                        </tr>
     <?
			$query = "SELECT `idMisionActividad`, `0030_modulos`.`modulo`, `orden` FROM `0030_modulos` INNER JOIN `0041_misionesModulos` ON `0030_modulos`.`idModulo`=`0041_misionesModulos`.`idModulo` WHERE `idMision` ='".$idMision."' ORDER BY `orden`";
			$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			while($datoB=mysqli_fetch_assoc($resultadoSQL_B)){
				$resultadosAlternos= 0;
				
				$query = "SELECT COUNT(`idModuloAlterno`) AS `cantidad` FROM `0042_modulosAlternos` WHERE `idMisionModulo` ='".$datoB['idMisionActividad']."'";
				$resultadoSQL_A = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				
				while($datoA=mysqli_fetch_assoc($resultadoSQL_A)){
					$resultadosAlternos=$datoA['cantidad'];
				}
    ?>
                        <tr id="asientos">
                        	<td style="vertical-align:middle;"><span class="textos"><?php echo $datoB["orden"]; ?></span></td>
                            <td style="vertical-align:middle;"><a href="modulosAsignar.php?idMision=<? echo $idMision;?>&id=<?php echo $datoB['idMisionActividad']."&refresh=".rand(); ?>"><?php echo ucwords($datoB['modulo']); ?></a></td>
                        	<td style="vertical-align:middle;"><span class="textos"><?php echo $resultadosAlternos; ?></span></td>
                        </tr> 
    <?		}  	?>
                    </table>
                </div>
            </div>
            <div class="clearfix"></div>
    	</section>
    	<?php include "parts/footer.html" ?>
	</div><!--cierre contenedor!-->
	<script>
		function sacarOrden()
		{
			var modulo= document.getElementById("modulo").value
			var actividad= document.getElementById("actividad").value
			$.get("parts/actividadEnModulo.php?modulo=" + modulo + "&actividad=" + actividad +"&refresh=" + new Date().getTime(), function(responseText) {
				dato = (responseText);
				//console.log("resultado de " + dato);
				document.getElementById("orden").innerHTML  = dato;
			});
		}
		<?php 
			$HTML_add=campoSelectMultiple("modulo","M&oacute;dulo",'', "icon-sitemap", $listaModulos, true, 2);
			agregarCampoJS(12, "modulo", "PRIMER", $HTML_add);
			if($mensajeModal) echo "mensajeModalA('".$mensaje."')"; 
		?>
	</script>
    
	
	
</body>
</html>
