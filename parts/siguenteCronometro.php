<?php
if(!$primerCarga){
	include '../includes/inc.funciones.php';
	include '../includes/inc.conexion.php';
	include '../includes/formsBase.php';
	
	if(!$jerarquia = permiteAcceso(false)) exit;
}

$noActividad = "";
$idInicio="";
$idModulo = moduloActual($noActividad, $idInicio);
if($jerarquia < 60 || $noActividad == "" || $noActividad == 0) exit;

$actividades = actividadActual($idModulo, $noActividad);

if($actividades[1])
{
	echo $actividades[1]['minutos']."|".comoCalificaElModulo($idModulo);
}



?>
