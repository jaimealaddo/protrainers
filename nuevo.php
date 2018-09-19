<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';
$jerarquia = permiteAcceso();

$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
$fecha = substr($ahora,0,10);

$primerCarga=true;
$estadoActivo=moduloActivo();

if(isset($_POST['filtro'])){
	$filtro = $_POST['filtro'];
	if($filtro =="A-L-L"){
		$filtroText = "";
		$banderaFiltro = false;
	}else{
		$filtroText = "AND `0015_usuariosClientes`.`idUsuario` = '".$filtro."'";
		$banderaFiltro = true;
	}
}else{
	$filtro = $idUsuario;
	$filtroText = "AND `0015_usuariosClientes`.`idUsuario` = '".$filtro."'";
	$banderaFiltro = true;
}

if(isset($_POST['activar']) && $jerarquia >= 60)
{
	$modulo = validaCampoSelect('modulo',$_POST,$errLista);
	$mision = validaCampoSelect('mision',$_POST,$errLista);
	if($_POST['moduloAlt']!="")$modulo = $_POST['moduloAlt'];
	if(!$estadoActivo){
		$query = "INSERT INTO `0100_inicioModulo`(`idModulo`, `idUsuario`, `fecha`) VALUES ('".$modulo."','".$idUsuario."','".$ahora."')";
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		$estadoActivo=true;
	}
}
elseif(isset($_POST['desactivar']) && $jerarquia >= 60)
{
	$idInicio="";
	$noActividad ="";
	moduloActual($noActividad, $idInicio);
	if($noActividad != 0 && $noActividad != "" && $idInicio != "")
	{
		$query = "UPDATE `0100_inicioModulo` SET `activo`= 0, `cronometro`= NULL WHERE `idInicio`='".$idInicio."'";
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		$estadoActivo=false;
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
        	<h2 class="text-center">BIENVENIDO <? echo $nombreHeader; ?></h2>
            <form method="post" name="localizacion" target="_self" role="form" id="myForm">
                
				<?
				if(!$estadoActivo)
				{ 
					if($jerarquia >= 60){
						?>
                        <h3 class="text-center">Vamos a comenzar, activa un m&oacute;dulo</h3>
                    	<div class="text-center"><img src="fotos/logo-protrainers.png" width="90%" alt="logo" style="margin-bottom: 20px;"/></div>
                        <?
						echo campoSelectBasico("mision","Filtrar",$idMision, "icon-search", misiones(),false,false,'filtrar()');
						?> <div id="modulos"> <?
						echo campoSelectBasico("modulo","M&oacute;dulo",$modulo, "icon-sitemap", modulos(),true,false,'alternos()');
						?> </div><div id="divAlternos"></div> <?
						echo botonSubmit("activar","Activar", "btn-success");
					}else{
						$query = "SELECT `mision`, `fecha`, `lun`, `mar`, `mie`, `jue`, `vie`, `sab`, `dom`,`foto`, `idCategoria` FROM `0010_usuarios` ".
								"INNER JOIN `0012_usuarioMision` ON `0010_usuarios`.`idUsuarioMision`=`0012_usuarioMision`.`idUsuarioMision` ".
								"INNER JOIN `0040_misiones` ON `0012_usuarioMision`.`idMision`=`0040_misiones`.`idMision` ".
								"WHERE  `0010_usuarios`.`idUsuario` =  '".$idUsuario."' ";
						$resultadoSQL = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI']." ->->->->-> inc.funciones.php", __LINE__);
						while($datoA=mysqli_fetch_assoc($resultadoSQL)){
							$nombreMision = $datoA['mision']; 
							$fechaBD= $datoA['fecha'];
							$lun= $datoA['lun'];
							$mar= $datoA['mar'];
							$mie= $datoA['mie'];
							$jue= $datoA['jue'];
							$vie= $datoA['vie'];
							$sab= $datoA['sab'];
							$dom= $datoA['dom'];
							$fotoUsr=$datoA['foto'];
							$idCategoria=$datoA['idCategoria'];
						}
						if($fotoUsr == "")
						{
							$fotoUsr = "fotos/usrSinFoto.jpg";
						}
						?>
                        
                        <h3 class="text-center">Tu misi&oacute;n es <? echo $nombreMision; ?></h3>
                        <div class="text-center"><img src="fotos/logo-protrainers.png" width="70%" alt="logo" style="margin-bottom: 20px;"/></div>
						<div id="panelDesplegableA">
                            <div class="panel-group col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2" id="accordionAyuda" role="tablist" aria-multiselectable="false">
                                <div class="panel panel-primary">
                                    <div class="panel-heading" role="tab" id="headingActivos" style="padding:0px;">
                                        <h4 class="panel-title">
                                            <a class="btn-block collapsed" style="padding:9px;" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseActivos" aria-expanded="false" aria-controls="collapseAyuda01">
                                                <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> MIS RECORDS
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseActivos" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingActivos" aria-expanded="false">
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-condensed"> 
                                                    <tr>
                                                        <th scope="row" style="background:#C0D3E9">Actividad</th>
                                                        <th scope="row" style="background:#C0D3E9">Record</th>
                                                        <th scope="row" style="background:#C0D3E9">fecha</th>
                                                    </tr>               
                                        <?
                                                                            /*
                                                                            ['idActividad']
                                                                            ['actividad']
                                                                            ['record']
                                                                            ['fecha']
                                                                            */
                                        $historiaMisiones = fechaMision($usr,$idMision);
										$records = todoRecordsUsuario($idUsuario,$historiaMisiones[0]['fecha']);
                                        foreach($records as $datos)
                                        {
                                        ?>
                                                    <tr>
                                                        <td style="vertical-align:middle;<? echo $colorAlerta; ?>"><span class="textos"><?php echo $datos["actividad"]; ?></span></td>
                                                        <td style="vertical-align:middle;<? echo $colorAlerta; ?>"><span class="textos"><?php echo $datos['record']; ?></span></td>
                                                        <td style="vertical-align:middle;<? echo $colorAlerta; ?>"><span class="textos"><?php echo fechaHora($datos["fecha"],true); ?></span></td>
                                                        <!--
                                                        <td style="vertical-align:middle;<? echo $colorAlerta; ?>"><?php if($datoA["foto"] != ""){ ?><span class="textos"><div class="fotoGal"><img style="height:100%;width:100%; max-width:40px;object-fit:contain;" src="<?php echo $datoA["foto"]; ?>" alt="sin foto"/></div></span><? } ?></td>
                                                        <td style="vertical-align:middle;<? echo $colorAlerta; ?>"><?php if($datoA["video"] != ""){ ?><a target="new" href="<?php echo $datoA['video']; ?>">Vid</a><? } ?></td>
                                                   		-->
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
                        <div class="row">
                        	<div class="control-label col-xs-12 col-md-2 col-md-offset-2 col-lg-2 col-lg-offset-4">
                        		Mi Horario es
                            </div>
                            <? if($lun != ""){ ?>
                            <div class="col-xs-12 col-md-6 col-lg-4 col-md-offset-4 col-lg-offset-6">
                            	<? echo "Lunes a las ". substr($lun,0,-2) ." Horas"; ?>
                            </div>
                            <? }
							if($mar != ""){ ?>
							<div class="col-xs-12 col-md-6 col-lg-4 col-md-offset-4 col-lg-offset-6">
                            	<? echo "Martes a las ". substr($mar,0,-2) ." Horas"; ?>
                            </div>
                            <? }
							if($mie != ""){ ?>
                            <div class="col-xs-12 col-md-6 col-lg-4 col-md-offset-4 col-lg-offset-6">
                            	<? echo "Mi&eacute;rcoles a las ". substr($mie,0,-2) ." Horas"; ?>
                            </div>
                            <? }
							if($jue != ""){ ?>
                            <div class="col-xs-12 col-md-6 col-lg-4 col-md-offset-4 col-lg-offset-6">
                            	<? echo "Jueves a las ". substr($jue,0,-2) ." Horas"; ?>
                            </div>
                            <? }
							if($vie != ""){ ?>
                            <div class="col-xs-12 col-md-6 col-lg-4 col-md-offset-4 col-lg-offset-6">
                            	<? echo "Viernes a las ". substr($vie,0,-2) ." Horas"; ?>
                            </div>
                            <? }
							if($sab != ""){ ?>
                            <div class="col-xs-12 col-md-6 col-lg-4 col-md-offset-4 col-lg-offset-6">
                            	<? echo "S&aacute;bado a las ". substr($sab,0,-2) ." Horas"; ?>
                            </div>
                            <? }
							if($dom != ""){ ?>
                            <div class="col-xs-12 col-md-6 col-lg-4 col-md-offset-4 col-lg-offset-6">
                            	<? echo "Domingo a las ". substr($dom,0,-2) ." Horas"; ?>
                            </div>
                        	<?	} ?>
						</div>
                        	
                           
                        <div class="media">
                            <h4 class="media-heading">Detalle de <? echo $nombreHeader; ?>:</h4>
                            <div class="media-left media-middle datosAnuncio fotoAnuncio">
                                <img class="media-object" width="100%" src="<? echo $fotoUsr; ?>" alt="imagen">
                            </div>
                            <div class="media-body datosAnuncio">
                                <h3>Categor&iacute;a <? echo nombreCategoria($idCategoria); ?></h3>
                                <ul>
                                	<li>INSIGNIAS</li>
                                </ul>
                                
                            </div>
                        </div>
                        <br /><br />
                        <?
					}
				}else{ 
					$noActividad=0;
                    $inicio="";
                    $moduloActual=moduloActual($noActividad, $inicio);
					$conSubgrupo="0";
					$nombreModulo = nombreDelModulo($idModulo, $conSubgrupo);
					?>
					<h3 class="text-center">Estas en el m&oacute;dulo/a <?php echo $nombreModulo; ?></h3>
					<?
                    //echo campoTextoBasico("giro","Giro",$giro,"icon-chain", true, "", true, "", $lista=array());
                    //echo campoTextArea("descripcion","Descripci&oacute;n",$descripcion, "icon-book2", false, "", 3);
                    
                    
                    echo botonBasico("usuarios","Grupo","grupo.php?idInicio=".$inicio, "btn-primary");
                    if(hayGrupo($inicio,1)) echo botonBasico("start","START","actividad.php?idInicio=".$inicio, "btn-success");
                    ?>
                    <div id="panelDesplegableA"> 
                        <div class="panel-group col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2" id="accordionAyuda" role="tablist" aria-multiselectable="false">
                            <div class="panel panel-primary">
                                <div class="panel-heading" role="tab" id="headingActivos" style="padding:0px;">
                                    <h4 class="panel-title">
                                        <a class="btn-block" style="padding:9px;" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseActivos" aria-expanded="true" aria-controls="collapseAyuda01">
                                            <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> ACTIVIDADES DEL MODULO
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseActivos" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingActivos" aria-expanded="true">
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-condensed"> 
                                                <tr>
                                                    <th scope="row" width="40px"style="background:#C0D3E9">Orden</th>
                                                    <th scope="row" style="background:#C0D3E9">Actividad</th>
                                                    <th scope="row" style="background:#C0D3E9">Minutos</th>
                                                    <th scope="row" style="background:#C0D3E9">Foto</th>
                                                    <th scope="row" style="background:#C0D3E9">Vid</th>
                                                </tr>               
                                    <?
                                    $query = "SELECT `0031_actividadModulo`.`idActividadModulo`, `actividad`, `descripcion`, `video`, `foto`, `orden`, `minutos` FROM `0020_actividades` INNER JOIN `0031_actividadModulo` ON `0020_actividades`.`idActividad`=`0031_actividadModulo`.`idActividad` WHERE `0031_actividadModulo`.`idModulo` = '".$moduloActual."' ORDER BY `orden` ";
                                    $resultadoSQL_A = ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
                                    while ($datoA = mysqli_fetch_array($resultadoSQL_A))
                                    { 
                                        
                                        if($datoA["orden"]==$noActividad){
                                            $colorAlerta = " background-image: linear-gradient(to bottom,#d9534f 0,#c12e2a 100%);";
                                        }else{
                                            $colorAlerta = "";
                                        }
                                        
                                        
                                    ?>
                                                <tr>
                                                    <td style="vertical-align:middle;<? echo $colorAlerta; ?>"><span class="textos"><?php echo $datoA["orden"]; ?></span></td>
                                                    <td style="vertical-align:middle;<? echo $colorAlerta; ?>"><span class="textos"><?php echo ucwords($datoA['actividad']); ?></span></td>
                                                    <td style="vertical-align:middle;<? echo $colorAlerta; ?>"><span class="textos"><?php echo $datoA["minutos"]; ?></span></td>
                                                    <td style="vertical-align:middle;<? echo $colorAlerta; ?>"><?php if($datoA["foto"] != ""){ ?><span class="textos"><div class="fotoGal"><img style="height:100%;width:100%; max-width:40px;object-fit:contain;" src="<?php echo $datoA["foto"]; ?>" alt="sin foto"/></div></span><? } ?></td>
                                                    <td style="vertical-align:middle;<? echo $colorAlerta; ?>"><?php if($datoA["video"] != ""){ ?><a target="new" href="<?php echo $datoA['video']; ?>">Vid</a><? } ?></td>
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
                    <?
                    //CANCELAR Y SUBMIT 
                    $destino = "nuevo.php";
                    echo botonSubmit("desactivar","forzar fin de actividad", "btn-danger",true,false);
				}
				?>
        	</form>
             

    	</section>
    	<?php include "parts/footer.html" ?>
	</div><!--cierre contenedor!-->
	<script>
		function filtrar()
		{
			var mision= document.getElementById("mision").value
			var modulo= document.getElementById("modulo").value
			$.get("parts/filtroMision.php?modulo=" + modulo + "&mision=" + mision +"&refresh=" + new Date().getTime(), function(responseText) {
				dato = (responseText);
				//console.log("resultado de " + dato);
				document.getElementById("modulos").innerHTML  = dato;
				alternos();
			});
			
		}
		function alternos()
		{
			var mision= document.getElementById("mision").value
			var modulo= document.getElementById("modulo").value
			var moduloAlt = '';
			if($('#moduloAlt').val()) moduloAlt = document.getElementById("moduloAlt").value;
			if(mision!="" && modulo !="")
			{
				$.get("parts/modulosAlternos.php?modulo=" + modulo + "&moduloAlt=" + moduloAlt + "&refresh=" + new Date().getTime(), function(responseText) {
					dato = (responseText);
					//console.log("resultado con " + moduloAlt);
					document.getElementById("divAlternos").innerHTML  = dato;
				});
			}else{
				document.getElementById("divAlternos").innerHTML  = '';
			}
		}
		<?php if($mensajeModal) echo "mensajeModalA('".$mensaje."')"; ?>
	</script>
    
	
	
</body>
</html>
