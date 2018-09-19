<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';
$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));

$jerarquia = permiteAcceso();
if($jerarquia < 75) salirJerarquia("nuevo.php");

$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
if(isset($_GET['id']))
{
	$idActivo = $_GET['id'];
}

if(isset($_POST['registrar']))
{	
	$errLista = '';
	$mision = validaCampoTXT('mision',2,TRUE, $_POST, $errLista);
	$activo = $_POST['activo'];
	$idActivo=$_POST['id'];
	
	//Verificación de $errLista por Errores o sino capturar
	if(noHayErrores($errLista))
	{
		if($idActivo == ""){
			$query="INSERT INTO `0040_misiones`(`mision`,`activo`) VALUES ('".$mision."','".$activo."')";
		}else{
			$query="UPDATE `0040_misiones` SET `mision`= '".$mision."', `activo`='".$activo."' WHERE `idMision`= '".$idActivo."'";
		}
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		$idActivo ="";
		$mision="";
		$activo=1;
		mostrarModal("Registro exitoso");
	}
}elseif($idActivo != ""){
	$query = "SELECT `mision`, `activo` FROM `0040_misiones` WHERE `idMision`= '".$idActivo."'";
	$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoB=mysqli_fetch_assoc($resultadoSQL_B))
	{
		$mision=$datoB['mision'];
		$activo=$datoB['activo'];
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
        	<h2 class="text-center">MISIONES</h2>
            <p>Misiones que se pueden asiganr a los inscritos</p>
            <form method="post" name="localizacion" target="_self" role="form" id="myForm">
				<input type="hidden" name="id" value="<? echo $idActivo; ?>"/>
				<?
				echo campoTextoBasico("mision","Misi&oacute;n",$mision,"icon-trophy", true, "", true, "", $lista=array());
				echo checkBox("activo", "Activo", $activo);
				?>
                <h3 class="text-center" id="venue"></h3>
                <?
				//CANCELAR Y SUBMIT 
				$destino = "misiones.php";
				if($idActivo == "")
					echo botonSubmit("registrar","Registrar", 'btn-primary');
				else{
					echo botonSubmit("registrar","Actualizar", 'btn-warning');
					echo botonBasico('modulos','Asignar modulos','modulosAsignar.php?idMision='.$idActivo, "btn-primary");
				}
				echo botonBasico("cancelar","Cancelar", $destino, "btn-info");
				?>
        	</form>
            
            <div class="well" style="background: #c2c2c2;">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed"> 
                        <tr id="asientos">
                            <th col colspan="3" style="vertical-align:middle; background:#DB0003"><span class="textos">Misiones</span></th>
                        </tr>
                        
                        <tr>
                            <th scope="row" width="32px" style="background:#C0D3E9">ID</th>
                            <th scope="row" width="80%" style="background:#C0D3E9;">Misi&oacute;n</th>
                            <th scope="row" width="52px" style="background:#C0D3E9">Act</th>
                        </tr>
     <?
        $query = "SELECT `idMision`, `mision`,`activo` FROM `0040_misiones` ORDER BY `mision`";
        $resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
        while($datoB=mysqli_fetch_assoc($resultadoSQL_B)){
    ?>
                        <tr id="asientos">
                        	<td style="vertical-align:middle;"><span class="textos"><?php echo $datoB["idMision"]; ?></span></td>
                            <td style="vertical-align:middle;"><a href="misiones.php?id=<?php echo $datoB['idMision']."&refresh=".rand(); ?>"><?php echo ucwords($datoB['mision']); ?></a></td>
                            <td style="vertical-align:middle;"><span class="textos"><?php echo checkBox("desc", "", $datoB["activo"],"","primary",true); ?></span></td>
                        </tr> 
    <?		} ?>
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
