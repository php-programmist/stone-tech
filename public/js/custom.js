$('.slider').glide({
    autoplay: 13000,
    hoverpause: false,
});

$('.ddr').find('br').remove();
$('.item-2').find('br').remove();

$('.ddr').animate({left:'-=957'}, 50000, 'linear');
$('.ddr').animate({left:'+=957'}, 50000, 'linear');

$(".gallery").imagesGrid({
    rowHeight: 246,
    margin: 10,
    imageSelector: 'img'
});

//$('button.popup-button').click(function () {showPopup2()});


$('.items .popup-button').addClass('raschet');
$('.items .popup-button1').addClass('choise');
$('input[type=phone]').attr('name', 'form-phone');
var thisuri = "http://st-wp/";
jQuery(document).ready(function($){
    //Скроллер вверх
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('.scrollup').fadeIn();
            } else {
                $('.scrollup').fadeOut();
            }
        });

        $('.scrollup').click(function () {
            $("html, body").animate({scrollTop: 0}, 600);
            return false;
        });

    //Скроллер вверх конец

    var formModal = $('.cd-user-modal'),
        formZvonok = formModal.find('#cd-zvonok'),
        formRaschet = formModal.find('#cd-raschet'),
        formChoise = formModal.find('#cd-choise'),
        formBottom = $('#cd-bottom'),
        formApplication = $('#application'),
        mainNav = $('body');

    //open modal
    mainNav.on('click', function(event){
        $(event.target).is(mainNav) && mainNav.children('ul').toggleClass('is-visible');
    });

    mainNav.on('click', '.zvonok', zvonok_selected);
    mainNav.on('click', '.raschet', raschet_selected);
    mainNav.on('click', '.choise', choise_selected);

    //close modal
    formModal.on('click', function(event){
        if( $(event.target).is(formModal) || $(event.target).is('.cd-close-form') ) {
            formModal.removeClass('is-visible');
            $('html').css('overflow-y', 'auto');
            $('.cd-close-form').css('display','none');
        }
    });

    //close modal when clicking the esc keyboard button
    $(document).keyup(function(event){
        if(event.which=='27'){
            formModal.removeClass('is-visible');
            $('.cd-close-form').css('display','none');
        }
    });

    function choise_selected(){
        $('.cd-close-form').css('display','block');
        formModal.addClass('is-visible');
        formRaschet.removeClass('is-selected');
        formZvonok.removeClass('is-selected');
        formChoise.addClass('is-selected');
        $('html').css('overflow-y', 'hidden');

    }

    function zvonok_selected(){
        $('.cd-close-form').css('display','block');
        formModal.addClass('is-visible');
        formRaschet.removeClass('is-selected');
        formChoise.removeClass('is-selected');
        formZvonok.addClass('is-selected');
        $('html').css('overflow-y', 'hidden');
    }

    var cart_text;

    function raschet_selected(){
        $('.cd-close-form').css('display','block');
        formModal.addClass('is-visible');
        formZvonok.removeClass('is-selected');
        formRaschet.addClass('is-selected');
        formChoise.removeClass('is-selected');
        $('html').css('overflow-y', 'hidden');
        cart_text = $(this).parent().parent().parent().find('h3').text();
    }

    formApplication.find('.applicationButton').on('click', function(event){
        event.preventDefault();

        var form = $(this).closest('form');
        var form_phone = $(form).find('input[name=telephone]');
        var form_name = $(form).find('input[name=name]');


        if (!$(form_phone).val() || !$(form_name).val() ) {
            if(!$(form_phone).val()) {
                $(form_phone).css('border-color', 'red');
            } else {
                $(form_phone).css('border-color', '#d2d8d8');
            }

            if(!$(form_name).val()) {
                $(form_name).css('border-color', 'red');
            } else {
                $(form_name).css('border-color', '#d2d8d8');
            }

        }
        else {
            $(form_phone).css('border-color', '#d2d8d8');
            $(form_name).css('border-color', '#d2d8d8');

            $.ajax({
                type: 'post',
                url: '/application',
                data:
                    $(form).serialize()
                ,
                success: function(response) {
                    if (response !== 'ERROR') {
                        console.log(response);
                        $('.successSendForm').text('Ваша заявка отправлена!');
                        $('.successSendForm').css('display','block');
                    }
                }
            });
        }
    });

    formRaschet.find('.form-submit').on('click', function(event){
        event.preventDefault();

        var form = $(this).closest('form');
        var form_phone = $(form).find('input[name=form-phone]');
        var form_name = $(form).find('input[name=client-name]');
        var agreement = $(form).find('input[name=agreement]');

        if (!$(form_phone).val() || !$(form_name).val() || agreement.prop('checked') === false) {
            if(!$(form_phone).val()) {
                $(form_phone).css('border-color', 'red');
            } else {
                $(form_phone).css('border-color', '#d2d8d8');
            }

            if(!$(form_name).val()) {
                $(form_name).css('border-color', 'red');
            } else {
                $(form_name).css('border-color', '#d2d8d8');
            }

            if (agreement.prop('checked') === false) {
                $('.checkbox-js-style').remove();
                $(agreement).next('label').append('<style class="checkbox-js-style">#cd-raschet .checkbox-custom + .checkbox-custom-label:before{ border-color: red; }</style>');
            } else {
                $('.checkbox-js-style').remove();
                $(agreement).next('label').append('<style class="checkbox-js-style">#cd-raschet .checkbox-custom + .checkbox-custom-label:before{ border-color: #d2d8d8; }</style>');
            }
        }
        else {
            $(form_phone).css('border-color', '#d2d8d8');
            $(form_name).css('border-color', '#d2d8d8');
            const data = $(form).serialize()+`&url=${window.location.href}&title=${document.title}`;

            $.ajax({
                type: 'post',
                url: '/raschet_form',
                data,
                success: function(response) {
                    if (response !== 'ERROR') {
                        console.log(response);
                        $(form).find('.form-body').hide();
                        $(form).find('.check_mark').show();
                    }
                }
            });
        }
    });


    formChoise.find('.form-submit').on('click', function(event){
        event.preventDefault();

        var form = $(this).closest('form');
        var form_phone = $(form).find('input[name=form-phone]');
        var form_name = $(form).find('input[name=client-name]');
        var agreement = $(form).find('input[name=agreement]');

        if (!$(form_phone).val() || !$(form_name).val() || agreement.prop('checked') === false) {
            if(!$(form_phone).val()) {
                $(form_phone).css('border-color', 'red');
            } else {
                $(form_phone).css('border-color', '#d2d8d8');
            }

            if(!$(form_name).val()) {
                $(form_name).css('border-color', 'red');
            } else {
                $(form_name).css('border-color', '#d2d8d8');
            }

            if (agreement.prop('checked') === false) {
                $('.checkbox-js-style').remove();
                $(agreement).next('label').append('<style class="checkbox-js-style">#cd-raschet .checkbox-custom + .checkbox-custom-label:before{ border-color: red; }</style>');
            } else {
                $('.checkbox-js-style').remove();
                $(agreement).next('label').append('<style class="checkbox-js-style">#cd-raschet .checkbox-custom + .checkbox-custom-label:before{ border-color: #d2d8d8; }</style>');
            }
        }
        else {
            $(form_phone).css('border-color', '#d2d8d8');
            $(form_name).css('border-color', '#d2d8d8');

            $.ajax({
                type: 'post',
                url: '/callback_form',
                data:
                    $(form).serialize()
                ,
                success: function(response) {
                    if (response !== 'ERROR') {
                        console.log(response);
                        $(form).find('.form-body').hide();
                        $(form).find('.check_mark').show();
                    }
                }
            });
        }
    });

    formZvonok.find('.form-submit').on('click', function(event){
        event.preventDefault();
        var form = $(this).closest('form');
        var form_phone = $(form).find('input[name=form-phone]');
        var form_name = $(form).find('input[name=client-name]');
        var agreement = $(form).find('input[name=agreement]');

        if (!$(form_phone).val() || !$(form_name).val() || agreement.prop('checked') === false) {
            if(!$(form_phone).val()) {
                $(form_phone).css('border-color', 'red');
            } else {
                $(form_phone).css('border-color', '#d2d8d8');
            }

            if(!$(form_name).val()) {
                $(form_name).css('border-color', 'red');
            } else {
                $(form_name).css('border-color', '#d2d8d8');
            }

            if (agreement.prop('checked') === false) {
                $('.checkbox-js-style').remove();
                $(agreement).next('label').append('<style class="checkbox-js-style">#cd-zvonok .checkbox-custom + .checkbox-custom-label:before{ border-color: red; }</style>');
            } else {
                $('.checkbox-js-style').remove();
                $(agreement).next('label').append('<style class="checkbox-js-style">#cd-zvonok .checkbox-custom + .checkbox-custom-label:before{ border-color: #d2d8d8; }</style>');
            }
        } else {
            $(form_phone).css('border-color', '#d2d8d8');
            $(form_name).css('border-color', '#d2d8d8');
            const data = $(form).serialize()+`&url=${window.location.href}&title=${document.title}`;
            $.ajax({
                type: 'post',
                url: '/raschet_form',
                data,
                success: function(response) {
                    if (response !== 'ERROR') {
                        console.log(response);
                        $(form).find('.form-body').hide();
                        $(form).find('.check_mark').show();
                    }
                }
            });
        }
    });

    formBottom.find('.form-submit').on('click', function(event){
        event.preventDefault();
        var form = $(this).closest('form');
        var form_phone = $(form).find('input[name=form-phone]');
        var form_name = $(form).find('input[name=u-name]');

        if (!$(form_phone).val() || !$(form_name).val()) {
            if(!$(form_phone).val()) {
                $(form_phone).css('border', '1px solid red');
            } else {
                $(form_phone).css('border', 'none');
            }

            if(!$(form_name).val()) {
                $(form_name).css('border', '1px solid red');
            } else {
                $(form_name).css('border', 'none');
            }
        } else {
            $(form_phone).css('border', 'none');
            $(form_name).css('border', 'none');

            $.ajax({
                type: 'post',
                url: '/formchecker.php',
                data: $(form).serialize(),
                success: function(response) {
                    if (response !== 'ERROR') {
                        console.log(response);
                        $('#bottom .row').fadeOut();
                        $('#bottom h3').fadeOut();
                        $('#bottom h2').text('СПАСИБО, МЫ СВЯЖЕМСЯ С ВАМИ В БЛИЖАЙШЕЕ ВРЕМЯ!');
                    }
                }
            });
        }
    });


    //IE9 placeholder fallback
    //credits https://www.hagenburger.net/BLOG/HTML5-Input-Placeholder-Fix-With-jQuery.html
    if(!Modernizr.input.placeholder){
        $('[placeholder]').focus(function() {
            var input = $(this);
            if (input.val() == input.attr('placeholder')) {
                input.val('');
            }
        }).blur(function() {
            var input = $(this);
            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                input.val(input.attr('placeholder'));
            }
        }).blur();
        $('[placeholder]').parents('form').submit(function() {
            $(this).find('[placeholder]').each(function() {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                }
            })
        });
    }

});
