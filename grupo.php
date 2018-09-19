<?

include 'includes/inc.funciones.php';
include 'includes/inc.conexion.php';
include 'includes/formsBase.php';
$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));

$jerarquia = permiteAcceso();
if($jerarquia < 60 || !isset($_GET['idInicio'])) salirJerarquia("nuevo.php");
$idInicio = $_GET['idInicio'];


$ahora = gmdate("Y-m-d H:i:s",time() + ($timeZone*60*60));
if(isset($_GET['id']))
{
	$idActivo = $_GET['id'];
}

if(isset($_POST['todosB']))
{
	$pregargar=1;
}
$laHora="";
if(isset($_POST['todosA']))
{
	$todos=1;
}elseif($_POST['dia']){
	$dia = $_POST['dia'];
	$hora = $_POST['horarioH'];
	$minuto = $_POST['horarioM'];
	$laHora=$hora.":".$minuto;
}

$noActividad="";
$inicioValidar="";
$idModulo = moduloActual($noActividad, $inicioValidar);

if($idInicio != $inicioValidar)die("ERROR FATAL NO COINCIDEN LOS TIPOS");
$conSubgrupo="";
$nombreModulo = nombreDelModulo($idModulo, $conSubgrupo);


if(isset($_POST['registrar']))
{	
	$errLista = '';
	$usuariosTemp = $_REQUEST['usuarioA'];
	
	//hacer un nuevo arreglo sin los usuarios duplicados y los usuarios que ya estan en la BD
	$usuariosForm=array();
	foreach($usuariosTemp as $cadaUsuario)
	{
		if(!in_array($cadaUsuario,$usuariosForm) && !in_array($cadaUsuario,$usuarios)) 
		{
			$usuariosForm[]=$cadaUsuario;
		}
	}
	
	$cantidadDeAspirantes=count($usuariosForm);
	
	if($cantidadDeAspirantes == 0)$errLista.="|No hay registros|";
	//Verificación de $errLista por Errores o sino capturar
	
	
	if(noHayErrores($errLista))
	{
		$dividir=false;
		//para subgrupos primero checar que hay aspirantes para subdividir y si el modulo lleva subgrupo
		if($cantidadDeAspirantes > 3 && $conSubgrupo == '1')$dividir=true;
		
		//borrar posibles grupos de aspirantes
		$query = "DELETE FROM `0101_grupos` WHERE `idInicio` = '".$idInicio."'";
		ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		
		
		//organizar los subgrupos
		switch($cantidadDeAspirantes){
			case 1: $orden=array(1=>1); break;
			case 2: $orden=array(1=>2); break;
			case 3: $orden=array(1=>3); break;
			case 4: $orden=array(1=>2,2=>2); break;
			case 5: $orden=array(1=>3,2=>2); break;
			case 6: $orden=array(1=>2,2=>2,3=>2); break;
			case 7: $orden=array(1=>3,2=>2,3=>2); break;
			case 8: $orden=array(1=>4,2=>2,3=>2); break;
			case 9: $orden=array(1=>4,2=>3,3=>2); break;
			case 10: $orden=array(1=>4,2=>4,3=>2); break;
			case 11: $orden=array(1=>4,2=>4,3=>3); break;
			default: $orden=array(1=>4,2=>4,3=>4); break;
		}
		
		
		$query = "";
		$grupo=1;
		$lugar=1;
		foreach($usuariosForm as $cadaUsuario)
		{
			if(!$dividir) $query .= "('".$cadaUsuario."','".$idInicio."'),";
			else $query .= "('".$cadaUsuario."','".$idInicio."','".$grupo."'),";
			
			if($orden[$grupo] == $lugar)
			{
				$grupo++;
				$lugar=1;
			}else{
				$lugar++;
			}
		}
		if($query != "")
		{
			$query = substr($query,0,-1);
			if(!$dividir)$query = "INSERT INTO `0101_grupos`(`idUsuario`, `idInicio`) VALUES " . $query;
			else $query = "INSERT INTO `0101_grupos`(`idUsuario`, `idInicio`,`subGrupo`) VALUES " . $query;
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
			
			$query = "UPDATE `0100_inicioModulo` SET `circuito`='1', `subGrupo`='1' WHERE `idInicio`='".$idInicio."'";
			ejecutarSQL($query, $idUsuario, $_SERVER['REQUEST_URI'], __LINE__);
		}
		
		$destino = "nuevo.php?alerta=2";
		header("Location:".$destino);
		echo '<script>window.location = "'.$destino.'";</script>';
		exit;
		
		mostrarModal("Registro exitoso");
		
		$pregargar='';
	}
}

$usuarios=usuariosEnUnInicio($idInicio);


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
        	<h2 class="text-center">Grupos</h2>
            <p>Grupos de esta misi&oacute;n, solo aprecen los aspirantes que estan en una misi&oacute;n que incluye este m&oacute;dulo</p>
            <form method="post" name="localizacion" target="_self" role="form" id="myForm">
				<input type="hidden" name="id" value="<? echo $idActivo; ?>"/>
				<div class="panel panel-primary">
                  	<div class="panel-heading">Filtros:</div>
                    <div class="panel-body">
                    	
						<?
                        
                        echo checkBoxBoton('todos', 'Ver Todos','pre-cargar','cargar',$todos, $pregargar,"icon-book",'icon-clock',"primary","submit()", $sinMargen=false, $valorIdent="1");
                        $listaDias=array('Todos','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado','Domingo');
						echo '<div class="well">';
						echo campoSelectBasico('dia','D&iacute;a',$dia, "icon-calendar", $listaDias, false);
						echo seleccionHoras('horario', 'Horario', $hora, $minuto, false);
						echo "</div>";
                        ?>
                	</div>
                    
                </div>
                <div class="PRIMER_wrapper" id="PRIMER_wrapper">
                	<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3" style="text-align: right; padding-right: 3px;">
                    	<a href="javascript:void(0);" class="btn btn-success usuarioAdd_button" title="Add field" role="button" aria-label="add-remove"><span class="icon-plus"></span> Agregar aspirante</a>
					</div>
					<?
					if($todos == 1)$listaUsuarios=usuariosLista();
					else $listaUsuarios = usuariosEnInicio($idInicio,array(),$dia,$laHora);
					
					if($pregargar==1){
						
						foreach($listaUsuarios as $key => $usuario)
						{
							if(!array_key_exists($key,$usuarios))$usuarios[]=$key;
						}
					}
					$i=0;
                    do{
						echo campoSelectMultiple('usuario', 'nombre', $usuarios[$i], "icon-user-plus2", $listaUsuarios, true, ($i+2));
						$i++;
					}while ($i < count($usuarios));
					
					if($todos == 1)$listaUsuarios=usuariosLista();
					else $listaUsuarios = usuariosEnInicio($idInicio,$dia,$laHora);
					
                    ?>
                </div>
                <h3 class="text-center" id="venue"></h3>
                <?
				//CANCELAR Y SUBMIT 
				$destino = "grupo.php";
				if($idActivo == "")
					echo botonSubmit("registrar","Registrar", 'btn-primary');
				else
					echo botonSubmit("registrar","Actualizar", 'btn-warning');
				echo botonBasico("cancelar","Cancelar", $destino, "btn-info");
				?>
        	</form>
            <div class="clearfix"></div>
    	</section>
    	<?php include "parts/footer.html" ?>
	</div><!--cierre contenedor!-->
	<script>
		<?php 
			$HTML_add=campoSelectMultiple('usuario', 'nombre', $usuario[$i], "icon-user-plus2", $listaUsuarios, true, (2));
			agregarCampoJS(12, "usuario", "PRIMER", $HTML_add);
			
			if($mensajeModal) echo "mensajeModalA('".$mensaje."')"; 
		?>
	</script>
    
	
	
</body>
</html>
