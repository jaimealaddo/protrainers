<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';

$jerarquia = permiteAcceso();
if($jerarquia < 70) salirJerarquia("nuevo.php");

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
		$query = "DELETE FROM `0020_actividades` WHERE `idActividad`= '".$idActivo."'";
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		$idActivo ="";
		$actividad="";
		$descripcion="";
		$video="";
		mostrarModal("Borrado");
	}
}elseif(isset($_POST['registrar']))
{	
	$errLista = '';
	$actividad = validaCampoTXT('actividad',4,TRUE, $_POST, $errLista);
	$descripcion = validaCampoTXT('descripcion',0,TRUE, $_POST, $errLista);
	$video = validaCampoTXT('video',0,TRUE, $_POST, $errLista);
	$idActivo=$_POST['id'];
	//Verificar la foto
	$errLista .= verificarFoto($_FILES["archivo"], false);
	
	if($descripcion == "")
		$descripcion = "NULL";
	else
		$descripcion = "'".$descripcion."'";
		
	if($video == "")
		$video = "NULL";
	else
		$video = "'".$video."'";
	
	//Verificación de $errLista por Errores o sino capturar
	if(noHayErrores($errLista))
	{
		if($idActivo == ""){
			$query="INSERT INTO `0020_actividades`(`actividad`, `descripcion`, `video`) VALUES ('".$actividad."',".$descripcion.",".$video.")";
			$idActivo = insertSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		}else{
			$query="UPDATE `0020_actividades` SET `actividad`= '".$actividad."', `descripcion`= ".$descripcion.", `video`=".$video." WHERE `idActividad`= '".$idActivo."'";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		}
		
		if($_FILES["archivo"]['name'] != ''){
			//guardar foto
			$imagen = guardarFotoSimple($_FILES["archivo"], "actividad", "act_".$idActivo,false,900,800);
			$query = "UPDATE `0020_actividades` SET `foto` = '" .$imagen . "' WHERE `idActividad` = '" . $idActivo . "'";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		}
		
		$idActivo ="";
		$actividad="";
		$descripcion="";
		$video="";
		mostrarModal("Registro exitoso");
	}
}elseif($idActivo != ""){
	$query = "SELECT `actividad`, `descripcion`, `video` FROM `0020_actividades` WHERE `idActividad`= '".$idActivo."'";
	$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoB=mysqli_fetch_assoc($resultadoSQL_B))
	{
		$actividad=$datoB['actividad'];
		$descripcion=utf8_encode($datoB['descripcion']);
		$video=$datoB['video'];
	}
	$descripcion = str_replace("<br />","\r\n", $descripcion);
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
        	<h2 class="text-center">Actividades</h2>
            <p class="text-center">Actividades que puede haber en un entrenamiento</p>
            <form enctype="multipart/form-data" method="post" name="localizacion" target="_self" role="form" id="myForm">
				<input type="hidden" name="id" value="<? echo $idActivo; ?>"/>
				<?
				echo campoTextoBasico("actividad","nombre de la actividad",$actividad,"icon-bolt");
				echo campoTextArea("descripcion","Descripci&oacute;n",$descripcion, "icon-pencil-square",false);
				echo campoTextoBasico("video","link a un video",$video,"icon-eject",false);
				echo campoFoto('archivo',"Imagen");
				//CANCELAR Y SUBMIT 
				$destino = "actividades.php";
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
                        <tr id="titulo">
                            <th col colspan="4" style="vertical-align:middle; background:#DB0003"><span class="textos">Giros</span></th>
                        </tr>
                        
                        <tr>
                            <th scope="row" width="30%" style="background:#C0D3E9">Actividad</th>
                            <th scope="row" style="background:#C0D3E9">Descripcion</th>
                            <th scope="row" width="125px" style="background:#C0D3E9">Foto</th>
                            <th scope="row" width="125px" style="background:#C0D3E9">Video</th>
                        </tr>
         <?
            $query = "SELECT `idActividad`, `actividad`, `descripcion`, `video`, `foto` FROM `0020_actividades` ORDER BY `actividad`";
            $resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
            while($datoB=mysqli_fetch_assoc($resultadoSQL_B)){
        ?>
                        <tr id="asientos">
                            <td style="vertical-align:middle;"><a href="actividades.php?id=<?php echo $datoB['idActividad']."&refresh=".rand(); ?>"><?php echo ucwords($datoB['actividad']); ?></a></td>
                            <td style="vertical-align:middle;"><span class="textos"><?php echo cortarTexto($datoB["descripcion"], 120, false); ?></span></td>
                            <td style="vertical-align:middle;"><? if($datoB['foto'] != ""){ ?><a href="<?php echo $datoB['foto']; ?>" target="new">Foto</a><? } ?></td>
                            <td style="vertical-align:middle;"><? if($datoB['video'] != ""){ ?><a href="<?php echo $datoB['video']; ?>" target="new">Video</a><? } ?></td>
                        </tr> 
    <?		} ?>
                        <!--
                        <tr id="asientos">
                            <td style="vertical-align:middle;"></td>
                            <td style="vertical-align:middle;"><span class="textos" style="font-size:large">$<?php echo number_format($total,2); ?></span></td>
                            <td style="vertical-align:middle;"></td>
                            <td style="vertical-align:middle;"></td>
                            <td style="vertical-align:middle;"></td>
                        </tr>
                        --> 
                    </table>
                </div>
            </div>
            <div class="clearfix"></div>
    	</section>
    	<?php include "parts/footer.html" ?>
	</div><!--cierre contenedor!-->
	<script>
		<?php if($mensajeModal) echo "mensajeModalA('".$mensaje."')"; ?>
	</script>
    
	
	
</body>
</html>
