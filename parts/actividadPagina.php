<?php
$hayUnError=false;

if(!$primerCarga){
	include_once '../includes/inc.funciones.php';
	include_once '../includes/inc.conexion.php';
	include_once '../includes/formsBase.php';
	
	if(!$jerarquia = permiteAcceso(false)) exit;

	$noActividad = "";
	$idInicio="";
	$idModulo = moduloActual($noActividad, $idInicio);
	if($jerarquia < 60 || $noActividad == "" || $noActividad == 0) exit;
	
	//cambiar a siguente actividad o cerrar
	$comoCalifica = comoCalificaElModulo($idModulo);
	if($comoCalifica == 0)
	{
		siguenteActividad($idInicio,$noActividad, $idModulo);
		$idModulo = moduloActual($noActividad, $idInicio);
	}

	//seguir con la carga normal
	$conSubgrupo="0";
	$nombreModulo = nombreDelModulo($idModulo, $conSubgrupo);
	$actividades=actividadActual($idModulo, $noActividad);
	
	$registrado = registrarActividad($idInicio,$noActividad,$actividades[0]['orden']);
	
	$timpoTrascurrido=0;
	if($registrado == "ERROR") $hayUnError=true;
	elseif($registrado != "REGISTRADO"){
		$timpoTrascurrido=$registrado;
	}
	
}

if($actividades[0]['enCircuito'] == 1)
{
	$subgruposActivos=gruposEnUnInicio($idInicio);
	$cantidadSubgrupos=count($subgruposActivos);
	$circuitoActual = circuitoEnInicio($idInicio);
	//var_dump($cantidadSubgrupos);
	if(($cantidadSubgrupos == 1 && $circuitoActual != 2)|| ($cantidadSubgrupos == 2 && $circuitoActual == 3))
	{
		$idActividad = $actividades[0]['idActividadFortaleza'];
		$detalle = detalleActividad($idActividad);
	}else{
		$detalle = $actividades[0];
	}
}else{
	$detalle = $actividades[0];
}

if($hayUnError){ ?> <h1 class="text-center">OCURRI&Oacute; UN ERROR</h1> <? } ?>
<h2 class="text-center">En <? echo $nombreModulo; ?></h2>
<h3 class="text-center"><? echo $detalle['actividad']; ?>:</h3>
<div class="row">
    <div class="col-xs-12 col-md-8 col-md-offset-2">
        <img class="img-thumbnail" style="height:100%;width:100%; object-fit:contain;margin-bottom: 20px;max-height: 560px;" src="<? echo $detalle['foto']; ?>"alt="foto"/>
    </div>
</div>

<div class="well">
    <h2 class="text-center" id="cronoTotal" style="font-size: 350%;">
        <div id="crono" style="width:220px;display:inline-block; text-align:right; padding-right:0px; margin-right:0px;"></div>
        <div class="small" style="width:60px;display:inline-block; text-align:left; padding-left:0px; margin-left:0px;" id="cronoDec"></div>
    </h2>
</div>

<input type="hidden" id="lugar" name="lugar" value="actividad"/>
<?
if($actividades[1])
{ ?>
	<p class="text-right">Siguente actividad: <b><? echo $actividades[1]['actividad']; ?></b></p> 
  <?
}else{ ?>
	<p class="text-right">Siguente actividad: <b>FIN</b></p>
<?
}
?>


    
    