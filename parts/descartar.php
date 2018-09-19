<?php
include '../includes/inc.funciones.php';
include '../includes/inc.conexion.php';

if(permiteAcceso(false) && isset($_GET['idClienteProv']) && isset($_GET['motivo']))
{
	$idUsuario = $_COOKIE['idUsuario'];
	$jerarquia = jerarquiaTarjeta($idUsuario);
	$idClienteProv = $_GET['idClienteProv'];
	$motivo = $_GET['motivo'];
}else{
	exit;
}

$query="INSERT INTO `0025_cientesDescartados`(`idClienteProv`, `idDescarte`,`idUsuario`) VALUES ('".$idClienteProv."','".$motivo."','".$idUsuario."')";
ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);


$query="UPDATE `0020_clienteProveedor` SET `descartado`='1' WHERE `idClienteProv`= '".$idClienteProv."'";
ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);

echo "OK";
?>
