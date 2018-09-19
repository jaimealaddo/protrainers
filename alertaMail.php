<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';

$para = "xfoficial@hotmail.com";
$CC = "samantha@cideaa.com";

$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
$haceTiempo = gmdate("Y-m-d H:i:s",(time() + ($timeZone*60*60) - (60*60*48))); //hace 48 horas
$br = "\r\n";
$salidaMail = '<html>'.$br.'<body>'.$br.'<h1>Mensaje ALERTA ERP CIdEAA</h1>'.$br;


$banderaMandar = false;
//crearr arreglo con datos de las actividades
$datosActividades=array();

$usuarios=usuariosEnBitacora($haceTiempo);
foreach($usuarios as $unUsuario){
    $salidaMailUsrTemp = "<h2>". ucwords(nombreById($unUsuario)). "</h2>";
	$salidaHeadUsrTemp = "";
	//echo $salidaMailUsrTemp;
    $actividadBitacora = actividadBitacoraPorUsuario($unUsuario, $haceTiempo);
    $diaEnTurno = "";
    $primerFecha = 0;
    $ultimaFecha = 0;
    $banderaSalida = true;
	$banderaUsuario = false;
    $ultimoTiempo = time() + ($timeZone*60*60);
    //echo "El ultimoTiempo es ahora<br/>";
    $sumaDia = 0;
    
	$contador = 0;
    $salida = 0;
    $seTarda=0; //para usarse en minutos
    foreach($actividadBitacora as $key => $datosBitacora) //['fechaTime']['idActividad']['idBitacora'] ($fechas as $key => $unaFecha)
    {
        $contador++;
		buscarActiv($datosBitacora['idActividad'], $datosActividades); 
        if($key===0)
        {
            $ultimaFecha = $datosBitacora['fechaTime'];
        }
        $primerFecha = $datosBitacora['fechaTime'];
        
		if($salida == 0 && $datosBitacora['idActividad']==1) //primer valor en entrada (sin salida)
        {
            $ultimaFecha = time();
            imprimirDetalleBitacora($actividadBitacora,$key,$ultimoTiempo, $datosActividades,$sumaDia,false,$salidaMailTemp);
            $ultimoTiempo = $datosBitacora['fechaTime'];
        }elseif($datosBitacora['idActividad'] == 2 && $banderaSalida){ //salida
            $ultimoTiempo = $datosBitacora['fechaTime']; //si salio, fijar como su ultimo tiempo
            $salida = $datosBitacora['fechaTime']; //guardar salida para su calculo
            $banderaSalida = false;
        }elseif($datosBitacora['idActividad'] == 1){ //entrada
            $banderaSalida = true;
            imprimirDetalleBitacora($actividadBitacora,$key,$ultimoTiempo, $datosActividades,$sumaDia,false,$salidaMailTemp);
            $ultimoTiempo = $datosBitacora['fechaTime']; //fijar ultimo tiempo
        }elseif($datosBitacora['idActividad'] != 1 && $datosBitacora['idActividad'] != 2){ //cualquier actividad
            imprimirDetalleBitacora($actividadBitacora,$key,$ultimoTiempo, $datosActividades,$sumaDia,false,$salidaMailTemp);
            $ultimoTiempo = $datosBitacora['fechaTime']; //fijar ultimo tiempo
        }
		
		//imprimir el dia al que corresponde
        $fechaEnTurno = gmdate("Y-m-d H:i:s",$datosBitacora['fechaTime']) ." - ".($horas) / (60 * 60). "<br/>";
        if($diaEnTurno != substr($fechaEnTurno,8,2) || $contador == count($actividadBitacora)){
            //$salidaFootUsr = $salidaFootTemp;
			$salidaFootUsr = "<h5>".number_format($sumaDia/(60),1). " horas trabajadas iniciando a las ".gmdate("h:i:s a",$actividadBitacora[$key-1]['fechaTime'])."</h5><br />";
            $salidaHeadUsr = $salidaHeadUsrTemp;
			$salidaHeadUsrTemp = "<br /><h4>".fechaHora($fechaEnTurno)."</h4>";
			if($salidaMailTemp != ''){
				$banderaMandar = true;
				$banderaUsuario = true;
				$salidaMailUsrTemp .= $salidaHeadUsr.$salidaMailTemp.$salidaFootUsr;
			}
			$salidaMailTemp = '';
			if($diaEnTurno != "") $sumaDia = 0;
			$diaEnTurno = substr($fechaEnTurno,8,2);
			
        }
    }
	if($salidaMailUsrTemp != "" && $banderaUsuario)
	{
		$salidaMail .= $salidaMailUsrTemp;
		$salidaMailUsrTemp = '';
	}
    
}
if($banderaMandar){
	$salidaMail .= "<h3>De parte de todo el equipo de CIdEAA &#161;Te deseamos una excelente semana!</h3>";
	$salidaMail .= '</body>'.$br.'</html>';
	mandarMail($salidaMail,$para,"ALERTA CIdEAA", $CC);
	//echo $salidaMail;
}
?>
        	