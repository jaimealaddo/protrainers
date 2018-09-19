<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';
$jerarquia = permiteAcceso(false);

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
        	<h2 class="text-center">RANKING POR CATEGORIA Y ACTIVIDAD</h2>
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
				$estaturaIni=array();
				$estaturaFin=array();
				$categorias = categorias($estaturaIni,$estaturaFin);
				
				//var_dump($historiaMisiones);
				foreach($categorias as $idCategoria => $nombreCategoria)
				{
					?>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                        	<h2 style="font-size: 24px;" class="panel-title"><? echo "Categor&iacute;a ".$nombreCategoria; ?></h2>
                        </div>
                        <div class="panel-body">
                        	<h5><? echo "De ".$estaturaIni[$idCategoria]. "m a ".$estaturaFin[$idCategoria]."m"; ?></h5>
                            <?
							$rankingActividades = ranking($idCategoria);
							foreach ($rankingActividades as $datos)
							{
								?>
                                <ul class="list-group" style="margin-bottom: 9px;">
                                	<li class="list-group-item">
                                    	<div><? echo $datos['actividad'];?></div>
                                        <h3 style="margin-top: 0; margin-bottom: 5px;"><span style="display: block;white-space: normal;" class="label label-default"><? echo $datos['nombre'];?></span></h3>
										<span class="badge" style="position: absolute; right: 7px; top: -7px;background-color: #256c8f;"><? echo $datos['puntuacion']; ?> puntos</span>
                                    	<div><? echo "Impuesto el ". fechaSola($datos['fecha']); ?></div>
                                    </li>
                                </ul>
                                <?
							}
                            ?>
                        </div>
                    </div>
                    <?
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
