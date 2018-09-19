<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';
$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));


if(permiteAcceso() && isset($_GET['idClienteProv']))
{
	$idUsuario = $_COOKIE['idERP'];
	$jerarquia = jerarquiaTarjeta($idUsuario);
	$idClienteProv = $_GET['idClienteProv'];
}else{
	salirJerarquia("index.php");
}


if(isset($_POST['registrar']))
{	
	$errLista = '';
	if(usuarioEnActividad($idUsuario) == 0) $errLista .= "|Tu estado no est&aacute; activo|";	
	$productoTemp = $_REQUEST['datoLista'];
	$precioTemp = $_REQUEST['datoB'];
	$observacionTemp = $_REQUEST['datoC'];
	$idClienteProv=$_POST['id'];
	
	$producto=array();
	$precio=array();
	$observacion=array();
	$i=0;
	foreach($productoTemp as $key => $unProducto)
	{
		if($unProducto == "") $errLista .= "|Un tipo de producto no fue seleccionado|";
		else
		{
			$datoTemp[$key] = trim($datoTemp[$key]);
			if($precioTemp[$key] != "" && $precioTemp[$key] != 0 && is_numeric($precioTemp[$key]))
			{
				$producto[$i]=$unProducto;
				$precio[$i]=$precioTemp[$key];
				$observacion[$i]=$observacionTemp[$key];
				$i++;
			}else{
				$errLista .= "|Un precio qued&oacute; en blanco o no es num&eacute;rico|";
			}
		}
	}
	
	//Verificación de $errLista por Errores o sino capturar<a href="../miembros/demo.html">IcoMoon Demo</a>
	if(noHayErrores($errLista))
	{
		$clavesUsadas = array();
		$query = "SELECT `idCatalogo`, `idProducto`, `precio` FROM `0050_catalogo` WHERE `idClienteProv`= '".$idClienteProv."'";
		$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		while($datoB=mysqli_fetch_assoc($resultadoSQL))
		{
			$clavesUsadas[]=$datoB['idCatalogo'];
		}
		$i=0;
		$borrar = "";
		foreach($clavesUsadas as $unaClave)
		{
			if(array_key_exists($i,$producto))
			{
				if($observacion[$i]=="")$observa="NULL";
				else $observa="'".$observacion[$i]."'";
				$query = "UPDATE `0050_catalogo` SET `idProducto`= '".$producto[$i]."', `precio` = '".$precio[$i]."', `observaciones`= ".$observa." WHERE `idCatalogo` = '".$unaClave."'";
				ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			}else{
				$borrar .= $unaClave.",";
			}
			$i++;
		}
		if($borrar != "")
		{
			$borrar = substr($borrar,0,-1);
			$query = "DELETE FROM `0050_catalogo` WHERE `idCatalogo` IN (".$borrar.")";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		}
		while($i<count($producto))
		{
			if($observacion[$i]=="")$observa="NULL";
			else $observa="'".$observacion[$i]."'";
			$query = "INSERT INTO `0050_catalogo`(`idProducto`, `precio`, `idClienteProv`, `observaciones`) VALUES ('".$producto[$i]."','".$precio[$i]."','".$idClienteProv."',".$observa.")";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			$i++;
		}
		$actividad = "Productos de clientes/proveedores actualizados";
		$query = "INSERT INTO `0100_bitacora`(`idActividad`, `idUsuario`,`observacion`,`registro`) VALUES ('19','".$idUsuario."','".$actividad."','".$idClienteProv."')";
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		
		mostrarModal("Registro exitoso");
	}else{
		$producto=$productoTemp;
		$precio=$precioTemp;
		$observacion=$observacionTemp;
	}
}elseif($idClienteProv != ""){
	$query = "SELECT `nombre` FROM `0020_clienteProveedor` WHERE `idClienteProv`= '".$idClienteProv."'";
	$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoB=mysqli_fetch_assoc($resultadoSQL_B))
	{
		$nombre=$datoB['nombre'];
	}
	$producto=array();
	$precio=array();
	$observacion=array();
	$query = "SELECT `idProducto`, `precio`, `observaciones` FROM `0050_catalogo` WHERE `idClienteProv`= '".$idClienteProv."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	$i=0;
	while($datoC=mysqli_fetch_assoc($resultadoSQL))
	{
		$producto[$i] = $datoC['idProducto'];
		$precio[$i]	=$datoC['precio'];
		$observacion[$i]=$datoC['observaciones'];
		$i++;
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
        	<h2 class="text-center">CATALOGO DE<br /><? echo mayusculas($nombre); ?></h2>
            <form method="post" name="localizacion" target="_self" role="form" id="myForm">
				<input type="hidden" name="id" value="<? echo $idClienteProv; ?>"/>
				<?
				$query = "SELECT `idProducto`, `nombreProducto`, `unidadMedida` FROM `0030_productos` INNER JOIN `0031_unidadMedida` ON `0030_productos`.`idUnidadMedida`=`0031_unidadMedida`.`idUnidadMedida` ORDER BY `nombreProducto`";
				$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				while($datoB=mysqli_fetch_assoc($resultadoSQL_B)){
					$listaProductos[$datoB['idProducto']]=$datoB['nombreProducto']."(".$datoB['unidadMedida'].")";
				}
				
				$query = "SELECT `unidadMedida`, `idUnidadMedida` FROM `0031_unidadMedida` ORDER BY `unidadMedida`";
				$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				while($datoB=mysqli_fetch_assoc($resultadoSQL_B)){
					$listaUnidadMedida[$datoB['idUnidadMedida']]=$datoB['unidadMedida'];
				}
				?>
                <div class="PRIMER_wrapper" id="PRIMER_wrapper">
					<?php
                    $i=0;
                    do{
                        echo campoSelectNumDecTxt("dato", "Producto", "Select un producto", "Precio", "Observaci&oacute;nes", $producto[$i], "icon-drink", "icon-coin-dollar", $precio[$i],$observacion[$i], $listaProductos, true, ($i+1));
						$i++;
                    }while($i<count($producto));
                    ?>
                </div>
                <?
				//CANCELAR Y SUBMIT 
				$destino = "contactos.php";
				echo "<br/><br/>";
				echo botonSubmit("registrar","Registrar", 'btn-primary');
				echo botonBasico("cancelar","Cancelar", $destino, "btn-info");
				echo botonBasico("contacto","Editar Contacto","contactos.php?id=".$idClienteProv, "btn-default",true,false);
				
				?>
        	</form>
            
            <div class="clearfix"></div>
    	</section>
    	<?php include "parts/footer.html" ?>
	</div><!--cierre contenedor!-->
	<script>
		$(document).ready(function(){
			<?
			$HTML_add=campoSelectNumDecTxt("dato", "Producto", "Select un producto", "Precio", "Observaci&oacute;nes", '', "icon-drink", "icon-coin-dollar", '', '', $listaProductos, true, 2);
			agregarCampoJS(50, "dato", "PRIMER", $HTML_add);
			?>
		});
		<?php if($mensajeModal) echo "mensajeModalA('".$mensaje."')"; ?>
		
		
	</script>
</body>
</html>
