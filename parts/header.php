<?php 


if (isset($_GET['alerta']))
{
	if($_GET['alerta'] == 1)
	{
		$mensajeTres = "Sin jerarquia";
		mostrarModal($mensajeTres); //sin jerarquia
	}elseif($_GET['alerta'] == 2){
		mostrarModal("Registro Exitoso"); //registro exitoso
	}elseif($_GET['alerta'] == 3){
		mostrarModal("Tu tiempo de compra ha caducado"); //este registro ya fue solucionado
	}elseif($_GET['alerta'] != ""){
		$mensajeHeader = $_GET['alerta'];
		mostrarModal($mensajeHeader); 
	}
}

$ahoraHeader = time();
?>
<header>
    <div class="menu_bar"> <!-- barra de menu en modo telefono -->
        <div class="logo-bar">
            <img src="imagenes/cideaa.png" height="50px" alt=""/>
        </div>
        <a href="javascript:void(0)" class="bt-menu">
            <span class="icon-menu"></span>
            <span class="texto-menu-bar">Menu</span>
        </a>
    </div>
    <nav>
        <ul>
            <li class="submenu">
            	<a href="javascript:void(0)"><span class="icon-book2"></span>Diario<span class="flechita glyphicon glyphicon-chevron-down" aria-hidden="true"></span></a>
                <ul class="children">
                	<li><a href="https://www.cideaa.com/protrainers/index.php?refresh=<? echo $ahoraHeader; ?>"><span class="icon-blackboard"></span>Inicio</a></li>
                    <?php 
					if($jerarquia !== false){ ?>
                    <li><a href="historia.php?refresh=<? echo $ahoraHeader; ?>">Hist&oacute;rico<span class="icon-book" aria-hidden="true"></span></a></li>
                    <?php } ?>
                    <li><a href="ranking.php?refresh=<? echo $ahoraHeader; ?>">Ranking<span class="icon-trophy" aria-hidden="true"></span></a></li>
                    <?php 
					if($jerarquia >= 77){ ?> <!-- Ver Actividad -->
                    	<li><a href="actividad.php?refresh=<? echo $ahoraHeader; ?>">Actividad<span class="icon-alarm" aria-hidden="true"></span></a></li>
                        <li><a href="contactosVer.php?refresh=<? echo $ahoraHeader; ?>">Ver Contactos<span class="icon-alarm" aria-hidden="true"></span></a></li>
					<?php }
					if($jerarquia > 90){ ?>
                    <li><a href="demo.html"><span class="icon-search"></span>iconos</a></li>
                    <?php } ?>
                </ul>
            </li>
            <li class="submenu">
                <a href="javascript:void(0)"><span class="icon-user"></span>Usuario<span class="flechita glyphicon glyphicon-chevron-down" aria-hidden="true"></span></a>
                <ul class="children">
                	<?php 
					if($jerarquia === false){ ?> <!-- NO usuarios -->
                        <li><a href="index.php?refresh=<? echo $ahoraHeader; ?>">Entrar<span class="icon-user-check" aria-hidden="true"></span></a></li>
                    <?php }
					else{ ?> <!-- NOMBRE usuario -->
						<li><div class="info"><span class="icon-checkmark" aria-hidden="true"></span> <?php echo $nombreHeader=ucwords(nombreById($_COOKIE['idPRO'])); ?></div></li>
					<?php }
					if($jerarquia >= 50 || $idUsuario == 0){ ?> <!-- NUEVO USUARIO -->
                    	<li><a href="registro.php?refresh=<? echo $ahoraHeader; ?>">Nuevo Usr<span class="icon-user-plus" aria-hidden="true"></span></a></li>
                    <?php } 
					if($jerarquia >= 65){ ?> <!-- SUPER USUARIOS -->
                    	<li><a href="superUsuarios.php?refresh=<? echo $ahoraHeader; ?>">SuperUsr<span class="icon-key2" aria-hidden="true"></span></a></li>
                    <?php } 
					if($jerarquia >= 60){ ?> <!-- SUPER USUARIOS -->
                    	<li><a href="estaturas.php?refresh=<? echo $ahoraHeader; ?>">Asignar<span class="icon-user-check" aria-hidden="true"></span></a></li>
                    <?php } 
					if($jerarquia !== false){ ?> <!-- Solo USUARIOS -->
                        <li><a href="preferencias.php?refresh=<? echo $ahoraHeader; ?>">Preferencias<span class="icon-heart" aria-hidden="true"></span></a></li>
						<li><a href="includes/salir.php?refresh=<? echo $ahoraHeader; ?>">Salir<span class="icon-user-minus" aria-hidden="true"></span></a></li>
					<?php } ?>
                </ul>
            </li>
            <?php if($jerarquia > 69){ ?> <!-- CONFIGURACION --> <!-- Solo USUARIOS -->
            <li class="submenu">
            	<a href="javascript:void(0)"><span class="icon-cog"></span>Config<span class="flechita glyphicon glyphicon-chevron-down" aria-hidden="true"></span></a>
            	<ul class="children">
                	<?php
					if($jerarquia >= 70){ ?> <!-- PRODUCTOS -->
                        <li><a href="actividades.php?refresh=<? echo $ahoraHeader; ?>">Actividades<span class="icon-bolt" aria-hidden="true"></span></a></li>
                    <?php }
					if($jerarquia >= 75){ ?> <!-- Cambiar datos base -->
                		<li><a href="modulos.php?refresh=<? echo $ahoraHeader; ?>">M&oacute;dulos<span class="icon-sitemap" aria-hidden="true"></span></a></li>
                        <li><a href="misiones.php?refresh=<? echo $ahoraHeader; ?>">Misiones<span class="icon-trophy" aria-hidden="true"></span></a></li>
                        <li><a href="actividadesAsignar.php?refresh=<? echo $ahoraHeader; ?>">Asignar Activ<span class="icon-chain" aria-hidden="true"></span></a></li>
                        <li><a href="retos.php?refresh=<? echo $ahoraHeader; ?>">Retos<span class="icon-target" aria-hidden="true"></span></a></li>
                        <li><a href="categorias.php?refresh=<? echo $ahoraHeader; ?>">Categ<span class="icon-sort-amount-asc" aria-hidden="true"></span></a></li>
                    <? }
					if($jerarquia >= 80){ ?> <!-- Cambiar datos muy base -->
                    	<li><a href="tipoActividades.php?refresh=<? echo $ahoraHeader; ?>">Tipo actividad<span class="icon-clock2" aria-hidden="true"></span></a></li>
                    <?php } ?>
                </ul>
            </li>
            <? } ?>
            <li class="submenu">
            	<a href="javascript:void(0)"><? echo gmdate("H:i",time() + ($timeZone*60*60)); ?></a>
            </li>
        </ul>
    </nav>
</header>

