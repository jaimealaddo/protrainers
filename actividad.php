<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';
$primerCarga=true;
$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));

$jerarquia = permiteAcceso();
$noActividad = "";
$idInicio="";
$idModulo = moduloActual($noActividad, $idInicio);
if($jerarquia < 60 || $noActividad == "" || $noActividad == 0) salirJerarquia("nuevo.php");

$conSubgrupo="0";
$nombreModulo = nombreDelModulo($idModulo, $conSubgrupo);
$actividades=actividadActual($idModulo, $noActividad);

if($actividades[0]['enCircuito']==1)$dividir = 3; else $dividir=1;

$timpoTrascurrido = verCronometro($idInicio,$noActividad);

if(isset($_GET['play']))$play=$_GET['play'];

if(isset($_POST['registrar']) && $timpoTrascurrido > 0)
{	
	
	$errLista = '';
	$usuariosTemp = $_REQUEST['idAspirante'];
	$calif = "";
	$comoCalifica = comoCalificaElModulo($idModulo);
	$play = $_POST['play'];
	
	$subGrupo = "NULL";
	if($comoCalifica == 0 && $conSubgrupo == 0) $query = "DELETE FROM `0013_usuarioActividad` WHERE `idInicio` = '".$idInicio."'";
	elseif($conSubgrupo == 0) $query = "DELETE FROM `0013_usuarioActividad` WHERE `idInicio` = '".$idInicio."' AND `idActividad` = '".$actividades[0]['idActividad']."'";
	else
	{
		$subGrupo = "'".subGrupoEnInicio($idInicio)."'";
		$query = "DELETE FROM `0013_usuarioActividad` WHERE `idInicio` = '".$idInicio."' AND `idActividad` = '".$actividades[0]['idActividad']."' AND `subGrupo`=".$subGrupo;
	}
	ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	
	$query = "";
	foreach($usuariosTemp as $aspirante)
	{
		$calif = $_POST['calif'.$aspirante]+0;
		if($comoCalifica == 0) $query .= "('".$aspirante."','".$calif."','".$ahora."','".$idInicio."'),";
		else $query .= "('".$aspirante."','".$actividades[0]['idActividad']."','".$calif."','".$ahora."','".$idInicio."',".$subGrupo."),";
	}
	if($query != "")
	{
		$query = substr($query,0,-1);
	
		if($comoCalifica == 0) $query = "INSERT INTO `0013_usuarioActividad`(`idUsuario`, `puntuacion`, `fecha`, `idInicio`) VALUES " . $query;
		else $query = "INSERT INTO `0013_usuarioActividad`(`idUsuario`, `idActividad`, `puntuacion`, `fecha`, `idInicio`, `subGrupo`) VALUES ". $query;
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	}
	
	//$errLista .= "|PAUSA|";
	if(noHayErrores($errLista))
	{
		siguenteActividad($idInicio,$noActividad, $idModulo);
		
		if($comoCalifica == 0)$destino = "nuevo.php?alerta=2&refresh=".rand();
		else $destino = "actividad.php?refresh=".rand();
		
		if($play==1)$destino .= "&play=1";

		header("Location:".$destino);
		echo '<script>window.location = "'.$destino.'";</script>';
		exit;
	}
}

?>
<!DOCTYPE HTML>
<html lang="es">
<head>
	<? include "parts/head.html"; ?>
</head>

<body>
	<div id="contenedor">
    <?php include "parts/header.php" ?>
    	<section class="container">
        	<form method="post" name="localizacion" target="_self" role="form" id="myForm">
                <div id="paginaCompleta">
                    <?
					if($timpoTrascurrido == 0){
						include 'parts/aspirantes.php';
					}else{
						include 'parts/actividadPagina.php';
    					/*
                        echo "<br/>El numero de actividad es: " . $noActividad."<br/>";
                        echo "El idInicio es: " . $idInicio."<br/>";
                        echo "El modulo tiene subgrupo: " . $conSubgrupo."<br/>";
                        echo "Duracion de : ".$actividades[0]['minutos']." minutos";
						echo "La actividad es en circuito: ".$actividades[0]['enCircuito']."";
                        
                        foreach($actividades as $key => $dato)
                        {
                            echo "<br>";
                            echo "<br> la actividad actual es: ".$dato['idActividad'];
                            
                        }
						*/
					}
                     ?>
                </div>
                
            	<input type="hidden" name="id" value="<? echo $idActivo; ?>"/>
                <input type="hidden" id="duracion" value="<? echo $actividades[0]['minutos']; ?>"/>
                <?
				echo checkBox('play', "movimiento automatico", $play, $funcionCheck="");
				?>
                <div id="boton"><? echo botonJS('siguente','Siguente',"cargarSiguente()","btn-success",true,true); ?></div>
                <? echo botonJS('restart','Reiniciar',"restart()","btn-warning"); ?>
            </form>
            <br /><br />   
    	</section>
    	<?php include "parts/footer.html" ?>
	</div><!--cierre contenedor!-->
	<script>
		<?php 
			$HTML_add=campoSelectMultiple('usuario', 'nombre', $usuario[$i], "icon-user-plus2", $listaUsuarios, true, (2));
			agregarCampoJS(12, "usuario", "PRIMER", $HTML_add);
			
			if($mensajeModal) echo "mensajeModalA('".$mensaje."')"; 
		?>
		
		// Set the date we're counting down to
		var minutos = <? echo ($actividades[0]['minutos'] / $dividir)- $timpoTrascurrido; ?>;
		var countDownDate = new Date().getTime() + (minutos*60*1000)
		var bandera = true;
		
		// Update the count down every 1 second
		var elCronometro = function() {
	
			// Get todays date and time
			var now = new Date().getTime();
			
			// Find the distance between now an the count down date
			var distance = countDownDate - now;
			
			// Time calculations for days, hours, minutes and seconds
			var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((distance % (1000 * 60)) / 1000);
			var decimals = Math.floor((distance % (1000 * 1)) / 100);
			
			// Output the result in an element with id="demo"
			document.getElementById("crono").innerHTML = hours + ":" + minutes + ":" + seconds;
			document.getElementById("cronoDec").innerHTML = ". " + decimals;
			
			// If the count down is over, write some text 
			if (distance < 0) {
				clearInterval(myTimer);
				document.getElementById("cronoTotal").innerHTML = "FIN";
				document.getElementById("boton").innerHTML = '<? echo botonJS('siguente','Siguente',"cargarSiguente()","btn-success",true,true); ?>';
				//console.log(document.getElementById("play").value );
				if(document.getElementById("play").checked == true) cargarSiguente();
			}
		};
		
		myTimer = setInterval(elCronometro, 100);
		
		function cargarSiguente()
		{
			clearInterval(myTimer);
			var destino = "";
			var actual = document.getElementById('lugar').value;
			console.log(actual);
			if(actual == 'aspirantes')
			{
				siguenteActividad();
			}else{
				destino = "parts/siguenteCronometro.php?refresh=" + new Date().getTime()
				
				$.get(destino, function(responseText) {
					dato = (responseText);
					console.log(dato);
					if(dato != "")
					{
						var myarr = dato.split("|");
						document.getElementById('duracion').value = myarr[0];
						if(myarr[1] == '1') //se califica por actividad
						{
							//pasar a la pantalla de calificar
							pasarCalificar();
						}else{ //se califica por modulo
							siguenteActividad();
						}
					}else{
						//es la ultima actividad
						pasarCalificar();
					}
				});
			}
		}
		
		function pasarCalificar()
		{
			$.get("parts/calificar.php?refresh=" + new Date().getTime(), function(responseText) {
				dato = (responseText);
				//console.log(dato);
				if(dato != "")
				{
					document.getElementById("boton").innerHTML = '<? echo botonSubmit("registrar","Registrar", 'btn-primary'); ?>';
					document.getElementById('paginaCompleta').innerHTML = dato;
				}else{
					<?
					if($comoCalifica == 1)echo 'window.location = "nuevo.php?alerta=2&refresh='.rand().'"';
					else echo "siguentesAspirantes();";
					?>
					
				}
			});
		}
		
		function siguenteActividad()
		{
			//console.log(document.getElementById("play").checked);
			$.get("parts/actividadPagina.php?refresh=" + new Date().getTime(), function(responseText) {
				dato = (responseText);
				//console.log(dato);
				if(dato != "")
				{
					document.getElementById('paginaCompleta').innerHTML = dato;
					minutos = ((document.getElementById("duracion").value) / (<? echo $dividir; ?>));
					console.log("Los nuevos minutos son "+ minutos);
					countDownDate = new Date().getTime() + (minutos*60*1000);
					myTimer = window.setInterval(elCronometro, 100);
				}
			});
			
		}
		
		function siguentesAspirantes()
		{
			//console.log(document.getElementById("play").checked);
			var destino = "parts/aspirantes.php?refresh=" + new Date().getTime();
			
			$.get(destino, function(responseText) {
				document.getElementById('paginaCompleta').innerHTML = (responseText);
			});
			
		}
		
		function restart()
		{
			console.log(document.getElementById("play").checked );
			document.getElementById("cronoTotal").innerHTML = '<div id="crono" style="width:220px;display:inline-block; text-align:right; padding-right:0px; margin-right:0px;"></div><div class="small" style="width:60px;display:inline-block; text-align:left; padding-left:0px; margin-left:0px;" id="cronoDec"></div>';
			minutos = ((document.getElementById("duracion").value) / (<? echo $dividir; ?>));
			countDownDate = new Date().getTime() + (minutos*60*1000);
			console.log("Los nuevos minutos son "+ minutos);
			myTimer = window.setInterval(elCronometro, 100);
		}
		
		function verDetalle(idActividad,lugar)
		{
			var destino = "parts/detalle.php?idActividad=" + idActividad + "&refresh=" + new Date().getTime();
			$.get(destino, function(responseText) {
				dato = (responseText);
				console.log(destino);
				if(lugar == 1)
				{
					document.getElementById('descripcionA').innerHTML = dato;
				}else{
					document.getElementById('descripcionB').innerHTML = dato;
				}
			});
		}
	</script>
    
	
	
</body>
</html>
