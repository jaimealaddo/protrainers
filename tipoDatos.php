<?

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
if($jerarquia < 75) salirJerarquia("nuevo.php");

if(isset($_GET['id']))
{
	$idActivo = $_GET['id'];
}

if(isset($_POST['registrar']))
{	
	$errLista = '';
	$tipoDato = validaCampoTXT('tipoDato',4,TRUE, $_POST, $errLista);
	$idActivo=$_POST['id'];
	
	//Verificación de $errLista por Errores o sino capturar
	if(noHayErrores($errLista))
	{
		if($idActivo == "")
			$query="INSERT INTO `0023_tipoDato`(`tipoDato`) VALUES ('".$tipoDato."')";
		else
			$query="UPDATE `0023_tipoDato` SET `tipoDato`= '".$tipoDato."' WHERE `idTipoDato`= '".$idActivo."'";
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		$idActivo ="";
		$tipoDato="";
		mostrarModal("Registro exitoso");
	}
}elseif($idActivo != ""){
	$query = "SELECT `tipoDato` FROM `0023_tipoDato` WHERE `idTipoDato`= '".$idActivo."'";
	$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoB=mysqli_fetch_assoc($resultadoSQL_B)){
		$tipoDato=$datoB['tipoDato'];
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
        	<h2 class="text-center">TIPOS DE DATOS</h2>
            <p>Tipo de datos que pueden tener los datos de contactos</p>
            <form method="post" name="localizacion" target="_self" role="form" id="myForm">
				<input type="hidden" name="id" value="<? echo $idActivo; ?>"/>
				<?
				echo campoTextoBasico("tipoDato","Tipo de dato",$tipoDato,"icon-chain", true, "", true, "", $lista=array());
				?>
                <h3 class="text-center" id="venue"></h3>
                <?
				//CANCELAR Y SUBMIT 
				$destino = "tipoDatos.php";
				if($idActivo == "")
					echo botonSubmit("registrar","Registrar", 'btn-primary');
				else
					echo botonSubmit("registrar","Actualizar", 'btn-danger');
				echo botonBasico("cancelar","Cancelar", $destino, "btn-warning");
				?>
        	</form>
            
            <div class="well" style="background: #c2c2c2;">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed"> 
                        <tr id="asientos">
                            <th col colspan="2" style="vertical-align:middle; background:#DB0003"><span class="textos">Datos</span></th>
                        </tr>
                        
                        <tr>
                            <th scope="row" width="125px" style="background:#C0D3E9">Id</th>
                            <th scope="row" style="background:#C0D3E9">Tipo de Dato</th>
                        </tr>  
     <?
        $query = "SELECT `tipoDato`, `idTipoDato` FROM `0023_tipoDato` ORDER BY `tipoDato`";
        $resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
        while($datoB=mysqli_fetch_assoc($resultadoSQL_B)){
    ?>
                        <tr id="asientos">
                        	<? if($datoB['idTipoDato'] == "1" || $datoB['idTipoDato'] == "2" || $datoB['idTipoDato'] == "3" || $datoB['idTipoDato'] == "4") { ?>
                            <td style="vertical-align:middle;"><span class="textos"><?php echo ($datoB['idTipoDato']); ?></span></td>
                            <? }else{ ?>
                            <td style="vertical-align:middle;"><a href="tipoDatos.php?id=<?php echo $datoB['idTipoDato']."&refresh=".rand(); ?>"><?php echo ($datoB['idTipoDato']); ?></a></td>
                            <? } ?>
                            <td style="vertical-align:middle;"><span class="textos"><?php echo ($datoB["tipoDato"]); ?></span></td>
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
