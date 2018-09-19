<?php
/**
 * Responsable de Modificar imagenes en el servidor.
 * @author	Marcelo Castro 
 * @author 	objetivophp
 * @version 4.0.0 15/03/2009.-
 * 			Incoporar ver propiedades 							
 * 			Chequeo de seguriadad en imagen.								
 * 			Soporte para png, gif y jpg						
 * @since 	Soporte para PHP 5 exclusivamente.-
 * @since 	Agragar soporte para transferencias y conversion entre formatos.
 */ 
class Imagen
{	/**
 	* Ubicacion de la imagen de origen. (Se puede utilizar con $_FILES[])
 	* @var		String
 	* @access 	private
 	*/
	private $imagenOrigen;

	/**
	 * Ubicacion para la imagen una vez editada, es decir donde quedara la nueva imagen.
	 * @var 	String
	 * @access 	Private
	 */
	private $imagenDestino;
	
	/**
	 * Ancho en pixeles para la imagen destino.
	 * @var		Integer
	 * @access 	Private
	 */
	private $anchoDestino;
	
	/**
	 * Alto en pixeles para la imagen destino.
	 * @var 	Integer
	 * @access	Private
	 */
	private $altoDestino;
	
	/**
	 * Calcula el ancho en pixeles de la imagen de Origen.
	 * @var 	Integer
	 * @access 	Private
	 */
	private $anchoOrigen;
	
	/**
	 * Calcula el alto en pixeles de la imagen de Origen.
	 * @var		Integer
	 * @access 	Private
	 */
	private $altoOrigen;
	
	/**
	 * Especifica como respetara o no la proporcionalidad de la imagen. (Forma de Edicion).-
	 * @var 	Integer
	 * @access 	Private
	 * @since	Modos disponibles
	 * 	#0 para que respete la proporcionalidad de la imagen y tome como base el ancho
	 * 	#1 para que respete la proporcionalidad de la imagen y tome como base el alto
	 *  #2 para que respete el ancho y el alto recortando el resto de la imagen
	 *  #3 para respetar el ancho y alto indicando pero deformando la imagen
	 *  #El balor por defecto sera 0.
	 */
	private $modo				= 0;
	
	/**
	 * Se activa con el modo 2.
	 * La opcion cuadricula establece de cuanto sera la cuadricula para realizar el recorte y la propiedad
	 * centrado en que cuadrante se centrara para realizar el recorte.	
	 * @var		array
 	 * @since  Ejemplo en una cuadricula de 3x4
 	 * 	Fila 1.[00][01][02][03]
	 *  Fila 2.[04][05][06][07]
	 *  Fila 3.[08][09][10][11]
	 */
	private $recorte			= array('filas'	=> 3, 'columnas'	=> 3, 'centrado'	=>	4);
	
	/**
	 * Guarda si la imagen se realizo satisfactoriamento o no en el servidor.
	 * @var 	Boolean
	 * @access 	private
	 */
	private $resultado			= "true";
	
	/**
	 * Guarda los mensajes de errores que se pudieron ocasionar.
	 * @var 	String
	 * @access 	private
	 */
	private $mensaje;
	
	/**
	 * Configura si se tiene que borrar o no la imagen de origen.
	 * @var		boolean
	 * @access 	private
	 */
	private $borrarOrigen		= false;
	
	/**
	 * Conserva los datos iniciales.
	 * @var 	object
	 * @access 	private
	 */
	private	$jsonArgumentos;
	
	/**
	 * Guarda las propiedades de la imagen extraidas mediante getimagesize.
	 * @access 	private
	 * @var		array
	 */
	private $propiedadesImagen;
	
	/**
	 * Calidad de la imagen destino.
	 * @access 	private
	 * @var		integer
	 */
	private	$calidadImagen		= 95;								
									
	/**
	 * Si la imagen es segura crea un puntero de imagen para utilizarlo para realizar las modificaciones.
	 * Es una imagen en Blanco.
	 * @access 	private
	 * @var 	object
	 */
	private $punteroImagen;

	/**
	 * Mantiene la extencion de la imagen.
	 * @access 	private
	 * @var 	string
	 */
	private $extencion;
	
	#########################################################################	
	# VARIABLES DE CONTROL													#
	#########################################################################
	/**
	 * Contiene las variables que no se podran setear por medio del metodo magico __set().
	 * @access 	private
	 * @var		array
	 */
	private $noPermitirSetVar	= array('anchoOrigen','altoOrigen','punteroImagen','extencion','resultado','mensaje','jsonArgumentos','propiedadesImagen','noPermitirSetVar','noPermitirGetVar','imageCreateFrom','msjError','tiposImagenes');
	
	/**
	 * Contiene las variables que no se podran ver por medio de el metodo magico __get().
	 * @access 	private
	 * @var 	array
	 */
	private $noPermitirGetVar	= array('punteroImagen','jsonArgumentos','propiedadesImagen','noPermitirSetVar','noPermitirGetVar','imageCreateFrom','tiposImagenes');
	
	/**
	 * Tiene los tipos de archivos de imagen permitidos y la funcion que crea la imagen editada.
	 * @access 	private
	 * @var 	array
	 */
	private $imageCreateFrom	= array(
									"JPG"	=> "jpeg", 
									"GIF"	=> "gif",
									"PNG"	=> "png"
									);
	
	/**
	 * Contiene los tipos de errores que se pueden ocasionar.
	 * @access 	private
	 * @var		array
	 */
	private static $msjError	= array(
									"ERR_File"		=> "No existe el archivo a modificar",
									"ERR_Param"		=> "No se pudieron resolver todos los parametros.",
									"ERR_Params"	=> "Ningun parametro pudo ser resuelto.<br />Verifique que sea una imagen.",
									"ERR_Exten"		=> "El tipo de archivo y su contenido no coinciden",
									"ERR_Taman"		=> "El archivo no contiene informacion que procesar",
									"ERR_ImgNP"		=> "Tipo de imagen no permitida.",
									"ERR_ImgNC"		=> "No se puede crear una imagen tomando como origen su imagen",
									"ERR_ImgNS"		=> "La imagen no a sido verificada como segura, por favor primero utilize el metodo esImagenSegura()",
									"ERR_FIOri"		=> "No se ha configurado cual sera la imagen de origen",
									"ERR_FIDes"		=> "No se ha configurado cual sera el destino de la imagen",
									"ERR_Modo2"		=> "No se encontraron los parametros para el modo 2."
									);
	
	
	private $tiposImagenes		= array(
        							1 	=> 'GIF',
        							2	=> 'JPG',
        							3 	=> 'PNG',
        							4 	=> 'SWF',
        							5 	=> 'PSD',
        							6 	=> 'BMP',
        							7 	=> 'TIFF_II',
        							8	=> 'TIFF_MM',
        							9 	=> 'JPC',
        							10 	=> 'JP2',
        							11 	=> 'JPX',
        							12 	=> 'JB2',
        							13 	=> 'SWC',
        							14 	=> 'IFF',
        							15 	=> 'WBMP',
        							16 	=> 'XBM',
    								17	=> 'ICO');
	#########################################################################	
	# METODOS PUBLICOS														#
	#########################################################################
    /**
	 * Metodo constructor.
	 * Se le pueden pasar los argumentos que deberian venir en formato JSON o
	 * tambien se pueden definir despues con los metodos SET correspondientes.
	 * FORMATO DE DATOS:
	 * '{ 	"imgOrigen"	: "direccion de origen",
 	 * 		"imgDestino": "direccion de destino",
 	 * 		"ancho"		: "ancho destino en piixeles",
 	 *		"alto"		: "alto destino",
 	 * 		"modo"		: 0,
 	 * 		"filas"		: 0,
 	 * 		"columnas"	: 0,
 	 * 		"centrado"	: 0,
 	 * 		"borrar"	: false,
 	 * 		"calidad"	: 95
 	 * }'
	 * Se pueden poner los parametros que se dese y complementarlos despues. 
	 *
	 * @param	String	$argumentos	cadena tipo JSON
	 */
	public function __construct($argumentos='')
	{	// No quiero que me largue errores si suceden.							
		//error_reporting(0);
		//set_time_limit(0);
	
		$this->jsonArgumentos	= json_decode(stripslashes($argumentos));
		if(is_object($this->jsonArgumentos))
		{	// Transfiero los datos JSON a las variables Correspondientes
			// Propiedades no configuradas por defecto
			$this->imagenOrigen			= $this->jsonArgumentos->imgOrigen;
			$this->imagenDestino		= $this->jsonArgumentos->imgDestino;
			$this->anchoDestino			= (int)($this->jsonArgumentos->ancho);
			$this->altoDestino			= (int)($this->jsonArgumentos->alto);
			$this->modo					= (int)($this->jsonArgumentos->modo);
			// Propiedades configuradas por defecto
			empty($this->jsonArgumentos->calidad)	?false:$this->calidadImagen			= (int)($this->jsonArgumentos->calidad);
			empty($this->jsonArgumentos->filas)		?false:$this->recorte["filas"]		= (int)($this->jsonArgumentos->filas);	
			empty($this->jsonArgumentos->columnas)	?false:$this->recorte["columnas"]	= (int)($this->jsonArgumentos->columnas);
			empty($this->jsonArgumentos->centrado)	?false:$this->recorte["centrado"]	= (int)($this->jsonArgumentos->centrado);
			empty($this->jsonArgumentos->borrar)	?false:$this->borrarOrigen			= $this->jsonArgumentos->borrar;
		}
	}
	
	public function cargarImagen($imagen)
	{	
		getcwd();
		try
		{	
			$propiedadImagen	= getimagesize($imagen); // $propiedadImagen	= getimagesize($imagen, $info);
			
			if(!$propiedadImagen)	
			{	throw new Exception(Imagen::$msjError["ERR_File"]);	}
			
			// Si Existe el Archivo Largo el codigo de Verificacion
			$extencion			= $this->extraerExtencion($imagen);
			$tamanio			= filesize($imagen);
			
			// Veerifico que todos los parametros tengan algo.
			if(is_array($propiedadImagen))
			{	$this->propiedadesImagen	= $propiedadImagen;
				foreach ($propiedadImagen as $valor)
				{	if(!$valor)
					{	throw new Exception(Imagen::$msjError["ERR_Param"]);	}
				}
			}
			else
			{	throw new Exception(Imagen::$msjError["ERR_Params"]);	}
			// Verifico que si el modo es 2 existan datos para ello
			if($this->modo==2)
			{	if($this->recorte['filas']<1 || $this->recorte['columnas']<1)
				{	throw new Exception(Imagen::$msjError["ERR_Modo2"]);	}
			} 
			
			// Verifico que coincidan las extenciones fisicas y del tipo mime
			if($extencion!=$this->tiposImagenes[$propiedadImagen[2]])
			{	throw new Exception(Imagen::$msjError["ERR_Exten"]);	}
			$this->extencion	= $extencion;
			
			if(!$tamanio)
			{	throw new Exception(Imagen::$msjError["ERR_Taman"]);	}
			
			// Veo que sea un tipo permitido y que sea efectivamente de ese tipo.
			if(!array_key_exists($extencion,$this->imageCreateFrom))
			{	throw new Exception(Imagen::$msjError["ERR_ImgNP"]);	}
						
			$funcionImagen	= "imagecreatefrom".$this->imageCreateFrom[$extencion];
			$img			= $funcionImagen($imagen);
			if(!img)
			{	throw new Exception(Imagen::$msjError["ERR_ImgNC"]);	}
			
			$this->punteroImagen	= $img;
			// Fin codigo de Seguridad en Imagenes
		}
		catch (Exception $e)
		{	//echo $e->getMessage();
			return false;
		}
		return true;
		/*
		if(isset($info['APP13']))
		{	
    		$iptc = iptcparse($info['APP13']);
    		var_dump($iptc);
		}
		*/
	}
	
	/**
	 * Muestra las propiedades de una imagen.
	 * @access 	public
	 * @return	void
	 */
	public function verPropiedades()
	{	if(!$this->punteroImagen)
		{	//echo Imagen::$msjError["ERR_ImgNS"];
			return false;
		}
		
		$propiedadesEtq		= array(	0			=> 'ancho:',
										1 			=> 'alto:',
										2			=> 'tipo:',
										3 			=> 'etiquetas img:',
										'bits'		=> 'Profundidad del color (bits):',
										'channels'	=> 'Canales:',
										'mime'		=> 'Tipo Mime:'
										);
		foreach ($this->propiedadesImagen as $clave => $valor)
		{	
			//echo $propiedadesEtq[$clave]."  ".$valor."<br />"; 	
		}
		return true;
	}
	
	public function procesarImagen()
	{	
		if(!$this->punteroImagen)
		{	
			$estado	= $this->cargarImagen($this->imagenOrigen);
			if(!$estado)
			{	
				$this->__destruct();
				return ;
			}
		}
		/*
		 * Nota:
		 * propiedadesImagen[0]	equivale a $anchoOrigen
		 * propiedadesImagen[1]	equivale a $altoOrigen 
		 */
		switch ($this->modo)
		{	
			case	0:	// Respeta Proporcionalidad y toma como base el ancho.
					
					if($this->propiedadesImagen[0]<$this->anchoDestino) { $this->anchoDestino = $this->propiedadesImagen[0]; }
					$coeficiente	= $this->propiedadesImagen[0] / $this->anchoDestino;
					$anchoFinal 	= ceil($this->propiedadesImagen[0] / $coeficiente);
					$altoFinal 		= ceil($this->propiedadesImagen[1] / $coeficiente);
					$resultadoImg	= $this->crearImagenSinRecorte($anchoFinal,$altoFinal);	
					break;
		
			case	1:	// Respeta Proporcionalidad y toma como base el alto.
					if($this->propiedadesImagen[1]<$this->altoDestino) { $this->altoDestino = $this->propiedadesImagen[1]; }
					$coeficiente	= $this->propiedadesImagen[1] / $this->altoDestino;
					$anchoFinal 	= ceil($this->propiedadesImagen[0] / $coeficiente);
					$altoFinal 		= ceil($this->propiedadesImagen[1] / $coeficiente);
					$resultadoImg	= $this->crearImagenSinRecorte($anchoFinal,$altoFinal);				
					break;
					
			case	2:	// Respeta ancho y alto recortando el resto.
					if ($this->propiedadesImagen[0]>=$this->anchoDestino &&  $this->propiedadesImagen[1]>=$this->altoDestino)
					{	$resultadoImg	= $this->recortarImagen();	}
					break;
			case 	3:	// Respeta ancho y alto pero deforma la imagen.
					$resultadoImg	=$this->crearImagenSinRecorte($this->anchoDestino,$this->anchoDestino);
					break;
		}
		$this->resultado	= $resultadoImg;
	}
	
	/*
	 * Destructor.
	 * @access 	public
	 * @since 	intenta borrar el archivo de origen si se le ha pedido.
	 */
	public function __destruct()
	{	
		if($this->borrarOrigen)
		{	if(is_file($this->imagenOrigen))
			{	
				if(is_writable($this->imagenOrigen))
				{	
					unlink($this->imagenOrigen);
				}
			}
		}
	}
	
	#########################################################################	
	# METODOS GET Y SET 													#
	#########################################################################
	
	/*
	 * Metodo Magico get.
	 * Retorna el valor que tiene un atributo de la clase.
	 * @access 	public
	 * @param 	string		$propiedad	Nombre de la propiedad o Atributo que se quiere saber su valor.
	 * @return	mixed		false si no existe o el valor del atributo.
	 */
	public function get($propiedad)
	{	
		if(in_array($propiedad,$this->noPermitirGetVar))
		{	return 	false;	}
		if(!property_exists(__CLASS__,$propiedad))
		{	return false;	}
		else 
		{	return $this->$propiedad;	 }
	}
	
	/**
	 * Metodo Magico set.
	 * Modifica los valores a los atributos de clase.
	 * @access 	public
	 * @var 	String		$propiedad	Nombre de la Porpiedad o Atributo que se le cambiara su valor.
	 * @var 	mixed		$valor		Valor que se le dara a la propiedad.
	 * @var 	boolean		
	 */
	public function set($propiedad,$valor)
	{	
		if(in_array($propiedad,$this->noPermitirSetVar))
		{	return 	false;	}
		if(property_exists(__CLASS__,$propiedad))
		{	$this->$propiedad	= $valor;
			return true;
		}
		else 
		{	//echo "El Atributo ".$propiedad." no existe.";
			return false;
		}
	}
	
	#########################################################################	
	# METODOS PRIVADOS														#
	#########################################################################
	
	/**
	 * Corrobora que tenga los datos minimos para ejecutar la rutina.
	 * En caso de no ser escenciales, los regresa al modo por defecto si no existen.
	 * @access 	private
	 * @return 	boolean		false (No se creara la imagen), true (Se puede crear la imagen)
	 */
	private function datosSuficientes()
	{	// Comprobamos el Origen y Destino de la imagen que existan
		// Con estos dos parametros solo ya funcionaria.
		if(!$this->imagenOrigen)
		{	//echo Imagen::$msjError["ERR_Ori"];
			return false;
		}
		if($this->imagenDestino)
		{	//echo Imagen::$msjError["ERR_Des"];
			return false;
		}
		// Compruebo seguridad de la Imagen
		$seguridad	= $this->cargarImagen($this->imagenOrigen);
		if(!$seguridad)
		{	return false;	}
		// Veo los parametros por defecto
		if(!$this->anchoDestino)
		{	$this->anchoDestino	= $this->propiedadesImagen[0];	}
		if(!$this->altoDestino)
		{	$this->altoDestino	= $this->propiedadesImagen[1];	}
		// Controles de Modo
		if($this->modo==2)
		{	if( (int)($this->recorte["filas"])<1 || (int)($this->recorte["columnas"])<1   )
			{	$this->recorte			= array('filas'	=> 3, 'columnas'	=> 3, 'centrado'	=>	4);	}
			else
			{	$opcionesRecorte		= $this->recorte["filas"]*$this->recorte["columnas"]-1;
				if($this->recorte["centrado"]>$opcionesRecorte)
				{	$this->recorte["centrado"]	= $opcionesRecorte;	}
			}	
		}
		return true;
	}
	
	/**
	 * Extrae la extencion que trae el archivo basandose en su nombre solamente.
	 * @access 	private
	 * @param	string		$archivo	Ruta hacia el archivo de imagen.
	 * @return	string		Contiene la extencion del archivo en Mayusculas.
	 */
	private function extraerExtencion($archivo)
	{	
		$puntero	= strripos($archivo,".");
		$extencion	= strtoupper(substr($archivo,$puntero+1));
		return $extencion;
	}
	
	/**
	 * Metodo CrearImagenSinRecorte.
	 * Es un sub-metodo que termina creando la imagen de Destino.
	 * @access 	private
	 * @param 	int 		$anchoFinal es el ancho final de la imagen destino. Si modo es 0 coincide con el propuesto , si no podria variar.
	 * @param 	int 		$altoFinal es el alto final de la imagen destino. Si modo es 1 coincide con el propuesto , si no podria variar.
	 * @return	Archivo		Es una imagen que se almacena en la direccion establecida.
	 */
	private function crearImagenSinRecorte($anchoFinal,$altoFinal)
	{
		$ImgMediana 	= imagecreatetruecolor($anchoFinal,$altoFinal);
		imagecopyresampled($ImgMediana,$this->punteroImagen,0,0,0,0,$anchoFinal,$altoFinal,$this->propiedadesImagen[0],$this->propiedadesImagen[1]);
		$this->crearArchivoDeImagen($ImgMediana);
		imagedestroy($ImgMediana);
	}
	
	/**
	 * Genera la Salida de la Imagen a un archivo, en el directorio especificado.
	 * @access 	private
	 * @var 	resource	$imgTrueColor	Es una referencia a una imagen creada con la funcion imagecreatetruecolor().
	 * @var 	String		$imgDestino		Nombre del archivo a generar. Si no se pasa se asume que es el de Imagen Destino.
	 * @return 	imagen		Archivo de Imagen
	 */
	private function crearArchivoDeImagen($imgTrueColor,$imgDestino='')
	{	if(empty($imgDestino))
		{	$imgDestino	= $this->imagenDestino;	}		
		
		if($this->calidadImagen<10) {	$this->calidadImagen = 10;	}
		switch ($this->extencion)
		{	case "JPG":	// Crea una imgen jpg
						imagejpeg($imgTrueColor,$imgDestino,$this->calidadImagen);
						break;
			case "PNG":	// Crea una imagen png
						($this->calidadImagen>99)? $comprension = 9:$comprension	= floor($this->calidadImagen/10);
						imagepng($imgTrueColor,$imgDestino,$comprension);
						break;
			case "GIF": // Crea una imagen gif
						imagegif($imgTrueColor,$imgDestino);		
						break;
		}
	}
	
	/*
	 * Metodo RecortarImagen.
	 * Se encarga de Recortar la imagen de origen y llevarla exactamente a los valores de destino.
	 * Trata de perder la porcion minima de imagen posible.
	 */	
	private function recortarImagen()
	{
		$ImgTemporal="temporal_clase_Imagen.".strtolower($this->extencion);

		$CoefAncho		= $this->propiedadesImagen[0]/$this->anchoDestino;
		$CoefAlto		= $this->propiedadesImagen[1]/$this->altoDestino;
		$Coeficiente=0;
		if ($CoefAncho>1 && $CoefAlto>1)
		{ if($CoefAncho>$CoefAlto){ $Coeficiente=$CoefAlto; } else {$Coeficiente=$CoefAncho;} }

		if ($Coeficiente!=0)
		{
			$anchoTmp	= ceil($this->propiedadesImagen[0]/$Coeficiente);
			$altoTmp	= ceil($this->propiedadesImagen[1]/$Coeficiente);

			$ImgMediana = imagecreatetruecolor($anchoTmp,$altoTmp);
			imagecopyresampled($ImgMediana,$this->punteroImagen,0,0,0,0,$anchoTmp,$altoTmp,$this->propiedadesImagen[0],$this->propiedadesImagen[1]);
			
			// Tengo que desagregar la funcion de image para crear para reUtilizarla
			//imagejpeg($ImgMediana,$ImgTemporal,97);
			$this->crearArchivoDeImagen($ImgMediana,$ImgTemporal);
		}

		$fila			= floor($this->recorte['centrado']/$this->recorte['columnas']);
		$columna		= $this->recorte['centrado'] - ($fila*$this->recorte["columnas"]);
		
		$centroX 	= floor(($anchoTmp / $this->recorte["columnas"])/2)+$columna*floor($anchoTmp / $this->recorte["columnas"]);
		$centroY 	= floor(($altoTmp / $this->recorte["filas"])/2)+$fila*floor($altoTmp / $this->recorte["filas"]);

		$centroX	-= floor($this->anchoDestino/2);
		$centroY 	-= floor($this->altoDestino/2);

		if ($centroX<0) {$centroX = 0;}
		if ($centroY<0) {$centroY = 0;}

		if (($centroX+$this->anchoDestino)>$anchoTmp) {$centroX = $anchoTmp-$this->anchoDestino;}
		if (($centroY+$this->altoDestino)>$altoTmp) {$centroY = $altoTmp-$this->altoDestino;}

		$ImgRecortada = imagecreatetruecolor($this->anchoDestino,$this->altoDestino);
		imagecopymerge ( $ImgRecortada,$ImgMediana,0,0,$centroX, $centroY, $this->anchoDestino, $this->altoDestino,100);

		//imagejpeg($ImgRecortada,$this->imagenDestino,97);
		$this->crearArchivoDeImagen($ImgRecortada,$this->imagenDestino);
		imagedestroy($ImgRecortada);
		unlink($ImgTemporal);
	}
}
##########################################################################################
# EJEMPLO DE USO.-								                                         #
##########################################################################################
/*
$datos		= '{ 	"imgOrigen"	: "cuboFrente.png",
 	 		 		"imgDestino": "cuboFrente_edit.png",
 	 		 		"ancho"		: "100",
 	 				"alto"		: "80",
 	 		 		"modo"		: 0,
 	 		 		"filas"		: 3,
 	 		 		"calidad"	: 95,
					"columnas"	: 4,
 	 		 		"centrado"	: 11,
 	 		 		"borrar"	: true
 	 			 }';

// Si es con variables
$datos		= '{ 
					"imgOrigen"	: "'.$foto.'",
 	 		 		"imgDestino": "FOTOS_CHICAS/'.$foto.'",
 	 		 		"ancho"		: "1024",
 	 				"alto"		: "768",
 	 		 		"modo"		: 0,
 	 		 		"filas"		: 3,
 	 		 		"columnas"	: 4,
 	 		 		"centrado"	: 11,
 	 		 		"borrar"	: false
 	 			 }';
*/

/*
 * Si se envia a crear una imagen de mayor tamaño a la original.
 * El modo 0 simplemente regresa la imagen con el mismo tamaño.
 * modo 1 lo mismo
 * modo 2 no regresa nada+
 * modo 3 reescala la i magen a los atributos pasados
 */

/*
$obj_img	= new Imagen();
$obj_img	-> set("imagenOrigen","cuboFrente.png");
$obj_img	-> set("imagenDestino",'pruebaSetsss.png');
$obj_img	-> set("anchoDestino",'250');
$obj_img	-> set("altoDestino",1500);
$obj_img	-> set("recorte",array('filas'	=> 3, 'columnas'	=> 4, 'centrado'	=>	12));
$obj_img	-> set ("calidadImagen",100);
$obj_img	-> set("modo",0);
$obj_img	-> procesarImagen();
*/
?>