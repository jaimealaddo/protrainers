<?php

if(!$primerCarga){
	include_once '../includes/inc.funciones.php';
	include_once '../includes/inc.conexion.php';
	include_once '../includes/formsBase.php';
	
	if(!$jerarquia = permiteAcceso(false)) exit;
}

$noActividad = "";
$idInicio="";
$idModulo = moduloActual($noActividad, $idInicio);
if($jerarquia < 60 || $noActividad == "" || $noActividad == 0) exit;

//cargar datos
$conSubgrupo="0";
$nombreModulo = nombreDelModulo($idModulo, $conSubgrupo);
$actividades=actividadActual($idModulo, $noActividad);
$comoCalifica = comoCalificaElModulo($idModulo);

$idActividad='';
$idFortaleza='';
$actPrincipal='';
$actividadFortaleza='';
$subGrupoAlterno='';
$seCalifica=true;
$subGrupoActual=usariosSeleccionados($actividades, $idInicio, $conSubgrupo,$idActividad,$idFortaleza,$actPrincipal, $actividadFortaleza,$subGrupoAlterno, $seCalifica);

//var_dump($subGrupoActual);

$usuarios=usuariosEnUnInicio($idInicio, true,$subGrupoActual);
if(count($usuarios) != 0 && $seCalifica){

?>

	<h2 class="text-center">En <? echo $nombreModulo; ?></h2>

	<div class="panel panel-primary">
    	<div class="panel-heading"><? if($comoCalifica == 0)echo "Calificaci&oacute;n del m&oacute;dulo ".$nombreModulo; else echo "Calificaci&oacute;n de ". $actPrincipal; ?>:</div>
    	<div class="panel-body">
			<?
            foreach($usuarios as $key => $dato)
            {
                if($comoCalifica == 0)
                {
                    echo checkBox('calif'.$key, $dato, $calif);
                }
                else
                {
                    echo campoTel('calif'.$key, $dato.' &nbsp;<span style="font-size: 90%;" class="label label-default">'.record($key,$idActividad).'</span>', 'puntuaci&oacute;n', $calif,'icon-trophy',false);
                }
            }
            ?>
    	</div>
	</div>

    <div class="well" hidden="hidden">
        <h2 class="text-center" id="cronoTotal" style="font-size: 350%;">
            <div id="crono" style="width:220px;display:inline-block; text-align:right; padding-right:0px; margin-right:0px;"></div>
            <div class="small" style="width:60px;display:inline-block; text-align:left; padding-left:0px; margin-left:0px;" id="cronoDec"></div>
        </h2>
    </div>
    
    <input type="hidden" id="lugar" name="lugar" value="calificar"/>  
<?
}else{
	siguenteActividad($idInicio,$noActividad, $idModulo);
}
?>
