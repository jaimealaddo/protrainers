<?php
if(!$primerCarga){
	include '../includes/inc.funciones.php';
	include '../includes/inc.conexion.php';
	include '../includes/formsBase.php';
	
	if($jerarquia = permiteAcceso(false) && isset($_GET['modulo'])) $modulo = $_GET['modulo'];
	else exit;
	if(isset($_GET['moduloAlt'])) $moduloAlt = $_GET['moduloAlt'];
}
$modulosAlternos = modulosAlternos($modulo);
if(count($modulosAlternos) !=0) echo campoSelectBasico("moduloAlt","M&oacute;dulo alterno",$moduloAlt, "icon-sitemap", $modulosAlternos,false);
?>
