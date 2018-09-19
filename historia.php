<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';
$jerarquia = permiteAcceso();

$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
$fecha = substr($ahora,0,10);

$primerCarga=true;

if(isset($_POST['usr']) && $jerarquia>60)
{
	$usr=$_POST['usr'];
}elseif(isset($_GET['usr']) && $jerarquia>60){
	$usr=$_GET['usr'];
}else{
	$usr=$idUsuario;
}

if(isset($_POST['activar']) && $jerarquia >= 60)
{
	$modulo = validaCampoSelect('modulo',$_POST,$errLista);
	$mision = validaCampoSelect('mision',$_POST,$errLista);
	if($_POST['moduloAlt']!="")$modulo = $_POST['moduloAlt'];
	if(!$estadoActivo){
		$query = "INSERT INTO `0100_inicioModulo`(`idModulo`, `idUsuario`, `fecha`) VALUES ('".$modulo."','".$idUsuario."','".$ahora."')";
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		$estadoActivo=true;
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
        	<h2 class="text-center">TU HISTORIAL <? echo mayusculas((nombreById($_COOKIE['idPRO']))); ?></h2>
            <form method="post" name="localizacion" target="_self" role="form" id="myForm">
				<?
				
				if($jerarquia > 60){	
					$listaUsuarios = array();
					$query = "SELECT `idUsuario`, `nombre` FROM `0010_usuarios` WHERE `jerarquia` <= '".$jerarquia."' AND `activo` ='1'";
					$resultadoSQL_A = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
					while($datoA=mysqli_fetch_assoc($resultadoSQL_A))
					{
						$listaUsuarios[$datoA['idUsuario']]=$datoA['nombre'];
					}
					echo campoSelectBasico("usr", "Usuario",$usr,"icon-search" ,$listaUsuarios,true,true,"",true);
				}
				$historiaMisiones = fechaMision($usr,$idMision);
				//var_dump($historiaMisiones);
				foreach($historiaMisiones as $historial)
				{
					echo "<br>Mision: ".$historial['idMision']. " desde: ". fechaSola($historial['fecha']);
					echo "<br>";
				}
				?>
        	</form>
             

    	</section>
    	<?php include "parts/footer.html" ?>
	</div><!--cierre contenedor!-->
	<script>
		
		<?php if($mensajeModal) echo "mensajeModalA('".$mensaje."')"; ?>
	</script>
    
	
	
</body>
</html>
