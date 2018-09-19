<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';
$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));

$jerarquia = permiteAcceso();
if($jerarquia < 60) salirJerarquia("nuevo.php");

$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
if(isset($_GET['id']))
{
	$idActivo = $_GET['id'];
}

if(isset($_POST['registrar']))
{	
	$errLista = '';
	$categoria = validaCampoTXT('categoria',2,TRUE, $_POST, $errLista);
	$estaturaIni = validaCampoTXT('estaturaIni',1, FALSE, $_POST, $errLista);
	$estaturaFin = validaCampoTXT('estaturaFin',1, FALSE, $_POST, $errLista);
	$idActivo=$_POST['id'];
	
	//Verificación de $errLista por Errores o sino capturar
	if(noHayErrores($errLista))
	{
		if($idActivo == ""){
			$query="INSERT INTO `0050_categorias`(`categoria`, `estaturaIni`,`estaturaFin`) VALUES ('".$categoria."','".$estaturaIni."','".$estaturaFin."')";
		}else{
			$query="UPDATE `0050_categorias` SET `categoria`= '".$categoria."', `estaturaIni`='".$estaturaIni."', `estaturaFin`='".$estaturaFin."' WHERE `idCategoria`= '".$idActivo."'";
		}
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		$idActivo ="";
		$estaturaIni="";
		$estaturaFin="";
		$categoria = "";
		mostrarModal("Registro exitoso");
	}
}elseif($idActivo != ""){
	$query = "SELECT `categoria`, `estaturaIni`, `estaturaFin` FROM `0050_categorias` WHERE `idCategoria`= '".$idActivo."'";
	$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoB=mysqli_fetch_assoc($resultadoSQL_B))
	{
		$categoria=$datoB['categoria'];
		$estaturaIni = ($datoB['estaturaIni']);
		$estaturaFin=$datoB['estaturaFin'];
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
        	<h2 class="text-center">Categor&iacute;as</h2>
            <p>Categorias en las que los inscritos estan</p>
            <form method="post" name="localizacion" target="_self" role="form" id="myForm">
				<input type="hidden" name="id" value="<? echo $idActivo; ?>"/>
				<?
				echo campoTextoBasico("categoria","Categor&iacute;a",$categoria,"icon-star-empty", true, "", true, "", $lista=array());
				echo campoTextoNumDec("estaturaIni","Estatura Inicial",$estaturaIni, "icon-sort-amount-asc",true,'','','m');
				echo campoTextoNumDec("estaturaFin","Estatura Final",$estaturaFin, "icon-sort-amount-desc",true,'','','m');
				?>
                <h3 class="text-center" id="venue"></h3>
                <?
				//CANCELAR Y SUBMIT 
				$destino = "categorias.php";
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
                            <th col colspan="4" style="vertical-align:middle; background:#DB0003"><span class="textos">Categor&iacute;as</span></th>
                        </tr>
                        
                        <tr>
                            <th scope="row" width="32px" style="background:#C0D3E9">ID</th>
                            <th scope="row" width="80%" style="background:#C0D3E9;">Categor&iacute;a</th>
                            <th scope="row" width="52px" style="background:#C0D3E9">estatura Ini</th>
                            <th scope="row" width="52px" style="background:#C0D3E9">estatura Fin</th>
                        </tr>
     <?
        $query = "SELECT `idCategoria`, `categoria`,`estaturaIni`,`estaturaFin` FROM `0050_categorias` ORDER BY `estaturaIni`";
        $resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
        while($datoB=mysqli_fetch_assoc($resultadoSQL_B)){
    ?>
                        <tr id="asientos">
                        	<td style="vertical-align:middle;"><span class="textos"><?php echo $datoB["idCategoria"]; ?></span></td>
                            <td style="vertical-align:middle;"><a href="categorias.php?id=<?php echo $datoB['idCategoria']."&refresh=".rand(); ?>"><?php echo ucwords($datoB['categoria']); ?></a></td>
                            <td style="vertical-align:middle;"><span class="textos"><?php echo $datoB["estaturaIni"]; ?></span></td>
                            <td style="vertical-align:middle;"><span class="textos"><?php echo $datoB["estaturaFin"]; ?></span></td>
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
