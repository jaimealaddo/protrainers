<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';

if(permiteAcceso() && isset($_GET['idClienteProv']))
{
	$idUsuario = $_COOKIE['idERP'];
	$jerarquia = jerarquiaTarjeta($idUsuario);
	$idClienteProv = $_GET['idClienteProv'];
}else{
	salirJerarquia("index.php");
}

//botar si no es tu cliente y no tienes jerarquia mayor a 77
if($jerarquia < 77){
	$query = "SELECT `idUsuario` FROM `0015_usuariosClientes` WHERE `idUsuario` = '".$idUsuario."' AND `idClienteProv` = '".$idClienteProv."' LIMIT 1";	
	$resultadoSQL_A = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	if(mysqli_num_rows($resultadoSQL_A) == 0) salirJerarquia("index.php");
}
					

$ahora = time();
$ahoraText = gmdate("Y-m-d H:i",$ahora + ($timeZone*60*60));
$ahoraActual = gmdate("Y-m-d H:i:s",$ahora + ($timeZone*60*60));

if(isset($_POST['registrar']))
{
	$ahora=$_POST['tiempo'];
	$ahoraText = gmdate("Y-m-d H:i",$ahora + ($timeZone*60*60));
	$errLista = '';
	if(usuarioEnActividad($idUsuario) != 1){
		$errLista .= "|Tu estado no est&aacute; activo|";
	}
	$concepto = validaCampoTXT('concepto',4,TRUE, $_POST, $errLista);
	$actividad = validaCampoSelect('actividad',$_POST,$errLista);
	$activo = $_POST['activo'];
	
	if(noHayErrores($errLista)){
		if($_POST['idPrincipalControl']=="")
		{
			if($activo == "1")
			{
				$query = "INSERT INTO `0040_seguimiento`(`fecha`, `idClienteProv`, `concepto`, `activo`, `idActividad`) VALUES ('".$ahoraActual."','".$idClienteProv."','Evento agregado para el ".$ahoraText." <br /><br />".$concepto."','0', '10')";
				ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			}
			$query = "INSERT INTO `0040_seguimiento`(`fecha`, `idClienteProv`, `concepto`, `activo`, `idActividad`) VALUES ('".$ahoraText."','".$idClienteProv."','".$concepto."','".$activo."', '".$actividad."')";
			$id=insertSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			
			$query = "INSERT INTO `0100_bitacora`(`idActividad`, `idUsuario`,`observacion`,`registro`) VALUES ('".$actividad."','".$idUsuario."','Nuevo seguimiento del cliente','".$id."')";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		}
		else
		{
			$query = "UPDATE `0040_seguimiento` SET `fecha`='".$ahoraText."',`concepto`='".$concepto."',`activo`='".$activo."', `idActividad`='".$actividad."' WHERE `idCalendario`= '".$_POST['idPrincipalControl']."'";
			$id=insertSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			
			$query = "INSERT INTO `0040_seguimiento`(`fecha`, `idClienteProv`, `concepto`, `activo`, `idActividad`) VALUES ('".$ahoraActual."','".$idClienteProv."','Evento ".$_POST['idPrincipalControl']." modificado','0', '9')";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			
			$query = "INSERT INTO `0100_bitacora`(`idActividad`, `idUsuario`,`observacion`,`registro`) VALUES ('".$actividad."','".$idUsuario."','Actualizacion de seguimiento del cliente','".$id."')";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		}
		
		$concepto = "";
		$actividad = "";
		$ahora = time();
		$ahoraText = gmdate("Y-m-d H:i:s",$ahora + ($timeZone*60*60));
		$activo = 1;
		$idPrincipal = "";
	}
}
elseif($_GET['id'])
{
	$idPrincipal = $_GET['id'];
	$query = "SELECT `fecha`,  `concepto`, `activo`, `idActividad` FROM `0040_seguimiento` WHERE `idCalendario` = '".$idPrincipal."'";
	$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
	while ($dato = mysqli_fetch_array($resultadoSQL))
	{
		$concepto = str_replace("<br />","\r\n", $dato['concepto']);
		$ahoraText = $dato['fecha'];
		$activo = $dato['activo'];
		$actividad = $dato['idActividad'];
		$ahora = strtotime($ahoraText);
	}
}else{
	$idPrincipal = "";
	$activo = 1;
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
            <h3 class="text-center">Seguimiento de<br/> <? echo ucwords(clienteProveedor($idClienteProv)); ?></h3>
            <div id="panelDesplegableA">
                <div class="panel-group col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2" id="accordionAyuda" role="tablist" aria-multiselectable="false">
                    <div class="panel panel-primary">
                        <div class="panel-heading" role="tab" id="headingActivos" style="padding:0px;">
                            <h4 class="panel-title">
                                <a class="btn-block" style="padding:9px;" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseActivos" aria-expanded="true" aria-controls="collapseAyuda01">
                                    <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> EVENTOS ACTIVOS
                                </a>
                            </h4>
                        </div>
                        <div id="collapseActivos" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingActivos" aria-expanded="true">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-condensed"> 
                                        <tr>
                                            <th scope="row"width="80px">ID</th>
                                            <th scope="row"width="120px">Fecha</th>
                                            <th scope="row" >Concepto</th>
                                        </tr>               
                            <?
                            $query = "SELECT `idCalendario`, `fecha`,  `concepto` FROM `0040_seguimiento` WHERE `idClienteProv` = '".$idClienteProv."' AND `activo` = '1' ORDER BY `fecha` LIMIT 0, 20";
                            $resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
                            while ($dato = mysqli_fetch_array($resultadoSQL))
                            { 
								$fechaTime = strtotime($dato['fecha']." GMT");
								$dentroDeMinutosTime = time() + ($timeZone*60*60) + (60*10);
								if($fechaTime<$dentroDeMinutosTime){
									$yaTime = time() + ($timeZone*60*60);
									if($fechaTime<=$yaTime){
										$colorAlerta = " background-image: linear-gradient(to bottom,#d9534f 0,#c12e2a 100%);";
										$colorBoton = "danger";
									}else{
										$colorAlerta = " background-image: linear-gradient(to bottom,#f0ad4e 0,#eb9316 100%);";
										$colorBoton = "warning";
									}
								}else{
									$colorAlerta = "";
									$colorBoton = "primary";
								}
								$laFecha = fechaHora($dato['fecha'], true);
							?>
                                        <tr>
                                            <td style="vertical-align:middle;<? echo $colorAlerta; ?>"><span class="textos"><?php echo ($dato['idCalendario']); ?></span></td>
                                            <td style="vertical-align:middle;"><span class="textos"><?php echo $laFecha; ?></span></td>
                                            <td style="vertical-align:middle;"><a href="seguimiento.php?idClienteProv=<?php echo $idClienteProv."&id=".$dato['idCalendario']; ?>" class="btn btn-<? echo $colorBoton; ?> btn-block btn-xs"><?php echo ($dato['concepto']); ?></a></td>
                                        </tr>
                        <?	}
                            ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form action="seguimiento.php?idClienteProv=<? echo $idClienteProv; ?>" method="post" name="form" target="_self">
                <div class="form-group">
                    <div class="input-group date col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3" id="datetimepicker12">
                        <span class="input-group-addon icon-clock2" aria-hidden="true"></span>
                        <input class="form-control fechaInput" style="text-align:center; cursor: default;" type="button" name="fecha"/>
                    </div>
                    <p style="float:none;" class="help-block col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">⇧ Click aqu&iacute; para cambiar la fecha</p>
                </div>
                
                <input type="hidden" value="<?php echo $ahora; ?>" id="tiempo" name="tiempo">
                <input type="hidden" value="<?php echo $idPrincipal; ?>" id="idPrincipalControl" name="idPrincipalControl">
                <?
				$listaAactividades = array();
				$query = "SELECT `idActividad`, `actividad` FROM `0101_actividades` WHERE `deSistema` = '0' ORDER BY `actividad`";
				$resultadoSQL_B = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
				while($datoB=mysqli_fetch_assoc($resultadoSQL_B)){
					$listaAactividades[$datoB["idActividad"]]=$datoB['actividad'];
				}
				
				echo campoSelectBasico("actividad","tipo de actividad",$actividad, "icon-clock2", $listaAactividades);
                echo campoTextArea("concepto","Concepto",$concepto, "icon-comment-o", true, "", $rows=6);
                echo checkBox("activo", "Activo (m&aacute;rcalo si quieres un recordatorio)", $activo,"onChange='activar()'");
                if($idPrincipal=="")
                {
                    $texto = "Registrar";
                    $color = "btn-success";
                }else{
                    $texto = "Actualizar";
                    $color = "btn-warning";
                }
                echo botonSubmit("registrar",$texto, $color);
                echo botonBasico("cancelar","Cancelar","seguimiento.php?idClienteProv=".$idClienteProv, "btn-primary");
				echo botonBasico("contacto","Editar Contacto","contactos.php?id=".$idClienteProv, "btn-default",true,false);
                ?>
            </form>
            <div id="panelDesplegable">
                <div class="panel-group col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2" id="accordionAyuda" role="tablist" aria-multiselectable="false">
                    <div class="panel panel-primary">
                        <div class="panel-heading" role="tab" id="headingAyuda01" style="padding:0px;">
                            <h4 class="panel-title">
                                <a class="btn-block collapsed" style="padding:9px;" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseAyuda01" aria-expanded="false" aria-controls="collapseAyuda01">
                                    <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> VER HISTORIAL
                                </a>
                            </h4>
                        </div>
                        <div id="collapseAyuda01" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingAyuda">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-condensed"> 
                                        <tr>
                                            <th scope="row" width="80px">ID</th>
                                            <th scope="row"width="120px">Fecha</th>
                                            <th scope="row" >Concepto</th>
                                            <th scope="row" width="5%">Activo</th>
                                        </tr>               
                            <?
                            $query = "SELECT `idCalendario`, `fecha`,  `concepto`, `activo` FROM `0040_seguimiento` WHERE `idClienteProv` = '".$idClienteProv."' ORDER BY `fecha` DESC LIMIT 0, 20";
                            $resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
                            while ($dato = mysqli_fetch_array($resultadoSQL))
                            { 
								$laFecha = fechaHora($dato['fecha'], true);
							?>
                                        <tr>
                                            <td style="vertical-align:middle;"><span class="textos"><?php echo ($dato['idCalendario']); ?></span></td>
                                            <td style="vertical-align:middle;"><span class="textos"><?php echo $laFecha; ?></span></td>
                                            <td style="vertical-align:middle;"><a href="seguimiento.php?idClienteProv=<?php echo $idClienteProv."&id=".$dato['idCalendario']; ?>" class="btn btn-primary btn-block btn-xs"><?php echo ($dato['concepto']); ?></a></td>
                                            <td style="vertical-align:middle;" class="text-center"><span class="textos"><input class="checo" type="checkbox" name="ckbox[]" value="1" <? if($dato['activo']) echo "checked" ?>/></span></td>
                                        </tr>
                        <?	}
                            ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
    	</section>
    	<?php include "parts/footer.html" ?>
	</div><!--cierre contenedor!-->
	<script>
		$(document).ready(function(){
			$('#datetimepicker12').datetimepicker({
				inline: false,
				sideBySide: true,
				locale: 'es',
				format:'dddd DD MMMM YYYY hh:mm A',
				date:"<? echo $ahoraText; ?>"
			});
			$('#datetimepicker12').on('dp.change', function (e) {
				actualizar(e.date);
			});
		});
		function actualizar(date)
		{
			date = Math.floor(date / 1000);
			document.getElementById("tiempo").value = date;
		}
		<?php if($mensajeModal) echo "mensajeModalA('".$mensaje."')"; ?>
	</script>
    
	
	
</body>
</html>
