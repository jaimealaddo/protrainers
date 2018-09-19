<?php
if(!$primerCarga){
	include '../includes/inc.funciones.php';
	include '../includes/inc.conexion.php';
	include '../includes/formsBase.php';
	
	if(!$jerarquia = permiteAcceso(false)) exit;
	
	$noActividad = "";
	$idInicio="";
	$idModulo=$idModulo = moduloActual($noActividad, $idInicio);
	
	if($jerarquia < 60 || $noActividad == "" || $noActividad == 0) 
	{
		echo botonBasico('regresar','REGRESAR',"nuevo.php?refresh=".rand(), "btn-primary");
        exit;
	}
	$conSubgrupo="0";
	$nombreModulo = nombreDelModulo($idModulo, $conSubgrupo);
	$actividades = actividadActual($idModulo, $noActividad);
	
}

$idActividad='';
$idFortaleza='';
$actPrincipal='';
$actividadFortaleza='';
$subGrupoAlterno='';
$subGrupoActual=usariosSeleccionados($actividades, $idInicio, $conSubgrupo,$idActividad,$idFortaleza,$actPrincipal, $actividadFortaleza,$subGrupoAlterno);

$usuarios = usuariosEnUnInicio($idInicio,true,$subGrupoActual);
?>
<h2 class="text-center">En <? echo $nombreModulo; ?></h2>

<div class="panel panel-primary">
    <div class="panel-heading">
    	<h3 class="panel-title"><? echo $actPrincipal; ?>:</h3>
    </div>
    <div class="panel-body">
    	<h4 class="text-center">Actividad principal: <a class="btn btn-info btn-xs" href="javascript:void(0)" onClick="verDetalle(<? echo $idActividad; ?>,1)" title"detalle" id="detalle" role="button">Ver detalle</a></h4>
        <div id="descripcionA"></div>
        <ul class="list-group">
        	<?	foreach($usuarios as $key => $aspirante) { ?> 
            <li class="list-group-item"><? echo $aspirante; ?> - <span style="font-size: 90%;" class="label label-default"> <? echo record($key,$idActividad); ?></span></li>
            <? } ?>
        </ul>
    </div>
</div>
<?
if($actividadFortaleza != ''){
	$usuarios = usuariosEnUnInicio($idInicio,true,$subGrupoAlterno);
?>
	<div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><? echo $actividadFortaleza; ?>:</h3>
        </div>
        <div class="panel-body">
            <h4 class="text-center">En actividad de fortalecimiento: <? if($idFortaleza != ""){ ?><a class="btn btn-info btn-xs" href="javascript:void(0)" onClick="verDetalle(<? echo $idFortaleza; ?>,2)" title"detalle" id="detalle" role="button">Ver detalle</a><? } ?></h4>
            <div id="descripcionB"></div>
            <ul class="list-group">
                <?	foreach($usuarios as $key => $aspirante) { ?> 
                <li class="list-group-item"><? echo $aspirante; ?></li>
                <? } ?>
            </ul>
        </div>
    </div>
	
<?
}
?>

<div class="well" hidden="hidden">
    <h2 class="text-center" id="cronoTotal" style="font-size: 350%;">
        <div id="crono" style="width:220px;display:inline-block; text-align:right; padding-right:0px; margin-right:0px;"></div>
        <div class="small" style="width:60px;display:inline-block; text-align:left; padding-left:0px; margin-left:0px;" id="cronoDec"></div>
    </h2>
</div>

<input type="hidden" id="lugar" name="lugar" value="aspirantes"/>  


