<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';
$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
$primerCarga=true;
$jerarquia = permiteAcceso();
if($jerarquia < 75) salirJerarquia("nuevo.php");

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
		$query = "DELETE FROM `0031_actividadModulo` WHERE `idActividadModulo`= '".$idActivo."'";
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		$idActivo ="";
		$modulo="";
		$actividad='';
		$minutos='';
		
		mostrarModal("Borrado");
	}
	
}elseif(isset($_POST['registrar']))
{	
	$errLista = '';
	$modulo = validaCampoSelect('modulo',$_POST,$errLista);
	$actividad = validaCampoSelect('actividad',$_POST,$errLista);
	$minutos = validaCampoTXT('minutos',1,FALSE, $_POST, $errLista);
	$despuesDe=$_POST['despuesDe'];
	$idActivo=$_POST['id'];
	
	$enCircuito = $_POST['enCircuito'];
	
	if($enCircuito == 1)
	{
		$idActividadFortaleza = $_POST['idActividadFortaleza'];
		$residuo = fmod ($minutos,3);
		if($residuo != 0)$errLista .= "|EL tiempo no es divisible entre 3 que requiere un circuito|";
	}
	
	$reto=$_POST['retoA'];
	$cantidad=$_POST['retoB'];
	
	if($reto!="" && ($cantidad == "" || $cantidad == 0))
	{
		$errLista .= "|indica la cantidad en el reto|";
	}
	
	if($reto=="" && $cantidad != "" && $cantidad != 0)
	{
		$errLista .= "|Escoje un tipo de reto o quita su cantidad|";
	}
	if($reto!="" && $cantidad != "" && $cantidad !=0)
	{
		$guardaReto=true;
	}else{
		$guardaReto=false;
	}
	
	//Verificación de $errLista por Errores o sino capturar
	if(noHayErrores($errLista))
	{
		$arreglo=array();
		if($idActivo == "")
		{
			if($despuesDe!="A-L-L" && $despuesDe != "")
			{
				//buscar orden
				$query="SELECT `orden` FROM `0031_actividadModulo` WHERE `idActividadModulo` = '".$despuesDe."'";
				$resultadoSQL = ejecutarSQL($query, $tarjeta, $_SERVER['REQUEST_URI'], __LINE__);
				while($datoA=mysqli_fetch_assoc($resultadoSQL)){
					$orden = $datoA['orden'];
				}
				
				//re ordenar
				$query = "UPDATE `0031_actividadModulo` SET `orden`=(`orden` + 1) WHERE `idModulo`= '".$modulo."' AND `orden`>='".($orden + 1)."'";
				ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				
				//registrar nuevo
				if($enCircuito == 1) $query="INSERT INTO `0031_actividadModulo`(`idActividad`, `idModulo`, `orden`, `minutos`, `enCircuito`, `idActividadFortaleza` ) VALUES ('".$actividad."','".$modulo."','".($orden + 1)."','".$minutos."','1','".$idActividadFortaleza."')";
				else $query="INSERT INTO `0031_actividadModulo`(`idActividad`, `idModulo`, `orden`, `minutos`) VALUES ('".$actividad."','".$modulo."','".($orden + 1)."','".$minutos."')";
				
				$idActivo = insertSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			}else{
				//reordenar
				$query = "UPDATE `0031_actividadModulo` SET `orden`=(`orden` + 1) WHERE `idModulo`= '".$modulo."'";
				ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				//registrar
				if($enCircuito == 1) $query="INSERT INTO `0031_actividadModulo`(`idActividad`, `idModulo`, `orden`, `minutos`, `enCircuito`, `idActividadFortaleza`) VALUES ('".$actividad."','".$modulo."','1','".$minutos."','1','".$idActividadFortaleza."')";
				else $query="INSERT INTO `0031_actividadModulo`(`idActividad`, `idModulo`, `orden`, `minutos`) VALUES ('".$actividad."','".$modulo."','1','".$minutos."')";
				$idActivo = insertSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				
			}
		}else{
			if($despuesDe!="A-L-L" && $despuesDe != "")
			{
				//buscar orden
				$query="SELECT `orden` FROM `0031_actividadModulo` WHERE `idActividadModulo` = '".$despuesDe."'";
				$resultadoSQL = ejecutarSQL($query, $tarjeta, $_SERVER['REQUEST_URI'], __LINE__);
				while($datoA=mysqli_fetch_assoc($resultadoSQL)){
					$orden = $datoA['orden'];
				}
				//reordenar
				$query="UPDATE `0031_actividadModulo` SET `orden`=(`orden` + 1) WHERE `idModulo`= '".$modulo."' AND `orden`>='".$orden."'";
				ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				
				//registrar actualizado
				if($enCircuito == 1) $query="UPDATE `0031_actividadModulo` SET `idActividad`='".$actividad."', `idModulo`='".$modulo."', `orden`='".($orden + 1)."', `minutos`= '".$minutos."',`enCircuito`='1',`idActividadFortaleza`='".$idActividadFortaleza."' WHERE `idActividadModulo` = '".$idActivo."'";
				else $query="UPDATE `0031_actividadModulo` SET `idActividad`='".$actividad."', `idModulo`='".$modulo."', `orden`='".($orden + 1)."', `minutos`= '".$minutos."' WHERE `idActividadModulo` = '".$idActivo."'";
				ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				
			}else{
				//reordenar
				$query = "UPDATE `0031_actividadModulo` SET `orden`=(`orden` + 1) WHERE `idModulo`= '".$modulo."'";
				ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				
				//registrar actualizacion
				if($enCircuito == 1) $query="UPDATE `0031_actividadModulo` SET `idActividad`='".$actividad."', `idModulo`='".$modulo."', `orden`='1', `minutos`= '".$minutos."',`enCircuito`='1',`idActividadFortaleza`='".$idActividadFortaleza."' WHERE `idActividadModulo` = '".$idActivo."'";
				else $query="UPDATE `0031_actividadModulo` SET `idActividad`='".$actividad."', `idModulo`='".$modulo."', `orden`='1', `minutos`= '".$minutos."' WHERE `idActividadModulo` = '".$idActivo."'";
				ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			}
		}
		if($guardaReto)
		{
			$query = "SELECT `idModReto` FROM `0032_actividadModRetos` WHERE `idActividadModulo` = '".$idActivo."'";
			$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			if(mysqli_num_rows($resultadoSQL_B)==0) $query = "INSERT INTO `0032_actividadModRetos`(`idActividadModulo`, `retoValor`, `idReto`) VALUES ('".$idActivo."','".$cantidad."','".$reto."')";
			else $query = "UPDATE `0032_actividadModRetos` SET `retoValor`='".$cantidad."',`idReto`='".$reto."' WHERE `idActividadModulo`= '".$idActivo."'";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		}else{
			//borrar reto
			$query = "DELETE FROM `0032_actividadModRetos` WHERE `idActividadModulo`= '".$idActivo."'";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		}
		$idActivo ="";
		$modulo="";
		$actividad='';
		$minutos='';
		$reto = '';
		$cantidad="";
		$idActividadFortaleza = "";
		$enCircuito = "";
		
		mostrarModal("Registro exitoso");
	}
}elseif($idActivo != ""){
	$query = "SELECT `idModulo`, `idActividad`, `minutos`, `orden`, `enCircuito`, `idActividadFortaleza` FROM `0031_actividadModulo` WHERE `idActividadModulo`= '".$idActivo."'";
	$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoB=mysqli_fetch_assoc($resultadoSQL_B))
	{
		$modulo=$datoB['idModulo'];
		$actividad=$datoB['idActividad'];
		$minutos = $datoB['minutos'];
		$orden = $datoB['orden'];
		$enCircuito = $datoB['enCircuito'];
		$idActividadFortaleza = $datoB['idActividadFortaleza'];
	}
	$query = "SELECT `retoValor`, `idReto` FROM `0032_actividadModRetos` WHERE `idActividadModulo` = '".$idActivo."'";
	$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoB=mysqli_fetch_assoc($resultadoSQL_B))
	{
		$reto=$datoB['idReto'];
		$cantidad=$datoB['retoValor'];
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
        	<h2 class="text-center">Asignar Actividades</h2>
            <p>Asignar actividades a cierto m&oacute;dulo</p>
            <form method="post" name="localizacion" target="_self" role="form" id="myForm">
				<input type="hidden" name="id" value="<? echo $idActivo; ?>"/>
				<?
				$listaActividades =  actividades();
				echo campoSelectBasico("modulo","M&oacute;dulo",$modulo, "icon-sitemap", modulos(),true,false,"sacarOrden()");
				echo campoSelectBasico("actividad","Actividad",$actividad, "icon-bolt", $listaActividades,true,false,"sacarOrden()");
				echo campoTextoNumDec("minutos","Duraci&oacute;n",$minutos,"icon-stopwatch", true, "", "", "Minutos");
				
				echo checkBox('enCircuito', "En Circuito", $enCircuito, 'onClick="mostrarFortalecimiento()"');
				?>
                <div id="circuito">
                <?
				if($enCircuito ==1)	echo campoSelectBasico("idActividadFortaleza","Actividad de fortaleza",$idActividadFortaleza, "icon-bolt", $listaActividades);
				?>
				</div>
				<?
				
				
				
				//echo campoTextoBasico("orden","Orden",$orden,"icon-sort-numeric-asc");
				?>
                <div id="orden">
                <? 
				if($modulo != "")
				{
					include 'parts/actividadEnModulo.php';
				}
				?>
                </div>
                <div id="">
                
                </div>
                <?
				echo campoSelectConNumEnt('reto', "Reto", "Cantidad", $reto, $cantidad, "icon-target", "icon-fire", retos(), false);
				//CANCELAR Y SUBMIT 
				$destino = "actividadesAsignar.php";
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
        <?
        $query = "SELECT `idModulo`, `modulo`, `activo` FROM `0030_modulos` WHERE `activo` ='1' ORDER BY `modulo`";
        $resultadoSQL_A = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
        while($datoA=mysqli_fetch_assoc($resultadoSQL_A))
		{
    ?>              
                    
                        <tr id="asientos">
                            <th col colspan="4" style="vertical-align:middle; background:#DB0003"><span class="textos">Actividades del modulo |<? echo checkBox("desc", "", $datoA["activo"],"","success",true,"1",false,true). " " .$datoA['modulo']; ?></span></th>
                        </tr>
                        <tr>
                            <th scope="row" width="40px"style="background:#C0D3E9">Orden</th>
                            <th scope="row" style="background:#C0D3E9">Actividad</th>
                            <th scope="row" style="background:#C0D3E9">Minutos</th>
                            <th scope="row" style="background:#C0D3E9">Foto</th>
                        </tr>
     <?
			$query = "SELECT `0031_actividadModulo`.`idActividadModulo`, `actividad`, `descripcion`, `video`, `foto`, `orden`, `minutos` FROM `0020_actividades` INNER JOIN `0031_actividadModulo` ON `0020_actividades`.`idActividad`=`0031_actividadModulo`.`idActividad` WHERE `0031_actividadModulo`.`idModulo` = '".$datoA['idModulo']."' ORDER BY `orden` ";
			$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			while($datoB=mysqli_fetch_assoc($resultadoSQL_B)){
    ?>
                        <tr id="asientos">
                        	<td style="vertical-align:middle;"><span class="textos"><?php echo $datoB["orden"]; ?></span></td>
                            <td style="vertical-align:middle;"><a href="actividadesAsignar.php?id=<?php echo $datoB['idActividadModulo']."&refresh=".rand(); ?>"><?php echo ucwords($datoB['actividad']); ?></a></td>
                            <td style="vertical-align:middle;"><span class="textos"><?php echo $datoB["minutos"]; ?></span></td>
                            <td style="vertical-align:middle;"><span class="textos"><img src="<?php echo $datoB["foto"]; ?>" height="50px" alt="sin foto"/></span></td>
                        </tr> 
    <?		} 
		}
	?>
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
		
		function mostrarFortalecimiento()
		{
			var enCircuito=document.getElementById("enCircuito").checked;
			console.log("resultado " + enCircuito);
			if(enCircuito){
				document.getElementById("circuito").innerHTML  = '<? echo campoSelectBasico("idActividadFortaleza","Actividad de fortaleza",$idActividadFortaleza, "icon-bolt", $listaActividades);?>';
			}else{
				document.getElementById("circuito").innerHTML  = '';
			}
		}
		<?php if($mensajeModal) echo "mensajeModalA('".$mensaje."')"; ?>
	</script>
    
	
	
</body>
</html>
