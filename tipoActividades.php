<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';

if(permiteAcceso())
{
	$idUsuario = $_COOKIE['idERP'];
	$jerarquia = jerarquiaTarjeta($idUsuario);
}else{
	salirJerarquia("index.php");
}
if($jerarquia < 80) salirJerarquia("nuevo.php");

if(isset($_GET['id']))
{
	$idActivo = $_GET['id'];
}

if(isset($_POST['registrar']))
{	
	$errLista = '';
	$actividad = validaCampoTXT('actividad',4,TRUE, $_POST, $errLista);
	$tiempoMax = validaCampoTXT('tiempoMax',1,FALSE, $_POST, $errLista);
	$deSistema = $_POST['deSistema'];
	$urgente = $_POST['urgente'];
	$idActivo=$_POST['id'];
	
	//Verificación de $errLista por Errores o sino capturar
	if(noHayErrores($errLista))
	{
		if($idActivo == "")
			$query="INSERT INTO `0101_actividades`(`actividad`, `deSistema`,`tiempoMax`,`urgente`) VALUES ('".$actividad."', '".$deSistema."','".$tiempoMax."','".$urgente."')";
		else
			$query="UPDATE `0101_actividades` SET `actividad`= '".$actividad."', `deSistema` = '".$deSistema."', `tiempoMax`='".$tiempoMax."',`urgente`='".$urgente."' WHERE `idActividad`= '".$idActivo."'";
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		$idActivo ="";
		$actividad="";
		$deSistema = 0;
		$tiempoMax = 0;
		$urgente = 0;
		mostrarModal("Registro exitoso");
	}
}elseif($idActivo != ""){
	$query = "SELECT `actividad`, `deSistema`,`tiempoMax`,`urgente` FROM `0101_actividades` WHERE `idActividad` = '".$idActivo."'";
	$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while($datoB=mysqli_fetch_assoc($resultadoSQL_B)){
		$actividad=$datoB['actividad'];
		$deSistema = $datoB["deSistema"];
		$tiempoMax = $datoB['tiempoMax'];
		$urgente = $datoB['urgente'];
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
        	<h2 class="text-center">TIPOS DE ACTIVIDADES</h2>
            <p class="text-center">Tipo de actividades que pueden registrarse en bitacora<br /><b>EXTREMO CUIDADO, ESTOS INDICES FUNCIONAN CON EL SISTEMA 1,2,3,6,7,9,10</b></p>
            <form method="post" name="localizacion" target="_self" role="form" id="myForm">
				<input type="hidden" name="id" value="<? echo $idActivo; ?>"/>
				<?
				echo campoTextoBasico("actividad","Tipo de actividad",$actividad,"icon-clock2", true, "", true, "", $lista=array());
				echo checkBox("deSistema", "es de sistema", $deSistema);
				echo campoTextoBasico("tiempoMax","Tiempo max en minutos",$tiempoMax,"icon-alarm", true, "", true, "", $lista=array());
				echo checkBox("urgente", "es urgente", $urgente);
				?>
                <h3 class="text-center" id="venue"></h3>
                <?
				//CANCELAR Y SUBMIT 
				$destino = "tipoActividades.php";
				if($idActivo == "")
					echo botonSubmit("registrar","Registrar", 'btn-primary');
				else
					echo botonSubmit("registrar","Actualizar", 'btn-danger');
				echo botonBasico("cancelar","Cancelar", $destino, "btn-warning");
				?>
        	</form>
            
            <div class="well" style="background: #c2c2c2;">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed"> 
                        <tr id="asientos">
                            <th col colspan="5" style="vertical-align:middle; background:#DB0003"><span class="textos">TIPO DE ACTIVIDADES</span></th>
                        </tr>
                        
                        <tr>
                            <th scope="row" width="125px" style="background:#C0D3E9">Id</th>
                            <th scope="row" style="background:#C0D3E9">Tipo de Actividad</th>
                            <th scope="row" width="120px" style="background:#C0D3E9">De sist</th>
                            <th scope="row" width="80px" style="background:#C0D3E9">Tiempo</th>
                            <th scope="row" width="80px" style="background:#C0D3E9">Urg</th>
                        </tr>  
     <?
        $query = "SELECT `actividad`, `idActividad`, `deSistema`,`tiempoMax`,`urgente` FROM `0101_actividades` ORDER BY `actividad`";
        $resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
        while($datoB=mysqli_fetch_assoc($resultadoSQL_B)){
    ?>
                        <tr id="asientos">
                            <td style="vertical-align:middle;"><a href="tipoActividades.php?id=<?php echo $datoB['idActividad']."&refresh=".rand(); ?>"><?php echo ($datoB['idActividad']); ?></a></td>
                            <td style="vertical-align:middle;"><span class="textos"><?php echo ($datoB["actividad"]); ?></span></td>
                            <td style="vertical-align:middle;"><span class="textos"><?php echo checkBox("sistema", "", $datoB["deSistema"],"","primary",true); ?></span></td>
                            <td style="vertical-align:middle;"><span class="textos"><?php echo number_format($datoB["tiempoMax"],0); ?></span></td>
                            <td style="vertical-align:middle;"><span class="textos"><?php echo checkBox("sistema", "", $datoB["urgente"],"","warning",true); ?></span></td>
                        </tr> 
    <?		} ?>
                    </table>
                </div>
            </div>
            <div class="clearfix"></div>
    	</section>
    	<?php include "parts/footer.html" ?>
	</div><!--cierre contenedor!-->
	<script>
		<?php if($mensajeModal) echo "mensajeModalA('".$mensaje."')"; ?>
	</script>
    
	
	
</body>
</html>
