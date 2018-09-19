// JavaScript Document
$(document).ready(main); //esperar a que carge todo
$(document).ready(fotitos);

var banderaMenu = true;

function main () { //funcion llamda main
	$('.menu_bar').click(function(){ //cuando presiones la barra de menu
		if (banderaMenu) {
			banderaMenu = false;
			$('nav').animate({ //aplica una animacion para desplasarlo
				top: '50px'
			});
		} else {
			banderaMenu = true;
			$('nav').animate({
				top: '-100%'
			});
		}
	});

	// Mostramos y ocultamos submenus
	
	$('.submenu').click(function(){
		if($(this).children('.children').css('display')!=='none') {
			$(this).children('.children').slideToggle();
		}
		else
		{
			$('.submenu').each(function() {
				if($(this).children('.children').css('display')!=='none') {
					$(this).children('.children').slideToggle();
				}
			});
			$(this).children('.children').slideToggle();
		}
	});
	$(".container").click(function() {
		//Hide the menus if visible
		$('.submenu').each(function() {
			if($(this).children('.children').css('display')!=='none') {
				$(this).children('.children').slideToggle();
			}
		});
	});
}

function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

var banderaFoto = true;

function fotitos () { //funcion llamda main
	$('.fotoGal').click(function(){ //cuando presiones la barra de menu
		if (banderaFoto) {
			banderaFoto = false;
			var centrado = ( $(window).width() - ($(this).width() ));
			centrado =0;
			$(this).css({ //aplica una animacion para desplasarlo
				position:'fixed',
				zIndex:'10000', 
				top: '5%',
				left:'5%',
				width:'90%',
				height:'90%',
				backgroundColor:'rgba(0, 0, 0, 0.8)'
			});
			$(this).children('img').css({ //aplica una animacion para desplasarlo
				maxWidth:'100%'
			});
			console.log("clic A en imagen ");
		} else {
			banderaFoto = true;
			$(this).children('img').css({ //aplica una animacion para desplasarlo
				maxWidth:'40px'
			});
			$(this).css({
				height:'32px',
				zIndex:'1', 
				top: '0px',
				left:'0px',
				position:'relative',
				backgroundColor:'white'
			});
			console.log("clic B en imagen ");
		}
	});
}

