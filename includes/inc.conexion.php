<?php 
function conectarse()
{
	$dbhost = "localhost";
	$dbuser = "********";	
	$dbpwd = "*********";
	$basedatos = "****************";
	
	$conectar = mysqli_connect($dbhost, $dbuser, $dbpwd, $basedatos);
	if (!$conectar) 
	{
		echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
		echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
		echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
		exit;
	}
	mysqli_query($conectar, "SET NAMES 'utf-8'");//Poner caracteres en la conexión como UTF-8
	mysqli_query($conectar, "SET time_zone = '-06:00'");
	mysqli_query($conectar, 'SET @@session.time_zone = "-06:00"');
	
	return $conectar;   //Regresa $conectar para su uso
}

function ejecutarSQL($query, $user, $origen, $linea)
{
	$link = conectarse();
	$resultadoSQL = mysqli_query($link, $query) or die(mailError($query, $linea, $origen." - USER: ".$user, mysqli_error($link)) . " - " . mysqli_close($link)." - Error ".$linea);
	mysqli_close($link);
	return $resultadoSQL;
}

function insertSQL($query, $user, $origen, $linea)
{
	$link = conectarse();
	mysqli_query($link, $query) or die(mailError($query, $linea, $origen." - USER: ".$user, mysqli_error($link)) . " - " . mysqli_close($link)." - Error ".$linea);
	$resultadoSQL = mysqli_insert_id($link);
	mysqli_close($link);
	return $resultadoSQL;
}



//fastmenu
function conectarseFast()
{
	$dbhost = "67.225.220.158";
	$dbuser = "fastmenu_t0t4l";	
	$dbpwd = "4cc3s0T0t4l";
	$basedatos = "fastmenu_fastmenu";
	/*
	$dbhost = "localhost";
	$dbuser = "cideaaco_credenc";	
	$dbpwd = "4cc3s0T0t4l";
	$basedatos = "cideaaco_credenciales";
	*/
	$conectarB = mysqli_connect($dbhost, $dbuser, $dbpwd, $basedatos);
	//var_dump($conectarB);
	if(!$conectarB) 
	{
		echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
		echo "<br/>errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
		echo "<br/>error de depuración: " . mysqli_connect_error() . PHP_EOL;
		exit;
	}else{
		echo "bien CONECTADO - ";
	}
	
	mysqli_query($conectarB, "SET NAMES 'utf-8'");//Poner caracteres en la conexión como UTF-8
	mysqli_query($conectarB, "SET time_zone = '-06:00'");
	mysqli_query($conectarB, 'SET @@session.time_zone = "-06:00"');
	
	return $conectarB;   //Regresa $conectar para su uso
}

function ejecutarSQLfast($query, $user, $origen, $linea)
{
	$linkB = conectarseFast();
	$resultadoSQL = mysqli_query($linkB, $query) or die(mailError($query, $linea, $origen." - USER: ".$user, mysqli_error($linkB)) . " - " . mysqli_close($linkB)." - Error ".$linea);
	mysqli_close($linkB);
	return $resultadoSQL;
}

function insertSQLfast($query, $user, $origen, $linea)
{
	$linkB = conectarseFast();
	mysqli_query($linkB, $query) or die(mailError($query, $linea, $origen." - USER: ".$user, mysqli_error($linkB)) . " - " . mysqli_close($linkB)." - Error ".$linea);
	$resultadoSQL = mysqli_insert_id($linkB);
	mysqli_close($linkB);
	return $resultadoSQL;
}


?>
