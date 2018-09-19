<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';
$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
$fecha = substr($ahora,0,10);

if(permiteAcceso() && isset($_GET['idBitacora']))
{
	$idUsuario = $_COOKIE['idERP'];
	$jerarquia = jerarquiaTarjeta($idUsuario);
	$idBitacora = $_GET['idBitacora'];
}else{
	salirJerarquia("index.php");
}
if($jerarquia < 77) salirJerarquia("nuevo.php");


if(isset($_POST['registrar']))
{
	
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
        	<h2 class="text-center">DETALLE BITACORA</h2>
            <form method="post" name="localizacion" target="_self" role="form" id="myForm">
				
                <div class="well" style="float:left; width:100%;">
                    <?
					$query = "SELECT `idActividad`, `hora`, `registro`, `observacion`, `idUsuario` FROM `0100_bitacora` WHERE `idBitacora` = '".$idBitacora."'";
					$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
					$i=0;
					while($dato = mysqli_fetch_array($resultadoSQL))
					{
						$fechas = $dato['hora'];
						$actividad = $dato['idActividad']; //1 entrada / 2 salida
						$registro = $dato['registro'];
						$observacion = $dato['observacion'];
						$usuario = $dato['idUsuario'];
					}
					
					$datosActividades = array();
					buscarActiv($actividad, $datosActividades)
					?>
					<div class="alert alert-info"><span class="icon-checkmark" aria-hidden="true"></span> <?php echo ucwords(nombreById($usuario)); ?> registro:</div>
                    
                    <div style="padding:5px 1px; float:left; width:100%; margin-bottom:2px; background-color:#4687a7;" class="well">
                        <? if($actividad == 1 || $actividad == 2){ //inicio o fin de sesion ?>
                        <span class="alert alert-info col-xs-12 col-sm-3"><? echo fechaHora($fechas,true); ?></span>
                        <? }elseif($actividad == 3 ||$actividad == 6 ||$actividad == 7 ){ //cambio en cliente ?>
                        <a href="contactos.php?id=<? echo $registro; ?>" class="btn btn-warning col-xs-12 col-sm-3"><? echo fechaHora($fechas,true); ?></a>
                        <? }elseif($actividad == 19){ //cambio en cliente ?>
                        <a href="catalogo.php?idClienteProv=<? echo $registro; ?>" class="btn btn-warning col-xs-12 col-sm-3"><? echo fechaHora($fechas,true); ?></a>
                        <? }else{ //cambio en seguimiento
								$query="SELECT `idClienteProv` FROM `0040_seguimiento` WHERE `idCalendario` = '".$registro."'";
								$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
								while($dato = mysqli_fetch_array($resultadoSQL))
								{
									$idClienteProv = $dato['idClienteProv'];
								}
						 ?>
                        <a href="seguimiento.php?idClienteProv=<? echo $idClienteProv; ?>&id=<? echo $registro; ?>" class="btn btn-primary col-xs-12 col-sm-3"><? echo fechaHora($fechas,true); ?></a>
                        <? } ?>
                        <span class="col-xs-12 col-sm-5"><? echo $observacion; ?></span>
                        <span class="col-xs-12 col-sm-4"><b><? echo $datosActividades[$actividad]['nombreActividad']; ?></b></span>
                    </div>
							
					<?
					
				?>
                </div>
        	</form>
             

    	</section>
    	<?php include "parts/footer.html" ?>
	</div><!--cierre contenedor!-->
	<script>
		<?php if($mensajeModal) echo "mensajeModalA('".$mensaje."')"; ?>
	</script>
    
	
	
</body>
</html>
