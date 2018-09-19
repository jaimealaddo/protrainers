<?php

$timeZone= -5;
$mensaje = '';
$mensajeModal = false;
$diferenciaHoraServidor = -1;

/*
	ESTA ES LA VERSION QUE FUNCIONA BIEN
	$timeEnBD = strtotime($FechaGuardadaEnMySqlEnHoraLocal." GMT") - ($timeZone*60*60);
	$cadenaDelServidorPHP = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60)) ."<br>";
	
	$cadenaDeBD = gmdate("Y-m-d H:i:s", $timeEnBD + ($timeZone*60*60));
	
	$cadenaDeBD = $cadenaDelServidorPHP
*/

if(isset($_COOKIE['idPRO'])) $idUsuario = $_COOKIE['idPRO'];

function permiteAcceso($salir=true)
{
	/*  FunciÃ³n que verifica si la cookie del usuario ha sido activada para permitir su acceso al resto
	de las distintas secciones del sistema, permitiendo su reingreso tras apagar o prender su dispositivo
	desde donde esta accesando al sistema mientras su cookie no haya expirado.<br />
	Al detectar actividad por parte del usuario antes de que la cookie haya expirado, extender el TTL de la cookie
	*/
	
	//Si la cookie no existe o el usuario no ingreso previamente redirigir a index.php
	if (isset($_COOKIE['idPRO']) && isset($_COOKIE['pwdPRO']))
	{	
		$idUsuario = $_COOKIE['idPRO'];
		$pwd = $_COOKIE['pwdPRO'];
		if ($idUsuario != "" && $pwd != "")
		{
			if (validaIdUsuario($idUsuario,$pwd))
			{	//Por indicacion de actividad, extender TTL de $_COOKIE['usuario']
				if(!isset($_COOKIE['duracionPRO']))
				{
					setcookie("idPRO",$idUsuario,time()+(30*24*60*60),"/"); //para un mes
					setcookie("pwdPRO" ,     $pwd ,     time()+(30*24*60*60),"/"); //para un mes
				}
				$jerarquia = jerarquiaTarjeta($idUsuario);
				return $jerarquia;
			}
		}
	}
	if($salir) salirJerarquia("index.php");
	return false;
}

function salirJerarquia($origen="")
{
	$destino = "index.php?alerta=1&origen=".$origen;
	header("Location:".$destino);
	echo '<script>window.location = "'.$destino.'";</script>';
	exit;
}

function jerarquiaTarjeta($idUsuario)
{
	$query = "SELECT `jerarquia` FROM `0010_usuarios` WHERE `idUsuario` = '".$idUsuario."'";
	$resultadoSQL = ejecutarSQL($query, $tarjeta, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$jerarquia = $datoA['jerarquia']+0;
	}
	return $jerarquia;
}

function usuarioEnActividad($idUsuario)
{
	$query = "SELECT `estado` FROM `0010_usuarios` WHERE `idUsuario` = '".$idUsuario."'";
	$resultadoSQL = ejecutarSQL($query, $tarjeta, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$estado = $datoA['estado']+0;
	}
	return $estado;
}

function salir()
{
	//	Funcion para cerrar la sesion por solicitud del usuario
	if (isset($_COOKIE['idPRO'])){
		setcookie("idPRO","",time()-(30*24*60*60),"/");
		unset($_COOKIE['idPRO']);
		setcookie("pwdPRO","",time()-(30*24*60*60),"/");
		unset($_COOKIE['pwdPRO']);
		}
	echo '<script>window.location = "https://www.cideaa.com";</script>';
	exit;
}

function validaUsuario($usuario,$pwd) // SOLO PARA ENTRAR
{
	/*	FunciÃ³n que valida el nombre de usuario para el registro de nuevos Asesores en el CRM.
	Permite letras y dÃ­gitos, forza las letras a minÃºsculas. Longitud mÃ¡xima 16 caracteres.
	No Permite espacios en blanco ni caracteres especiales. 
	Creada por: IT. Admin. Samuel L. Valadez.<br />
	Fecha: Ene/15/2014 	 */
	/*	Funcion para validar el acceso del nombre de usuario al sistema. devuelve el id del usuario o una cadena vacia */
	
	if($usuario == "" || $pwd == '')
	{
		return "";
	}
	include_once 'includes/inc.conexion.php';
	$link = conectarse();
	
	$usuario = mysqli_real_escape_string($link,$usuario);
	$pwd = mysqli_real_escape_string($link, $pwd);
	
	$query = "SELECT `idUsuario` FROM `0010_usuarios` WHERE `user` = '".$usuario."' AND `pwd` = '".$pwd."' AND `activo` = '1' LIMIT 1";
	$resultadoSQL = mysqli_query($link, $query) or die(mailError($query, $linea, $origen." - USER: ".$usuario, mysqli_error($link)) . " - " . mysqli_close($link)." - Error ".$linea);
	mysqli_close($link);
	$res = mysqli_num_rows($resultadoSQL);
	if ($res != 0){
		$res = mysqli_fetch_assoc($resultadoSQL);
		$valida = ($res['idUsuario']);
	}else{
		$valida = "";
	}
	return $valida;
}

function validaIdUsuario($idUsuario,$pwd)
{ /*	Funcion para validar el acceso con id del usuario al sistema. */
	if($idUsuario == "" || !is_numeric($idUsuario) || $pwd == "")
	{
		return FALSE;
	}
	
	include_once 'includes/inc.conexion.php';
	$link = conectarse();
	$pwd = mysqli_real_escape_string($link, $pwd);
	$query = "SELECT `idUsuario` FROM `0010_usuarios` WHERE `idUsuario` = '".$idUsuario."' AND `pwd` = '".$pwd."'";
	$busca_usuario = mysqli_query($link, $query) or die(mailError($query, $linea, $origen." - USER: ".$tarjeta, mysqli_error($link)) . " - " . mysqli_close($link)." - Error ".$linea);
	mysqli_close($link);
	$res = mysqli_num_rows($busca_usuario);
	//Resultados q coinciden con el nombre de usuario (0-no existe, 1-encontrado)
	if ($res != 0){
		$valida = TRUE;
	}else{
		$valida = FALSE;
	}
	return $valida;
}

function cortarPalabras($texto, $tam)
{//cortar una cadena de texto donde termina la palabra
	if(strlen($texto)>$tam) 
	{
		$car="";
		while($car != " ")
		{
			$car = substr($texto,$tam,1);
			$tam++;
			if($tam > strlen($texto)) break;
		}
		$tam--;
		$texto = substr($texto,0,$tam)."...";
	}
	return $texto;
}

function cortarTexto($texto, $tam, $form=false)
{ //cortar una cadena de texto HTML en una cantidad exacta de caracteres con caracteres especiales
	if($form)
	{
		$texto = htmlentities($texto);
	}
	if(strlen($texto)>$tam) 
	{
		$i = 0;
		$j = 0;
		$esp = 0;
		$bandera = false;
		$cadenaFinal="";
		$cadenaTemp = "";
		while($i < $tam && $j<strlen($texto))
		{
			$carTemp = substr($texto,$j,1);
			if($carTemp == "&" && !$bandera)
			{
				$bandera = true;
				//echo ("BANDERA-ON<br>");
			}
			elseif($bandera)
			{
				if($carTemp == " "){
					$bandera = false;
					//echo ("BANDERA-OFF - ESPACIO<br>");
					$i = $i + (strlen($cadenaTemp));
				}
				elseif($carTemp == ";")
				{
					$bandera = false;
					$esp = $esp +  (strlen($cadenaTemp));
					//echo ( "BANDERA-OFF - FIN CARACTER ESP ".$esp."<br>");
				}
				elseif($j == (strlen($texto) - 1))
				{
					$bandera = false;
					//echo ("BANDERA-OFF - FINAL<br>");
				}
			}
			
			//echo ("j=".$j." -> "."i=".$i." -> ".$carTemp."<br>");
			if($bandera)
			{
				$cadenaTemp .= $carTemp;
			}
			else
			{
				$cadenaTemp .= $carTemp;
				$cadenaFinal .= $cadenaTemp;
				$cadenaTemp = "";
				$i++;
			}
			$j++;
		}
		$corte = ($tam + $esp);
		//echo("Esp ".$esp." - tam ".$tam. "CORTE ".$corte."<br>");
		$texto = substr($cadenaFinal,0,$corte);
	}
	return $texto;
}

function mayusculas($texto, $formularioNoHtml=false)
{
	if($formularioNoHtml)
	{
		$texto = htmlentities($texto);
	}
	
	$j = 0;
	$bandera = false;
	$banderaB = false;
	$cont=0;
	$cadenaFinal="";
	while($j<strlen($texto))
	{
		if($cont === 2){
			$cont = 0;
			$bandera = true;
			$banderaB= false;
		}
		
		$carTemp = substr($texto,$j,1);
		if(($carTemp == "&" || $banderaB) && $cont <= 2)
		{
			$banderaB=true;
			$cont++;
		}
		elseif($bandera)
		{
			if($carTemp == " ")
			{
				$bandera = false;
			}
			elseif($carTemp == ";")
			{
				$bandera = false;
			}
		}
		
		if($bandera)
		{
			$cadenaTemp .= $carTemp;
		}
		else
		{
			$cadenaTemp .= strtoupper($carTemp);
		}
		
		$j++;
	}
	return $cadenaTemp;
}

function nombreById($id)
{
	//Buscar el nombre de usuario con el id
	$nombre = false;
	$query = "SELECT `nombre` FROM `0010_usuarios` WHERE `idUsuario` = '".$id."' LIMIT 1";
	$resultadoSQL = ejecutarSQL($query, $user, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	$noResult = mysqli_num_rows($resultadoSQL);
	//Resultados q coinciden con el nombre de usuario (0-no existe, 1-encontrado)
	if ($noResult != 0){
		$res = mysqli_fetch_assoc($resultadoSQL);
		$nombre = ($res['nombre']);
	}
	return $nombre;
}

function userMailById($id)
{//Buscar el mail/usuario del usuario con el id
	$query = "SELECT `user` FROM `0010_usuarios` WHERE `idUsuario` = '".$id."' LIMIT 1";
	$resultadoSQL = ejecutarSQL($query, $user, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	$noResult = mysqli_num_rows($resultadoSQL);
	//Resultados q coinciden con el nombre de usuario (0-no existe, 1-encontrado)
	if ($noResult != 0){
		$res = mysqli_fetch_assoc($resultadoSQL);
		$nombre = ($res['user']);
	}else{
		$nombre = false;
	}
	return $nombre;
}

function idByMail($mail)
{
	//Buscar el id[0] y nombre [1] usuario con mail/usuario
	$query = "SELECT `idUsuario`, `nombre` FROM `0010_usuarios` WHERE `mail` = '".$mail."' LIMIT 1";
	$resultadoSQL=ejecutarSQL($query, $user, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	$res = mysqli_num_rows($resultadoSQL);
	if($res!=0)
	{
		$datoA= mysqli_fetch_assoc($resultadoSQL);
		$cUsuario = $datoA['cUsuario'];
		$nombre = $datoA['nombreUsuario'];
		$usuario[0] = ($cUsuario);
		$usuario[1] = ($nombre);
		return $usuario;
	}else{
		return array();
	}
}

function dameURL()
{ 
	//URL ACTUAL
	$url=$_SERVER['REQUEST_URI'];
	$pos=strpos($url,"?");
	if($pos>0){
		$url=substr($url,10,$pos-10); //cambiar el 10 a 1 cuando migre a un sitio
	}else{
		$url=substr($url,10,strlen($url)); //cambiar el 10 a 1 cuando migre a un sitio
	}
	return $url;
}

function dameUrlCompleta($sinCodificar=false)
{
	$url = $_SERVER['REQUEST_URI'];
	if(!$sinCodificar)
	{
		$url = str_replace("&", "|", $url);
	}
	$url = substr($url,1,strlen($url));
	return $url;
}

function mostrarModal($mens)
{
	global $mensaje;
	$mensaje = $mens;
	global $mensajeModal;
	$mensajeModal = true;
	return true;
}

function enumDropdown($table_name, $column_name)
{
	/* Funcion que devuelve la lista de campos de una lista ENUM dentro de la base de datos */
   	$query = "SELECT COLUMN_TYPE 
   			FROM INFORMATION_SCHEMA.COLUMNS
       		WHERE TABLE_NAME = '$table_name' AND COLUMN_NAME = '$column_name'";
   	$result = ejecutarSQL($query, $user, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	
	$row = mysql_fetch_array($result);
	$enumList = explode(",", str_replace("'", "", substr($row['COLUMN_TYPE'], 5, (strlen($row['COLUMN_TYPE'])-6))));
	
    return $enumList;
}

function emptyRecursivo($matriz, $salir = FALSE)
{
	static $salir;
	$salir = FALSE;
	$nuevaMatriz=array();
 	if(is_array($matriz)){
		foreach($matriz as $value){
        	if(!empty($value)&& is_array($value)){
				$nuevaMatriz = array_merge($nuevaMatriz,$value);
			}elseif($value != '' && !is_array($value)){
				$salir = TRUE;
				return FALSE;
			}
		}
		if(!empty($nuevaMatriz)){
			emptyRecursivo($nuevaMatriz, $salir);
		}
	}elseif($matriz != ''){
        $salir=TRUE;
		return FALSE;
	}
	//si llega a este punto si esta vacio
	if($salir === FALSE){
		return TRUE;
	}
} 

function encriptarNumero($numero)
{
    
	/* * Funcion para encriptar un numero
 * todos los numero estan cifrados en 4 caracteres y dependiendo del digito en donde se encuentre
 * se asignan valores random para esconderlos
 * 1 = GMd5
 * 2 = kS5B
 * 3 = kV6S
 * 4 = n6H9
 * 5 = P5Fg
 * 6 = t*6V
 * 7 = mJQl
 * 8 = 5LR4
 * 9 = TyS2
 * 0 = q3Zu */
	//el primer digito se encripta con este formato donde las % son valores random y las X los valores
    //alfanumericos que tiene la equivalencia
    //%X%X%%XX% con una extencion de 8 caracteres
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $arrayA = array(2,4,7,8);
    $arrayB = array(1,3,7,9);
    $arrayC = array(1,4,6,8);
    $numero = (string)$numero;
    $cadena = '';
    $totalDigitos = strlen($numero);
    for ($noDigito = 0, $caso = 1; $noDigito<$totalDigitos; $noDigito++, $caso++){
        $digito[$noDigito] = substr($numero, $noDigito, 1);
        switch ($digito[$noDigito]){
            case '1': $clave = "GMd5";		break;
            case '2': $clave = "kS5B";		break;
            case '3': $clave = "kV6S";		break;
            case '4': $clave = "n6H9";		break;
            case '5': $clave = "P5Fg";		break;
            case '6': $clave = "t*6V";		break;
            case '7': $clave = "mJQl";		break;
            case '8': $clave = "5LR4";		break;
            case '9': $clave = "TyS2";		break;
            case '0': $clave = "q3Zu";		break;
        }
        $posicionClave = 0;
        
        for ($j = 1; $j <= 9; $j++){
            switch (TRUE){
                case (in_array($j,$arrayA) && ($caso==1)):
                    $cadena .= substr($clave, $posicionClave, 1);
                    $posicionClave++;
                    break;
                case (in_array($j,$arrayB) && ($caso==2)):
                    $cadena .= substr($clave, $posicionClave, 1);
                    $posicionClave++;
                    break;
                case (in_array($j,$arrayC) && ($caso==3)):
                    $cadena .= substr($clave, $posicionClave, 1);
                    $posicionClave++;
                    break;
                default :
                    $cadena .= $characters[mt_rand(0, strlen($characters) - 1)];
            }
        }
        if ($caso == 3){
            $caso = 0;
        }    
    }
    return $cadena;
}

function desencriptarNumero($cadena)
{
    $arrayA = array(2,4,7,8);
    $arrayB = array(1,3,7,9);
    $arrayC = array(1,4,6,8);
    $largoCadena = strlen($cadena);
    $totalDigitos = 0;
    $clave = '';
    for ($noDigito = 0, $caso = 1, $j = 1; $noDigito<$largoCadena; $noDigito++, $j++){
       if ($caso == 4){
            $caso = 1;
        } 
        switch (TRUE){
            case (in_array($j,$arrayA) && ($caso==1)):
                $clave .= substr($cadena, $noDigito, 1);
                break;
            case (in_array($j,$arrayB) && ($caso==2)):
                $clave .= substr($cadena, $noDigito, 1);
                break;
            case (in_array($j,$arrayC) && ($caso==3)):
                $clave .= substr($cadena, $noDigito, 1);
                break;
        }   
        if ($j == 9){
            $j = 0;
            $caso ++;
            $totalDigitos++;
        }
    }
    $numero = '';
    for ($noDigito = 0; $noDigito<$totalDigitos; $noDigito++){
        $posicion = $noDigito * 4;
        $subClave = substr($clave, $posicion, 4);
        switch ($subClave){
            case 'GMd5': $numero .= "1"; break;
            case 'kS5B': $numero .= "2"; break;
            case 'kV6S': $numero .= "3"; break;
            case 'n6H9': $numero .= "4"; break;
            case 'P5Fg': $numero .= "5"; break;
            case 't*6V': $numero .= "6"; break;
            case 'mJQl': $numero .= "7"; break;
            case '5LR4': $numero .= "8"; break;
            case 'TyS2': $numero .= "9"; break;
            case 'q3Zu': $numero .= "0"; break;
        }
    }
    return $numero;
}

function validaCampoTXT($dato,$tamMinimo,$nom=TRUE, $POST="", &$errLista="")
{	
	/*	
		Funcion para validar campos de cliente que contienen TEXTO: nombres,descripcion,dirección. 
		El parámetro "nom" indica que se debe también verificar que el nombre de cliente o cónyuge 
		no contengan unicamente números
	*/
	if($POST != "")
	{
		$nombre = $dato;
		$dato = $_POST[$dato];
	}
	$errores = false;
	$dato = (trim($dato));
	if ((strlen($dato) < $tamMinimo)||($nom===FALSE && !is_numeric("$dato"))){
		if($tamMinimo==0 && $dato === "")
		{
			if($nom===TRUE){
				return "";
			}else{
				return 0;
			}
		}
		$errores = true;
	}else{	//Remover acentos a todas las vocales, convertir a minÃºsculas y solo conservar la " Ã± "
		if($nom===false)
		{
			return $dato+0;
		}
		
		$dato = htmlentities($dato, ENT_QUOTES | ENT_IGNORE);
		$dato = str_replace(array("\r\n", "\r", "\n"), "<br />", $dato);
		
		return $dato;
	}
	if($errores){
		$errLista .= '|Hay errores en los datos ingresados en el campo "<b>'.$nombre.'</b>"|';
		return false;
	}
}

function validaCampoSelect($dato,$POST,&$errLista="",$valorNo="")
{	
	/*	
		Funcion para validar campos de cliente que contienen TEXTO: nombres,descripcion,dirección. 
		El parámetro "nom" indica que se debe también verificar que el nombre de cliente o cónyuge 
		no contengan unicamente números
	*/
	$nombre = $dato;
	$dato = $POST[$dato];
		
	if ($dato == $valorNo)
	{
		$errLista .= '|Error en el campo "<b>'.$nombre.'</b>"|';
	}
	return $dato;
}

function to_utf8($string) 
{ 
// From https://w3.org/International/questions/qa-forms-utf-8.html 
    if (preg_match('%^(?: 
      [\x09\x0A\x0D\x20-\x7E]            # ASCII 
    | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte 
    | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs 
    | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte 
    | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates 
    | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3 
    | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15 
    | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16 
)*$%xs', $string) ) { 
        return $string; 
    } else { 
        return iconv( 'CP1252', 'UTF-8', $string); 
    } 
} 

function mailError($query,$linea,$archivo,$error)
{
	//funcion que manda un mail con el error
	$para    ='xfoficial@hotmail.com,';
	$asunto  ='Error desde cideaa PROTRAINERS';
	$mensaje = "ARCHIVO: ".$archivo . "\r\n" . "\r\n" .
				"LINEA: " . $linea . "\r\n" . "\r\n" .
				"ERROR: " . $error . "\r\n" . "\r\n" .
				"QUERY: " . $query;
				
	$desde='from:cideaa<atencion@cideaa.com.mx>';
	return mail($para,$asunto,$mensaje,$desde);
}

function mailSimple($mensaje,$para)
{
	//funcion que manda un mail con el error
	$asunto  ='PROTRAINERS DICE';
	
				
	$desde='from:bfamous<atencion@cideaa.com.mx>';
	return mail($para,$asunto,$mensaje,$desde);
}

function mandarMail($mensaje, $para="", $titulo="", $CC="")
{
	//funcion que manda un mail 
	require_once("../includes/class.phpmailer.php");
	//include_once("includes/class.phpmailer.php");
	if($titulo == "") $titulo = "Contacto";
    if($para == "") $para = "xfoficial@hotmail.com";
	$mail = new PHPMailer();

    $mail->From     = "atencion@cideaa.com";
    $mail->FromName = "CIdEAA PROTRAINERS"; 
    $mail->AddAddress($para); // Dirección a la que llegaran los mensajes.
	if($CC != "") $mail->AddAddress($CC);
// Aquí van los datos que apareceran en el correo que reciba

    $mail->WordWrap = 50; 
    $mail->IsHTML(true);     
    $mail->Subject  =  $titulo;
    $mail->Body     =  $mensaje;

// Datos del servidor SMTP

    $mail->IsSMTP(); 
    $mail->Host = "mail.cideaa.com:2525";  // Servidor de Salida.
    $mail->SMTPAuth = true; 
    $mail->Username = "atencion@cideaa.com";  // Correo Electrónico
    $mail->Password = "zer2Ama2Tres"; // Contraseña
	
	//echo "Mandando mail: <br />";
	return $mail->send();
}

function mensajeErrValida($nombre, $idioma=1)
{
	switch ($idioma){
		case(1):
			$mensaje = '|Revisa el campo "';
			$mensaje .= $nombre;
			$mensaje .= '", contiene errores|';
			break;
		case(2):
			$mensaje = '|Check the "';
			$mensaje .= $nombre;
			$mensaje .= '" field, it have errors|';
			break;
	}
	return $mensaje;
}

function validaCampoTelefono($dato,$digitos=10)
{
	/*
	quita todo caracter especial dejando solo digitos.
	Regresa cadena vacia si no es correcto
	*/
	
	$hayError = false;
	$dato = trim($dato);
	if (empty($dato) || strlen($dato) < $digitos){
		$hayError = true;
	}else{	
		//Si existe una coma( , ) indicador de que se captura mÃ¡s de un nÃºmero telefÃ³nico y separar los valores
		
		$caracteres = array("-","_",".","(",")","#","\\","/"," "); 
		$dato = str_replace($caracteres,"",$dato); //Remover caracteres especiales de cada valor
		$largoTLF = strlen($dato);
		if (!is_numeric($dato)){	//Verificar que no exista ningun caracter del abcedario en la cadena
			$hayError = true;
		}elseif($largoTLF != $digitos){
			$hayError = true; //Num. no cumple o excede la longitud (10 dÃ­gitos)
		}
	}
	if($hayError){
		return "";
	}else{
		return $dato;
	}
}
	
function validEmail($email)
{
   //Validador de Email que cumple la normativa RFC 2822
	/**
	Validate an email address.
	Provide email address (raw input)
	Returns true if the email address has the email 
	address format and the domain exists.
	*/
	//echo "UNO";
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
	  //echo"sin @";
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
		 //echo " muy corto o vacio";
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
		 //echo "dominio muy corto";
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
		 //echo "empieza o termina con punto";
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
		 //echo "tiene diagonales";
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
		 //echo "caracter invlaido en el dominio";
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
		 //echo "diagonal en el dominio";
      }
      else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local)))
         {
            $isValid = false;
			//echo "diagonal dos";
         }
      }
	  /*
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
		 echo "dns invalido";
      }
	  */
   }
   return $isValid;
}

function RestarSumarEnDate($min=0, $hora=0, $dia=0, $mes=0, $ano=0)
{
	//devuelve la fecha actual en para guardar en formato mySql y se puede sumar o restar desde minutos hasta aneos 
	$hora=$hora-2;
	$t = date("Y-m-d H:i:s", mktime(date("H")+ $hora, date("i") + $min, date("s"), date("m") + $mes, date("d") + $dia, date("Y") + $ano));
	return $t;
}

function fechaHora($fecha, $chica=false)
{
	//recibe fecha en formato mySql y la devuelve en humano
	$amPm = " a.m.";
	$dia = explode("-", $fecha);
	$dia[2] = substr($dia[2],0,2);
	$hora = explode(" ",$fecha);
	$hora = $hora[1];
	$hora = explode(":", $hora);
	switch ($dia[1]){
		case "01": $dia[1] = "Enero"; 		break;
		case "02": $dia[1] = "Febrero"; 	break;
		case "03": $dia[1] = "Marzo"; 		break;
		case "04": $dia[1] = "Abril"; 		break;
		case "05": $dia[1] = "Mayo"; 		break;
		case "06": $dia[1] = "Junio"; 		break;
		case "07": $dia[1] = "Julio"; 		break;
		case "08": $dia[1] = "Agosto"; 		break;
		case "09": $dia[1] = "Septiembre"; 	break;
		case "10": $dia[1] = "Octubre"; 	break;
		case "11": $dia[1] = "Noviembre"; 	break;
		case "12": $dia[1] = "Diciembre"; 	break;
	}
	if($hora[0] >= 12){
		$amPm = " p.m.";
		if($hora[0] != 12){
			$hora[0] = $hora[0] - 12;
		}
	}
	if($chica)
		$fechaHora = $dia[2] . "-" . substr($dia[1],0,3) . "-" .substr($dia[0],2,2)." ". $hora[0] . ":" . $hora[1] . substr($amPm,1,1); 
	else
		$fechaHora = $dia[2] . " de " . $dia[1] . " de " . $dia[0] . " a las " . $hora[0] . ":" . $hora[1] . $amPm; 
	return $fechaHora;
}

function horaSimple($fecha)
{
	//recibe fecha en formato mySql y la devuelve la hora en humano am/pm
	$amPm = " a.m.";
	$hora = explode(" ",$fecha);
	if(count($hora)>1){
		$hora = $hora[1];
	}elseif(count($hora)==1){
		$hora = $hora[0];
	}
	$hora = explode(":", $hora);
	if($hora[0] >= 12){
		$amPm = " p.m.";
		if($hora[0] != 12){
			$hora[0] = $hora[0] - 12;
		}
	}
	$fechaHora = $hora[0] . ":" . $hora[1] . $amPm; 
	return $fechaHora;
}

function fechaSola($fecha)
{
	//recibe fecha en formato mySql y la devuelve la fecha en humano
	$amPm = " a.m.";
	$dia = explode("-",$fecha);
	$dia[2] = substr($dia[2],0,2);
	switch ($dia[1]){
		case "01": $dia[1] = "Enero"; break;
		case "02": $dia[1] = "Febrero"; break;
		case "03": $dia[1] = "Marzo"; break;
		case "04": $dia[1] = "Abril"; break;
		case "05": $dia[1] = "Mayo"; break;
		case "06": $dia[1] = "Junio"; break;
		case "07": $dia[1] = "Julio"; break;
		case "08": $dia[1] = "Agosto"; break;
		case "09": $dia[1] = "Septiembre"; break;
		case "10": $dia[1] = "Octubre"; break;
		case "11": $dia[1] = "Noviembre"; break;
		case "12": $dia[1] = "Diciembre"; break;
	}
	$fechaHora = $dia[2] . " de " . $dia[1] . " de " . $dia[0]; 
	return $fechaHora;
}

function fechaObtenerDato($fecha)
{
	//recibe la fecha en formato mySQL y devuelve un array con los datos por separado
	if(preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $fecha)){
		$dia = explode("-",$fecha);
		$dia[2] = substr($dia[2],0,2);
		
		$hora = explode(" ",$fecha);
		$hora = $hora[1];
		$hora = explode(":", $hora);
		$fechaHora[0] = $dia[2]; //es el dia
		$fechaHora[1] = $dia[1]; //es el mes
		$fechaHora[2] = $dia[0]; //es el año
		$fechaHora[3] = $hora[0]; //es la hora formato 24h
		$fechaHora[4] = $hora[1]; //es el minuto
		$fechaHora[5] = $hora[2]; //es el segundo 
		return $fechaHora;
	}else{
		return false;
	}	
}

function horaDecimal($hora)
{
	//recibe la hora en decimales y regresa un array com hora y minutos
	if($hora == '') return array("-1","-1");
	$arrayHora = explode(".",$hora);
	if (count($arrayHora) > 1){
		$numero = "0." . $arrayHora[1];
		$numero = round($numero*60,-1);
		if($numero == 0)
		{
			$numero = $numero."0";
		}else{
			$numero = $numero."";
		}
		$arrayHora[1] = $numero;
	}else{
		$arrayHora[1] = "00";
	}
	return $arrayHora;
}

function formatoFechas($fecha, $hora, $min, $AmPm)
{
	//dar formato a la fecha para mySql partiendo de varios select con los datos por separado(citas)
	if($fecha == "0000-00-00" || substr($fecha,0,4)=== "0000" || substr($fecha,5,2)=== "00" || substr($fecha,8,2)=== "00" || !(preg_match("/^[0-9]{4}[\-]{1}[0-9]{2}[\-]{1}[0-9]{2}$/", $fecha))){
		$fecha = "NULL";
	}else{
		if($hora != '12'){
			$hora = $hora + $AmPm;
		}elseif($AmPm == "0"){
			$hora = "0";
		}
		if($hora < 10){
			$hora = "0".$hora;
		}
		//0000-00-00 00:00:00
		$fecha = "'".$fecha." ".$hora. ":" .$min. ":00'";
	}
		
	if(substr($fecha,12,2)=="00" &&  substr($fecha,15,2)=="00" &&  substr($fecha,18,2)=="00"){
		//echo substr($fecha,18,2);
		//echo "<br> ". $fecha;
		return "NULL";
	}
	return $fecha;
}

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function geoDistancia($lat1, $lon1, $lat2, $lon2)
{ 
 	//calcular la distancia en metros entre dos puntos de geolocalizacion
	// generally used geo measurement function
    $R = 6378.137; // Radius of earth in KM
    $dLat = $lat2 * M_PI / 180 - $lat1 * M_PI / 180;
    $dLon = $lon2 * M_PI / 180 - $lon1 * M_PI / 180;
    $a = sin($dLat/2) * sin($dLat/2) + cos($lat1 * M_PI / 180) * cos($lat2 * M_PI / 180) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $d = $R * $c;
    return $d * 1000; // meters
}

function extencionArchivo($nombreArchivo)
{
	$arrayArchivo = explode(".",$nombreArchivo);
	$extencionArchivo = $arrayArchivo[count($arrayArchivo)-1];
	$extencionArchivo = trim($extencionArchivo);
	if ($extencionArchivo[strlen($msgError)-1] == "/"){ //Borrar ultimo caracter "/" del mensaje de error
		$extencionArchivo = substr($extencionArchivo,0,strlen($extencionArchivo)-2);
	}
	return $extencionArchivo;
}

function verificarFoto($archivo, $requerida=true)
{
	$errores="";
	//Verificar la foto
	if ($archivo['name'] != ''){
		$tipo = $archivo['type'];
		
		if (!($tipo == "image/jpeg" || $tipo == "image/png" || $tipo == "image/jpg" || $tipo == "image/gif")){
			$errores .= '|la imagen debe estar en formato jpg o .png|';
		}
	}elseif($requerida){
		$errores = "|No se encontr&oacute; la foto|";
	}
	return $errores;
}

function guardarFotoSimple($archivo, $carpeta, $nombre, $multiples=false, $anchoDeseado=400, $altoDeseado=400)
{
	chdir("/home/cideaaco/public_html/protrainers/");	
	$ruta = "fotos/".$carpeta."/";
	if($multiples === false){
		$nombreOriginal = $archivo['name']; 
		$tmp_name = $archivo['tmp_name'];
	}
	else
	{
		$nombreOriginal = $archivo['name'][$multiples];
		$tmp_name = $archivo['tmp_name'][$multiples];
	}
	
	if(!file_exists($ruta)) //crear directorio
	{
		$respuesta = mkdir($ruta, 0777, true);
	}
	//rotar la foto
	Photo::adjustPhotoOrientation($nombreOriginal); 
		
	//guardar el archivo en la carpeta correspondiente
	$extencionArchivo = extencionArchivo($nombreOriginal);
	$foto = '';
	$destino =  $ruta.$nombre.".".$extencionArchivo;
	$destinoTemp =  $ruta.$nombre."B.".$extencionArchivo;
	$atributos=getimagesize($tmp_name); //[0]=ancho [1]=alto
	if($atributos[0]>$anchoDeseado || $atributos[1]>$altoDeseado){
		move_uploaded_file($tmp_name, $destinoTemp);
		if($atributos[0]<=$anchoDeseado)$anchoDeseado = $atributos[0]-1;
		if($atributos[1]<=$altoDeseado)$altoDeseado = $atributos[1]-1;
		cambiarTamFoto($destinoTemp, $destino, $anchoDeseado, $altoDeseado);
	}else{
		move_uploaded_file($tmp_name, $destino);
	}
	return $destino;
	
}

function cambiarTamFoto($origen, $destino, $ancho, $alto)
{
	include_once 'Imagen.php';
	$datos = '{    
				"imgOrigen" : "'.$origen.'",  
				"imgDestino": "'.$destino.'",  
				"ancho"     : "'.$ancho.'", 
				"alto"      : "'.$alto.'",
				"modo"      : 2,
				"filas"     : 5,
				"calidad"   : 95,
				"columnas"  : 5,
				"centrado"  : 2,
				"borrar"    : true
			  }';  
	$obj_img = new Imagen($datos);  
	$obj_img -> procesarImagen();
	
}

class Photo 
{
    private static function mirrorImage ( $imgsrc ) 
	{
        $width = imagesx ( $imgsrc );
        $height = imagesy ( $imgsrc );
 
        $src_x = $width -1;
        $src_y = 0;
        $src_width = -$width;
        $src_height = $height;
 
        $imgdest = imagecreatetruecolor ( $width, $height );
 
        if ( imagecopyresampled ( $imgdest, $imgsrc, 0, 0, $src_x, $src_y, $width, $height, $src_width, $src_height ) ) 
		{
            return $imgdest;
        }
 
        return $imgsrc;
    }
 
    public static function adjustPhotoOrientation($full_filename)
	{        
        $exif = exif_read_data($full_filename);
        if($exif && isset($exif['Orientation'])) {
            $orientation = $exif['Orientation'];
            if($orientation != 1)
			{
                $img = imagecreatefromjpeg($full_filename);
 
                $mirror = false;
                $deg    = 0;
 
                switch ($orientation) 
				{
                    case 2:
                        $mirror = true;
                        break;
                    case 3:
                        $deg = 180;
                        break;
                    case 4:
                        $deg = 180;
                        $mirror = true;  
                        break;
                    case 5:
                        $deg = 270;
                        $mirror = true; 
                        break;
                    case 6:
                        $deg = 270;
                        break;
                    case 7:
                        $deg = 90;
                        $mirror = true; 
                        break;
                    case 8:
                        $deg = 90;
                        break;
                }
                if ($deg) $img = imagerotate($img, $deg, 0); 
                if ($mirror) $img = self::mirrorImage($img);
                imagejpeg($img, $full_filename);
            }
        }
        return true;
    }
}

function agregarCampoJS($maximoCam, $nombre, $wrapperNombre, $codigoHTML_Agregar, $nombreQuitar="", $mensajeA="Exceeded")
{	
	if($nombreQuitar == "")$nombreQuitar=$nombre;
	?>
	var maxField<?php echo $nombre ?> = <?php echo $maximoCam ?>; //Input fields increment limitation
	var addButton<?php echo $nombre ?> = $('.<? echo $nombre; ?>Add_button'); //Add button selector
	var wrapper<?php echo $nombre ?> = $('.<? echo $wrapperNombre; ?>_wrapper'); //Input field wrapper
	var fieldHTML<?php echo $nombre ?> = '<? echo $codigoHTML_Agregar; ?>'; //New input field html 
	var x<?php echo $nombre ?> = 1; //Initial field counter is 1
	$(addButton<?php echo $nombre ?>).click(function(){ //Once add button is clicked
		if(x<?php echo $nombre ?> < maxField<?php echo $nombre ?>){ //Check maximum number of input fields
			x<?php echo $nombre ?>++; //Increment field counter
            console.log(maxField<?php echo $nombre ?>+" y contador-> "+x<?php echo $nombre ?>);
			$(wrapper<?php echo $nombre ?>).append(fieldHTML<?php echo $nombre ?>); // Add field html
		}else{
            mensajeModalA("<? echo $mensajeA; ?>");
        }
	});
	$(wrapper<?php echo $nombre ?>).on('click', '.<? echo $nombreQuitar; ?>Remove_button', function(e){ //Once remove button is clicked
		e.preventDefault();
		$(this).parents('div').eq(1).remove(); //Remove field html - $(this).parents().eq(4)
		x<?php echo $nombre ?>--; //Decrement field counter
	});
	<?
}

function agregarCampoJsContadorGlobal($nombre, $wrapperNombre, $nombreQuitar="", $mensajeA="Exceeded")
{	
	if($nombreQuitar == "")$nombreQuitar=$nombre;
	?>
	var addButton<?php echo $nombre ?> = $('.<? echo $nombre; ?>Add_button'); //Add button selector
	var wrapper<?php echo $nombre ?> = $('.<? echo $wrapperNombre; ?>_wrapper'); //Input field wrapper
	x<?php echo $nombre ?> = 1; //Initial field counter is 1
	$(addButton<?php echo $nombre ?>).click(function(){ //Once add button is clicked
		console.log("Maximo permitido " + maxField<?php echo $nombre ?> + " y en -> " + x<?php echo $nombre ?>);    
        var banderaBlanco = false;
        var banderaDuplicado = false;
        var valores = [];
        var valorDuplicado;
        var contadorDuplicados = 0;
        var contadorGeneral = 0;
        $('.<? echo $nombre; ?>Cla').each(function(){ //revisar todos los valores
            contadorDuplicados = 0;
            if(this.value == 0 || this.value == "")
            {
                banderaBlanco = true;
            }else{
                valores.push(this.value);
            }
        });
        if(!banderaBlanco)
        {
            for(cont = 0; cont < valores.length; cont++)
            {
                for(contB = 0; contB < valores.length; contB++)
                {
                    if(valores[cont] == valores[contB] && cont != contB)
                    {
                        valorDuplicado = valores[cont];
                        banderaDuplicado = true;
                        break;
                    }
                }
                if(banderaDuplicado) break;
            }
        }
        $('.<? echo $nombre; ?>Cla').each(function(){ //revisar todos los valores
            contadorGeneral++;
            if(this.value == valorDuplicado) contadorDuplicados++;
            if(contadorDuplicados == 2)
            {
                $(this).parents('div').eq(1).remove(); //Remove field html - $(this).parents().eq(4)
                x<?php echo $nombre ?>--; //Decrement field counter
            }
        });
        if(banderaBlanco)
        {
            mensajeModalA('<div class="alert alert-warning" role="alert">Asigna un lugar antes de agregar otros</div>');
        }else if(banderaDuplicado){
            mensajeModalA('<div class="alert alert-danger" role="alert">Estas duplicando un lugar</div>');
        }else if(x<?php echo $nombre ?> >= maxField<?php echo $nombre ?>){ //Check maximum number of input fields
            mensajeModalA('<div class="alert alert-warning" role="alert"><? echo $mensajeA; ?></div>');
        }else{
            x<?php echo $nombre ?>++; //Increment field counter
            $(wrapper<?php echo $nombre ?>).append(fieldHTML<?php echo $nombre ?>); // Add field html
            document.getElementById("total").value = parseInt(document.getElementById("precioBol").value) * (contadorGeneral + 1);
            mensajeModalA("Recuerda que son tus invitados y t&uacute; eres el miembro de este club, por lo tanto el acceso solo ser&aacute; permitido si llegan juntos y con tu presencia");
        }
	});
	$(wrapper<?php echo $nombre ?>).on('click', '.<? echo $nombreQuitar; ?>Remove_button', function(e){ //Once remove button is clicked
		e.preventDefault();
		$(this).parents('div').eq(1).remove(); //Remove field html - $(this).parents().eq(4)
		x<?php echo $nombre ?>--; //Decrement field counter
        document.getElementById("total").value = parseInt(document.getElementById("total").value) - parseInt(document.getElementById("precioBol").value);
	});
	<?
}

function noHayErrores($errLista)
{
	if (!empty($errLista)) //mostrar errores
	{
		if ($errLista[0] == "|") 
			$temp = substr($errLista,1);
		else 
			$temp = $errLista;
		
		$errLista = str_ireplace("|","<br />",$temp);
		mostrarModal($errLista);
		return false;
	}
	return true;
}
/* CUNSULTAS */

function estrellasEditables($i, $ordenPlat)
{
	$edit ='onClick="calificar('.$i.','.$ordenPlat.')" id="cal'.$ordenPlat.$i.'" ';
	return $edit;
}

function calificacion($calificacion, $ordenPlat, $editable=true, $chico = false)
{
	$parteDecimalCal = explode(".", $calificacion);
	$bandera = true;
	for($i = 0; $i<=5; $i = $i + 0.5)
	{
		$parteDecimal = explode(".", $i);
		
		if($i == 0){
			if($editable)
			{
				if($calificacion == "")
				{
					$iconoCara="icon-neutral";
				}
				elseif($calificacion == 0)
				{
					$iconoCara="icon-frustrated";
				}
				elseif($parteDecimalCal['0'] == 1)
				{
					$iconoCara="icon-angry";
				}
				elseif($parteDecimalCal['0'] == 2)
				{
					$iconoCara="icon-sad";
				}
				elseif($parteDecimalCal['0'] == 3)
				{
					$iconoCara="icon-neutral";
				}
				elseif($parteDecimalCal['0'] == 4)
				{
					$iconoCara="icon-smile";
				}
				elseif($parteDecimalCal['0'] == 5)
				{
					$iconoCara="icon-happy";
				}
				
				$edit = estrellasEditables("0", $ordenPlat);
				$calificadoCon = '<div '.$edit.' class="'.$iconoCara.' carita" aria-hidden="true"></div>';
			}
		}elseif(count($parteDecimal) == 2){
			if($calificacion <= $i && $calificacion > $parteDecimal[0] && count($parteDecimalCal) == 2){
				if($editable){
					$edit = estrellasEditables($parteDecimal[0], $ordenPlat);
				}else{
					$edit = '';
				}
				$calificadoCon = '<span '.$edit.' class="icon-star-half A'.$parteDecimal[0].'" aria-hidden="true"></span>'. $calificadoCon;
				$bandera = false;
			}
		}elseif($bandera){
			if($editable){
				$edit = estrellasEditables($i, $ordenPlat);
			}else{
				$edit ='';
			}
			if($calificacion >= $i){
				$calificadoCon ='<span '.$edit.' class="icon-star-full A'.$i.'" aria-hidden="true"></span>'.$calificadoCon;
			}else{
				$calificadoCon ='<span '.$edit.' class="icon-star-empty A'.$i.'" aria-hidden="true"></span>'.$calificadoCon;
			}
		}else{
			$bandera = true;
		}
	}
	
	if($editable){
		return '<div class="rating">'.$calificadoCon.'</div>';
	}else{
		$estilo = "";
		if($chico) $estilo = ' style="font-size: 14px;"';
		return '<div class="ratingStatic"'.$estilo.'>'.$calificadoCon.'</div>';
	}
}

function misiones($activo=true)
{	
	global $idUsuario;
	$res=array();
	if($activo)	$query = "SELECT `idMision`, `mision` FROM `0040_misiones` WHERE `activo` = '1' ORDER BY `mision`";
	else $query = "SELECT `idMision`, `mision` FROM `0040_misiones` ORDER BY `mision`";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[$datoA['idMision']] = $datoA['mision'];
	}
	return $res;
}

function categorias(&$estaturaIni=array(),&$estaturaFin=array())
{
	global $idUsuario;
	$res=array();
	$query = "SELECT `idCategoria`, `estaturaIni`, `estaturaFin`, `categoria` FROM `0050_categorias` ORDER BY `estaturaIni`";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[$datoA['idCategoria']] = $datoA['categoria'];
		$estaturaIni[$datoA['idCategoria']] = $datoA['estaturaIni'];
		$estaturaFin[$datoA['idCategoria']] = $datoA['estaturaFin'];
	}
	return $res;
}

function nombreCategoria($idCategoria)
{
	global $idUsuario;
	$query = "SELECT `categoria` FROM `0050_categorias` WHERE `idCategoria` = '".$idCategoria."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res = $datoA['categoria'];
	}
	return $res;
}

function modulos($activo=true)
{	
	global $idUsuario;
	$res=array();
	if($activo)	$query = "SELECT `idModulo`, `modulo` FROM `0030_modulos` WHERE `activo` = '1' ORDER BY `modulo`";
	else $query = "SELECT `idModulo`, `modulo` FROM `0030_modulos` ORDER BY `modulo`";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[$datoA['idModulo']] = $datoA['modulo'];
	}
	return $res;
}

function modulosPorMision($mision)
{	
	global $idUsuario;
	$res=array();
	$query = "SELECT `idMisionActividad`, `0030_modulos`.`modulo` FROM `0030_modulos` INNER JOIN `0041_misionesModulos` ON `0030_modulos`.`idModulo`=`0041_misionesModulos`.`idModulo` WHERE `idMision` ='".$mision."' ORDER BY `orden`";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[$datoA['idMisionActividad']] = $datoA['modulo'];
	}
	return $res;
}

function modulosPorMisionNormal($mision)
{	
	global $idUsuario;
	$res=array();
	$query = "SELECT DISTINCT `0030_modulos`.`idModulo`, `0030_modulos`.`modulo` FROM `0030_modulos` INNER JOIN `0041_misionesModulos` ON `0030_modulos`.`idModulo`=`0041_misionesModulos`.`idModulo` WHERE `idMision` ='".$mision."' ORDER BY `orden`";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[$datoA['idModulo']] = $datoA['modulo'];
	}
	return $res;
}

function modulosAlternos($modulo)
{	
	global $idUsuario;
	$res=array();
	$query = "SELECT `0030_modulos`.`idModulo`, `0030_modulos`.`modulo` FROM `0042_modulosAlternos` INNER JOIN `0030_modulos` ON `0030_modulos`.`idModulo`=`0042_modulosAlternos`.`idModulo` INNER JOIN `0041_misionesModulos` ON `0042_modulosAlternos`.`idMisionModulo`=`0041_misionesModulos`.`idMisionActividad` WHERE `0041_misionesModulos`.`idModulo` ='".$modulo."' ORDER BY `0030_modulos`.`modulo`";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[$datoA['idModulo']] = $datoA['modulo'];
	}
	return $res;
}

function actividades($modulo="")
{	
	//tener cuidado que cuando se ingresa el modulo devuelve el id de actividadModulo en lugar del idActividad
	global $idUsuario;
	$res=array();
	if($modulo=="")	$query = "SELECT `0020_actividades`.`idActividad`, `actividad` FROM `0020_actividades` ORDER BY `actividad` ";
	else $query = "SELECT `0031_actividadModulo`.`idActividadModulo` AS `idActividad`, `actividad` FROM `0020_actividades` INNER JOIN `0031_actividadModulo` ON `0020_actividades`.`idActividad`=`0031_actividadModulo`.`idActividad` WHERE `0031_actividadModulo`.`idModulo` = '".$modulo."' ORDER BY `orden` ";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[$datoA['idActividad']] = $datoA['actividad'];
	}
	return $res;
}

function retos($activo=true)
{	
	global $idUsuario;
	$res=array();
	if($activo)	$query = "SELECT `idReto`, `reto` FROM `0033_retos` WHERE `activo` = '1' ORDER BY `reto`";
	else $query = "SELECT `idReto`, `reto` FROM `0033_retos` ORDER BY `reto`";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[$datoA['idReto']] = $datoA['reto'];
	}
	return $res;
}

function nombreMision($mision)
{	
	global $idUsuario;
	$res='';
	$query = "SELECT `mision` FROM `0040_misiones` WHERE `idMision` = '".$mision."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res = $datoA['mision'];
	}
	return $res;
}

function moduloActivo()
{
	global $idUsuario;
	$res='';
	$query = "SELECT `idInicio` FROM `0100_inicioModulo` WHERE `idUsuario` = '".$idUsuario."' AND `activo` != '0'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	if(mysqli_num_rows($resultadoSQL) != 0) return true;
	else return false;	
}

function moduloActual(&$noActividad, &$inicio)
{
	global $idUsuario;
	$res='';
	$query = "SELECT `idInicio`, `idModulo`, `activo` FROM `0100_inicioModulo` WHERE `idUsuario` = '".$idUsuario."' AND `activo` != '0'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res = $datoA['idModulo'];
		$noActividad=$datoA['activo'];
		$inicio = $datoA['idInicio'];
	}
	return $res;
}

function circuitoEnInicio($idInicio)
{
	$subGrupo="";
	$query = "SELECT `circuito` FROM `0100_inicioModulo` WHERE `idInicio` = '".$idInicio."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$subGrupo = $datoA['circuito'];
	}
	return $subGrupo;
}

function subGrupoEnInicio($idInicio)
{
	$subGrupo="";
	$query = "SELECT `subGrupo` FROM `0100_inicioModulo` WHERE `idInicio` = '".$idInicio."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$subGrupo = $datoA['subGrupo'];
	}
	return $subGrupo;
}

function modulosEnInicio($idInicio)
{
	global $idUsuario;
	$res='';
	$query = "SELECT `idModulo` FROM `0100_inicioModulo` WHERE `idInicio` = '".$idInicio."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res = $datoA['idModulo'];
	}
	return $res;
}

function misionesConElModulo($idModulo)
{
	global $idUsuario;
	$res=array();
	$query = "SELECT `idMision` FROM `0041_misionesModulos` WHERE `idModulo` = '".$idModulo."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[] = $datoA['idMision'];
	}
	$query = "SELECT `0041_misionesModulos`.`idMision` FROM `0042_modulosAlternos` INNER JOIN `0041_misionesModulos` ON `0042_modulosAlternos`.`idMisionModulo`=`0041_misionesModulos`.`idMisionActividad` WHERE `0042_modulosAlternos`.`idModulo` = '".$idModulo."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		
		if(!in_array($datoA['idMision'],$res))$res[] = $datoA['idMision'];
	}
	return $res;
}

function usuariosEnUnaMision($idMision, $dia="",$hora="")
{
	global $timeZone;
	if($dia == "") $dia = gmdate("N",time() + ($timeZone*60*60));
	if($hora == "")$hora = time();
	else $hora = (strtotime(gmdate("Y-m-d ",time()).$hora." GMT")) - ($timeZone*60*60);
	
	$enUnRato = gmdate("H:i:s",$hora + (($timeZone + 0.5)*60*60)); //muestra los resultados con media hora antes de que inicie para los que llegan antes
	$haceRato = gmdate("H:i:s",$hora + (($timeZone - 1.0)*60*60)); //muestra los resultados una hora durante el entrenamiento para los que llegan tarde
	switch($dia)
	{
		case 1: $filtro = " AND `lun` < '".$enUnRato."' AND `lun` > '".$haceRato."'"; break;
		case 2: $filtro = " AND `mar` < '".$enUnRato."' AND `mar` > '".$haceRato."'"; break;
		case 3: $filtro = " AND `mie` < '".$enUnRato."' AND `mie` > '".$haceRato."'"; break;
		case 4: $filtro = " AND `jue` < '".$enUnRato."' AND `jue` > '".$haceRato."'"; break;
		case 5: $filtro = " AND `vie` < '".$enUnRato."' AND `vie` > '".$haceRato."'"; break;
		case 6: $filtro = " AND `sab` < '".$enUnRato."' AND `sab` > '".$haceRato."'"; break;
		case 7: $filtro = " AND `dom` < '".$enUnRato."' AND `dom` > '".$haceRato."'"; break;
	}
	
	global $idUsuario;
	$res=array();
	$query="SELECT `0010_usuarios`.`idUsuario`, `0010_usuarios`.`nombre` FROM `0010_usuarios` INNER JOIN `0012_usuarioMision` ON `0010_usuarios`.`idUsuarioMision`=`0012_usuarioMision`.`idUsuarioMision` WHERE `0012_usuarioMision`.`idMision` = '".$idMision."' ".$filtro;
	
	//echo $query."<br/>";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[$datoA['idUsuario']] = $datoA['nombre'];
	}
	return $res;
}

function usuariosEnInicio($idInicio, $filtroUsuarios=array(),$dia="",$hora="")
{
	
	$idModulo=modulosEnInicio($idInicio);
	$misiones=misionesConElModulo($idModulo);
	$res=array();
	//var_dump($misiones);
	foreach($misiones as $idMision)
	{
		//echo "<br/>El dia es ".$dia." y la hora es ".$hora."<br/>";
		$usuarios = usuariosEnUnaMision($idMision,$dia,$hora);
		foreach($usuarios as $idUsuario => $nombre)
		{
			if(!in_array($idUsuario,$filtroUsuarios)) $res[$idUsuario]=$nombre;
		}
	}
	return $res;
}

function usuariosLista()
{
	global $idUsuario;
	$res=array();
	
	$query = "SELECT `idUsuario`, `nombre` FROM `0010_usuarios` WHERE `activo` = '1'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[$datoA['idUsuario']] = $datoA['nombre'];
	}
	return $res;
}

function usuariosEnUnInicio($idInicio,$conIds=false, $subGrupo="")
{
	global $idUsuario;
	$res=array();
	$subGrupo!=""?$filtroSub=" AND `subGrupo`='".$subGrupo."'":$filtroSub="";
	if($conIds) $query = "SELECT `0101_grupos`.`idUsuario`, `nombre` FROM `0101_grupos` INNER JOIN `0010_usuarios` ON `0101_grupos`.`idUsuario`=`0010_usuarios`.`idUsuario` WHERE `idInicio` = '".$idInicio."'".$filtroSub;
	else $query = "SELECT `idUsuario` FROM `0101_grupos` WHERE `idInicio` = '".$idInicio."'".$filtroSub;
	//echo $query;
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		if($conIds) $res[$datoA['idUsuario']] = $datoA['nombre'];
		else $res[] = $datoA['idUsuario'];
	}
	return $res;
}

function hayGrupo($idInicio, $cuantos=1)
{
	global $idUsuario;
	$res=false;
	$query="SELECT `idGrupo`, `idUsuario` FROM `0101_grupos` WHERE `idInicio` ='".$idInicio."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	if(mysqli_num_rows($resultadoSQL)>=$cuantos) $res = true;
	return $res;
}

function nombreDelModulo($idModulo, &$conSubgrupo)
{
	global $idUsuario;
	$res="";
	$query="SELECT `modulo`, `subgrupo` FROM `0030_modulos` WHERE `idModulo` = '".$idModulo."' ";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res = $datoA['modulo'];
		$conSubgrupo = $datoA['subgrupo'];
	}
	return $res;
}

function comoCalificaElModulo($idModulo)
{
	global $idUsuario;
	$res="";
	$query="SELECT `calificaPorAct` FROM `0030_modulos` WHERE `idModulo` = '".$idModulo."' ";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res = $datoA['calificaPorAct'];
	}
	return $res;
}

function actividadActual($idModulo, $noActividad)
{	
	global $idUsuario;
	$res=array();
	if($noActividad == -1 || $noActividad == 0) $query = "SELECT `0020_actividades`.`idActividad`, `0031_actividadModulo`.`idActividadFortaleza`, `actividad`, `minutos`, `foto`, `orden`,`enCircuito` FROM `0020_actividades` INNER JOIN `0031_actividadModulo` ON `0020_actividades`.`idActividad`=`0031_actividadModulo`.`idActividad` WHERE `0031_actividadModulo`.`idModulo` = '".$idModulo."' ORDER BY `orden` LIMIT 2 ";
	else $query = "SELECT `0020_actividades`.`idActividad`, `0031_actividadModulo`.`idActividadFortaleza`, `actividad`, `minutos`, `foto`, `orden`,`enCircuito` FROM `0020_actividades` INNER JOIN `0031_actividadModulo` ON `0020_actividades`.`idActividad`=`0031_actividadModulo`.`idActividad` WHERE `0031_actividadModulo`.`idModulo` = '".$idModulo."' AND `orden`>= '".$noActividad."' ORDER BY `orden` LIMIT 2";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	$i=0;
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[$i]['actividad'] = $datoA['actividad'];
		$res[$i]['minutos'] = $datoA['minutos'];
		$res[$i]['idActividad'] = $datoA['idActividad'];
		$res[$i]['idActividadFortaleza'] = $datoA['idActividadFortaleza'];
		if($datoA['foto'] != "") $res[$i]['foto'] = $datoA['foto'];
		else $res[$i]['foto'] = 'imagenes/sin-imagen.png';
		$res[$i]['orden'] = $datoA['orden'];
		$res[$i]['enCircuito'] = $datoA['enCircuito'];
		$i++;
	}
	return $res;
}

function detalleActividad($idActividad)
{	
	global $idUsuario;
	$res=array();
	$query = "SELECT `actividad`, `foto`,`descripcion`,`video` FROM `0020_actividades` WHERE `idActividad` = '".$idActividad."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res['actividad'] = $datoA['actividad'];
		$res['video'] = $datoA['video'];
		if($datoA['foto'] != "") $res['foto'] = $datoA['foto'];
		else $res['foto'] = 'imagenes/sin-imagen.png';
		$res['descripcion'] = $datoA['descripcion'];
	}
	return $res;
}

function nombreActividad($idActividad)
{
	global $idUsuario;
	$query = "SELECT `actividad` FROM `0020_actividades` WHERE `idActividad` = '".$idActividad."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res = $datoA['actividad'];
	}
	return $res;
}

function actividadAnterior($idModulo, $noActividad)
{	
	global $idUsuario;
	$res=array();
	if($noActividad == -1 || $noActividad == 0) return $res;
	else $query = "SELECT `0020_actividades`.`idActividad`, `actividad`, `minutos`, `foto`, `orden` FROM `0020_actividades` INNER JOIN `0031_actividadModulo` ON `0020_actividades`.`idActividad`=`0031_actividadModulo`.`idActividad` WHERE `0031_actividadModulo`.`idModulo` = '".$idModulo."' ORDER BY `orden`";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	$i=0;
	$bandera=false;
	
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		
		if($bandera || $noActividad == $datoA['orden'])
		{
			if(count($res) != 0) $i++;
			$bandera=true;
			if($i>3) break;
		}
		$res[$i]['actividad'] = $datoA['actividad'];
		$res[$i]['minutos'] = $datoA['minutos'];
		$res[$i]['idActividad'] = $datoA['idActividad'];
		$res[$i]['foto'] = $datoA['foto'];
		$res[$i]['orden'] = $datoA['orden'];
	}
	return $res;
}

function registrarActividad($idInicio,$noActividad, $orden)
{
	//devuelve "ERROR" si no coincide la actividad
	//devuelve "REGISTRADO" si inicio la actividad
	//devuelve minutos decimales si ya comenzo el conteo
	global $idUsuario;
	global $timeZone;
	$bandera=true;
	$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
	$query = "SELECT `activo`, `cronometro` FROM `0100_inicioModulo` WHERE `idInicio`= '".$idInicio."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		if($datoA['activo'] != $noActividad)return "ERROR";
		$cronometro = $datoA['cronometro']; 
	}
	if($cronometro == NULL)
	{
		$query = "UPDATE `0100_inicioModulo` SET `activo`='".$orden."',`cronometro`='".$ahora."' WHERE `idInicio`= '".$idInicio."'";		
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
		return "REGISTRADO";
	}else{
		$timeEnBD = strtotime($cronometro." GMT") - (($timeZone)*60*60);
		$diferenciaEnMinutos=(time()-$timeEnBD)/60;
		return $diferenciaEnMinutos;
	}
	return "ERROR";
}

function verCronometro($idInicio,$noActividad)
{
	global $idUsuario;
	
	$query = "SELECT `activo`, `cronometro` FROM `0100_inicioModulo` WHERE `idInicio`= '".$idInicio."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		if($datoA['activo'] != $noActividad)return "";
		$cronometro = $datoA['cronometro']; 
	}
	if($cronometro != NULL)
	{
		global $timeZone;
		$timeEnBD = strtotime($cronometro." GMT") - (($timeZone)*60*60);
		$diferenciaEnMinutos=(time()-$timeEnBD)/60;
		return $diferenciaEnMinutos;
	}
	return "";
}

function siguenteActividad($idInicio,$noActividad, $idModulo)
{	
	global $idUsuario;
	$actividades = actividadActual($idModulo, $noActividad);
	
	//$conSubgrupo = subGrupoEnInicio($idInicio);
	//$circuitoActual = circuitoEnInicio($idInicio);
	
	if($actividades[0]['enCircuito'] =='1')
	{
		$circuito = circuitoEnInicio($idInicio);
		if($circuito<3)
		{
			$circuito++;
			$subgruposActivos=gruposEnUnInicio($idInicio);
			$cantidadSubgrupos=count($subgruposActivos);
			if($cantidadSubgrupos !=1)
			{
				$subGrupoActual=subGrupoEnInicio($idInicio);
				if($subGrupoActual < $cantidadSubgrupos) $subGrupoActual++;
				else $subGrupoActual = 1;
			}else{
				$subGrupoActual = 1;
			}
			$query = "UPDATE `0100_inicioModulo` SET `circuito`='".$circuito."',`cronometro`= NULL, `subGrupo`='".$circuito."' WHERE `idInicio`= '".$idInicio."'";		
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
			return true;
		}
	}
	
	$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
	if($actividades[1])
	{
		$query = "UPDATE `0100_inicioModulo` SET `activo`='".$actividades[1]['orden']."',`cronometro`= NULL, `circuito`='1', `subGrupo`='1' WHERE `idInicio`= '".$idInicio."'";		
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	}else{
		$query = "UPDATE `0100_inicioModulo` SET `activo`='0',`cronometro`= NULL, `circuito`='1', `subGrupo`='1' WHERE `idInicio`= '".$idInicio."'";		
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	}
	return true;
}

function gruposEnUnInicio($idInicio)
{
	global $idUsuario;
	$res=array();
	$query = "SELECT `subGrupo` FROM `0101_grupos` WHERE `idInicio` = '".$idInicio."' GROUP BY `subGrupo`";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[] = $datoA['subGrupo']; 
	}
	return $res;
}

function usariosSeleccionados($actividades, $idInicio, $conSubgrupo,&$idActividad,&$idFortaleza,&$actPrincipal, &$actividadFortaleza,&$subGrupoAlterno,&$seCalifica=true)
{
	$actividadEnCircuito=$actividades[0]['enCircuito'];
	
	$subgruposActivos=gruposEnUnInicio($idInicio);
	$cantidadSubgrupos=count($subgruposActivos);
	$subGrupoActual='1';
	if($conSubgrupo != '0' && $conSubgrupo != "") $subGrupoActual=subGrupoEnInicio($idInicio);
	
	$circuitoActual = circuitoEnInicio($idInicio);

	$idFortaleza = $actividades[0]['idActividadFortaleza'];
	$idActividad = $actividades[0]['idActividad'];
	$actPrincipal = $actividades[0]['actividad'];
	//var_dump($actividadEnCircuito);
	
	if($actividadEnCircuito != '1'){
		if($cantidadSubgrupos == 1)$subGrupoActual = '';
		else $subGrupoActual = ''; //no se que va a pasar con esta opcion cuando haya circuitos con subgrupos
	}else{
		if($cantidadSubgrupos == 1)
		{
			$actividadFortaleza = '';
			if($circuitoActual != 2)
			{
				$idActividad = $idFortaleza;
				$actPrincipal = nombreActividad($idFortaleza);
				$seCalifica=false;
			}
			$subGrupoActual = '';
		}elseif($cantidadSubgrupos == 2){
			if($circuitoActual == 3){
				$idActividad = $idFortaleza;
				$actPrincipal = nombreActividad($idFortaleza);
				$seCalifica=false;
				$subGrupoActual = '';
				$actividadFortaleza = '';
			}else{
				$actividadFortaleza = 'Supervisi&oacute;n';
				$idFortaleza = "";
			}
			if($subGrupoActual == 1)$subGrupoAlterno=2;
			else $subGrupoAlterno=1;
		}elseif($cantidadSubgrupos == 3){
			$actividadFortaleza = nombreActividad($idFortaleza);
			if($subGrupoActual < 3)$subGrupoAlterno=$subGrupoActual+1;
			else $subGrupoAlterno=1;
		}
	}
	return $subGrupoActual;
}

function record($usr,$idActividad)
{
	global $idUsuario;
	$query= "SELECT `puntuacion`, `fecha` FROM `0013_usuarioActividad` WHERE `idUsuario` = '".$usr."' AND `idActividad` = '".$idActividad."' ORDER BY `puntuacion` DESC LIMIT 1";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$puntuacion = $datoA['puntuacion']; 
		$fecha= $datoA['fecha'];
	}
	if($puntuacion != "" && $puntuacion != 0) $res="".number_format($puntuacion).'<span style=" font-size:85%"> el '. fechaHora($fecha, true)."</span>";
	else $res = '<span style=" font-size:85%">Primera vez</span>';
	return $res;
}

function todoRecordsUsuario($usr,$fecha)
{
	global $idUsuario;
	$res=array();
	$query = "SELECT `a`.`idActividad`, `actividad`, `puntuacion` as `record`, `fecha` ".
				"FROM `0013_usuarioActividad` AS `a` ".
				"INNER JOIN (".
					"SELECT `idActividad`, MAX(`puntuacion`) as `rec` FROM `0013_usuarioActividad` WHERE `idUsuario` = '".$usr."' AND `fecha`>='".$fecha."' AND `idActividad` IS NOT NULL GROUP BY `idActividad` ".
				") AS `b` ON `a`.`idActividad` = `b`.`idActividad` AND `a`.`puntuacion` = `b`.`rec` " .
				"INNER JOIN `0020_actividades` AS `c` ON `a`.`idActividad`=`c`.`idActividad` ".
				"WHERE `idUsuario` = '".$usr."' AND `fecha`>='".$fecha."' AND `a`.`idActividad` IS NOT NULL";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	$i=0;
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[$i]['idActividad'] = $datoA['idActividad']; 
		$res[$i]['actividad'] = $datoA['actividad'];
		$res[$i]['record'] = $datoA['record'];
		$res[$i]['fecha'] = $datoA['fecha'];
		$i++;
	}
	return $res;
}

function fechaMision($usr,$idMision)
{
	global $idUsuario;
	$res=array();
	$mision = "";
	$fecha="";
	$i=1;
	$reg=0;
	$query = "SELECT `idMision`, `fecha` FROM `0012_usuarioMision` WHERE `idUsuario` = '".$usr."' ORDER BY `fecha` DESC";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	$numRes=(mysqli_num_rows($resultadoSQL));
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		if($i < $numRes){
			if($i===1){
				$mision = $datoA['idMision'];
				$fecha=$datoA['fecha'];
			}elseif($mision !== $datoA['idMision']){
				$res[$reg]['fecha']=$fecha;
				$res[$reg]['idMision']=$mision;
				$reg++;
				$mision = $datoA['idMision'];
				$fecha=$datoA['fecha'];
			}
		}else{
			if($mision !== $datoA['idMision'] && $i !== $noRes)
			{
				$res[$reg]['fecha']=$fecha;
				$res[$reg]['idMision']=$mision;
				$reg++;
			}
			$res[$reg]['idMision'] = $datoA['idMision'];
			$res[$reg]['fecha'] = $datoA['fecha'];
		}
		$i++;
	}
	return $res;
}

function ranking($idCategoria)
{
	global $idUsuario;
	$res=array();
	$i=0;
	$query = "SELECT `nombre`,`actividad`, `puntuacion`, `fecha` FROM `0013_usuarioActividad` AS `a` ".
			"INNER JOIN (SELECT `idActividad`, MAX(`puntuacion`) as `rec` FROM `0013_usuarioActividad` ".
				"INNER JOIN `0010_usuarios` ON `0013_usuarioActividad`.`idUsuario` = `0010_usuarios`.`idUsuario` ".
				"WHERE `idActividad` IS NOT NULL AND `idCategoria` = '".$idCategoria."' GROUP BY `idActividad`) AS `b` ON `a`.`idActividad` = `b`.`idActividad` AND `a`.`puntuacion` = `b`.`rec` ".
			"INNER JOIN `0010_usuarios` ON `a`.`idUsuario` = `0010_usuarios`.`idUsuario` ".
			"INNER JOIN `0020_actividades` ON `a`.`idActividad` = `0020_actividades`.`idActividad` WHERE `a`.`idActividad` IS NOT NULL ORDER BY `actividad`";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
	while($datoA=mysqli_fetch_assoc($resultadoSQL)){
		$res[$i]['nombre']=$datoA['nombre'];
		$res[$i]['actividad']=$datoA['actividad'];
		$res[$i]['puntuacion']=$datoA['puntuacion'];
		$res[$i]['fecha']=$datoA['fecha'];
		$i++;
	}
	return $res;
}





