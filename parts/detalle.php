<?php
if(!$primerCarga){
	include '../includes/inc.funciones.php';
	include '../includes/inc.conexion.php';
	include '../includes/formsBase.php';
	
	if($jerarquia = permiteAcceso(false) && isset($_GET['idActividad']))
	{
		$idActividad = $_GET['idActividad'];
	}else{
		exit;
	}
}
$detalle = detalleActividad($idActividad);
//var_dump($detalle);
?>

<div class="media">
	<h4 class="media-heading">Detalle de <? echo $detalle['actividad']; ?>:</h4>
    <div class="media-left media-middle datosAnuncio fotoAnuncio">
        <img class="media-object" width="100%" src="<? echo $detalle['foto']; ?>" alt="imagen">
    </div>
    <div class="media-body datosAnuncio">
    	
        
        <? if($detalle['video'] != ""){; ?><a class="btn btn-primary" target="new" href="<? echo $detalle['video']; ?>" title"video" id="video" role="button">Video</a><? } ?>
        <p><? echo $detalle['descripcion']; ?></p>
    </div>
</div>