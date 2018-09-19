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
	$modulo = validaCampoTXT('modulo',2,TRUE, $_POST, $errLista);
	$activo = $_POST['activo'];
	$subgrupo = $_POST['subgrupo'];
	$calificaPorAct=$_POST['calificaPorAct'];
	$idActivo=$_POST['id'];
	
	//Verificación de $errLista por Errores o sino capturar
	if(noHayErrores($errLista))
	{
		if($idActivo == ""){
			$query="INSERT INTO `0030_modulos`(`modulo`,`activo`,`subgrupo`,`calificaPorAct`) VALUES ('".$modulo."','".$activo."','".$subgrupo."','".$calificaPorAct."')";
		}else{
			$query="UPDATE `0030_modulos` SET `modulo`= '".$modulo."', `activo`='".$activo."', `subgrupo`='".$subgrupo."', `calificaPorAct`='".$calificaPorAct."' WHERE `idModulo`= '".$idActivo."'";
		}
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		$idActivo ="";
		$activo=1;
		$subgrupo=1;
		$calificaPorAct=1;
		$modulo="";
		mostrarModal("Registro exitoso");
	}
}elseif($idActivo != ""){
	$query = "SELECT `modulo`,`activo`,`subgrupo`, `calificaPorAct` FROM `0030_modulos` WHERE `idModulo`= '".$idActivo."'";
	$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoB=mysqli_fetch_assoc($resultadoSQL_B))
	{
		$modulo=$datoB['modulo'];
		$activo=$datoB['activo'];
		$subgrupo=$datoB['subgrupo'];
		$calificaPorAct= $datoB['calificaPorAct'];
	}
}else{
	$activo=1;
	$subgrupo=1;
	$calificaPorAct=1;
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
        	<h2 class="text-center">M&Oacute;DULOS</h2>
            <p>Modulos que pueden ser parte de las misiones</p>
            <form method="post" name="localizacion" target="_self" role="form" id="myForm">
				<input type="hidden" name="id" value="<? echo $idActivo; ?>"/>
				<?
				echo campoTextoBasico("modulo","Modulo",$modulo,"icon-sitemap", true, "", true, "", $lista=array());
				echo checkBox("activo", "Activo", $activo,'','success');
				echo checkBox("subgrupo", "de divide en subGrupos", $subgrupo);
				echo checkBox("calificaPorAct", "Se calif&iacute;ca por actividad", $calificaPorAct,'','warning');
				?>
                <h3 class="text-center" id="venue"></h3>
                <?
				//CANCELAR Y SUBMIT 
				$destino = "modulos.php";
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
                            <th col colspan="5" style="vertical-align:middle; background:#DB0003"><span class="textos">M&oacute;dulos</span></th>
                        </tr>
                        
                        <tr>
                            <th scope="row" width="40%" style="background:#C0D3E9">ID</th>
                            <th scope="row" style="background:#C0D3E9">M&oacute;dulo</th>
                            <th scope="row" width="40px" style="background:#C0D3E9">SubG</th>
                            <th scope="row" width="40px" style="background:#C0D3E9">Cal</th>
                            <th scope="row" width="40px" style="background:#C0D3E9">Activo</th>
                        </tr>
     <?
        $query = "SELECT `idModulo`, `modulo`, `activo`, `subgrupo`, `calificaPorAct` FROM `0030_modulos` ORDER BY `modulo`";
        $resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
        while($datoB=mysqli_fetch_assoc($resultadoSQL_B)){
    ?>
                        <tr id="asientos">
                        	<td style="vertical-align:middle;"><span class="textos"><?php echo $datoB["idModulo"]; ?></span></td>
                            <td style="vertical-align:middle;"><a href="modulos.php?id=<?php echo $datoB['idModulo']."&refresh=".rand(); ?>"><?php echo ucwords($datoB['modulo']); ?></a></td>
                            <td style="vertical-align:middle;"><span class="textos"><?php echo checkBox("subG", "", $datoB["subgrupo"],"","primary",true); ?></span></td>
                            <td style="vertical-align:middle;"><span class="textos"><?php echo checkBox("cali", "", $datoB["calificaPorAct"],"","warning",true); ?></span></td>
                            <td style="vertical-align:middle;"><span class="textos"><?php echo checkBox("acti", "", $datoB["activo"],"","success",true); ?></span></td>
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
