//Код jQuery, установливающий маску для ввода телефона элементу input
//1. После загрузки страницы,  когда все элементы будут доступны выполнить...
$(function () {
	//2. Получить элемент, к которому необходимо добавить маску
	$("[name=form-phone]").mask("8 (999) 999-9999");
	
	let headertop = $('#menu-main').height();
	$('#menu-main').append("<li id='header_phone'>8(495)767-23-83</li>");
	let header_phone = $('#header_phone');
	header_phone.hide();
	$(window).scroll(function () {
		if ($(window).scrollTop() > headertop) {
			$('ul#menu-main > li.menu-item:last').hide();
			header_phone.show();
		}
		else {
			$('ul#menu-main > li.menu-item:last').show();
			header_phone.hide();
		}
		
	});


	$('.js-mobile-menu-burger').click(function (){
		$('.mobile-menu__container').toggleClass('visible');
	});

	$('.js-submenu-toggle').click(function (){
		$(this).parent().parent().toggleClass('d-hover-effect');
	});
});



