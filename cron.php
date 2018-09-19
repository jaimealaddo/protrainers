<?php 

include "includes/inc.funciones.php";
include 'includes/inc.conexion.php';

$br = "\r\n";
$CC = "xfoficial@hotmail.com";

$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
$dentroUnaHora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60) + (60*60));
$query = "SELECT `0010_usuarios`.`nombre` AS `usuario`, 
				 `0010_usuarios`.`mail`, 
				 `0040_seguimiento`.`idCalendario`, 
				 `0040_seguimiento`.`fecha`, 
				 `0040_seguimiento`.`concepto`, 
				 `0020_clienteProveedor`.`idClienteProv`, 
				 `0020_clienteProveedor`.`nombre` AS `cliente` 
			FROM `0040_seguimiento` 
			INNER JOIN `0015_usuariosClientes` ON `0040_seguimiento`.`idClienteProv`=`0015_usuariosClientes`.`idClienteProv` 
			INNER JOIN `0010_usuarios` ON `0015_usuariosClientes`.`idUsuario`=`0010_usuarios`.`idUsuario` 
			INNER JOIN `0020_clienteProveedor` ON `0040_seguimiento`.`idClienteProv` = `0020_clienteProveedor`.`idClienteProv` 
			WHERE `0040_seguimiento`.`activo` = '1' 
				 AND `fecha` > '".$ahora."' 
				 AND `fecha` < '".$dentroUnaHora."' 
			ORDER BY `fecha` 
			LIMIT 0, 20";
$resultadoSQL = ejecutarSQL($query, $id_Personal, $_SERVER['REQUEST_URI'], __LINE__);
while ($dato = mysqli_fetch_array($resultadoSQL))
{ 
	$fecha = $dato['fecha'];
	$concepto = $dato['concepto'];
	$idCalendario = $dato['idCalendario'];
	$idCliente = $dato['idClienteProv'];
	$cliente = $dato['cliente'];
	$usuario = $dato['usuario'];
	$para = $dato['mail'];
	
	
	
	$body = '<html>'.$br
			.'<body>'.$br;
	$body .= "<h2>Mensaje Cron ERP del cliente: $cliente - ID=$idCliente</h2><br />";
	$body .= "<p>A las " . horaSimple($fecha) . " tienes la siguente actividad: <b>" . $concepto . '</b><br />idCalendario = <a role="button" href="https://cideaa.com/ERP/seguimiento.php?idClienteProv='.$idCliente.'&id='.$idCalendario.'">'.$idCalendario.'</a></p>';
	$body .= "<h3>De parte de todo el equipo de CIdEAA &#161;Te deseamos una excelente semana!</h3>";
	$body .= '</body>'.$br.'</html>';
	//echo $para.$body;
	
	mandarMail($body,$para,"ERP CIdEAA", $CC);
	
	
}
	
	/*
	$body = '<html>'.$br
			.'<body>'.$br;
	$body .= "<p>Mensaje Cron CRM</p><br />";
	$body .= "<p>Rutina TERMINADA con el query $query</p><br /><br />";
	$body .= "<p>De parte de todo el equipo de Facilitadora Inmobiliaria S.A. de C.V. &#161;Te deseamos una excelente semana!</p>";
	$body .= '</body>'.$br.'</html>';
	email($body,"AVISO CRM",$CC, $para);
	*/
	


	

 
 