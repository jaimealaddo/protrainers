<?
/* DISENOS BASE PARA FORMULARIOS */

function botonBasico($nombre,$textoUsuario,$destino, $color, $habilitado=true, $grande=true)
{
	if(!$habilitado) $destino="javascript:void(0)";
	if($grande)
	{
		$big=" btn-lg";
	}
	else
	{
		$big="";
	}
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'" class="sr-only text-right control-label col-xs-12 col-sm-12 col-md-3 col-lg-4">'.$textoUsuario.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">'.
				'<a class="btn '.$color.' btn-block'.$big.'" href="'.$destino.'" name="'.$nombre.'" id="'.$nombre.'" role="button"';
	if(!$habilitado) $salida.= " disabled";
	$salida.= '>'.$textoUsuario.'</a>'.
			'</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function botonJS($nombre,$textoUsuario,$funcion, $color, $habilitado=true, $grande=false)
{
	if($grande)
	{
		$big=" btn-lg";
	}
	else
	{
		$big="";
	}
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'" class="sr-only text-right control-label col-xs-12 col-sm-12 col-md-3 col-lg-4">'.$textoUsuario.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">'.
				'<a class="btn '.$color.' btn-block'.$big.'" href="javascript:void(0)" onClick="'.$funcion.'" title"'.$nombre.'" id="'.$nombre.'" role="button"';
	if(!$habilitado) $salida.= " disabled";
	$salida.= '>'.$textoUsuario.'</a>'.
			'</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function botonSubmit($nombre,$textoUsuario, $color, $habilitado=true, $grande=true)
{
	if($grande)
	{
		$big=" btn-lg";
	}
	else
	{
		$big="";
	}
	$salida.='<div class="form-group">'.
			'<label for="'.$nombre.'" class="sr-only text-right control-label col-xs-12 col-sm-12 col-md-3 col-lg-4">'.$textoUsuario.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">'.
				'<input class="btn '.$color.' btn-block '.$big.'" type="submit" name="'.$nombre.'" id="'.$nombre.'" value="'.$textoUsuario.'"';
	if(!$habilitado) $salida.= " disabled";
	$salida.= '/>'.
			'</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function checkBox($nombre, $textoUsuario, $valor, $funcionCheck="" ,$color="primary", $sinMargen=false, $valorIdent="1", $multiple="", $enLinea=false)
{
	if($valor == 1) $checado = " checked";
	else $checado = "";
	($enLinea)?$estilo='style="display: inline-flex; margin-bottom: 0px;"':$estilo="";
	if($multiple!="")$esMultiple="[]";
	if($multiple!="")$idMultiple=$multiple;
	($sinMargen)?$margen='style="margin:0px"':$margen="";
	($sinMargen)?$padd='style="padding-left:0px"':$padd="";
	$salida = 	'<div class="form-group" '.$estilo.'>'.
                	'<div '.$margen.' class="checkbox checkbox-'.$color.' col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">'.
                    	'<input type="checkbox" value="'.$valorIdent.'" id="'.$nombre.$idMultiple.'" name="'.$nombre.$esMultiple.'"'. $checado." ".$funcionCheck.' />'.
						'<label for="'.$nombre.$idMultiple.'"'.$padd.'> '.$textoUsuario.'</label>'.
                	'</div>'.
            	'</div><div class="clearfix"></div>';
	return $salida;
}

function checkBoxBoton($nombre, $textoUsuario,$textoUsuarioB,$textoBoton,$valorA,$valorB,$icono,$iconoB,$color="primary",$funcionBoton="", $sinMargen=false, $valorIdent="1")
{
	$valorA==1?$checado=" checked":$checado='';
	$valorB==1?$checadoB=" checked":$checadoB='';
	if($multiple)$esMultiple="[]";
	($sinMargen)?$margen='style="margin:0px"':$margen="";
	$salida = 	'<div class="form-group" '.$estilo.'>'.
                	//'<label for="'.$nombre.'" style="margin-bottom:0px" class="control-label col-xs-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.$textoUsuario.'</label>'.
					'<div id="'.$nombre.'Grupo" '.$margen.' class="input-group col-xs-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
                    	'<span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
						'<div class="checkbox checkbox-'.$color.'">'.
							'<input type="checkbox" class="form-control" style="opacity: 0;margin-top: -10px;position: absolute;" value="'.$valorIdent.'" id="'.$nombre.'A" name="'.$nombre."A".$esMultiple.'"'. $checado.'/>'.
							'<label for="'.$nombre.'A" style="position: absolute;left: 23px;top: -1px;"> '.$textoUsuario.'</label>'.
						'</div>'.
						'<span class="input-group-addon '.$iconoB.'" aria-hidden="true"></span>'.
						'<div class="checkbox checkbox-'.$color.'">'.
							'<input type="checkbox" class="form-control" style="opacity: 0;margin-top: -10px;position: absolute;" value="'.$valorIdent.'" id="'.$nombre.'B" name="'.$nombre."B".$esMultiple.'"'. $checadoB.'/>'.
							'<label for="'.$nombre.'B" style="position: absolute;left: 23px;top: -1px;"> '.$textoUsuarioB.'</label>'.
						'</div>'.
                		'<span class="input-group-btn">'.
							'<button class="btn btn-primary" type="button" onClick="'.$funcionBoton.'">'.$textoBoton.'</button>'.
						'</span>'.
					'</div>'.
            	'</div><div class="clearfix"></div>';
	return $salida;
}

function campoTextoBasico($nombre,$textoUsuario,$valor, $icono, $requerido=true, $ayuda="", $activo=true, $funcion="", $lista=array(), $boton=false, $funcionBtn="")
{
	if($funcion != "") $funcion = 'onChange="'.$funcion.'" ';
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'" class="text-right control-label col-xs-12 col-md-2 col-lg-3">'.$textoUsuario.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
				'<span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<input type="text" class="form-control" autocomplete="off" '.$funcion.' value="'.$valor.'" placeholder="'.$textoUsuario.'" name="'.$nombre.'" id="'.$nombre.'"';
	if($requerido) $salida.= " required";
	if(!$activo) $salida .= " readonly";
	if(count($lista)!=0) $salida .= ' list="'.$nombre.'Lista"';
	$salida.= '/>';
	if(is_array($lista)){
		$salida .= '<datalist id="'.$nombre.'Lista">';
		foreach($lista as $key => $dato){
			$dato = ucwords(utf8_decode($dato));
			$salida .= '<option value="'.$dato.'">'.$key.'</option>';
		}
		$salida .= '</datalist>';
	}
	if($boton){
		$salida .= '<span class="input-group-btn">';
        $salida .=  '<button class="btn btn-default" type="button" '.$funcionBtn.'>Go!</button>';
      	$salida .= '</span>';
	}
	$salida.='</div>';
	if($ayuda!="")$salida.= '<p class="help-block col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.$ayuda.'</p>';
	$salida.='</div><div class="clearfix"></div>';  
	return $salida;                
}

function campoMail($nombre,$textoUsuario,$valor, $icono, $requerido=true, $ayuda="", $funcion="", $lista=array())
{
	if($icono == "")$arroba="@";
	if($funcion != "") $funcion = 'onChange="'.$funcion.'" ';
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3">'.$textoUsuario.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
				'<span class="input-group-addon '.$icono.'" aria-hidden="true">'.$arroba.'</span>'.
				'<input type="email" class="form-control" '.$funcion.' value="'.$valor.'" placeholder="'.$textoUsuario.'" name="'.$nombre.'" id="'.$nombre.'"';
	if($requerido) $salida.= " required";
	if(count($lista)!=0) $salida .= ' list="'.$nombre.'Lista"';
	$salida.= '/>';
	if(is_array($lista)){
		$salida .= '<datalist id="'.$nombre.'Lista">';
		foreach($lista as $dato){
			$dato = (utf8_decode($dato));
			$salida .= '\t\t<option value="'.$dato.'">\r\n';
		}
		$salida .= '</datalist>';
	}
	$salida .='</div>';
	if($ayuda!="")$salida.= '<p class="help-block col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.$ayuda.'</p>';
	$salida.='</div><div class="clearfix"></div>';  
	return $salida;                
}

function campoTel($nombre, $textoUsuario, $textoUsuarioB, $valor, $icono, $requerido=true, $multiple="", $habilitadoA=TRUE, $grande=false)
{
	($multiple!=""?$nomMult="[]":$nomMult="");
	($habilitadoA?$hab="":$hab=" readonly");
	($requerido?$req=" required":$req="");
	($grande?$tam=" col-xs-12":$tam=" col-xs-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3");
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'A" class="control-label '.$tam.'">'.$textoUsuario.'</label>'.
			'<div class="input-group '.$tam.'">'.
				'<span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<input type="tel" autocomplete="off" class="form-control" value="'.$valor.'" placeholder="'.$textoUsuarioB.'" name="'.$nombre.'" id="'.$nombre.$multiple.'"'.$hab.$req."/>";
	$salida.='</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function campoTextoNumDec($nombre,$textoUsuario,$valor, $icono, $requerido=true, $ayuda="", $funcion="", $iconoB="", $activo = true)
{
	$salida='<div class="form-group" style="float:left;">'.
			'<label for="'.$nombre.'" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3">'.$textoUsuario.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
				'<span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<input type="number" step="0.01" '.$funcion.' autocomplete="off" class="form-control" value="'.$valor.'" placeholder="'.$textoUsuario.'" name="'.$nombre.'" id="'.$nombre.'"';
	if($requerido) $salida.= " required";
	if(!$activo) $salida .= " readonly";
	$salida.= '/>';
	if($iconoB != "") $salida .= '<span class="input-group-addon" aria-hidden="true">'.$iconoB.'</span>';
	$salida .='</div>';
	if($ayuda!="")$salida.= '<p class="help-block col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.$ayuda.'</p>';
	$salida.='</div><div class="clearfix"></div>';  
	return $salida;                
}

function campoTextoNumEnt($nombre,$textoUsuario,$valor, $icono, $requerido=true, $ayuda="", $funcion="", $iconoB="", $activo = true)
{
	$salida='<div class="form-group">';
	if($ayuda!=""){
	$salida.= 	'<p class="help-block col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.$ayuda.'</p>';
	}else{
	$salida.=	'<label for="'.$nombre.'" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3">'.$textoUsuario.'</label>';
	}
	$salida.=	'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
					'<span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
					'<input type="number" step="1.0"  '.$funcion.' autocomplete="off" class="form-control" value="'.$valor.'" placeholder="'.$textoUsuario.'" name="'.$nombre.'" id="'.$nombre.'"';
	if($requerido) $salida.= " required";
	if(!$activo) $salida .= " readonly";
	$salida.= '/>';
	if($iconoB != "") $salida .= '<span class="input-group-addon" aria-hidden="true">'.$iconoB.'</span>';
	$salida.= '</div>'.
			'</div><div class="clearfix"></div>';  
	return $salida;                
}

function campoTextArea($nombre,$textoUsuario,$valor, $icono="icon-pencil-square", $requerido=true, $textoTitulo="", $rows=8, $funcion="",$habilitado=true)
{
	if($textoTitulo == "") $textoTitulo=$textoUsuario;
	$salida= '<div class="form-group" style="float:left;">'.
			'<label for="'.$nombre.'" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3">'.$textoTitulo.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
				'<span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<textarea class="form-control" '.$funcion.' rows="'.$rows.'" placeholder="'.$textoUsuario.'" id="'.$nombre.'" name="'.$nombre.'"';
	if($requerido) $salida.= " required";
	if(!$habilitado) $salida.= " disabled";
	$salida.= '/>'.$valor.'</textarea>'.
			'</div>'.
		'</div><div class="clearfix"></div>';  
	return $salida;                
}

function campoFoto($nombre,$textoUsuario)
{
	$salida='<div class="form-group" style="float:left;">'.
				'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
					'<span class="input-group-addon icon-camera" aria-hidden="true"></span>'.
					'<input name="'.$nombre.'" type="file" class="form-control"/>'.
					'<input name="'.$nombre.'Action" type="hidden" value="upload"/>'.
				'</div>'.
				'<p class="help-block col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.$textoUsuario.'</p>'.
			'</div><div class="clearfix"></div>';   
	return $salida;               
}

function campoFotoSumbit($nombre,$textoUsuario, $id)
{
	$salida='<div class="form-group">'.
				'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
					'<span class="input-group-addon icon-camera" aria-hidden="true"></span>'.
					'<input name="'.$nombre.$id.'" type="file" class="form-control"/>'.
					'<span class="input-group-btn">'.
						'<button name="'.$nombre.'Action" type="submit" value="'.$id.'" class="btn btn-primary">Upload</button>'.
					'</span>'.
				'</div>'.
				'<p class="help-block col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.$textoUsuario.'</p>'.
			'</div><div class="clearfix"></div>';   
	return $salida;               
}

function campoSelectBasico($nombre,$textoUsuario,$valor, $icono, $listaArray, $requerido=true, $submitVar=false, $funcion="", $todos=false, $readOnly=false)
{
	if($valor!="" && is_numeric($valor))$valor = $valor + 0;
	($requerido?$req=" required":$req="");
	($readOnly?$read=' disabled="true"':$read="");
	(($funcion != "")?$fun=' onChange="'.$funcion.'"':$fun="");
	($submitVar?$sub=' onChange="form.submit()"':$sub="");
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3">'.$textoUsuario.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
				'<span id="icono'.$nombre.'" class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<select class="form-control" id="'.$nombre.'" name="'.$nombre.'"'.$req.$sub.$fun.$read.'>';
	$sel = " selected";
	foreach($listaArray as $indiceArray => $contenido)
	{
		if($indiceArray===$valor || $indiceArray==="A-L-L") $sel="";
	}
	$salida.= 		'<option value="" '.$sel.' disabled>'.$textoUsuario.'</option><span id="'.$nombre.'Id">';
	if($indiceArray==="A-L-L") $sel=" selected";
	if($todos===true) $salida .= '<option value="A-L-L" '.$sel.'>TODOS</option>';
	elseif($todos !== false) $salida .= '<option value="A-L-L" '.$sel.'>'.$todos.'</option>';
	foreach($listaArray as $indiceArray => $contenido)
	{
		($indiceArray===$valor?$sel=" selected":$sel="");
		$salida.=	'<option value="'.$indiceArray.'"'.$sel.'>'.$contenido.'</option>';
	}
	$salida.=	'</span></select>'.	
			'</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function campoSelectFiltro($nombre, $textoUsuario, $listaArray, $funcion="")
{
	(($funcion != "")?$fun=' onChange="'.$funcion.'"':$fun="");
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'" class="sr-only text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3">'.$textoUsuario.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
				'<span class="input-group-addon icon-search" aria-hidden="true"></span>'.
				'<select class="form-control" id="'.$nombre.'" name="'.$nombre.'"'.$fun.'>';
	$salida.= 		'<option value="all" selected>'.$textoUsuario.'</option>';
	
	foreach($listaArray as $indiceArray => $contenido)
	{
		$salida.=	'<option value="'.$indiceArray.'">'.$contenido.'</option>';
	}
	$salida.=	'</select>'.	
			'</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function campoSelectMultiple($nombre, $textoUsuario, $valor, $icono, $listaArray, $requerido=true, $multiple="", $coloreado="-1", $activo=true)
{
	($multiple!=""?$nomMult="[]":$nomMult="");
	($requerido?$req=" required":$req="");
	$salida='<div class="form-group"';
	if($multiple!="") $salida .= ' style="margin-bottom: 0px;"';
	$salida .= '>';
	if($multiple == 1 || $multiple == "") 
		$salida .= '<label for="'.$nombre.'A" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3" style="margin-top: 11px;">'.$textoUsuario.'</label>';
	$salida .=	'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3"';
	if($multiple != "")
		$salida .= ' style="margin-bottom: 0px; padding: 5px 2px; background:#D3D3D3;"';
	$salida .=	'><span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<select class="form-control '.$nombre.'Cla" id="'.$nombre.'A'.$multiple.'" name="'.$nombre.'A'.$nomMult.'"';
	if($requerido) $salida.= " required";
	if(!$activo) $salida .= ' disabled="true"';
	$salida.= '>';
	$sel = " selected";
	foreach($listaArray as $indiceArray => $contenido)
	{
		if($indiceArray==$valor) $sel="";
	}
	$salida.=		'<option value="" '.$sel.' disabled>'.$textoUsuario.'</option>';
	
	foreach($listaArray as $indiceArray => $contenido)
	{
		($indiceArray==$valor?$sel=" selected":$sel="");
		($indiceArray==$coloreado?$col=' style="background:#9FEBFF"':$col="");
		$salida.=	'<option value="'.$indiceArray.'"'.$sel.$col.'>'.$contenido.'</option>';
	}
	$salida.=	'</select>';
	if($multiple != ''){
		$salida.='<span class="input-group-btn">';
		if($multiple==1)
		{
			$salida.='<a href="javascript:void(0);" class="btn btn-success '.$nombre.'Add_button" title="Add field" role="button" aria-label="add-remove"><span class="icon-plus"></span></a>';
		}else{
			$salida.='<a href="javascript:void(0);" class="btn btn-danger '.$nombre.'Remove_button" title="Remove field" role="button" aria-label="add-remove"><span class="icon-minus"></span></a>';
		}
		$salida.='</span>';
	}
	$salida.='</div>'.
		'</div>';
	return $salida;
}

function campoTextDoble($nombre, $textoUsuario, $textoUsuarioB, $valor, $valorB, $icono, $iconoB, $requerido=true, $multiple="", $habilitadoA=TRUE, $grande=false)
{
	($multiple!=""?$nomMult="[]":$nomMult="");
	($habilitadoA?$hab="":$hab=" disabled");
	($requerido?$req=" required":$req="");
	($grande?$tam=" col-md-12":$tam=" col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3");
	
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'A" class="sr-only text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3">'.$textoUsuario.'</label>'.
			'<div class="input-group '.$tam.'">'.
				'<span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<textarea autocomplete="off" class="form-control areaDeTexto" placeholder="'.$textoUsuario.'" id="'.$nombre.'A'.$multiple.'" name="'.$nombre.'A'.$nomMult.'"'.$hab.$req.">".$valor.'</textarea>'.
				'<span class="input-group-addon '.$iconoB.'" aria-hidden="true"></span>'.
				'<textarea autocomplete="off" onClick="textAreaAdjust(this,'.$multiple.')" class="form-control" placeholder="'.$textoUsuarioB.'" id="'.$nombre.'B'.$multiple.'" name="'.$nombre.'B'.$nomMult.'"'.$req.">".$valorB.'</textarea>';
	if($multiple != ''){
		$salida.='<span class="input-group-btn">';
		if($multiple==1)
		{
			$salida.='<a href="javascript:void(0);" class="btn btn-success '.$nombre.'Add_button" title="Add field" role="button" aria-label="add-remove"><span class="icon-plus"></span></a>';
		}else{
			$salida.='<a href="javascript:void(0);" class="btn btn-danger '.$nombre.'Remove_button" title="Remove field" role="button" aria-label="add-remove"><span class="icon-minus"></span></a>';
		}
		$salida.='</span>';
	}
	$salida.='</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function campoTxtTxtNumDec($nombre, $textoUsuario, $textoUsuarioB, $valor, $valorB, $icono, $iconoB, $requerido=true, $multiple="", $habilitadoA=TRUE, $grande=false)
{
	($multiple!=""?$nomMult="[]":$nomMult="");
	($habilitadoA?$hab="":$hab=" disabled");
	($requerido?$req=" required":$req="");
	($grande?$tam=" col-md-12":$tam=" col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3");
	
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'A" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3">'.$textoUsuario.'</label>'.
			'<div class="input-group '.$tam.'">'.
				'<span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<input type="text" autocomplete="off" class="form-control" placeholder="'.$textoUsuario.'" id="'.$nombre.'A'.$multiple.'" name="'.$nombre.'A'.$nomMult.'"'.$hab.$req.' value="'.$valor.'"/>'.
				'<span class="input-group-addon '.$iconoB.'" aria-hidden="true"></span>'.
				'<input type="number" step="0.01" autocomplete="off" class="form-control" placeholder="'.$textoUsuarioB.'" id="'.$nombre.'B'.$multiple.'" name="'.$nombre.'B'.$nomMult.'"'.$req.' value="'.$valorB.'"/>';
	if($multiple != ''){
		$salida.='<span class="input-group-btn">';
		if($multiple==1)
		{
			$salida.='<a href="javascript:void(0);" class="btn btn-success '.$nombre.'Add_button" title="Add field" role="button" aria-label="add-remove"><span class="icon-plus"></span></a>';
		}else{
			$salida.='<a href="javascript:void(0);" class="btn btn-danger '.$nombre.'Remove_button" title="Remove field" role="button" aria-label="add-remove"><span class="icon-minus"></span></a>';
		}
		$salida.='</span>';
	}
	$salida.='</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function campoSelectConTel($nombre, $textoUsuario, $textoUsuarioB, $valor, $valorB, $icono, $iconoB, $listaArray, $requerido=true, $multiple="")
{
	($multiple!=""?$nomMult="[]":$nomMult="");
	($requerido?$req=" required":$req="");
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'A" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3">'.$textoUsuario.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
				'<span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<select class="form-control" name="'.$nombre.'A'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '>';
	$sel = " selected";
	foreach($listaArray as $indiceArray => $contenido)
	{
		if($indiceArray===$valor) $sel="";
	}
	$salida.=		'<option value="" '.$sel.' disabled>'.$textoUsuario.'</option>';
	
	foreach($listaArray as $indiceArray => $contenido)
	{
		($indiceArray===$valor?$sel=" selected":$sel="");
		$salida.=	'<option value="'.$indiceArray.'"'.$sel.'>'.$contenido.'</option>';
	}
	$salida.=	'</select>'.
				'<span class="input-group-addon '.$iconoB.'" aria-hidden="true"></span>'.
				'<input type="tel" autocomplete="off" class="form-control" value="'.$valorB.'" placeholder="'.$textoUsuarioB.'" name="'.$nombre.'B'.$nomMult.'" id="'.$nombre.'B'.$nomMult.'"'.$req."/>";
	if($multiple != ''){
		$salida.='<span class="input-group-btn">';
		if($multiple==1)
		{
			$salida.='<a href="javascript:void(0);" class="btn btn-success '.$nombre.'Add_button" title="Add field" role="button" aria-label="add-remove"><span class="icon-plus"></span></a>';
		}else{
			$salida.='<a href="javascript:void(0);" class="btn btn-danger '.$nombre.'Remove_button" title="Remove field" role="button" aria-label="add-remove"><span class="icon-minus"></span></a>';
		}
		$salida.='</span>';
	}
	$salida.='</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function campoTelBoton($nombre, $textoUsuario, $textoUsuarioB,$textoBoton, $valor, $icono, $funcionBoton='', $requerido=false)
{
	($requerido?$req=" required":$req="");
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'" style="margin-bottom:0px" class="control-label col-xs-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.$textoUsuario.'</label>'.
			'<div class="input-group col-xs-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
				'<span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<input type="tel" autocomplete="off" class="form-control" value="'.$valor.'" placeholder="'.$textoUsuarioB.'" name="'.$nombre.'" id="'.$nombre.'"'.$req."/>";
	$salida.='<span class="input-group-btn"><button class="btn btn-primary" type="button" onClick="'.$funcionBoton.'">'.$textoBoton.'</button></span>';
	
	$salida.='</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function campoSelectDoble($nombre, $textoUsuario, $textoUsuarioB, $valor, $valorB, $icono, $iconoB, $listaArray, $listaArrayB, $requerido=true, $multiple="",$readOnleyA=false,$readOnleyB=false)
{
	($readOnleyA?$reOa=' disabled="true"':$reOa="");
	($readOnleyB?$reOb=' disabled="true"':$reOb="");
	($multiple!=""?$nomMult="[]":$nomMult="");
	($requerido?$req=" required":$req="");
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'A" class="sr-only text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3">'.$textoUsuario.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
				'<span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<select class="form-control"'.$reOa.' name="'.$nombre.'A'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '>';
	$sel = " selected";
	foreach($listaArray as $indiceArray => $contenido)
	{
		if($indiceArray==$valor) $sel="";
	}
	$salida.=		'<option value="" '.$sel.' disabled>'.$textoUsuario.'</option>';
	
	foreach($listaArray as $indiceArray => $contenido)
	{
		($indiceArray==$valor?$sel=" selected":$sel="");
		$salida.=	'<option value="'.$indiceArray.'"'.$sel.'>'.$contenido.'</option>';
	}
	$salida.=	'</select>';
	//-----------------------------------------------------------------------------------------
	$salida.='<span class="input-group-addon '.$iconoB.'" aria-hidden="true"></span>'.
				'<select class="form-control"'.$reOb.' name="'.$nombre.'B'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '>';
	$sel = " selected";
	foreach($listaArrayB as $indiceArray => $contenido)
	{
		if($indiceArray==$valorB) $sel="";
	}
	$salida.=		'<option value="" '.$sel.' disabled>'.$textoUsuarioB.'</option>';
	
	foreach($listaArrayB as $indiceArray => $contenido)
	{
		($indiceArray==$valorB?$sel=" selected":$sel="");
		$salida.=	'<option value="'.$indiceArray.'"'.$sel.'>'.$contenido.'</option>';
	}
	$salida.=	'</select>';
	
	if($multiple != ''){
		$salida.='<span class="input-group-btn">';
		if($multiple==1)
		{
			$salida.='<a href="javascript:void(0);" class="btn btn-success '.$nombre.'Add_button" title="Add field" role="button" aria-label="add-remove"><span class="icon-plus"></span></a>';
		}else{
			$salida.='<a href="javascript:void(0);" class="btn btn-danger '.$nombre.'Remove_button" title="Remove field" role="button" aria-label="add-remove"><span class="icon-minus"></span></a>';
		}
		$salida.='</span>';
	}
	$salida.='</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function campoSelectDobleTxt($nombre, $textoUsuario, $textoUsuarioB, $textoUsuarioTxt, $valor, $valorB, $valorTxt, $icono, $iconoB, $iconoTxt, $listaArray, $listaArrayB, $requerido=true, $multiple="")
{
	($multiple!=""?$nomMult="[]":$nomMult="");
	($requerido?$req=" required":$req="");
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'A" class="sr-only text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3">'.$textoUsuario.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
				'<span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<select class="form-control" name="'.$nombre.'A'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '>';
	$sel = " selected";
	foreach($listaArray as $indiceArray => $contenido)
	{
		if($indiceArray==$valor) $sel="";
	}
	$salida.=		'<option value="" '.$sel.' disabled>'.$textoUsuario.'</option>';
	
	foreach($listaArray as $indiceArray => $contenido)
	{
		($indiceArray==$valor?$sel=" selected":$sel="");
		$salida.=	'<option value="'.$indiceArray.'"'.$sel.'>'.$contenido.'</option>';
	}
	$salida.=	'</select>';
	//-----------------------------------------------------------------------------------------
	$salida .= '<span class="input-group-addon '.$iconoTxt.'" aria-hidden="true"></span>'.
				'<div id="'.$nombre.'DivInput'.$multiple.'"><input type="text" autocomplete="off" class="form-control '.$nombre.'Class" value="'.$valorTxt.'" placeholder="'.$textoUsuarioTxt.'" id="'.$nombre.'C'.$multiple.'" name="'.$nombre.'B'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '/></div>';
	//------------------------------------------------------------------------------------------
	$salida.='<span class="input-group-addon '.$iconoB.'" aria-hidden="true"></span>'.
				'<select class="form-control" name="'.$nombre.'B'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '>';
	$sel = " selected";
	foreach($listaArrayB as $indiceArray => $contenido)
	{
		if($indiceArray==$valorB) $sel="";
	}
	$salida.=		'<option value="" '.$sel.' disabled>'.$textoUsuarioB.'</option>';
	
	foreach($listaArrayB as $indiceArray => $contenido)
	{
		($indiceArray==$valor?$sel=" selected":$sel="");
		$salida.=	'<option value="'.$indiceArray.'"'.$sel.'>'.$contenido.'</option>';
	}
	$salida.=	'</select>';
	
	if($multiple != ''){
		$salida.='<span class="input-group-btn">';
		if($multiple==1)
		{
			$salida.='<a href="javascript:void(0);" class="btn btn-success '.$nombre.'Add_button" title="Add field" role="button" aria-label="add-remove"><span class="icon-plus"></span></a>';
		}else{
			$salida.='<a href="javascript:void(0);" class="btn btn-danger '.$nombre.'Remove_button" title="Remove field" role="button" aria-label="add-remove"><span class="icon-minus"></span></a>';
		}
		$salida.='</span>';
	}
	$salida.='</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function campoSelectConNumEnt($nombre, $textoUsuario, $textoUsuarioB, $valor, $valorB, $icono, $iconoB, $listaArray, $requerido=true, $multiple="")
{
	if($multiple === '')
	{
		$nomMult="";
	}else{
		$nomMult="[]";
	}
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'A'.$nomMult.'" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3">'.$textoUsuario.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
				'<span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<select class="form-control" name="'.$nombre.'A'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '>';
	$sel = " selected";
	foreach($listaArray as $indiceArray => $contenido)
	{
		if($indiceArray==$valor) $sel="";
	}
	$salida.=		'<option value="" '.$sel.' disabled>'.$textoUsuario.'</option>';
	
	foreach($listaArray as $indiceArray => $contenido)
	{
		($indiceArray==$valor?$sel=" selected":$sel="");
		$salida.=	'<option value="'.$indiceArray.'"'.$sel.'>'.$contenido.'</option>';
	}
	$salida.=	'</select>'.
				'<span class="input-group-addon '.$iconoB.'" aria-hidden="true"></span>'.
				'<input type="number" class="form-control" autocomplete="off" value="'.$valorB.'" placeholder="'.$textoUsuarioB.'" name="'.$nombre.'B'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '/>';
	if($multiple != ''){
		$salida.='<span class="input-group-btn">';
		if($multiple==1)
		{
			$salida.='<a href="javascript:void(0);" class="btn btn-success '.$nombre.'Add_button" title="Add field" role="button" aria-label="add-remove"><span class="icon-plus"></span></a>';
		}else{
			$salida.='<a href="javascript:void(0);" class="btn btn-danger '.$nombre.'Remove_button" title="Remove field" role="button" aria-label="add-remove"><span class="icon-minus"></span></a>';
		}
		$salida.='</span>';
	}
	$salida.='</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function campoSelectConNumDec($nombre, $textoUsuario, $textoUsuarioB, $valor, $valorB, $icono, $iconoB, $listaArray, $requerido=true, $multiple="")
{
	if($multiple != '')
	{
		$nomMult="[]";
	}else{
		$nomMult="[]";
	}
	$salida='<div class="form-group">'.
			'<label for="'.$nombre.'A" class="sr-only text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3">'.$textoUsuario.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
				'<span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<select class="form-control" name="'.$nombre.'A'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '>';
	$sel = " selected";
	foreach($listaArray as $indiceArray => $contenido)
	{
		if($indiceArray==$valor) $sel="";
	}
	$salida.=		'<option value="" '.$sel.' disabled>'.$textoUsuario.'</option>';
	
	foreach($listaArray as $indiceArray => $contenido)
	{
		($indiceArray==$valor?$sel=" selected":$sel="");
		$salida.=	'<option value="'.$indiceArray.'"'.$sel.'>'.$contenido.'</option>';
	}
	$salida.=	'</select>'.
				'<span class="input-group-addon '.$iconoB.'" aria-hidden="true"></span>'.
				'<input type="number" step="0.01" class="form-control" autocomplete="off" value="'.$valorB.'" placeholder="'.$textoUsuarioB.'" name="'.$nombre.'B'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '/>';
	if($multiple != ''){
		$salida.='<span class="input-group-btn">';
		if($multiple==1)
		{
			$salida.='<a href="javascript:void(0);" class="btn btn-success '.$nombre.'Add_button" title="Add field" role="button" aria-label="add-remove"><span class="icon-plus"></span></a>';
		}else{
			$salida.='<a href="javascript:void(0);" class="btn btn-danger '.$nombre.'Remove_button" title="Remove field" role="button" aria-label="add-remove"><span class="icon-minus"></span></a>';
		}
		$salida.='</span>';
	}
	$salida.='</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function campoSelectConText($nombre, $textoUsuario, $textoUsuarioB, $valor, $valorB, $icono, $iconoB, $listaArray, $requerido=true, $multiple="")
{
	if($multiple != '')
	{
		$nomMult="[]";
	}else{
		$nomMult="[]";
	}
	
	$salida='<div class="form-group"';
	if($multiple!="") $salida .= ' style="margin-bottom: 0px;"';
	$salida .= '>';
	if($multiple == 1 || $multiple == "") 
		$salida .= '<label for="'.$nombre.'A" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3" style="margin-top: 11px;">'.$textoUsuario.'</label>';
	$salida .=	'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3"';
	if($multiple != "")
		$salida .= ' style="margin-bottom: 0px; padding: 5px 2px; background:#D3D3D3;"';
	$salida .= '><span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<select class="form-control" name="'.$nombre.'A'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '>';
	$sel = " selected";
	foreach($listaArray as $indiceArray => $contenido)
	{
		if($indiceArray==$valor) $sel="";
	}
	$salida.=		'<option value="" '.$sel.' disabled>'.$textoUsuario.'</option>';
	
	foreach($listaArray as $indiceArray => $contenido)
	{
		($indiceArray==$valor?$sel=" selected":$sel="");
		$salida.=	'<option value="'.$indiceArray.'"'.$sel.'>'.$contenido.'</option>';
	}
	$salida.=	'</select>'.
				'<span class="input-group-addon '.$iconoB.'" aria-hidden="true"></span>'.
				'<input type="text" class="form-control" autocomplete="off" value="'.$valorB.'" placeholder="'.$textoUsuarioB.'" name="'.$nombre.'B'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '/>';
	if($multiple != ''){
		$salida.='<span class="input-group-btn">';
		if($multiple==1)
		{
			$salida.='<a href="javascript:void(0);" class="btn btn-success '.$nombre.'Add_button" title="Add field" role="button" aria-label="add-remove"><span class="icon-plus"></span></a>';
		}else{
			$salida.='<a href="javascript:void(0);" class="btn btn-danger '.$nombre.'Remove_button" title="Remove field" role="button" aria-label="add-remove"><span class="icon-minus"></span></a>';
		}
		$salida.='</span>';
	}
	$salida.='</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function campoSelectConTextConCheckBox($nombre, $textoUsuario, $textoUsuarioA, $textoUsuarioB, $valor, $valorB, $valorC, $icono, $listaArray, $requerido=true, $multiple="")
{
	if($multiple != '')
	{
		$nomMult="[]";
	}else{
		$nomMult="[]";
	}
	if($valorC == 1) $checado = " checked";
	else $checado = "";
	
	$salida='<div class="form-group"';
	if($multiple!="") $salida .= ' style="margin-bottom: 0px;"';
	$salida .= '>';
	if($multiple == 1 || $multiple == "") 
		$salida .= '<label for="'.$nombre.'A" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3" style="margin-top: 11px;">'.$textoUsuario.'</label>';
	$salida .=	'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3"';
	if($multiple != "")
		$salida .= ' style="margin-bottom: 0px; padding: 5px 2px; background:#D3D3D3;"';
	$salida .= '><span class="input-group-addon '.$icono.'" aria-hidden="true"></span>'.
				'<select class="form-control" name="'.$nombre.'A'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '>';
	$sel = " selected";
	foreach($listaArray as $indiceArray => $contenido)
	{
		if($indiceArray==$valor) $sel="";
	}
	$salida.=		'<option value="" '.$sel.' disabled>'.$textoUsuarioA.'</option>';
	
	foreach($listaArray as $indiceArray => $contenido)
	{
		($indiceArray==$valor?$sel=" selected":$sel="");
		$salida.=	'<option value="'.$indiceArray.'"'.$sel.'>'.$contenido.'</option>';
	}
	$salida.=	'</select>'.
				'<span class="input-group-addon" aria-hidden="true"><input type="checkbox" value="1" id="'.$nombre.'C" name="'.$nombre.'C'.$nomMult.'"'. $checado.'/></span>'.
				'<input type="text" class="form-control" autocomplete="off" value="'.$valorB.'" placeholder="'.$textoUsuarioB.'" name="'.$nombre.'B'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '/>';
	if($multiple != ''){
		$salida.='<span class="input-group-btn">';
		if($multiple==1)
		{
			$salida.='<a href="javascript:void(0);" class="btn btn-success '.$nombre.'Add_button" title="Add field" role="button" aria-label="add-remove"><span class="icon-plus"></span></a>';
		}else{
			$salida.='<a href="javascript:void(0);" class="btn btn-danger '.$nombre.'Remove_button" title="Remove field" role="button" aria-label="add-remove"><span class="icon-minus"></span></a>';
		}
		$salida.='</span>';
	}
	$salida.='</div>'.
		'</div>';
	return $salida;
}

function campoSelectTxtCheckTxt($nombre, $textoUsuarioGeneral, $textoUsuarioA, $textoUsuarioB, $textoUsuarioC, $valorLista, $valorCheckA, $valorCheckB, $valorB, $valorC, $listaArray, $requerido=true, $multiple="", $funcionCheck="")
{
	if($multiple != '')
	{
		$nomMult="[]";
	}else{
		$nomMult="[]";
	}
	if($valorCheckA == 1) $checadoA = " checked";
	else $checado = "";
	if($valorCheckB == 1) $checadoB = " checked";
	else $checado = "";
	$salida='<div class="form-group"';
	if($multiple!="") $salida .= ' style="margin-bottom: 0px;"';
	$salida .= '>';
	if($multiple == 1 || $multiple == "") 
		$salida .= '<label for="'.$nombre.'Lista" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3" style="margin-top: 11px;">'.$textoUsuarioGeneral.'</label>';
	$salida .=	'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3" style="margin-bottom: 0px; padding: 5px 2px 0px; background:#D3D3D3;">';
	$salida.='<span class="input-group-addon" aria-hidden="true"><input type="checkbox" value = "1" id="'.$nombre.'CheckA'.$multiple.'" name="'.$nombre.'CheckA'.$nomMult.'"'. $checadoA.'/></span>'.
				'<select class="form-control" name="'.$nombre.'Lista'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '>';
	$sel = " selected";
	foreach($listaArray as $indiceArray => $contenido)
	{
		if($indiceArray==$valorLista) $sel="";
	}
	$salida.=		'<option value="" '.$sel.' disabled>'.$textoUsuarioA.'</option>';
	
	foreach($listaArray as $indiceArray => $contenido)
	{
		($indiceArray==$valorLista?$sel=" selected":$sel="");
		$salida.=	'<option value="'.$indiceArray.'"'.$sel.'>'.$contenido.'</option>';
	}
	$salida.=	'</select>'.
				'<span class="input-group-addon" aria-hidden="true"><input type="checkbox" value = "1" id="'.$nombre.'CheckB'.$multiple.'" '.$funcionCheck.' name="'.$nombre.'CheckB'.$nomMult.'"'. $checadoB.'/></span>'.
				'<div id="'.$nombre.'DivInput'.$multiple.'"><input type="text" autocomplete="off" class="form-control '.$nombre.'ClassB" value="'.$valorB.'" placeholder="'.$textoUsuarioB.'" id="'.$nombre.'Input'.$multiple.'" name="'.$nombre.'B'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '/></div>';
	if($multiple != ''){
		$salida.='<span class="input-group-btn">';
		if($multiple==1)
		{
			$salida.='<a href="javascript:void(0);" class="btn btn-success '.$nombre.'Add_button" title="Add field" role="button" aria-label="add-remove"><span class="icon-plus"></span></a>';
		}else{
			$salida.='<a href="javascript:void(0);" class="btn btn-danger '.$nombre.'Remove_button" title="Remove field" role="button" aria-label="add-remove"><span class="icon-minus"></span></a>';
		}
		$salida.='</span>';
	}
	$salida.='</div>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3" style="margin-bottom: 0px; padding: 0px 2px 5px; background:#D3D3D3;">';
	$salida .= '<input type="text" autocomplete="off" class="form-control" value="'.$valorC.'" placeholder="'.$textoUsuarioC.'" name="'.$nombre.'C'.$nomMult.'"';
	if($requerido){ $salida.= " required";}
	$salida.= '/>'.
				'</div>'.
			'</div>';
	return $salida;
}

function campoSelectTxtTxt($nombre, $textoUsuarioGeneral, $textoUsuarioA, $textoUsuarioB, $textoUsuarioC, $valorLista, $iconoA, $iconoB, $valorB, $valorC, $listaArray, $requerido=true, $multiple="")
{
	if($multiple != '')
	{
		$nomMult="[]";
	}else{
		$nomMult="[]";
	}
	if($valorCheckA == 1) $checadoA = " checked";
	else $checado = "";
	if($valorCheckB == 1) $checadoB = " checked";
	else $checado = "";
	$salida='<div class="form-group"';
	if($multiple!="") $salida .= ' style="margin-bottom: 0px;"';
	$salida .= '>';
	if($multiple == 1 || $multiple == "") 
		$salida .= '<label for="'.$nombre.'Lista" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3" style="margin-top: 11px;">'.$textoUsuarioGeneral.'</label>';
	$salida .=	'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3" style="margin-bottom: 0px; padding: 5px 2px 0px; background:#D3D3D3;">';
	$salida.='<span class="input-group-addon '.$iconoA.'" aria-hidden="true"></span>'.
				'<select class="form-control" name="'.$nombre.'Lista'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '>';
	$sel = " selected";
	foreach($listaArray as $indiceArray => $contenido)
	{
		if($indiceArray==$valorLista) $sel="";
	}
	$salida.=		'<option value="" '.$sel.' disabled>'.$textoUsuarioA.'</option>';
	
	foreach($listaArray as $indiceArray => $contenido)
	{
		($indiceArray==$valorLista?$sel=" selected":$sel="");
		$salida.=	'<option value="'.$indiceArray.'"'.$sel.'>'.$contenido.'</option>';
	}
	$salida.=	'</select>'.
				'<span class="input-group-addon '.$iconoB.'" aria-hidden="true"></span>'.
				'<div id="'.$nombre.'DivInput'.$multiple.'"><input type="text" autocomplete="off" class="form-control '.$nombre.'ClassB" value="'.$valorB.'" placeholder="'.$textoUsuarioB.'" id="'.$nombre.'Input'.$multiple.'" name="'.$nombre.'B'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '/></div>';
	if($multiple != ''){
		$salida.='<span class="input-group-btn">';
		if($multiple==1)
		{
			$salida.='<a href="javascript:void(0);" class="btn btn-success '.$nombre.'Add_button" title="Add field" role="button" aria-label="add-remove"><span class="icon-plus"></span></a>';
		}else{
			$salida.='<a href="javascript:void(0);" class="btn btn-danger '.$nombre.'Remove_button" title="Remove field" role="button" aria-label="add-remove"><span class="icon-minus"></span></a>';
		}
		$salida.='</span>';
	}
	$salida.='</div>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3" style="margin-bottom: 0px; padding: 0px 2px 5px; background:#D3D3D3;">';
	$salida .= '<input type="text" autocomplete="off" class="form-control" value="'.$valorC.'" placeholder="'.$textoUsuarioC.'" name="'.$nombre.'C'.$nomMult.'"';
	$salida.= '/>'.
				'</div>'.
			'</div>';
	return $salida;
}

function campoSelectNumDecTxt($nombre, $textoUsuarioGeneral, $textoUsuarioA, $textoUsuarioB, $textoUsuarioC, $valorLista, $iconoA, $iconoB, $valorB, $valorC, $listaArray, $requerido=true, $multiple="")
{
	if($multiple != '')
	{
		$nomMult="[]";
	}else{
		$nomMult="[]";
	}
	if($valorCheckA == 1) $checadoA = " checked";
	else $checado = "";
	if($valorCheckB == 1) $checadoB = " checked";
	else $checado = "";
	$salida='<div class="form-group"';
	if($multiple!="") $salida .= ' style="margin-bottom: 0px;"';
	$salida .= '>';
	if($multiple == 1 || $multiple == "") 
		$salida .= '<label for="'.$nombre.'Lista" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3" style="margin-top: 11px;">'.$textoUsuarioGeneral.'</label>';
	$salida .=	'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3" style="margin-bottom: 0px; padding: 5px 2px 0px; background:#D3D3D3;">';
	$salida.='<span class="input-group-addon '.$iconoA.'" aria-hidden="true"></span>'.
				'<select class="form-control" name="'.$nombre.'Lista'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '>';
	$sel = " selected";
	foreach($listaArray as $indiceArray => $contenido)
	{
		if($indiceArray==$valorLista) $sel="";
	}
	$salida.=		'<option value="" '.$sel.' disabled>'.$textoUsuarioA.'</option>';
	
	foreach($listaArray as $indiceArray => $contenido)
	{
		($indiceArray==$valorLista?$sel=" selected":$sel="");
		$salida.=	'<option value="'.$indiceArray.'"'.$sel.'>'.$contenido.'</option>';
	}
	$salida.=	'</select>'.
				'<span class="input-group-addon '.$iconoB.'" aria-hidden="true"></span>'.
				'<div id="'.$nombre.'DivInput'.$multiple.'"><input type="number" step="0.01" autocomplete="off" class="form-control '.$nombre.'ClassB" value="'.$valorB.'" placeholder="'.$textoUsuarioB.'" id="'.$nombre.'Input'.$multiple.'" name="'.$nombre.'B'.$nomMult.'"';
	if($requerido) $salida.= " required";
	$salida.= '/></div>';
	if($multiple != ''){
		$salida.='<span class="input-group-btn">';
		if($multiple==1)
		{
			$salida.='<a href="javascript:void(0);" class="btn btn-success '.$nombre.'Add_button" title="Add field" role="button" aria-label="add-remove"><span class="icon-plus"></span></a>';
		}else{
			$salida.='<a href="javascript:void(0);" class="btn btn-danger '.$nombre.'Remove_button" title="Remove field" role="button" aria-label="add-remove"><span class="icon-minus"></span></a>';
		}
		$salida.='</span>';
	}
	$salida.='</div>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3" style="margin-bottom: 0px; padding: 0px 2px 5px; background:#D3D3D3;">';
	$salida .= '<input type="text" autocomplete="off" class="form-control" value="'.$valorC.'" placeholder="'.$textoUsuarioC.'" name="'.$nombre.'C'.$nomMult.'"';
	$salida.= '/>'.
				'</div>'.
			'</div>';
	return $salida;
}
                                   
function seleccionHoras($nombre, $textoUsuarioDesc, $valor, $valorB, $requerido=true)
{
	$salida='<div class="form-group" style="margin-bottom: 0px;">'.
			'<label for="'.$nombre.'H" style="margin-bottom: 0px;" class="text-right control-label col-xs-12 col-sm-12 col-md-2 col-lg-3">'.$textoUsuarioDesc.'</label>'.
			'<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">'.
				'<span class="input-group-addon icon-clock2" aria-hidden="true"></span>'.
				'<select class="form-control" id="'.$nombre.'H" name="'.$nombre.'H"';
	if($requerido) $salida.= " required";
	$salida.= '>'.
	$sel = " selected ";
	for($i = 0; $i <= 24; $i++)
	{ 
		$i<10?$dato="0".$i:$dato="".$i;
		if($dato===$valor)$sel="";
	}
	$salida.=		'<option value="" '.$sel.'>Horas</option>';
	
	for($i = 0; $i <= 24; $i++)
	{
		$i<10?$dato="0".$i:$dato="".$i;
		$dato===$valor?$sel=" selected":$sel="";
		$salida.=	'<option value="'.$dato.'"'.$sel.'>'.$dato.'</option>';
	}
	$salida.=	'</select>'.
				'<span class="input-group-addon icon-clock" aria-hidden="true"></span>'.
				'<select class="form-control" id="'.$nombre.'M" name="'.$nombre.'M"';
	if($requerido) $salida.=" required";
	$salida.='>'.
	$sel = " selected ";
	for($i = 0; $i < 60; $i=$i+10)
	{ 
		$i<10?$dato="0".$i:$dato="".$i;
		if($dato===$valorB)$sel="";
	}
	$salida.=		'<option value="" '.$sel.' >Minutos</option>';
	
	for($i = 0; $i < 60; $i=$i+10)
	{ 
		$i<10?$dato="0".$i:$dato="".$i;
		$dato===$valorB?$sel=" selected":$sel="";
		$salida.=	'<option value="'.$dato.'"'.$sel.'>'.$dato.'</option>';
	}
	
	$salida.=	'</select>'.
			'</div>'.
		'</div><div class="clearfix"></div>';
	return $salida;
}

function SetDeHorarios($nombre, $horaTxt, $minTxt, $dia, $datoHa, $datoMa, $datoHc, $datoMc, $multiple="")
{	
	$salida = '<div class="form-group">';
	if($multiple==1)$salida .= '<label for="abreH1" class="control-label text-center col-xs-12">'.$dia.'</label>';	
	$salida .= '<div class="input-group col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">';
	
	$salida .= parDeHorarios($horaTxt, $minTxt, $nombre, $datoHa, $datoMa, "abre");
	$salida .= parDeHorarios($horaTxt, $minTxt, $nombre, $datoHc, $datoMc, "cierra");
	if($multiple != ''){
		$salida.='<span class="input-group-btn">';
		if($multiple==1)
		{
			$salida.='<a href="javascript:void(0);" class="btn btn-success '.$nombre.'Add_button" title="Add field" role="button" aria-label="add-remove" style="padding: 22px 8px;"><span class="icon-plus"></span></a>';
		}else{
			$salida.='<a href="javascript:void(0);" class="btn btn-danger '.$nombre.'Remove_button" title="Remove field" role="button" aria-label="add-remove" style="padding: 22px 8px;"><span class="icon-minus"></span></a>';
		}
		$salida.='</span>';
	}
	$salida .= '</div>';
	$salida .= '</div><div class="clearfix"></div>';
	return $salida;
}

function parDeHorarios($horaTxt, $minTxt, $dia, $datoHora, $datoMin, $abreCierra)
{
    $salida = '<div class="input-group">'.
        '<span class="input-group-addon icon-clock2" aria-hidden="true"></span>'.
        '<select class="form-control" name="'.$abreCierra.'H'.$dia.'[]">'.
            '<option value="">'.$horaTxt.'</option>';
	for($i = 0; $i <= 24; $i++)
	{ 
		$sel = '';
		if($datoHora !== "" && $datoHora !== NULL) $datoHora=$datoHora+0;
		if($i === $datoHora)$sel = " selected";
		$salida .='<option value="'.$i.'" '.$sel.'>'; 
		if($i<10) $salida.= "0" . $i; 
		else $salida.= $i;
		$salida.='</option>';
	
	} 
	$salida.='</select>'.
        '<span class="input-group-addon icon-clock" aria-hidden="true"></span>'.
        '<select class="form-control" name="'.$abreCierra.'M'.$dia.'[]">'.
            '<option value="">'.$minTxt.'</option>';
	for($i = 0; $i < 60; $i=$i+10)
	{ 
		$sel = '';
		if($datoMin !== "" && $datoMin !== NULL) $datoMin=$datoMin+0;
		if($i === $datoMin)$sel = " selected";
		$salida.='<option value="'.$i.'" '.$sel.' >';
		if($i<10) $salida.= "0" . $i; 
		else $salida.= $i;
		$salida.='</option>';
	}
	$salida.='</select>'.
    '</div><div class="clearfix"></div>';
	return $salida;
}

function panelHeadSimple($nombre,$titulo)
{
	$salida='<div class="panel-heading" role="tab" id="heading'.$nombre.'">'.
				'<h3 class="panel-title">'.
					'<a class="btn-block collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse'.$nombre.'" aria-expanded="false" aria-controls="collapse'.$nombre.'">'.
						'<span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> '.$titulo.
					'</a>'.
    			'</h3>'.
			'</div>';
	return $salida;
}

function panelHeadBoton($nombre,$titulo, $texto)
{
	$salida='<div class="panel-heading panelMenu" role="tab" id="heading'.$nombre.'">'.
				'<div class="container-fluid">'.
					'<div class="col-sm-7 col-md-8">'.
						'<div class="conBoton">'.
							'<p class="panel-title navbar-brand">'.$titulo.'</p>'.
						'</div>'.
					'</div>'.
					'<div class="col-sm-5 col-md-4">'.
						'<a class="btn btn-info btn-block" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse'.$nombre.'" aria-expanded="false" aria-controls="collapse'.$nombre.'">'.
							$texto.' <span class="glyphicon icon-move-down" style=" font-size:28px;" aria-hidden="true"></span>'.
						'</a>'.
					'</div>'.
				'</div>'.
			'</div>';
	return $salida;
}

function panelPlanes($nombre, $titulo, $descripcion, $detalle, $txtA, $txtB, $txtC, $datoA='', $datoB='')
{
	$sal=	'<div id="collapse'.$nombre.'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading'.$nombre.'">'.
				'<div class="panel-body">'.
					'<h3>'.$titulo.'</h3>'.
					'<p>'.$descripcion.'</p>'.
					'<hr /><br />'.
					'<p>'.$detalle.'</p>'.
					'<div class="form-group">'.
						'<label for="'.$nombre.'A" class="control-label sr-only col-xs-12 col-sm-12 col-md-3 col-lg-4">'.$txtA.'</label>'.
						'<div class="input-group col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">'.
							'<span class="input-group-addon icon-whatsapp" aria-hidden="true"></span>'.
							'<input type="tel" class="form-control" value="'.$datoA.'" placeholder="'.$txtA.'" id="'.$nombre.'A" name="'.$nombre.'A">'.
						'</div>'.
					'</div>'.
					'<div class="form-group">'.
						'<label for="'.$nombre.'B" class="control-label sr-only col-xs-12 col-sm-12 col-md-3 col-lg-4">'.$txtB.'</label>'.
						'<div class="input-group col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">'.
							'<span class="input-group-addon icon-file-text2" aria-hidden="true"></span>'.
							'<textarea class="form-control" rows="3" placeholder="'.$txtB.'" id="'.$nombre.'B" name="'.$nombre.'B">'.$datoB.'</textarea>'.
						'</div>'.
					'</div>'.
					'<div class="clearfix"></div>'.
					'<div class="form-group">'.
						'<label for="enviar" class="sr-only control-label col-xs-12 col-sm-12 col-md-3 col-lg-4">'.$txtC.'</label>'.
						'<div class="input-group col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">'.
							'<button class="btn btn-primary btn-block btn-lg" type="submit" name="enviar" value="'.$nombre.'">'.$txtC.'</button>'.
						'</div>'.
					'</div>'.
				'</div>'.
			'</div>';
	return $sal;
}



?>