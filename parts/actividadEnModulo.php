<?php
if(!$primerCarga){
	include '../includes/inc.funciones.php';
	include '../includes/inc.conexion.php';
	include '../includes/formsBase.php';
	
	if($jerarquia = permiteAcceso(false) && isset($_GET['modulo']) && isset($_GET['actividad']))
	{
		$modulo = $_GET['modulo'];
		$actividad = $_GET['actividad'];
	}else{
		exit;
	}
}

if($modulo != "")
{
	if($despuesDe == "")
	{
		if($actividad =="")
		{
			$query="SELECT `idActividadModulo` FROM `0031_actividadModulo` WHERE `idModulo` = '".$modulo."' ORDER BY `orden` DESC LIMIT 1";
			//echo $query;
			$resultadoSQL = ejecutarSQL($query, $tarjeta, $_SERVER['REQUEST_URI'], __LINE__);
			while($datoA=mysqli_fetch_assoc($resultadoSQL)){
				$despuesDe = $datoA['idActividadModulo'];
			}
		}else{
			$query="SELECT `orden` FROM `0031_actividadModulo` WHERE `idModulo` = '".$modulo."' AND `idActividad` = '".$actividad."' LIMIT 1";
			$resultadoSQL = ejecutarSQL($query, $tarjeta, $_SERVER['REQUEST_URI'], __LINE__);
			while($datoA=mysqli_fetch_assoc($resultadoSQL)){
				$orden = $datoA['orden'];
			}
			if($orden != "")
			{
				$query="SELECT `idActividadModulo` FROM `0031_actividadModulo` WHERE `idModulo` = '".$modulo."' AND `orden` < '".$orden."' ORDER BY `orden` DESC LIMIT 1";
				$resultadoSQL = ejecutarSQL($query, $tarjeta, $_SERVER['REQUEST_URI'], __LINE__);
				while($datoA=mysqli_fetch_assoc($resultadoSQL)){
					$despuesDe = $datoA['idActividadModulo'];
				}
			}else{
				$query="SELECT `idActividadModulo` FROM `0031_actividadModulo` WHERE `idModulo` = '".$modulo."' ORDER BY `orden` DESC LIMIT 1";
				$resultadoSQL = ejecutarSQL($query, $tarjeta, $_SERVER['REQUEST_URI'], __LINE__);
				while($datoA=mysqli_fetch_assoc($resultadoSQL)){
					$despuesDe = $datoA['idActividadModulo'];
				}
			}
		}
		if($despuesDe == "") $despuesDe="A-L-L";
	}
	echo campoSelectBasico("despuesDe","Desp&uacute;es de:",$despuesDe, "icon-sort-numeric-asc", actividades($modulo),true,false,'','AL INICIO');
}
?>
