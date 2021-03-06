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
	$reto = validaCampoTXT('reto',2,TRUE, $_POST, $errLista);
	$descripcion = validaCampoTXT('descripcion',2,TRUE, $_POST, $errLista);
	$activo = $_POST['activo'];
	$idActivo=$_POST['id'];
	
	//Verificación de $errLista por Errores o sino capturar
	if(noHayErrores($errLista))
	{
		if($idActivo == ""){
			$query="INSERT INTO `0033_retos`(`reto`, `descripcion`,`activo`) VALUES ('".$reto."','".$descripcion."','".$activo."')";
		}else{
			$query="UPDATE `0033_retos` SET `reto`= '".$reto."', `activo`='".$activo."', `descripcion`='".$descripcion."' WHERE `idReto`= '".$idActivo."'";
		}
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		$idActivo ="";
		$reto="";
		$descripcion="";
		$activo=1;
		mostrarModal("Registro exitoso");
	}
}elseif($idActivo != ""){
	$query = "SELECT `reto`, `activo`, `descripcion` FROM `0033_retos` WHERE `idReto`= '".$idActivo."'";
	$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoB=mysqli_fetch_assoc($resultadoSQL_B))
	{
		$reto=$datoB['reto'];
		$descripcion = utf8_encode($datoB['descripcion']);
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
        	<h2 class="text-center">RETOS</h2>
            <p>Retos que se pueden asignar a las actividades de un modulo</p>
            <form method="post" name="localizacion" target="_self" role="form" id="myForm">
				<input type="hidden" name="id" value="<? echo $idActivo; ?>"/>
				<?
				echo campoTextoBasico("reto","Reto",$reto,"icon-trophy", true, "", true, "", $lista=array());
				echo campoTextArea('descripcion','Descripci&oacute;n',$descripcion);
				echo checkBox("activo", "Activo", $activo);
				?>
                <h3 class="text-center" id="venue"></h3>
                <?
				//CANCELAR Y SUBMIT 
				$destino = "retos.php";
				if($idActivo == "")
					echo botonSubmit("registrar","Registrar", 'btn-primary');
				else
					echo botonSubmit("registrar","Actualizar", 'btn-warning');
				echo botonBasico("cancelar","Cancelar", $destino, "btn-info");
				?>
        	</form>
            
            <div class="well" style="background: #c2c2c2;">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed"> 
                        <tr id="asientos">
                            <th col colspan="3" style="vertical-align:middle; background:#DB0003"><span class="textos">Retos</span></th>
                        </tr>
                        
                        <tr>
                            <th scope="row" width="32px" style="background:#C0D3E9">ID</th>
                            <th scope="row" width="80%" style="background:#C0D3E9;">Reto</th>
                            <th scope="row" width="52px" style="background:#C0D3E9">Act</th>
                        </tr>
     <?
        $query = "SELECT `idReto`, `reto`,`activo` FROM `0033_retos` ORDER BY `reto`";
        $resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
        while($datoB=mysqli_fetch_assoc($resultadoSQL_B)){
    ?>
                        <tr id="asientos">
                        	<td style="vertical-align:middle;"><span class="textos"><?php echo $datoB["idReto"]; ?></span></td>
                            <td style="vertical-align:middle;"><a href="retos.php?id=<?php echo $datoB['idReto']."&refresh=".rand(); ?>"><?php echo ucwords($datoB['reto']); ?></a></td>
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
