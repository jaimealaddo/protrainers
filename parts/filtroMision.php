<?php
if(!$primerCarga){
	include '../includes/inc.funciones.php';
	include '../includes/inc.conexion.php';
	include '../includes/formsBase.php';
	
	if($jerarquia = permiteAcceso(false) && isset($_GET['modulo']) && isset($_GET['mision']))
	{
		$modulo = $_GET['modulo'];
		$idMision = $_GET['mision'];
	}else{
		exit;
	}
}
echo campoSelectBasico("modulo","M&oacute;dulo",$modulo, "icon-sitemap", modulosPorMisionNormal($idMision),true,false,'alternos()');
?>
